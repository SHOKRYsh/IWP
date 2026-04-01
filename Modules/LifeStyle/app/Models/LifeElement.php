<?php

namespace Modules\LifeStyle\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Auth\Models\User;

class LifeElement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'life_style_id',
        'name',
        'suggested_tasks',
        'icon_key',
    ];

    protected $casts = [
        'suggested_tasks' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lifeStyle()
    {
        return $this->belongsTo(LifeStyle::class);
    }

    public function taskTypes()
    {
        return $this->hasMany(LifeTaskType::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'life_element_user');
    }
}
