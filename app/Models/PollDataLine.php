<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollDataLine extends Model
{
    use HasFactory;
    protected $fillable = [
        'poll_data_id',
        'poll_line_id',
        'value',
    ];

    public function poll_data()
    {
        return $this->belongsTo(PollData::class);
    }

    public function poll_line()
    {
        return $this->belongsTo(PollLine::class);
    }

    
}
