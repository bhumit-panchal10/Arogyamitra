<?php

namespace App\Traits;

use App\Models\{
    User,
    LatLongHistory,
    MedicineRequest
};
use Illuminate\Support\Facades\DB;

trait GlobalHelper
{
    static public function getGramStockiest($user)
    {
        $gramIds = explode(',', $user->gram_id);
        $arogyaMitraIdStr = '';
        foreach ($gramIds as $val) {
            $userDetails = User::select(DB::raw('GROUP_CONCAT(gram_id) as gram_id'))
                ->where('id', '<>', $user->id)
                ->where(['role' => 2])
                ->whereRaw('FIND_IN_SET(?, gram_id)', [$val])
                ->first();

            if ($userDetails) {
                $arogyaMitraIdStr .= $userDetails->gram_id . ',';
            }
        }

        $arogyaMitraGramIds = $arogyaMitraIdStr ? array_unique(explode(',', rtrim($arogyaMitraIdStr, ','))) : [];

        return $arogyaMitraGramIds;
    }

    static public function getAppUserList($user)
    {
        $gramIds = explode(',', $user->gram_id);
        $arogyaMitraIdStr = '';
        $userArr = [];
        foreach ($gramIds as $val) {
            $userDetails = User::select(DB::raw('GROUP_CONCAT(id) as id'))
                ->where('id', '<>', $user->id)
                ->where(['role' => 2])
                ->whereRaw('FIND_IN_SET(?, gram_id)', [$val])
                ->first();

            if ($userDetails) {
                $arogyaMitraIdStr .= $userDetails->id . ',';
            }
        }

        $arogyaMitraIds = $arogyaMitraIdStr ? array_unique(explode(',', rtrim($arogyaMitraIdStr, ','))) : [];

        if ($arogyaMitraIds) {
            $users = User::whereIn('id', $arogyaMitraIds)->get();

            foreach ($users as $key => $val) {
                $userArr[$key]['id'] = $val['id'];
                $userArr[$key]['name'] = $val['name'];
                $userArr[$key]['mobile_no'] = $val['mobile_no'];

                $medicineRequest = MedicineRequest::where(['medicine_request.status' => '1', 'app_user_id' => $val['id']])->count();
                if ($medicineRequest) {
                    $userArr[$key]['is_medicine_request'] = '1';
                } else {
                    $userArr[$key]['is_medicine_request'] = '0';
                }
            }
        }

        return $userArr;
    }

    public function latLong($request)
    {
        $location = new LatLongHistory();
        $location->arogyamitra_id = isset($request['arogyamitra_id']) ? $request['arogyamitra_id'] : $request['stockiest_id'];
        $location->type = $request['type'];
        $location->latitude = $request['latitude'];
        $location->longitude = $request['longitude'];
        $location->save();

        //return $location;
    }
}
