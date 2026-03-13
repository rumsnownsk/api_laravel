<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'topic' => $this->whenLoaded('topic') && $this->topic
                ? [
                    'id' => $this->topic->id,
                    'name' => $this->topic->name
                ] : null,
            'description' => $this->description,
            'image' => $this->image ?? 'default-post.jpg',
            'tags' => $this->tags->map(fn($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
            ]),
            'published_at' => $this->created_at_formatted,
            'updated_at' => $this->updated_at_formatted
        ];
    }
}
