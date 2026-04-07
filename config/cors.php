<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'], // Пути, к которым применяется CORS

    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'], // Разрешённые HTTP-методы

//    'allowed_origins' => [env('FRONTEND_URL', 'http://localhost:3000')],
    'allowed_origins' => [],

    'allowed_origins_patterns' => [
        '/^https?:\/\/([a-zA-Z0-9-]+\.)?iocode\.ru$/'
    ],

    'allowed_headers' => [
        'Content-Type',
        'Authorization',
        'X-Requested-With',
        'X-XSRF-TOKEN',    // обязательно для CSRF
        'X-CSRF-TOKEN'    // альтернативное имя
    ],

    'exposed_headers' => [],

    'max_age' => 3600,

    'supports_credentials' => true, // Поддержка учётных данных (cookies, авторизация)

];
