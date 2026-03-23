<?php

namespace Modules\Expense\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Auth\Models\User;
use Modules\LifeStyle\Models\LifeTaskType;

class Expense extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'amount',
        'life_task_type_id',
        'date',
        'payment',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lifeTaskType()
    {
        return $this->belongsTo(LifeTaskType::class);
    }
}
