<?php

namespace App\Exports;

use App\Models\Cosplayer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CosplayersWithEvent implements FromCollection, WithHeadings, WithMapping
{

    protected $event_id;

    public function __construct($event_id)
    {
        $this->event_id = $event_id;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Cosplayer::where('event_id', $this->event_id)->get();
    }

    public function map($cosplayer): array
    {
        return [
            $cosplayer->number,
            $cosplayer->name,
            $cosplayer->character,
            $cosplayer->anime,
            $cosplayer->event->name,
            $cosplayer->calculateJudgeScore(),
        ];
    }

    public function headings(): array
    {
        return [
            'Number',
            'Stage Name',
            'Character',
            'From',
            'Event',
            'Judge Score (%)',
        ];
    }
}
