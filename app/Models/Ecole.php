<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ecole extends Model
{
    protected $fillable = [
        'nom',
        'adresse',
        'telephone',
        'contact_nom',
        'contact_telephone',
        'created_by',
    ];

    public function programmes()
    {
        return $this->hasMany(Programme::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}