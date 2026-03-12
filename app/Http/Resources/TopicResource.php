<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopicResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'slug'=>$this->slug,
            'description'=>$this->description,
            'posts_count' => $this->whenCounted('posts'), // Автоматически подставляет posts_count, если загружен через withCount()
            'created_at' => $this->created_at?->diffForHumans() ?? 'not set',
            'updated_at' => $this->updated_at?->diffForHumans() ?? 'not set',
        ];
    }
}
