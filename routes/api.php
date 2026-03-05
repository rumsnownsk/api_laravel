<?php

use App\Http\Controllers\api\v1\PostController;
use App\Http\Controllers\api\v1\TopicController;
use App\Http\Controllers\api\v1\TagController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('api')->prefix('v1')->group(function () {

    Route::apiResource('topics', TopicController::class)
//    ->middleware('auth:sanctum')
        ->except(['create', 'edit']);

//    Route::get('topics/{topicId}/tags', [TopicController::class, 'getTagsByTopic']);


    Route::apiResource('tags', TagController::class)
        ->except(['create', 'edit']);
//    Route::get('tags/by_topic_id/{topicId}', [TagController::class, 'tagsByTopicId']);


    Route::ApiResource('posts', PostController::class);
//    Route::get('posts/by_topic_id/{topicId}', [PostController::class, 'postsByTopicId']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
