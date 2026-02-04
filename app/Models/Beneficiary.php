<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Beneficiary extends Model
{
    use HasFactory;

    protected $table        = 'beneficiaries';
    protected $primaryKey   = 'id';
    protected $fillable     = ['arogyamitra_id', 'gram_id', 'request_date', 'number_of_beneficiary', 'created_at', 'updated_at'];

    const CREATED_AT        = 'created_at';
    const UPDATED_AT        = 'updated_at';

    public static function getBeneficiaryCount($userIdsArr, $days)
    {
        $beneficiary = self::select(DB::raw("ROUND(SUM(number_of_beneficiary) / $days,0) AS beneficiaries_total"))->whereIn('arogyamitra_id', $userIdsArr)->groupBy('arogyamitra_id')->get()->toArray();
        $total = 0;
        foreach ($beneficiary as $data) {
            $total += $data['beneficiaries_total'];
        }
        return $total;
    }
}
