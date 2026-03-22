<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedVibe extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'vibe_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vibe()
    {
        return $this->belongsTo(Vibe::class, 'vibe_id');
    }
}
