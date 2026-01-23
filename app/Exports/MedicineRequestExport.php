<?php

namespace App\Exports;

use App\Models\MedicineRequest;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MedicineRequestExport implements FromCollection, WithHeadings
{
    protected $status;

    public function __construct($status = null)
    {
        $this->status = $status;
    }

    public function collection()
    {
        return MedicineRequest::from('medicine_request as mr')
            ->select([
                'u.name as vibhag_name',
                'j.name as jilla_name',
                'm.name as medicine_name',
                DB::raw('SUM(mr.delivered_quantity) as medicinereq_delivered_quantity'),
                DB::raw("DATE_FORMAT(mr.created_at, '%d-%m-%Y') as request_date"),
                DB::raw("
                    CASE mr.status
                        WHEN 3 THEN 'Accepted'
                    END as status
                "),
            ])
            ->join('users as u', 'u.id', '=', 'mr.arogyamitra_id')
            ->join('medicine as m', 'm.id', '=', 'mr.medicine_id')
            ->join('jilla as j', 'j.id', '=', 'u.jilla_id')
            ->where('u.role', 6)
            ->where('mr.status', $this->status)
            ->groupBy(
                'mr.medicine_id',
                'mr.arogyamitra_id'
            )
            ->get();
    }

    public function headings(): array
    {
        return [
            'Stockiest Name',
            'Jilla',
            'Medicine Name',
            'Quantity',
            'Requested Date',
            'Status',

        ];
    }
}
