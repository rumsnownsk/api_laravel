<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Topic extends Model
{
        use HasFactory;

    protected $fillable = ['name', 'slug', 'description'];
//    protected $casts = [
//        'published' => 'boolean'
//    ];
    protected $hidden = ['created_at', 'updated_at'];

//    public function tags(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
//    {
//        return $this->belongsToMany(Tag::class, 'topic_tag', 'topic_id', 'tag_id');
//    }
    public function tags(): belongsToMany
    {
        return $this->belongsToMany(Tag::class, 'tag_topic', 'topic_id', 'tag_id');
    }

    public function posts(): hasMany
    {
        return $this->hasMany(Post::class, 'topic_id', 'id');
    }


}
