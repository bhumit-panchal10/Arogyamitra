<?php

namespace App\Http\Controllers;

use App\Models\{
    Vibhag,
    Jilla,
    Taluka,
    Gramjuth,
    Gram,
    User
};
use Illuminate\Http\Request;

class CsvController extends Controller
{
    public function show()
    {
        $title = '';
        return view('importCsv', compact('title'));
    }
    public function importCsv(Request $request)
    {
        $farogyamitra = fopen("farogyamitra.csv", "a");
        $fappuser = fopen("fappuser.csv", "a");

        if ($request->hasFile('csv_file')) {
            $file = $request->file('csv_file');
            $rows = array_map('str_getcsv', file($file->getRealPath()));
            $export1 = [];
            $cnt = 2;

            foreach ($rows as $row) {
                $implode = implode(' ', $row);
                $explode = explode(' ', $implode);
                $explode = $row;

                $arrayData = [
                    'વિભાગ' => $explode[0],
                    'જિલ્લો' => $explode[1],
                    'તાલુકો' => $explode[2],
                    'ગ્રામજૂથ' => $explode[3],
                    'ગામ/સ્થાન' => $explode[4],
                    'આરોગ્યમિત્ર' => $explode[5],
                    'મોબાઇલ નંબર' => $explode[6],
                    'પ્રવાસી કાર્યકર્તા' => $explode[7],
                    'મોબાઇલ નંબર1' => $explode[8],
                ];

                $vibhag_id = $this->vibhag($arrayData['વિભાગ']);
                $jilla_id = $this->jilla($arrayData['જિલ્લો'], $vibhag_id);
                $taluka_id = $this->taluka($arrayData['તાલુકો'], $jilla_id);
                $gramjuth_id = $this->gramJuth($arrayData['ગ્રામજૂથ'], $taluka_id);
                $gram_id = $this->gram($arrayData['ગામ/સ્થાન'], $gramjuth_id);

                if (!empty($arrayData['આરોગ્યમિત્ર'] && $arrayData['મોબાઇલ નંબર'])) {
                    $userId = $this->user($arrayData['આરોગ્યમિત્ર'], $arrayData['મોબાઇલ નંબર'], $gram_id, null, '3', null);
                    if (!$userId) {
                        $arrayData["cnt"] = $cnt;
                        fputcsv($farogyamitra, $arrayData);
                    }
                } else {
                    $arrayData["cnt"] = $cnt;
                    fputcsv($farogyamitra, $arrayData);
                }
                if (!empty($arrayData['પ્રવાસી કાર્યકર્તા'] && $arrayData['મોબાઇલ નંબર1'])) {
                    $userId = $this->user($arrayData['પ્રવાસી કાર્યકર્તા'], $arrayData['મોબાઇલ નંબર1'], null, $jilla_id, '2', null);
                    if (!$userId) {
                        $arrayData["cnt"] = $cnt;
                        fputcsv($fappuser, $arrayData);
                    }
                }
                $cnt++;

                $export1[] = $arrayData;
            }
            fclose($farogyamitra);
            fclose($fappuser);
        }
        return redirect()->back();
    }

    public function vibhag($name)
    {
        $vibhagExists = Vibhag::where('name', $name)->first();

        if (!$vibhagExists) {
            $vibhagId = Vibhag::insertGetId(['name' => $name, 'status' => '1']);
            return $vibhagId;
        }

        return $vibhagExists->id;
    }

    public function jilla($name, $vibhag_id)
    {
        $jillaExists = Jilla::where('name', $name)->first();

        if (!$jillaExists) {
            $jillaId = Jilla::insertGetId(['name' => $name, 'status' => '1', 'vibhag_id' => $vibhag_id]);
            return $jillaId;
        }

        return $jillaExists->id;
    }

    public function taluka($name, $jilla_id)
    {
        $talukaExists = Taluka::where('name', $name)->first();

        if (!$talukaExists) {
            $talukaId = Taluka::insertGetId(['name' => $name, 'status' => '1', 'jilla_id' => $jilla_id]);
            return $talukaId;
        }

        return $talukaExists->id;
    }

    public function gramJuth($name, $taluka_id)
    {
        $gramJuthExists = Gramjuth::where('name', $name)->first();

        if (!$gramJuthExists) {
            $gramJuthId = Gramjuth::insertGetId(['name' => $name, 'status' => '1', 'taluka_id' => $taluka_id]);
            return $gramJuthId;
        } else

            return $gramJuthExists->id;
    }

    public function gram($name, $gramJuth_id)
    {
        $gramExists = Gram::where('name', $name)->first();

        if (!$gramExists) {
            $gramId = Gram::insertGetId(['name' => $name, 'status' => '1', 'gramjuth_id' => $gramJuth_id]);
            return $gramId;
        }

        return $gramExists->id;
    }

    public function user($name, $mobile_no, $gram_id, $jilla_id, $role, $vibhag_id, $cnt = false)
    {

        $matchThese = ['name' => $name, 'mobile_no' => $mobile_no, 'role' => $role];
        $userExists = User::where($matchThese)->first();

        if (!$userExists) {
            return User::insert([
                'name' => $name,
                'password' => '$2y$10$QgpU1BErV5.G.WoCXRNfau/GCxKjxKGRiKA5aEZQ0MeRHsdWPxUG2',
                'status' => 'Active',
                'gram_id' => $gram_id,
                'jilla_id' => $jilla_id,
                'vibhag_id' => $vibhag_id,
                'role' => $role,
                'mobile_no' => $mobile_no,
            ]);
        } else {
            return false;
        }
    }
}
