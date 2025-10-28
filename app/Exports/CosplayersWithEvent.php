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
        $baseData = [
            $cosplayer->number,
            $cosplayer->name,
            $cosplayer->character,
            $cosplayer->anime,
            $cosplayer->event->name,
            $cosplayer->calculateJudgeScore(),
        ];

        // Add custom data fields if they exist
        $customData = $cosplayer->getAllCustomData();
        if ($customData) {
            // Get all unique custom field names across all cosplayers for this event
            $allCustomFields = $this->getAllCustomFields();
            foreach ($allCustomFields as $field) {
                $baseData[] = $customData[$field] ?? '';
            }
        }

        return $baseData;
    }

    public function headings(): array
    {
        $baseHeadings = [
            'Number',
            'Stage Name',
            'Character',
            'From',
            'Event',
            'Judge Score (%)',
        ];

        // Add custom field headings
        $customFields = $this->getAllCustomFields();
        foreach ($customFields as $field) {
            $baseHeadings[] = ucfirst(str_replace('_', ' ', $field));
        }

        return $baseHeadings;
    }

    private function getAllCustomFields()
    {
        // Get all unique custom field names from cosplayers in this event
        $allFields = [];
        $cosplayers = Cosplayer::where('event_id', $this->event_id)->get();

        foreach ($cosplayers as $cosplayer) {
            $customData = $cosplayer->getAllCustomData();
            if ($customData) {
                $allFields = array_merge($allFields, array_keys($customData));
            }
        }

        return array_unique($allFields);
    }
}
