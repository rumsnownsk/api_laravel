<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackMessage extends Model
{
     use HasFactory;

    protected $fillable = [
        'contactInfo',
        'contact_type',
        'topic',
        'message',
        'ip_address',
        'user_agent',
        'referrer',
        'user_id',
        'is_read',
        'is_spam',
        'spam_reason'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_spam' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
