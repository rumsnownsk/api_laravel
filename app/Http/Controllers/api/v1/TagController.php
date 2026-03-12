<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Http\Resources\TagResource;
use Illuminate\Http\JsonResponse;
use App\Models\Tag;
use App\Models\Topic;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tags = Tag::query()->get();
        return TagResource::collection($tags);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTagRequest $request)
    {
        $data = $request->validated();
        return new TagResource(Tag::create($data));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = Tag::where('id', $id)->firstOrFail();
        return new TagResource($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTagRequest $request, $id)
    {
        $item = Tag::where('id', $id)->firstOrFail();
        $data = $request->validated();
        $item->update($data);
        return new TagResource($item);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $tag = Tag::findOrFail($id);
            DB::transaction(function () use ($tag) {

                // проверяем есть ли связи с Topic
                $hasPosts = $tag->posts()->exists();
                if ($hasPosts) {
                    // Открепляем тег от всех постов (удаляем записи из post_tag)
                    $tag->posts()->detach();
                }
                $tag->delete();
            });
            return response()->json([
                'success' => true,
                'message' => 'Tag deleted successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Тег не найден'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Ошибка удаления тега: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при удалении тега'
            ], 500);
        }

    }
}
