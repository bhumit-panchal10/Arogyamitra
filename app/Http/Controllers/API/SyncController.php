<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\{
    Beneficiary,
    Medicine,
    MedicineRequest,
    MedicineStock,
    MedicineTrack,
    User,
};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    Auth,
    DB,
    Validator
};
use App\Traits\GlobalHelper;

class SyncController extends Controller
{
    public $user;
    use GlobalHelper;

    function __construct()
    {
        if (auth()->guard('api')->user()) {
            $user = User::getUserByToken(auth()->guard('api')->user()->token()->id);
            if ($user) {
                $this->user = $user;
            }
        }
    }

    /**
     * Data sync.
     */
    public function syncData(Request $request)
    {
        if ($this->user) {
            $stock = $request->get('stock');
            $beneficiaryArr = $request->get('beneficiary');
            date_default_timezone_set('Asia/kolkata');

            $response = false;
            if ($stock) {
                $rules = [
                    'stock.*.qty' => 'numeric|gt:0',
                    'stock.*.medicine_id' => 'required|numeric|gt:0',
                    'stock.*.type' => 'required|in:current,request,received,consume',
                    'stock.*.arogyamitra_id' => 'required|numeric|gt:0'
                ];
                $messages = [
                    'stock.*.medicine_id' => trans('validation.required'),
                    'stock.*.medicine_id' => trans('validation.numeric'),
                    'stock.*.qty' => trans('validation.numeric'),
                    'stock.*.type' => trans('validation.required'),
                    'stock.*.arogyamitra_id' => trans('validation.required'),
                ];
                $sysData = Validator::make($request->all(), $rules, $messages);
                // if validation is fail so return validation msg
                if ($sysData->fails()) {
                    // set validation msg
                    return response()->json([
                        'status'    => '0',
                        'result'    => 'failure',
                        'message'   => $sysData->errors()->all()
                    ], 422);
                }
                $response = self::syncDataByType($stock);

                $user = User::where('id', $this->user->id)->first();
                $activeMedicine = Medicine::where('status', '1')->orderBy('name', 'asc')->get()->toArray();
                $inactiveMedicine = Medicine::where('status', '0')->orderBy('name', 'asc')->get()->toArray();
                $arrData['user'] = $user->status;
                $arrData['activeMedicine'] = $activeMedicine;
                $arrData['inactiveMedicine'] = $inactiveMedicine;
            }

            $benSave = false;
            if ($beneficiaryArr) {
                $rules = [
                    'beneficiary.*.beneficiary' => 'required|numeric|gt:0',
                    'beneficiary.*.created_date' => 'required',
                    'beneficiary.*.arogyamitra_id' => 'required|numeric|gt:0'
                ];
                $messages = [
                    'beneficiary.*.beneficiary' => trans('validation.required'),
                    'beneficiary.*.beneficiary' => trans('validation.numeric'),
                    'beneficiary.*.created_date' => trans('validation.required'),
                    'beneficiary.*.arogyamitra_id' => trans('validation.required'),
                ];
                $sysData = Validator::make($request->all(), $rules, $messages);
                // if validation is fail so return validation msg
                if ($sysData->fails()) {
                    // set validation msg
                    return response()->json([
                        'status'    => '0',
                        'result'    => 'failure',
                        'message'   => $sysData->errors()->all()
                    ], 422);
                }


                foreach ($beneficiaryArr as $val) {
                    $beneficiary = new Beneficiary();
                    $beneficiary->arogyamitra_id = $val['arogyamitra_id'];
                    $beneficiary->gram_id = self::getGramByArogyaMitraId($val['arogyamitra_id'])->gram_id;
                    $beneficiary->number_of_beneficiary = $val['beneficiary'];
                    $beneficiary->created_at = date('Y-m-d', strtotime($val['created_date']));
                    $beneficiary->save();
                }
                $benSave = true;
            }

            if ($response) {
                return response()->json([
                    'status'    => '1',
                    'result'    => 'success',
                    'message'  => trans('messages.syn_data'),
                    'medicine' => $arrData
                ], 200);
            } else if ($benSave) {
                return response()->json([
                    'status' => '1',
                    'result' => 'success',
                    'message' => trans('messages.beneficiary_save'),
                ], 200);
            } else {
                return response()->json([
                    'status'    => '0',
                    'result'    => 'failure',
                    'message'   => trans('messages.no_found')
                ], 200);
            }
        } else {
            return response()->json([
                'messages'  => trans('messages.unauthorized_user')
            ], 401);
        }
    }

    public function syncDataByType($stock)
    {
        foreach ($stock as $value) {
            // for request stock
            if ($value['type'] == 'request') {
                $requestArr = [
                    'arogyamitra_id' => $value['arogyamitra_id'],
                    'medicine_id' => $value['medicine_id'],
                    'gram_id' => self::getGramByArogyaMitraId($value['arogyamitra_id'])->gram_id,
                    'qty' => $value['qty'],
                    'status' => '1',
                    'app_user_id' => Auth::user()->id,
                    'app_user_name' => Auth::user()->name,
                    'created_at' => Carbon::now(),
                ];
                $response = MedicineRequest::InsertGetId($requestArr);
            } else if ($value['type'] == 'current') {
                // if medicine is available
                $isMedicineStockAvailable = MedicineStock::where(['arogyamitra_id' => $value['arogyamitra_id'], 'medicine_id' => $value['medicine_id']])->first();
                if ($isMedicineStockAvailable) {
                    $currentArr = [
                        'qty' => $value['qty'],
                        'created_at' => Carbon::now(),
                        'gram_id' => self::getGramByArogyaMitraId($value['arogyamitra_id'])->gram_id,
                    ];
                    $response = $isMedicineStockAvailable->update($currentArr);
                } else {
                    $currentArr = [
                        'arogyamitra_id' => $value['arogyamitra_id'],
                        'medicine_id' => $value['medicine_id'],
                        'gram_id' => self::getGramByArogyaMitraId($value['arogyamitra_id'])->gram_id,
                        'qty' => $value['qty'],
                        'created_at' => Carbon::now(),
                    ];
                    $response = MedicineStock::InsertGetId($currentArr);
                }
            } else if ($value['type'] == 'received') {
                $isMedicineStockAvailable = MedicineStock::where(['arogyamitra_id' => $value['arogyamitra_id'], 'medicine_id' => $value['medicine_id']])->first();
                if ($isMedicineStockAvailable) {
                    $newStock = $isMedicineStockAvailable['qty'] + $value['qty'];
                    $currentArr = [
                        'qty' => $newStock,
                        'created_at' => Carbon::now(),
                    ];
                    $response = $isMedicineStockAvailable->update($currentArr);
                } else {
                    $receivedArr = [
                        'arogyamitra_id' => $value['arogyamitra_id'],
                        'medicine_id' => $value['medicine_id'],
                        'qty' => $value['qty'],
                        'gram_id' => self::getGramByArogyaMitraId($value['arogyamitra_id'])->gram_id,
                        'created_at' => Carbon::now(),
                    ];
                    $response = MedicineStock::InsertGetId($receivedArr);
                }
                self::MedicineTrack($value, "R");
            } else if ($value['type'] == 'consume') {
                $isMedicineStockAvailable = MedicineStock::where(['arogyamitra_id' => $value['arogyamitra_id'], 'medicine_id' => $value['medicine_id']])->first();
                if ($isMedicineStockAvailable && ($isMedicineStockAvailable['qty'] > $value['qty'] || $value['qty'])) {
                    $newStock = $isMedicineStockAvailable['qty'] - $value['qty'];
                    if ($newStock < 0) {
                        $newStock = 0;
                    }
                    $currentArr = [
                        'qty' => $newStock,
                        'created_at' => Carbon::now(),
                    ];
                    $response = $isMedicineStockAvailable->update($currentArr);
                } else {
                    $receivedArr = [
                        'arogyamitra_id' => $value['arogyamitra_id'],
                        'medicine_id' => $value['medicine_id'],
                        'gram_id' => self::getGramByArogyaMitraId($value['arogyamitra_id'])->gram_id,
                        'qty' => $value['qty'] ?: 0,
                        'created_at' => Carbon::now(),
                    ];

                    $response = MedicineStock::InsertGetId($receivedArr);
                }
                self::MedicineTrack($value, "C");
            }
        }

        return $response;
    }

    public function MedicineTrack($medicineData, $type)
    {
        $ReceivedArr = [
            'arogyamitra_id' => $medicineData['arogyamitra_id'],
            'medicine_id' => $medicineData['medicine_id'],
            'qty' => $medicineData['qty'],
            'mode' => $type,
            'gram_id' => self::getGramByArogyaMitraId($medicineData['arogyamitra_id'])->gram_id,
            'created_at' => date('Y-m-d H:i:s', strtotime($medicineData['created_date']))
        ];
        MedicineTrack::InsertGetId($ReceivedArr);
    }

    public function getGramByArogyaMitraId($userId)
    {
        return User::select('name', 'gram_id')->where(['id' => $userId, 'status' => 'Active'])->first();
    }
}
