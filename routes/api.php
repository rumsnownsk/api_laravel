<?php

use App\Http\Controllers\api\v1\AuthController;
use App\Http\Controllers\api\v1\ImportController;
use App\Http\Controllers\api\v1\PostController;
use App\Http\Controllers\api\v1\SaleController;
use App\Http\Controllers\api\v1\TopicController;
use App\Http\Controllers\api\v1\TagController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('api')->prefix('v1')->group(function () {

    Route::apiResource('topics', TopicController::class)
        ->except(['create', 'edit']);

    Route::apiResource('tags', TagController::class)
        ->except(['create', 'edit']);

    Route::ApiResource('posts', PostController::class)
        ->except(['create', 'edit']);


    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    Route::get('user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');



    /**
     * РОУТ ДЛЯ ImportController
     */
    Route::get('import/{type}', [ImportController::class, 'importData'])
    ->where('type', 'sales|orders|stocks|incomes');
});


