<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollVote extends Model
{
    use HasFactory;
    protected $fillable = [
        'poll_data_id',
        'rating',
        'ip',
        'user_agent',
    ];

    public function poll_data()
    {
        return $this->belongsTo(PollData::class);
    }
    
}
