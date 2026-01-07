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
        return MedicineRequest::select([
            'u.name as stockiest_name',
            'j.name as jilla_name',
            'm.name as medicine_name',

            // ✅ FIX: aggregate stock safely
            DB::raw('IFNULL(MAX(ms.qty), 0) as current_qty'),

            DB::raw('SUM(medicine_request.qty) as total_request'),

            DB::raw("
            CASE medicine_request.status
                WHEN '0' THEN 'Cancelled'
                WHEN '1' THEN 'Pending'
                WHEN '2' THEN 'Accepted'
                WHEN '3' THEN 'Delivered'
            END as status
        "),

            'medicine_request.created_at as request_date'
        ])
            ->join('medicine as m', 'm.id', '=', 'medicine_request.medicine_id')
            ->join('users as u', 'u.id', '=', 'medicine_request.arogyamitra_id')
            ->join('jilla as j', 'j.id', '=', 'u.jilla_id')

            ->leftJoin('medicine_stock as ms', function ($join) {
                $join->on('ms.medicine_id', '=', 'medicine_request.medicine_id')
                    ->on('ms.arogyamitra_id', '=', 'medicine_request.arogyamitra_id');
            })

            ->where('u.role', 6)
            ->where('medicine_request.status', 2)

            ->groupBy(
                'medicine_request.medicine_id',
                'medicine_request.status',
                'u.id',
                DB::raw('DATE(medicine_request.updated_at)')
            )

            ->orderBy('medicine_request.updated_at', 'DESC')
            ->get();
    }




    public function headings(): array
    {
        return [
            'Stockiest Name',
            'Jilla',
            'Medicine Name',
            'Current Qty',
            'Requested Qty',
            'Status',
            'Request Date',
        ];
    }
}
