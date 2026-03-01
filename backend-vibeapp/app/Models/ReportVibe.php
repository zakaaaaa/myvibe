<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportVibe extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vibe_id',
        'status',
        'desc'
    ];

    public function user_report()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function vibe()
    {
        return $this->belongsTo(Vibe::class, 'vibe_id');
    }

}
