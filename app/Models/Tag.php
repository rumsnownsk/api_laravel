<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = ['name'];
    protected $hidden = ['created_at', 'updated_at'];

//    public function topics(): BelongsToMany
//    {
//        return $this->belongsToMany(Topic::class, 'tag_topic', 'tag_id', 'topic_id');
//    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_tag', 'tag_id', 'post_id');
    }
}
