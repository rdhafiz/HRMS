<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'body',
        'recipients',
        'attachments',
        'sender_id',
        'sent_at',
    ];

    protected $casts = [
        'recipients' => 'array',
        'attachments' => 'array',
        'sent_at' => 'datetime',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
