<?php

namespace App\Exports;

use App\Models\Cosplayer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CosplayersExport implements FromCollection, WithMapping, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Cosplayer::all();
    }

    public function map($cosplayer): array
    {
        return [
            $cosplayer->number,
            $cosplayer->name,
            $cosplayer->character,
            $cosplayer->anime,
            $cosplayer->event->name,
            $cosplayer->calculateJudgeScore() ,
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
