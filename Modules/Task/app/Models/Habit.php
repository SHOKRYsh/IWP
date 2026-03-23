<?php

namespace Modules\Task\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Auth\Models\User;
use Carbon\Carbon;

class Habit extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'icon_key',
        'daily_goal',
        'frequency',
        'daily_reminder_at',
        'last_completed_at',
        'streak_count',
    ];

    protected $casts = [
        'last_completed_at' => 'datetime',
    ];

    protected $appends = ['is_completed_today'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function completions()
    {
        return $this->hasMany(HabitCompletion::class);
    }

    public function getIsCompletedTodayAttribute()
    {
        if (!$this->last_completed_at) {
            return false;
        }

        return $this->last_completed_at->isToday();
    }
}
