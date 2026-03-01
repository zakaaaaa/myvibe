<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public function vibes()
    {
        return $this->hasMany(Vibe::class, 'category_id');
    }

    public function vibe_()
    {
        return $this->videos()->hasManywhere('available', '=', 1);
    }
}
