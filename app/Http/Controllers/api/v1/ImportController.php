<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Sale;
use App\Services\ImportModelFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportController extends Controller
{
    public string $key;
    public string $type;
    protected string $dateFrom = '2020-01-01';
    protected string $dateTo;
    protected int $limit = 500;

    public function __construct()
    {
        $this->type = request()->route('type');
        if (!$this->type) { // Проверяем, что тип установлен
            abort(404, 'Page Not Found');
        }
        $this->key = env('API_KEY');
        $this->dateTo = date('Y-m-d'); // до сегодняшнего дня
//        $this->dateTo = '2025-05-21';  // для отладки, по меньше данных
        if ($this->type == 'stocks'){
            $this->dateFrom = date('Y-m-d');
        }
    }

    public function importData($type): JsonResponse
    {
        if (!isset($type)) {
            return response()->json([
                'success' => false,
                'message' => 'Page Not Found',
            ], 404);
        }

        $page = 1;
        $totalSaved = 0;

        try {
            // Загрузка только первой страницы, и для получения всей информации об объеме данных
            $firstResponse = $this->fetchPage($page);

            if (!$firstResponse['success']) {
                return response()->json($firstResponse, $firstResponse['status'] ?? 500);
            }

            $totalTypes = $firstResponse['countLoadTypes'];
            $totalPages = $firstResponse['countPagesPagination'];

            Log::info("Starting import: {$totalTypes} sales across {$totalPages} pages");

            // сохранение первой страницы
            $totalSaved += $this->saveToDatabase($firstResponse['data']);

            // загрузка остальных страниц
            for ($page = 2; $page <= $totalPages; $page++) {
                $response = $this->fetchPage($page);
                if (!$response['success']) {
                    Log::warning("Failed to fetch page {$page}: " . $response['error']);
                    continue;
                }

                // сохранение каждой страницы
                $savedCount = $this->saveToDatabase($response['data']);
                $totalSaved += $savedCount;

                // Логируем прогресс каждые 10 страниц
                if ($page % 10 == 0) {
                    Log::info("Progress: Page {$page}/{$totalPages}, Total saved: {$totalSaved}");
                }
                sleep(1); // пауза для снижения нагрузки на API
            }
            return response()->json([
                'success' => true,
                'message' => "Successfully imported all $this->type",
                'total_types' => $totalTypes,
                'total_pages' => $totalPages,
                'saved_count' => $totalSaved,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error importing sales data: ' . $e->getMessage());
            return response()->json([
                'error' => "Error importing $this->type data",
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Загрузка одной страницы
     */
    private function fetchPage($page): array
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])
            ->timeout(30)
            ->get("http://109.73.206.144:6969/api/$this->type", [
                'dateFrom' => $this->dateFrom,
                'dateTo' => $this->dateTo,
                'page' => $page,
                'limit' => $this->limit,
                'key' => $this->key
            ]);

        if (!$response->successful()) {
            return [
                'success' => false,
                'error' => 'Failed to fetch data from external API',
                'status' => $response->status(),
                'message'=>$response->body()
            ];
        }

        $jsonData = $response->json();

        if (!isset($jsonData['data']) || !is_array($jsonData['data'])) {
            return [
                'success' => false,
                'error' => 'Invalid JSON structure: missing "data" field',
            ];
        }

        return [
            'success' => true,
            'data' => $jsonData['data'],
            'countLoadTypes' => $jsonData['meta']['total'] ?? 0,
            'countPagesPagination' => $jsonData['meta']['last_page'] ?? 1,
        ];
    }

    /**
     * Сохранение в свою Базу по 500 штук за раз
     */
    private function saveToDatabase($data): int
    {
        $savedCount = 0;

        $model = ImportModelFactory::create($this->type);
        $modelClass = get_class($model);

        foreach (array_chunk($data, 500) as $chunk) {
            $insertData = [];
            foreach ($chunk as $sale) {
                $insertData[] = $sale;
            }

            if (!empty($insertData)) {
                $modelClass::insert($insertData);
                $savedCount += count($insertData);
            }
        }
        return $savedCount;
    }
}
