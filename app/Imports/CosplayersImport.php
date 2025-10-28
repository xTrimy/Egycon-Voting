<?php

namespace App\Imports;

use App\Models\Cosplayer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;

class CosplayersImport implements ToCollection, WithHeadingRow
{
    use Importable;

    private int $event_id;
    private array $existingCosplayers = [];
    private array $stats = ['created' => 0, 'updated' => 0];

    public function __construct(int $event_id)
    {
        $this->event_id = $event_id;

        // Pre-load existing cosplayers for this event to optimize database queries
        $this->loadExistingCosplayers();
    }

    /**
     * Get import statistics
     */
    public function getStats(): array
    {
        return $this->stats;
    }

    /**
     * Load all existing cosplayers for the event into memory for fast lookups
     */
    private function loadExistingCosplayers()
    {
        $cosplayers = Cosplayer::where('event_id', $this->event_id)
            ->get(['id', 'number', 'name', 'character', 'anime', 'stage_name', 'custom_data']);

        foreach ($cosplayers as $cosplayer) {
            // Create multiple lookup keys for flexible number matching
            $numbers = [
                $cosplayer->number,
                ltrim($cosplayer->number, '0') ?: '0', // Remove leading zeros
                str_pad(ltrim($cosplayer->number, '0') ?: '0', 3, '0', STR_PAD_LEFT) // Pad to 3 digits
            ];

            foreach (array_unique($numbers) as $number) {
                $this->existingCosplayers[$number] = $cosplayer;
            }
        }
    }

    /**
     * Process the collection in batches for better performance
     */
    public function collection(Collection $rows)
    {
        $batchSize = 50; // Process in batches of 50
        $batches = $rows->chunk($batchSize);

        foreach ($batches as $batch) {
            $this->processBatch($batch);
        }
    }

    /**
     * Process a batch of rows
     */
    private function processBatch(Collection $batch)
    {
        $updateData = [];
        $insertData = [];

        foreach ($batch as $row) {
            try {
                // Convert Collection to array for processing
                $rowArray = $row instanceof \Illuminate\Support\Collection ? $row->toArray() : (array) $row;
                $processedData = $this->processRow($rowArray);

                if ($processedData['existing_cosplayer']) {
                    $updateData[] = $processedData;
                } else {
                    $insertData[] = $processedData['data'];
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error processing row in CosplayersImport', [
                    'error' => $e->getMessage(),
                    'row_type' => gettype($row),
                    'row_data' => is_array($row) || is_object($row) ? json_encode($row) : $row
                ]);
                // Skip this row and continue with the next one
                continue;
            }
        }

        // Perform batch updates
        if (!empty($updateData)) {
            $this->batchUpdate($updateData);
            $this->stats['updated'] += count($updateData);
        }

        // Perform batch inserts
        if (!empty($insertData)) {
            Cosplayer::insert($insertData);
            $this->stats['created'] += count($insertData);
        }
    }

    /**
     * Process a single row and prepare data
     */
    private function processRow(array $row): array
    {
        // Define the core required columns
        $coreColumns = ['name', 'character', 'anime', 'number', 'stage_name'];

        // Clean and prepare number for lookup
        $number = trim($row['number'] ?? 'N/A');

        // Extract core data
        $coreData = [
            'name' => trim($row['name'] ?? 'N/A'),
            'character' => trim($row['character'] ?? 'N/A'),
            'anime' => trim($row['anime'] ?? 'N/A'),
            'number' => $number,
            'stage_name' => trim($row['stage_name'] ?? 'N/A'),
            'event_id' => $this->event_id,
        ];

        // Extract custom columns (any column not in core columns)
        $customData = [];
        foreach ($row as $key => $value) {
            // Skip core columns and empty values
            if (!in_array($key, $coreColumns) && !empty(trim($value))) {
                $customData[trim($key)] = trim($value);
            }
        }

        // Add custom data to core data if any custom columns exist
        if (!empty($customData)) {
            $coreData['custom_data'] = json_encode($customData);
        } else {
            $coreData['custom_data'] = null;
        }

        // Add timestamps for insert operations
        $now = now();
        $coreData['created_at'] = $now;
        $coreData['updated_at'] = $now;

        // Check if cosplayer already exists
        $existingCosplayer = $this->findExistingCosplayer($number);

        return [
            'existing_cosplayer' => $existingCosplayer,
            'data' => $coreData
        ];
    }

    /**
     * Find existing cosplayer by number with flexible matching
     */
    private function findExistingCosplayer(string $number): ?Cosplayer
    {
        // Try direct lookup first
        if (isset($this->existingCosplayers[$number])) {
            return $this->existingCosplayers[$number];
        }

        // Try normalized lookup (remove leading zeros)
        $normalized = ltrim($number, '0') ?: '0';
        if (isset($this->existingCosplayers[$normalized])) {
            return $this->existingCosplayers[$normalized];
        }

        // Try padded lookup (pad to 3 digits)
        $padded = str_pad($normalized, 3, '0', STR_PAD_LEFT);
        if (isset($this->existingCosplayers[$padded])) {
            return $this->existingCosplayers[$padded];
        }

        return null;
    }

    /**
     * Perform batch updates for existing cosplayers
     */
    private function batchUpdate(array $updateData)
    {
        foreach ($updateData as $data) {
            $existingCosplayer = $data['existing_cosplayer'];
            $newData = $data['data'];

            // Prepare update data (exclude timestamps for updates, Laravel will handle updated_at)
            $updateFields = [
                'name' => $newData['name'],
                'character' => $newData['character'],
                'anime' => $newData['anime'],
                'stage_name' => $newData['stage_name'],
                'updated_at' => now()
            ];

            // Handle custom data merging
            if ($newData['custom_data']) {
                $newCustomData = json_decode($newData['custom_data'], true);
                $existingCustomData = $existingCosplayer->custom_data ?? [];

                // Merge custom data (new data overwrites existing)
                $mergedCustomData = array_merge($existingCustomData, $newCustomData);
                $updateFields['custom_data'] = json_encode($mergedCustomData);
            }

            // Update the cosplayer
            Cosplayer::where('id', $existingCosplayer->id)->update($updateFields);

            // Update our in-memory cache
            foreach ($updateFields as $field => $value) {
                if ($field === 'custom_data') {
                    $existingCosplayer->custom_data = json_decode($value, true);
                } else {
                    $existingCosplayer->$field = $value;
                }
            }
        }
    }
}
