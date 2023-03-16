<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'logo',
        'background',
        'color',
        'is_disabled',
    ];

    public function poll_data()
    {
        return $this->hasMany(PollData::class);
    }

    public function poll_lines()
    {
        return $this->hasMany(PollLine::class);
    }

    public function poll_votes()
    {
        return $this->hasManyThrough(PollVote::class, PollData::class);
    }
}
