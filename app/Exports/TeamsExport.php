<?php

namespace App\Exports;

use App\Models\Team;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TeamsExport implements FromCollection, WithHeadings, WithColumnWidths, WithMapping, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Team::with('leader', 'members')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Team Name',
            'Team Leader',
            'Members',
        ];
    }


    public function map($team): array
    {
        $memberNames = $team->members->pluck('name')->implode(', ') ?: 'No members';
        $leaderName = $team->leader?->name ?? 'N/A';

        return [
            $team->id,
            $team->name,
            $leaderName,
            $memberNames,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'B' => 30,
            'C' => 30,
            'D' => 50
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('1:1')->getFont()->setBold(true);
        $sheet->getStyle('A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    }
}
