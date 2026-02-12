<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;


class BeneficiariesReportExport implements FromCollection, WithHeadings
{
    protected $fromDate;
    protected $toDate;

    public function __construct($fromDate, $toDate)
    {
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

   public function collection()
   {
       
    $from = Carbon::parse($this->fromDate)->startOfDay();
    $to   = Carbon::parse($this->toDate)->endOfDay();
    
    $data = DB::table('beneficiaries as b')
        ->leftJoin('users as aro', function ($join) {
            $join->on('aro.gram_id', '=', 'b.gram_id')
                ->where('aro.role', 3)
                ->where('aro.status', 'Active');
        })

        ->join('gram', 'gram.id', '=', 'b.gram_id')
        ->join('gramjuth', 'gramjuth.id', '=', 'gram.gramjuth_id')
        ->join('taluka', 'taluka.id', '=', 'gramjuth.taluka_id')
        ->join('jilla', 'jilla.id', '=', 'taluka.jilla_id')
        ->join('vibhag', 'vibhag.id', '=', 'jilla.vibhag_id')
        ->join('prant', 'prant.id', '=', 'vibhag.prant_id')

        ->leftJoin('users as stock', function ($join) {
            $join->whereRaw("FIND_IN_SET(b.gram_id, stock.gram_id)")
                ->where('stock.role', 6)
                ->where('stock.status', 'Active');
        })

        ->leftJoin('users as app', function ($join) {
            $join->whereRaw("FIND_IN_SET(b.gram_id, app.gram_id)")
                ->where('app.role', 2)
                ->where('app.status', 'Active');
        })

        ->whereBetween('b.created_at', [$from, $to])

        ->select([
            'b.request_date',
            'prant.name as Prant',
            'vibhag.name as Vibhag',
            'jilla.name as Jilla',
            'taluka.name as Taluka',
            'gramjuth.name as Gramjuth',
            'gram.name as Gram',
            DB::raw('SUM(b.number_of_beneficiary) as total_beneficiary'),
            'aro.name as arogyamitraName',
            'aro.mobile_no',
            'app.name as AppUser',
            'app.mobile_no as AppUserMobile',
            'stock.name as StockiestUser',
            'stock.mobile_no as StockiestMobile',
        ])

        ->groupBy(
            'b.arogyamitra_id',
            'b.gram_id',
            'b.request_date',
            'aro.name',
            'aro.mobile_no',
            'stock.name',
            'stock.mobile_no',
            'app.name',
            'app.mobile_no',
            'gram.name',
            'gramjuth.name',
            'taluka.name',
            'jilla.name',
            'vibhag.name',
            'prant.name'
        )

        ->orderBy('b.request_date')

        ->get();

    $counter = 1;

    return $data->map(function ($row) use (&$counter) {
        return [
            $counter++,
            Carbon::parse($row->request_date)->format('d-m-Y'),
            $row->Prant,
            $row->Vibhag,
            $row->Jilla,
            $row->Taluka,
            $row->Gramjuth,
            $row->Gram,
            $row->total_beneficiary,
            $row->arogyamitraName,
            $row->mobile_no,
            $row->AppUser,
            $row->AppUserMobile,
            $row->StockiestUser,
            $row->StockiestMobile,
        ];
    });
}

   public function headings(): array
   {
        return [
            '#',
            'Date',
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