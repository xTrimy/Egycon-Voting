<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CosplayerVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'cosplayer_id',
        'user_id',
        'vote',
        'comment',
        'is_public',
        'is_anonymous',
        'is_approved',
    ];

    public function cosplayer()
    {
        return $this->belongsTo(Cosplayer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
