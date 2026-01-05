<?php

namespace App\Http\Controllers;


use App\Models\{
    Gram,
    User
};
use Carbon\Carbon;
use Illuminate\Support\Facades\{
    DB,
    Auth,
    Hash
};

class UserExportController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function export()
    {
        $data = [];
        $users = User::whereNull('deleted_at')
            ->where(['status' => 'Active', 'role' => 3]);
            if (Auth::user()->role == 5) {
                $users = $users->where('prant_id', Auth::user()->prant_id);
            } elseif (Auth::user()->role == 4) {
                $users = $users->where('vibhag_id', Auth::user()->vibhag_id);
            }

            $users = $users->orderBy('id', 'DESC')->get();

            foreach ($users as $key => $value) {
                $data[$key]['user_name'] = $value->name;
                $data[$key]['user_mobile_no'] = $value->mobile_no;

                $locationDetails = Gram::select(DB::raw('prant.name as prant_name, vibhag.name as vibhag, jilla.name as jilla, taluka.name as taluka , gramjuth.name as gramjuth, gram.name as gram'))
                    ->join('gramjuth', 'gramjuth.id', '=', 'gramjuth_id')
                    ->join('taluka', 'taluka.id', '=', 'gramjuth.taluka_id')
                    ->join('jilla', 'jilla.id', '=', 'taluka.jilla_id')
                    ->join('vibhag', 'vibhag.id', '=',  'jilla.vibhag_id')
                    ->join('prant', 'prant.id', '=', 'vibhag.prant_id')
                    ->where('gram.id', '=', $value['gram_id'])
                    ->first();

                if ($locationDetails) {
                    $data[$key]['gram_name'] = $locationDetails->gram;
                    $data[$key]['gramjuth_name'] = $locationDetails->gramjuth;
                    $data[$key]['taluka'] = $locationDetails->taluka;
                    $data[$key]['jilla'] = $locationDetails->jilla;
                    $data[$key]['vibhag_name'] = $locationDetails->vibhag;
                    $data[$key]['prant_name'] = $locationDetails->prant_name;
                } else {
                    $data[$key]['gram_name'] = 'gram_name_blank';
                    $data[$key]['gramjuth_name'] = 'gramjuth_name_blank';
                    $data[$key]['taluka'] = 'taluka_blank';
                    $data[$key]['jilla'] = 'jilla_blank';
                    $data[$key]['vibhag_name'] = 'vibhag_name_blank';
                    $data[$key]['prant_name'] = 'prant_name_blank';
                }

                $appUser = User::select(DB::raw('group_concat(name) as names, group_concat(mobile_no) as mobile_no'))
                    ->where(['status' => 'Active', 'role' => 2])
                    ->whereNull('deleted_at')
                    ->whereRaw('FIND_IN_SET(?, gram_id)', [$value['gram_id']])
                    ->first();

                if ($appUser) {
                    $data[$key]['app_user'] = $appUser->names;
                    $data[$key]['app_user_mobile_no'] = $appUser->mobile_no;
                } else {
                    $data[$key]['app_user'] = 'app_user_blank';
                    $data[$key]['app_user_mobile_no'] = 'app_user_mobile_no_blank';
                }

                $stockUser = User::select(DB::raw('group_concat(name) as names, group_concat(mobile_no) as mobile_no'))
                    ->where(['status' => 'Active', 'role' => 6])
                    ->whereNull('deleted_at')
                    ->whereRaw('FIND_IN_SET(?, gram_id)', [$value['gram_id']])
                    ->first();

                if ($stockUser) {
                    $data[$key]['stockiest_user'] = $stockUser->names;
                    $data[$key]['stockiest_mobile_no'] = $stockUser->mobile_no;
                } else {
                    $data[$key]['stockiest_user'] = 'stockiest_user_blank';
                    $data[$key]['stockiest_mobile_no'] = 'stockiest_mobile_no_blank';
                }
                if (Auth::user()->role == 1 || Auth::user()->role == 5) {
                    $vibhagUser = User::select(DB::raw('group_concat(name) as names, group_concat(mobile_no) as mobile_no'))
                        ->where(['status' => 'Active', 'role' => 4])
                        ->whereNull('deleted_at')
                        ->whereRaw('FIND_IN_SET(?, vibhag_id)', [$value['vibhag_id']])
                        ->first();

                    if (!is_null($vibhagUser->names)) {
                        $data[$key]['vibhag_user'] = $vibhagUser->names;
                        $data[$key]['vibhag_mobile_no'] = $vibhagUser->mobile_no;
                    } else {
                        $data[$key]['vibhag_user'] = '';
                        $data[$key]['vibhag_mobile_no'] = '';
                    }
                }

                if (Auth::user()->role == 1) {
                    $prantUser = User::select(DB::raw('group_concat(name) as names, group_concat(email) as email'))
                        ->where(['status' => 'Active', 'role' => 5])
                        ->whereNull('deleted_at')
                        ->whereRaw('FIND_IN_SET(?, prant_id)', [$value['prant_id']])
                        ->first();

                    if (!is_null($prantUser)) {
                        $data[$key]['prant_user'] = $prantUser->names;
                        $data[$key]['prant_email'] = $prantUser->email;
                    } else {
                        $data[$key]['prant_user'] = 'prant_user_blank';
                        $data[$key]['prant_email'] = 'prant_email_blank';
                    }
                }
            }

        return response()->stream(function () use ($data) {
            $handle = fopen('php://output', 'w');
            if (Auth::user()->role == 1){
                $keys = [
                    'gram_name', 'user_name', 'user_mobile_no', 'gramjuth_name', 'app_user', 'app_user_mobile_no', 'taluka', 'jilla', 'stockiest_user', 'stockiest_mobile_no'
                    , 'vibhag_name', 'vibhag_user','vibhag_mobile_no', 'prant_name', 'prant_user', 'prant_email'
                ];

                fputcsv($handle, [
                    'ગામ', 'આરોગ્ય મિત્ર', 'મોબાઇલ', 'ગામ જૂથ', 'પ્રવાસી કાર્યકર્તા', 'મોબાઇલ', 'તાલુકો', 'જિલ્લો', 'સ્ટોક યુજર', 'મોબાઇલ'
                    , 'વિભાગ', 'વિભાગ યુજર', 'મોબાઇલ', 'પ્રાંત', 'પ્રાંત યુજર', 'ઈમેઈલ',
                ]);
            } else if (Auth::user()->role == 4){
                $keys = [
                    'gram_name', 'user_name', 'user_mobile_no', 'gramjuth_name', 'app_user', 'app_user_mobile_no', 'taluka', 'jilla', 'stockiest_user', 'stockiest_mobile_no'
                ];

                fputcsv($handle, [
                    'ગામ', 'આરોગ્ય મિત્ર', 'મોબાઇલ', 'ગામ જૂથ', 'પ્રવાસી કાર્યકર્તા', 'મોબાઇલ', 'તાલુકો', 'જિલ્લો', 'સ્ટોક યુજર', 'મોબાઇલ'
                ]);
            } else if (Auth::user()->role == 5){
                $keys = [
                    'gram_name', 'user_name', 'user_mobile_no', 'gramjuth_name', 'app_user', 'app_user_mobile_no', 'taluka', 'jilla', 'stockiest_user', 'stockiest_mobile_no'
                    , 'vibhag_name', 'vibhag_user','vibhag_mobile_no'
                ];

                fputcsv($handle, [
                    'ગામ', 'આરોગ્ય મિત્ર', 'મોબાઇલ', 'ગામ જૂથ', 'પ્રવાસી કાર્યકર્તા', 'મોબાઇલ', 'તાલુકો', 'જિલ્લો', 'સ્ટોક યુજર', 'મોબાઇલ'
                    , 'વિભાગ', 'વિભાગ યુજર', 'મોબાઇલ'
                ]);
            }

            foreach ($data as $key => $val) {
                foreach ($keys as $nKey) {
                    $newData[$key][$nKey] = $val[$nKey];
                }
            }

            foreach ($newData as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users.csv"',
        ]);
    }
}
