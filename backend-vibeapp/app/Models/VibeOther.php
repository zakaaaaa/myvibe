<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VibeOther extends Model
{
    use HasFactory;
    public $incrementing = false; // Disable auto-incrementing IDs
    protected $keyType = 'string'; // Set key type to string

    protected $fillable = [
        'id',
        'user_id',
        'category_other_id',
        'image_url',
        'comment',
        'rating',
        'title',
        'desc',
        'path'
    ];

    public function category()
    {
        return $this->belongsTo(CategoryOther::class, 'category_other_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
