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
        'judge_voting_enabled',
        'voting_starts_at',
        'voting_ends_at',
    ];

    protected $casts = [
        'judge_voting_enabled' => 'boolean',
        'voting_starts_at' => 'datetime',
        'voting_ends_at' => 'datetime',
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

    /**
     * Check if judge voting is currently enabled for this event
     */
    public function isJudgeVotingEnabled()
    {
        if (!$this->judge_voting_enabled) {
            return false;
        }

        $now = now();

        // Check voting start time
        if ($this->voting_starts_at && $now->isBefore($this->voting_starts_at)) {
            return false;
        }

        // Check voting end time
        if ($this->voting_ends_at && $now->isAfter($this->voting_ends_at)) {
            return false;
        }

        return true;
    }

    /**
     * Get voting status message
     */
    public function getVotingStatusMessage()
    {
        if (!$this->judge_voting_enabled) {
            return 'Judge voting is disabled for this event.';
        }

        $now = now();

        if ($this->voting_starts_at && $now->isBefore($this->voting_starts_at)) {
            return 'Judge voting will start on ' . $this->voting_starts_at->format('M j, Y \a\t g:i A');
        }

        if ($this->voting_ends_at && $now->isAfter($this->voting_ends_at)) {
            return 'Judge voting ended on ' . $this->voting_ends_at->format('M j, Y \a\t g:i A');
        }

        if ($this->voting_ends_at) {
            return 'Judge voting is open until ' . $this->voting_ends_at->format('M j, Y \a\t g:i A');
        }

        return 'Judge voting is currently open.';
    }

    /**
     * Enable judge voting for this event
     */
    public function enableJudgeVoting($startTime = null, $endTime = null)
    {
        $this->update([
            'judge_voting_enabled' => true,
            'voting_starts_at' => $startTime,
            'voting_ends_at' => $endTime,
        ]);
    }

    /**
     * Disable judge voting for this event
     */
    public function disableJudgeVoting()
    {
        $this->update([
            'judge_voting_enabled' => false,
        ]);
    }
}
