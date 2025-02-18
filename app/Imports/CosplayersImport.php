<?php

namespace App\Imports;

use App\Models\Cosplayer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithSkipDuplicates;

class CosplayersImport implements ToModel, WithHeadingRow, WithSkipDuplicates
{
    use Importable;

    private int $event_id;

    public function __construct(int $event_id)
    {
        $this->event_id = $event_id;
    }
    /**
    * @param Collection $collection 
    */
    public function model(array $row)
    {
        if(!isset($row['name']) || !isset($row['character']) || !isset($row['anime']) || !isset($row['number']) || !isset($row['stage_name'])) {
            return null;
        }

        return new Cosplayer([
            'name' => $row['name'],
            'character' => $row['character'],
            'anime' => $row['anime'],
            'number' => $row['number'],
            'stage_name' => $row['stage_name'],
            'event_id' => $this->event_id,
        ]);
    }

    
}
