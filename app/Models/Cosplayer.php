<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Cosplayer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'character',
        'anime',
        'number',
        'stage_name',
        'event_id',
        'custom_data',
    ];

    protected $casts = [
        'custom_data' => 'array',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function images()
    {
        return $this->hasMany(CosplayerImage::class);
    }

    public function references()
    {
        return $this->hasMany(CosplayerReference::class);
    }

    public function votes()
    {
        return $this->hasMany(CosplayerVote::class);
    }

    public function vote(User $user){
        return $this->votes()->where('user_id', $user->id)->first();
    }

    private function getMaxJudgeWeight()
    {
        return 100;
    }

    public function calculateJudgeScore()
    {
        $votes = $this->votes;
        if ($votes->count() == 0)
            return 0;
        $score = 0;
        foreach($votes as $vote)
        {
            $vote_weight = $vote->user->vote_weight;
            $score += $vote->vote * $vote_weight;
        }
        // normalize score to 100 percent
        $score = $score / $this->getMaxJudgeWeight();
        $score = round($score, 2);
        return $score;
    }

    /**
     * Get a custom data field value
     */
    public function getCustomData($key, $default = null)
    {
        return $this->custom_data[$key] ?? $default;
    }

    /**
     * Set a custom data field value
     */
    public function setCustomData($key, $value)
    {
        $customData = $this->custom_data ?? [];
        $customData[$key] = $value;
        $this->custom_data = $customData;
        return $this;
    }

    /**
     * Get all custom data fields
     */
    public function getAllCustomData()
    {
        return $this->custom_data ?? [];
    }

    /**
     * Check if a custom data field exists
     */
    public function hasCustomData($key)
    {
        return isset($this->custom_data[$key]);
    }

    /**
     * Remove a custom data field
     */
    public function removeCustomData($key)
    {
        $customData = $this->custom_data ?? [];
        unset($customData[$key]);
        $this->custom_data = $customData;
        return $this;
    }




}
