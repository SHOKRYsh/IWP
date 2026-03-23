<?php

namespace Modules\Task\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Auth\Models\User;
use Modules\LifeStyle\Models\LifeElement;
use Modules\LifeStyle\Models\LifeTaskType;

class Task extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'icon_key',
        'description',
        'priority',
        'life_element_id',
        'life_task_type_id',
        'due_date',
        'reminder_at',
        'extra_data',
        'completed_at'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'reminder_at' => 'datetime',
        'extra_data' => 'array',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lifeElement()
    {
        return $this->belongsTo(LifeElement::class);
    }

    public function lifeTaskType()
    {
        return $this->belongsTo(LifeTaskType::class);
    }
}
