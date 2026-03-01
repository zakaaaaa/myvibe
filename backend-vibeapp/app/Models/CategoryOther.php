<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryOther extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'user_id',
    ];

    public function vibes()
    {
        return $this->hasMany(VibeOther::class, 'category_other_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
