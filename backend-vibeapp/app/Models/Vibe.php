<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vibe extends Model
{
    use HasFactory;
    public $incrementing = false; // Disable auto-incrementing IDs
    protected $keyType = 'string'; // Set key type to string

    protected $fillable = [
        'id',
        'user_id',
        'category_id',
        'image_url',
        'comment',
        'rating',
        'title',
        'desc',
        'path',
        'blocked'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
