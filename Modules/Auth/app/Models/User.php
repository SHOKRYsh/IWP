<?php

namespace Modules\Auth\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Http\Traits\ArchiveTrait;
use Modules\Children\Models\Child;
use Modules\LifeStyle\Models\LifeStyle;
use Modules\Subscription\Models\Subscription;
use Modules\Subscription\Models\Billing;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements \Illuminate\Contracts\Auth\Access\Authorizable
{
    use HasApiTokens, HasRoles, HasFactory, Notifiable, SoftDeletes, ArchiveTrait;

    protected $guard_name = 'web';
    
    protected $fillable = [
        'name',
        'email',
        'country_code',
        'phone',
        'password',
        'profile_image',
        'gender',
        'marital_status',
        'life_style_id',
        'otp',
        'otp_sent_at',
        'otp_verified_at',
        'otp_expires_at',
        'otp_attempts',
    ];
    
    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'otp_sent_at' => 'datetime',
        'otp_verified_at' => 'datetime',
        'otp_expires_at' => 'datetime',
    ];
    
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function billings()
    {
        return $this->hasMany(Billing::class);
    }

    public function children()
    {
        return $this->hasMany(Child::class);
    }

    public function lifeStyle()
    {
        return $this->belongsTo(LifeStyle::class);
    }
}
