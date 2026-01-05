<?php

namespace App\Exports;

use App\Models\Taluka;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportUsers implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Taluka::select('taluka.name as taluka', 'gj.name as gramjuth', 'g.name as gram', 'u.name as name', 'u.email as users_email', 'u.mobile_no as users_mobile')
            ->join('gramjuth as gj', 'gj.taluka_id', 'taluka.id')
            ->join('gram as g', 'g.gramjuth_id', 'gj.id')
            ->join('users as u', 'u.gram_id', 'g.id')
            ->orderBy('g.name', 'asc')
            ->where(['u.jilla_id' => Auth::user()->jilla_id, 'u.role' => '3', 'u.status' => 'Active'])
            ->get()
            ->toArray();
        return $query;
    }
}
