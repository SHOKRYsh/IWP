<?php

namespace Modules\Children\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Auth\Models\User;

class Child extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'gender',
        'educational_stage',
        'age',
        'extracurricular_activities',
        'ballet_class',
    ];

    protected $casts = [
        'extracurricular_activities' => 'boolean',
        'age' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
