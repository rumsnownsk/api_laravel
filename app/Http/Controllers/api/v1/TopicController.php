<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTopicRequest;
use App\Http\Requests\UpdateTopicRequest;
use App\Http\Resources\TopicResource;
use App\Models\Topic;
use Illuminate\Support\Str;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $topics = Topic::select('id', 'name', 'slug','image', 'description')
            ->withCount('posts')
//            ->orderByDesc('posts_count')
            ->get();
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
}
