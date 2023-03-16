<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
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
}
