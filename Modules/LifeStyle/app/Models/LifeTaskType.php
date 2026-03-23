<?php

namespace Modules\LifeStyle\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Auth\Models\User;

class LifeTaskType extends Model
{
    use HasFactory;

    protected $fillable = [
        'life_element_id',
        'user_id',
        'name',
        'icon_key',
    ];

    public function element()
    {
        return $this->belongsTo(LifeElement::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
