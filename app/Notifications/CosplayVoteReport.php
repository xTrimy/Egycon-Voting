<?php

namespace App\Notifications;

use App\Helpers\HttpHelper;
use App\Helpers\RequestsHelper;
use App\Helpers\StringUtils;
use App\Models\Event;
use App\Models\Post;
use App\Services\Telegram\TelegramMessage;
use App\Services\Whatsapp\WhatsappMessage;
use Clockwork\Request\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log as FacadesLog;

class CosplayVoteReport extends Notification implements Telegramable, ShouldQueue
{
    use Queueable;
    private $event;
    private $top_cosplayers;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Event $event, int $top_cosplayers = 20)
    {
        $this->event = $event;
        $this->top_cosplayers = $top_cosplayers;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable): TelegramMessage{
        $cosplayers = $this->event->cosplayers()->get();
        $message =   (new TelegramMessage)
            ->line("Voting Report for " . $this->event->name)
            ->line("Total Votes: " . $this->event->votes->count())
            ->separator();
        $top_cosplayers = [];
        $i = 0;
        foreach($cosplayers as $cosplayer){
            $score = $cosplayer->calculateJudgeScore();
            $top_cosplayers[] = [
                "No."=> $cosplayer->number,
                "Name" => $cosplayer->stage_name,
                "Score" => $score
            ];
        }
        // sort by score
        usort($top_cosplayers, function($a, $b){
            return $b["Score"] - $a["Score"];
        });

        $top_cosplayers = array_slice($top_cosplayers, 0, $this->top_cosplayers);

        foreach($top_cosplayers as $key => $cosplayer){
            $cosplayer["Place"] = $key + 1;
            // add st, nd, rd to place
            if($cosplayer["Place"] == 11 || $cosplayer["Place"] == 12 || $cosplayer["Place"] == 13){
                $cosplayer["Place"] .= "th";
            }else{
                switch (substr($cosplayer["Place"], -1)) {
                    case 1:
                        $cosplayer["Place"] .= "st";
                        break;
                    case 2:
                        $cosplayer["Place"] .= "nd";
                        break;
                    case 3:
                        $cosplayer["Place"] .= "rd";
                        break;
                    default:
                        $cosplayer["Place"] .= "th";
                        break;
                }
            }
            $top_cosplayers[$key] = $cosplayer;
        }

        $top_cosplayers = $this->arrayToTable($top_cosplayers, ["Place", "No.", "Name", "Score"]);
        $message->line($top_cosplayers);
        return $message;
    }

    private function arrayToTable(array $array, array $headers): string
    {
        $table = "";
        $table .= "|";
        foreach($headers as $header){
            $table .= " ". $header . " |";
        }
        $table .= "\n";
        foreach($array as $row){
            $table .= "|";
            foreach($headers as $header){
                $table .= " ".$row[$header] . " |";
            }
            $table .= "\n";
            $table .= "---------------------\n";
            $table .= "\n";
        }
        return $table;
    }


}
