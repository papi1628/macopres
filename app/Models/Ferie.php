<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ferie extends Model
{
    protected $fillable = [
        'nom',
        'date',
        'description',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}