<?php

namespace App\Exports;

use App\Models\Poll;
use App\Models\PollData;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PollsDataExport implements FromCollection, WithMapping, WithHeadings
{
    private $poll_id;
    public function __construct($poll_id)
    {
        $this->poll_id = $poll_id;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return PollData::where('poll_id', $this->poll_id)->with('poll_data_lines', 'poll_votes')->get();
    }

    public function map($pollData): array
    {
        $poll_data_lines = $pollData->poll_data_lines;
        $poll_votes = $pollData->poll_votes;
        $unique_ip = $poll_votes->unique('ip')->count();
        $number_of_votes = $unique_ip;
        $votes = $poll_votes->sum('rating');
        if ($number_of_votes == 0) {
            $number_of_votes = 1;
        }
      
        $avg = $votes / $number_of_votes;
        $percentage = $votes / $number_of_votes / 5 * 100;
        $data = [
            $pollData->poll->name,
        ];
        
        foreach($poll_data_lines as $poll_data_line) {
                $data[] = $poll_data_line->value;
        }
        $data[] = $avg;
        $data[] = $percentage;
        
        return $data;
    }

    public function headings(): array
    {
        $poll = Poll::find($this->poll_id);
        $poll_lines = $poll->poll_lines;
        $data = [
            'Poll Name',
        ];
        foreach($poll_lines as $poll_line) {
            $data[] = $poll_line->name;
        }
        $data[] = 'Average / 5';
        $data[] = 'Percentage (%)';
        return $data;
    }

}
