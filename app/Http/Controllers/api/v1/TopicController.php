<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTopicRequest;
use App\Http\Requests\UpdateTopicRequest;
use App\Http\Resources\TagResource;
use App\Http\Resources\TopicResource;
use App\Models\Tag;
use App\Models\Topic;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $topics = Topic::query()->get();
        return TopicResource::collection($topics);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTopicRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        return new TopicResource(Topic::create($data));
    }

    /**
     * Display the specified resource.
     */
    public function show($id): TopicResource
    {
        $topic= Topic::where('id', $id)->firstOrFail();
        return new TopicResource($topic);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTopicRequest $request, $id)
    //$topic — уже загруженная модель из БД (по slug);
    {
        $item = Topic::where('id', $id)->firstOrFail();

        $data = $request->validated();
        // Обновляем slug только если name изменился
            $data['slug'] = Str::slug($data['name']);

        $updated = $item->update($data);

        if (!$updated) {
            return response()->json([
                'message' => 'No changes were made'
            ], 200);
        }

        return response([
            'data' => new TopicResource($item->fresh()),
            'message' => 'Topic updated successfully'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Topic::where('id', $id)->firstOrFail()->delete();
        return response(null, 204);
    }

    /**
     * Получить уникальные теги для всех постов конкретной темы
     * Оптимизированный запрос — один запрос к БД
     */
//    public function getTagsByTopic($topicId)
//    {
//        // проверка существования Темы
//        $topic = Topic::find($topicId);
//        if (!$topic) {
//            return response()->json([
//                'message' => 'Topic not found'
//            ], 404);
//        }
//
////        return response()->json([
////            'data' => $topic
////        ]);
//        // Оптимизированный Eloquent-запрос: один SQL-запрос вместо N+1
//        $tags = Tag::select([
//            'tags.id',
//            'tags.name',
//            'tags.slug',
//            DB::raw('COUNT(DISTINCT posts.id) as post_count')
//        ])
//        ->join('post_tag', 'tags.id', '=', 'post_tag.tag_id')
//        ->join('posts', 'post_tag.post_id', '=', 'posts.id')
//        ->where('posts.topic_id', $topicId)
//        ->groupBy('tags.id', 'tags.name', 'tags.slug')
//        ->orderBy('post_count', 'desc')
//        ->get();
//        return response()->json([
//            'topicId' => $topicId,
//            'topic_name' => $topic->name,
//            'topic_slug' => $topic->slug,
//            'tags_count' => $tags->count(),
//            'tags' => TagResource::collection($tags)
//        ]);
//    }

//    public function getTagsByTopic($topicId)
//    {
//        $topic = Topic::with(['posts.tags' => function ($query) {
//            $query->select('tags.id', 'tags.name', 'tags.slug');
//        }])->find($topicId);
//
//        if (!$topic) {
//            return response()->json([
//                'error' => 'Категория не найдена'
//            ], 404);
//        }
//
//        // Собираем уникальные теги из всех постов категории
//        $tagsMap = [];
//        $tagPostCount = [];
//
//        foreach ($category->posts as $post) {
//            foreach ($post->tags as $tag) {
//                $tagId = $tag->id;
//
//                if (!isset($tagsMap[$tagId])) {
//                    $tagsMap[$tagId] = [
//                'id' => $tag->id,
//                'name' => $tag->name,
//                'slug' => $tag->slug,
//                'post_count' => 0
//            ];
//        }
//        $tagPostCount[$tagId] = ($tagPostCount[$tagId] ?? 0) + 1;
//    }
}
