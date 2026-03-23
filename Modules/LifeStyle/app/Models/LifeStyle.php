<?php

namespace Modules\LifeStyle\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LifeStyle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon_key',
    ];

    public function elements()
    {
        return $this->hasMany(LifeElement::class);
    }
}
