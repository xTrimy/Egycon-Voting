<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CosplayersSampleExport implements FromArray, WithHeadings, WithColumnWidths, WithStyles
{
    public function array(): array
    {
        return [
            ['John Doe', 'Naruto Uzumaki', 'Naruto', '001', 'Johnny Cosplay', 'Male', '25', 'Tokyo, Japan', 'First time participant'],
            ['Jane Smith', 'Sailor Moon', 'Sailor Moon', '002', 'Moon Princess', 'Female', '23', 'Osaka, Japan', 'Professional cosplayer'],
            ['Mike Johnson', 'Edward Elric', 'Fullmetal Alchemist', '003', 'Alchemy Mike', 'Male', '28', 'Kyoto, Japan', 'Won previous contest'],
            ['Sarah Wilson', 'Rem', 'Re:Zero', '004', 'Dream Girl Sarah', 'Female', '21', 'Hiroshima, Japan', 'Student participant'],
            ['Tom Brown', 'Goku', 'Dragon Ball Z', '005', 'Super Tom', 'Male', '30', 'Nagoya, Japan', 'Veteran cosplayer'],
            ['Lisa Davis', 'Nezuko Kamado', 'Demon Slayer', '006', 'Bamboo Lisa', 'Female', '19', 'Sendai, Japan', 'Newcomer'],
            ['Chris Lee', 'Light Yagami', 'Death Note', '007', 'Justice Chris', 'Male', '26', 'Fukuoka, Japan', 'Fan favorite'],
            ['Emma Taylor', 'Asuka Langley', 'Evangelion', '008', 'Pilot Emma', 'Female', '24', 'Sapporo, Japan', 'International participant'],
            ['Alex Martinez', 'Levi Ackerman', 'Attack on Titan', '009', 'Captain Alex', 'Male', '27', 'Yokohama, Japan', 'Military precision'],
            ['Mia Thompson', 'Tohru Honda', 'Fruits Basket', '010', 'Sweet Mia', 'Female', '22', 'Kobe, Japan', 'Sweet personality match'],
        ];
    }

    public function headings(): array
    {
        return [
            'name',
            'character',
            'anime',
            'number',
            'stage_name',
            'gender',
            'age',
            'location',
            'notes'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 25,
            'C' => 25,
            'D' => 10,
            'E' => 20,
            'F' => 12,
            'G' => 8,
            'H' => 18,
            'I' => 25,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
