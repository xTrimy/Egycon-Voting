<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function cosplayers()
    {
        return $this->hasMany(Cosplayer::class);
    }

    public function users(){
        return $this->belongsToMany(User::class);
    }

    public function user_has_access($user_id){
        return $this->users()->where('user_id', $user_id)->exists();
    }

    public function votes(){
        return $this->hasManyThrough(CosplayerVote::class, Cosplayer::class);
    }

    
}
