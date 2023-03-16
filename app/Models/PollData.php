<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollData extends Model
{
    use HasFactory;
    protected $fillable = [
        'poll_id',
    ];

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    public function poll_data_lines()
    {
        return $this->hasMany(PollDataLine::class);
    }

    public function poll_votes()
    {
        return $this->hasMany(PollVote::class);
    }

    
}
