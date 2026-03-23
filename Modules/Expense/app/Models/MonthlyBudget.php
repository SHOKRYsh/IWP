<?php

namespace Modules\Expense\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Auth\Models\User;

class MonthlyBudget extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'month',
        'year',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
