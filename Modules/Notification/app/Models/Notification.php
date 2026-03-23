<?php

namespace Modules\Notification\Models;


use Illuminate\Database\Eloquent\Model;
use Modules\Auth\Models\User;

class Notification extends Model
{
    protected $fillable = [
        'sender_id',
        'recipient_id',
        'title',
        'image',
        'body',
        'extra_data',
        'is_read'
    ];

    protected $casts = [
        'extra_data' => 'array',
        'is_read' => 'boolean',
    ];

    public function sender()
    {
        return $this->belongTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongTo(User::class, 'recipient_id');
    }
}
