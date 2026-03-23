<?php

namespace Modules\Task\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Auth\Models\User;

class HabitCompletion extends Model
{
    protected $fillable = [
        'habit_id',
        'user_id',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function habit()
    {
        return $this->belongsTo(Habit::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
