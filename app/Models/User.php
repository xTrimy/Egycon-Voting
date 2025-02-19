<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Helpers\QRHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'vote_weight'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function cosplayer_votes()
    {
        return $this->hasMany(CosplayerVote::class);
    }

    public function events(){
        return $this->belongsToMany(Event::class);
    }

    public function getTelegramCode(bool $newCode = false): string
    {
        if ($this->telegram_code == null || $this->telegram_code == '' || $newCode) {
            $this->telegram_code = Str::random(16);
            $this->save();
        }
        return $this->telegram_code;
    }

    public function getTelegramCodeQR()
    {
        $telegramBotUsername = env('TELEGRAM_BOT_USERNAME');
        $url = "https://t.me/{$telegramBotUsername}?start={$this->getTelegramCode()}";
        return (new QRHelper)->generate($url, false, true);
    }

    public function telegram_chat()
    {
        return $this->hasOne(TelegramChat::class);
    }

    public function getTelegramChatId()
    {
        return $this->telegram_chat()->exists() ? $this->telegram_chat->chat_id : null;
    }

    public function isTelegramNotificationsEnabled()
    {
        return $this->telegram_chat()->exists();
    }

    public function getTelegramChatIdObject()
    {
        return $this->telegram_chat()->exists() ? $this->telegram_chat : null;
    }

    public function saveTelegramChatId($chat_id)
    {
        if ($this->telegram_chat()->exists()) {
            $this->telegram_chat()->update(['chat_id' => $chat_id]);
        } else {
            $this->telegram_chat()->create(['chat_id' => $chat_id]);
        }
        $this->getTelegramCode(true);
    }
}
