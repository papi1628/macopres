<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleProduction extends Model
{
    protected $table = 'articles_production';

    protected $fillable = [
        'programme_id',
        'designation',
        'description',
        'quantite',
        'photo',
    ];

    public function programme()
    {
        return $this->belongsTo(Programme::class);
    }

    public function photoUrl(): ?string
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }
}