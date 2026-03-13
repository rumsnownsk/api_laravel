<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    public $table = 'posts';
    public $fillable = ['title', 'body', 'slug', 'topic_id', 'image', 'description'];

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class, 'topic_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tag', 'post_id', 'tag_id');
    }

    public function getCreatedAtFormattedAttribute(): string
    {
        return $this->created_at
            ? $this->created_at->format('d-m-Y H:i')
            : 'not set';
    }

    public function getUpdatedAtFormattedAttribute(): string
    {
        return $this->updated_at
            ? $this->updated_at->format('d-m-Y')
            : 'not set';
    }
}
