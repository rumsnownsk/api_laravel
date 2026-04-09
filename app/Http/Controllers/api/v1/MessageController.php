<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\FeedbackMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        // Защита от частых запросов и дневного лимита
        if (!$this->allowRequest()) {
            Log::warning('Попытка превышения лимита', ['ip' => $request->ip()]);

            return response()->json([
                'error' => 'Слишком много запросов. Попробуйте позже.',
                'message' => 'От вашего клиента поступает слишком много запросов. Попробуйте позже',
            ], 429);
        }

        Validator::extend('custom_contact', function ($attribute, $value, $parameters, $validator) {
            // Проверяем, является ли значение email
            if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                return true;
            }

            // Очистка номера телефона от лишних символов и проверка телефона
            // Базовые проверки для телефона:
            // - начинается с + или цифры
            // - длина от 7 до 15 цифр
            $cleanedPhone = preg_replace('/[^0-9+]/', '', $value);
            if (preg_match('/^\+?[0-9]{7,15}$/', $cleanedPhone)) {
                return true;
            }

            return false;
        });

        $validator = Validator::make($request->all(), [
            'contactInfo' => 'required|string|max:255|custom_contact',
            'topic' => 'nullable|string|max:255',
            'message' => 'required|string|min:10|max:2000',
//            'g-recaptcha-response' => 'required|recaptcha',
        ], [
            'contactInfo.required' => 'Контактные данные обязательны для связи',
            'contactInfo.custom_contact' => 'Введите корректный email или номер телефона',
            'message.required' => 'Сообщение не может быть пустым',
            'message.min' => 'Сообщение должно содержать минимум 10 символов'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Ошибка валидации. Пожалуйста, проверьте правильность заполнения полей формы',
            ], 422);
        }

        try {
            // Создаём или находим пользователя по слепку
            $user = $this->getOrCreateUser($request);
            $contactInfo = $request->contactInfo;
            // Определяем тип контакта
            $contactType = filter_var($contactInfo, FILTER_VALIDATE_EMAIL)
                ? 'email'
                : 'phone';
            if ($contactType !== 'email') {
                $contactInfo = $this->normalizePhone($contactInfo);
            }
            $feedback = FeedbackMessage::create([
                'user_id' => $user->id,
                'contactInfo' => $contactInfo,
                'contact_type' => $contactType,
                'topic' => $request->input('topic'),
                'message' => $request->input('message'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'referrer' => $request->headers->get('referer'),
                'is_spam' => $this->isSpam($request),
                'spam_reason' => $this->getSpamReason($request)
            ]);

            Log::info('Успешно создан фидбэк', ['feedback_id' => $feedback->id]);

            // Отправка уведомления администратору (опционально)
            $this->sendAdminNotification($feedback);

            return response()->json([
                'success' => true,
                'message' => 'Сообщение успешно отправлено!',
                'data' => $feedback
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Ошибка сохранения сообщения обратной связи: ' . $e->getMessage());
            return response()->json([
                'error' => 'Произошла ошибка при отправке сообщения',
                'message'=>'мысаги: '.$e->getMessage(),
            ], 500);
        }
    }

    private function allowRequest(): bool
    {
        $ip = request()->ip();
        $key = 'feedback_' . $ip;

        // Проверка общего лимита: не более 10 фидбэков в сутки с любого IP
        $dailyLimitKey = 'daily_feedback_' . $ip;
        if (RateLimiter::tooManyAttempts($dailyLimitKey, 10)) {
            return false;
        }

        // Лимит частых запросов: 3 попытки за 5 минут
        $rateLimit = RateLimiter::attempt(
            $key,
            5,
            fn() => true,
            300
        );

        // Если лимит частых запросов пройден, увеличиваем счётчик дневного лимита
        if ($rateLimit) {
            RateLimiter::hit($dailyLimitKey, 86400); // 24 часа в секундах
        }

        return $rateLimit;
    }

    private function normalizePhone(string $phone): string
    {
        $cleaned = preg_replace('/[^0-9+]/', '', $phone);

        // Если номер уже с международным кодом
        if (str_starts_with($cleaned, '+')) {
            return $cleaned;
        }
        // Для российских номеров добавляем +7
        if (strlen($cleaned) === 10 && str_starts_with($cleaned, '9')) {
            return '+7' . $cleaned;
        }
        return $cleaned;
    }

    private function isSpam(Request $request): bool
    {
        $message = $request->input('message');
        $contactInfo = $request->input('contactInfo');

        // Простые правила спама
        $spamKeywords = ['viagra', 'casino', 'loan', 'buy now', 'free money', 'xxx', 'porn'];
        foreach ($spamKeywords as $keyword) {
            if (stripos($message, $keyword) !== false) {
                return true;
            }
        }

        // Проверка на подозрительные символы в контактных данных
        if (preg_match('/[<>{}]/', $contactInfo)) {
            return true;
        }

        return false;
    }

    private function getSpamReason(Request $request): ?string
    {
        if (!$this->isSpam($request)) {
            return null;
        }

        $message = $request->input('message');

        // Проверка ключевых слов
        $spamKeywords = ['viagra', 'casino', 'loan', 'buy now', 'free money', 'xxx', 'porn'];
        foreach ($spamKeywords as $keyword) {
            if (stripos($message, $keyword) !== false) {
                return 'Содержит спам‑ключевое слово: ' . $keyword;
            }
        }

        // Проверка повторяющихся символов
        if (preg_match('/(.)\1{5,}/', $message)) {
            return 'Повторяющиеся символы в сообщении';
        }

        // Проверка подозрительных символов в контактах
        if (preg_match('/[<>{}]/', $request->input('contactInfo'))) {
            return 'Подозрительные символы в контактных данных';
        }

        return 'Неизвестная причина спама';
    }

    private function sendAdminNotification(FeedbackMessage $feedback): void
    {
        // Здесь можно добавить отправку email, Telegram‑уведомления и т. д.
        // Пример для email:
        /*
        \Mail::to('admin@example.com')->send(new FeedbackReceived($feedback));
        */
    }

    private function getOrCreateUser(Request $request): User
    {
        $ip = $request->ip();
        $userAgent = $request->header('User-Agent');
        $fingerprintHash = md5($ip . $userAgent);

        $user = User::firstOrCreate(
            ['fingerprint_hash' => $fingerprintHash],
            [
                'name'=>$ip,
                'email'=>null,
                'password'=>'none_password',
                'ip_address' => $ip,
                'user_agent' => $userAgent,
                'geolocation' => $this->getGeolocation($ip),
                'first_feedback_at' => now(),
                'last_feedback_at' => now()
            ]
        );

        // Обновляем время последнего фидбэка
        if (!$user->wasRecentlyCreated) {
            $user->update(['last_feedback_at' => now()]);
        }

        return $user;
    }

    private function getGeolocation(string $ip): ?string
    {
        try {
            // Используем бесплатный API для геолокации
            $response = Http::get("http://ip-api.com/json/{$ip}");
            if ($response->successful()) {
                $data = $response->json();
                return "{$data['city']}, {$data['country']}";
            }
        } catch (\Exception $e) {
            \Log::warning('Не удалось получить геолокацию для IP: ' . $ip);
        }
        return null;
    }
}
