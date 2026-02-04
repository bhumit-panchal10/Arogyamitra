<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BeneficiariesReportExport implements FromCollection, WithHeadings
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
                'Prant' => $row->Prant,
                'Vibhag' => $row->Vibhag,
                'Jilla' => $row->Jilla,
                'Taluka' => $row->Taluka,
                'Gramjuth' => $row->Gramjuth,
                'Gram' => $row->Gram,
                'Beneficiaries' => $row->total_beneficiary,
                'Arogyamitra' => $row->arogyamitraName,
                'Arogyamitra Mobile' => $row->mobile_no,
                'App User' => $row->AppUser,
                'App User Mobile' => $row->AppUserMobile,
                'Stockiest' => $row->StockiestUser,
                'Stockiest Mobile' => $row->StockiestMobile,
            ];
        });
    }

    public function headings(): array
    {
        return [
            '#',
            'Prant',
            'Vibhag',
            'Jilla',
            'Taluka',
            'Gramjuth',
            'Gram',
            'Beneficiaries',
            'Arogyamitra',
            'Arogyamitra Mobile',
            'App User',
            'App User Mobile',
            'Stockiest',
            'Stockiest Mobile',
        ];
    }
}
