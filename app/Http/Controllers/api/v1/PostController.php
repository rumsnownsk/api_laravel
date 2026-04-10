<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PostController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $topicId = $request->input('topic');
        $tags = $request->input('tags', []);

        $query = Post::select('posts.*')
            ->with(['topic', 'tags']);

        if (!empty($tags)) {
            $query->join('post_tag', 'posts.id', '=', 'post_tag.post_id')
                ->whereIn('post_tag.tag_id', $tags);

            // Если нужно, чтобы пост имел ВСЕ указанные теги
            if (count($tags) > 1) {
                $query->groupBy('posts.id')
                    ->havingRaw('COUNT(DISTINCT post_tag.tag_id) = ?', [count($tags)]);
            } else {
                $query->groupBy('posts.id');
            }
        }

        if ($topicId) {
            $query->where('posts.topic_id', $topicId);
        }


        // TODO добавить пагинациюб
        // Добавляем пагинацию (10 постов на страницу)
        $posts = $query->get();

        return response()->json([
            'success' => true,
            'data' => PostResource::collection($posts),

            'filters_applied' => [
                'tags' => $tags,
                'topic' => $topicId
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        try {
            $post = DB::transaction(function () use ($request) {
                $validated = $request->validated();

                // Создаём пост
                $post = Post::create([
                    'title' => $validated['title'],
                    'body' => $validated['body'],
                    'topic_id' => $validated['topic_id'],
                    'description' => $validated['description'],
                    'slug' => Str::slug($validated['title'])
                ]);
//                dd($post);

                // Связываем теги (attach безопасно работает с пустым массивом)
                if (!empty($validated['tag_ids'])) {
                    $post->tags()->attach($validated['tag_ids']);
                }
                return $post; // Возвращаем созданный пост из DB::transaction
            });


            return response()->json([
                'message' => 'Post created successfully',
                'data' => new PostResource($post)
            ], 201); // 201 Created — корректный статус для создания

        } catch (\Exception $e) {
            // Логируем ошибку для отладки
            Log::error('Post creation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при создании поста'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::query()->findOrFail($id);
        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     * @param UpdatePostRequest $request
     * @param string $id
     * @string $id
     * @return JsonResponse
     */
    public function update(UpdatePostRequest $request, string $id): JsonResponse
    {
        try {
            $post = Post::query()->findOrFail($id);

            DB::transaction(function () use ($request, $id, $post) {
                $validated = $request->validated();

                // Формируем данные для обновления поста и обновляем
                $post->update([
                    'title' => $validated['title'],
                    'body' => $validated['body'],
                    'topic_id' => $validated['topic_id'],
                    'description' => $validated['description'],
                    'slug' => Str::slug($validated['title'])
                ]);
                if (!empty($validated['tag_ids'])) {
                    $post->tags()->sync($validated['tag_ids']);
                }
                return $post;
            });
            return response()->json([
                'success' => true,
                'data' => new PostResource($post)
            ], 200);

        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error when updating post: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка базы данных'
            ], 500);
        } catch (\Exception $e) {
            Log::error('Error when updating post: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при обновлении поста'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::transaction(function () use ($id) {
            $post = Post::query()->findOrFail($id);

            // Удаляем связи с тегами
            $post->tags()->detach();

            $post->delete();
        });
        return response()->json([
            'message' => 'Post HAS BEEN DELETED successfully'
        ]);
    }
}
