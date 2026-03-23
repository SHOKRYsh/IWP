<?php

namespace Modules\Subscription\Models;

use App\Http\Traits\ArchiveTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Auth\Models\User;

class Subscription extends Model
{
    use HasFactory, SoftDeletes, ArchiveTrait;

    protected $fillable = [
        'user_id',
        'plan_id',
        'type',
        'started_at',
        'ends_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isActive()
    {
        if ($this->ends_at && $this->ends_at->isFuture()) {
            return true;
        }

        return false;
    }

    public function billings()
    {
        return $this->hasOne(Billing::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
