<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CosplayerImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'cosplayer_id',
    ];

    public function cosplayer()
    {
        return $this->belongsTo(Cosplayer::class);
    }

    
}
