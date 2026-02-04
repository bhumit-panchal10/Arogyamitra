<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StockiestReportExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data->map(function ($row, $index) {
            return [
                '#' => $index + 1,
                'Medicine ID' => $row->medicine_name ?? '-',
                'Quantity' => $row->total_dispatch ?? 0,
                'Prant' => $row->prant ?? '-',
                'Vibhag' => $row->vibhag ?? '-',
                'Jilla' => $row->jilla ?? '-',
                'Stockiest' => $row->stockiest ?? '-',
                'Mobile' => $row->mobile ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            '#',
            'Medicine',
            'Quantity',
            'Prant',
            'Vibhag',
            'Jilla',
            'Stockiest',
            'Mobile',
        ];
    }
}
