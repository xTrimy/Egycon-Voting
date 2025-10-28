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
        // Define the core required columns
        $coreColumns = ['name', 'character', 'anime', 'number', 'stage_name'];

        // Extract core data
        $coreData = [
            'name' => $row['name'] ?? "N/A",
            'character' => $row['character'] ?? "N/A",
            'anime' => $row['anime'] ?? "N/A",
            'number' => $row['number'] ?? "N/A",
            'stage_name' => $row['stage_name'] ?? "N/A",
            'event_id' => $this->event_id,
        ];

        // Extract custom columns (any column not in core columns)
        $customData = [];
        foreach ($row as $key => $value) {
            // Skip core columns and empty values
            if (!in_array($key, $coreColumns) && !empty($value)) {
                $customData[$key] = $value;
            }
        }

        // Add custom data to core data if any custom columns exist
        if (!empty($customData)) {
            $coreData['custom_data'] = $customData;
        }

        return new Cosplayer($coreData);
    }


}
