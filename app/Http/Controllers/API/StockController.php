<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\{
    Gram,
    Gramjuth,
    Medicine,
    MedicineStock,
    MedicineTrack,
    PdfTrack,
    RequestStock,
    Taluka,
    User,
    Beneficiary,
    LatLongHistory,
    MedicineRequest
};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    Auth,
    DB,
    Validator
};
use App\Traits\GlobalHelper;

class StockController extends Controller
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

    public function updateStockDetailsByType(Request $request)
    {
        /* -------------------------------------------------
     | AUTH CHECK
     -------------------------------------------------*/
        if (!$this->user || $this->user->status !== "Active") {
            return response()->json([
                'status'  => '0',
                'result'  => 'failure',
                'message' => trans('messages.unauthorized_user')
            ], 401);
        }

        date_default_timezone_set('Asia/Kolkata');

        /* -------------------------------------------------
     | VALIDATION
     -------------------------------------------------*/
        $rules = [
            'type' => 'required|in:current,request,received,consume',
        ];

        if ($this->user->role == 2) {
            $rules['arogyamitra_id'] = 'required|numeric|gt:0';
        } elseif ($this->user->role == 6) {
            $rules['stockiest_id'] = 'required|numeric|gt:0';
        }

        if ($request->type !== 'consume') {
            $rules['stock'] = 'required|array|min:1';
            $rules['stock.*.medicine_id'] = 'required|numeric|gt:0';
            $rules['stock.*.qty'] = 'required|numeric|gt:0';
        }

        if ($request->type === 'request') {
            $rules['gram_id'] = 'nullable';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status'  => '0',
                'result'  => 'failure',
                'message' => $validator->errors()->all()
            ], 422);
        }

        /* -------------------------------------------------
     | COMMON VARIABLES
     -------------------------------------------------*/
        $stock     = $request->stock ?? [];
        $requestData = $request->all();

        $id = ($this->user->role == 2)
            ? $request->arogyamitra_id
            : $request->stockiest_id;

        /* -------------------------------------------------
     | QTY LIMIT CHECK
     -------------------------------------------------*/
        foreach ($stock as $s) {
            if ($s['qty'] >= 100000) {
                return response()->json([
                    'status'  => '0',
                    'result'  => 'failure',
                    'message' => trans('messages.qty_max_length')
                ], 422);
            }
        }

        /* -------------------------------------------------
     | FIND STOCKIEST BY MULTIPLE GRAM IDS
     -------------------------------------------------*/
        $arogyamitraGramIds = explode(',', Auth::user()->gram_id);

        $stockiest = DB::table('users')
            ->where('role', 6)
            ->where('status', 'Active')
            ->where(function ($q) use ($arogyamitraGramIds) {
                foreach ($arogyamitraGramIds as $gid) {
                    $q->orWhereRaw('FIND_IN_SET(?, gram_id)', [$gid]);
                }
            })
            ->first();

        if (!$stockiest) {
            return response()->json([
                'status'  => '0',
                'result'  => 'failure',
                'message' => 'Stockiest not found for this Gram'
            ], 404);
        }

        $stockiest_id = $stockiest->id;

        /* -------------------------------------------------
     | TYPE : REQUEST
     -------------------------------------------------*/
        if ($request->type === 'request') {

            $gramId = (int) $request->gram_id;
            $medicineRequest = false;

            DB::transaction(function () use (
                $stock,
                $id,
                $stockiest_id,
                $gramId,
                &$medicineRequest
            ) {

                // Gram user (role = 3)
                $gramUser = DB::table('users')
                    ->where('role', 3)
                    ->where('gram_id', $gramId)
                    ->first();

                foreach ($stock as $item) {

                    /* --------------------------------
                 | ROLE : AROGYAMITRA (2)
                 --------------------------------*/
                    if (Auth::user()->role == 2) {

                        // Arogyamitra → Stockiest
                        $medicineRequest = RequestStock::insertGetId([
                            'arogyamitra_id' => $id,
                            'medicine_id'    => $item['medicine_id'],
                            'qty'            => $item['qty'],
                            'gram_id'        => $gramId,
                            'status'         => '1',
                            'app_user_id'    => Auth::id(),
                            'app_user_name'  => Auth::user()->name,
                            'iRequestTo'     => $stockiest_id,
                            'created_at'     => now(),
                            'updated_at'     => now()
                        ]);

                        // Gram → Arogyamitra
                        if ($gramUser) {
                            RequestStock::insert([
                                'arogyamitra_id' => $gramUser->id,
                                'medicine_id'    => $item['medicine_id'],
                                'qty'            => $item['qty'],
                                'gram_id'        => $gramId,
                                'status'         => '1',
                                'app_user_id'    => Auth::id(),
                                'app_user_name'  => Auth::user()->name,
                                'iRequestTo'     => $id,
                                'created_at'     => now(),
                                'updated_at'     => now()
                            ]);
                        }
                    }

                    /* --------------------------------
                 | ROLE : STOCKIEST (6)
                 --------------------------------*/ else {

                        $medicineRequest = RequestStock::insertGetId([
                            'arogyamitra_id' => $id,
                            'medicine_id'    => $item['medicine_id'],
                            'qty'            => $item['qty'],
                            'gram_id'        => null,
                            'status'         => '1',
                            'app_user_id'    => null,
                            'app_user_name'  => null,
                            'iRequestTo'     => 1,
                            'created_at'     => now(),
                            'updated_at'     => now()
                        ]);
                    }
                }
            });

            if ($medicineRequest) {
                self::latLong($requestData);

                return response()->json([
                    'status'  => '1',
                    'result'  => 'success',
                    'message' => trans('messages.request_stock')
                ], 200);
            }

            return response()->json([
                'status'  => '0',
                'result'  => 'failure',
                'message' => trans('messages.fails')
            ], 400);
        }

        /* -------------------------------------------------
     | INVALID TYPE
     -------------------------------------------------*/
        return response()->json([
            'status'  => '0',
            'result'  => 'failure',
            'message' => trans('messages.invalid_request')
        ], 400);
    }



    /**
     * Display a listing of the resource.
     */
    // public function updateStockDetailsByType(Request $request)
    // {
    //     if ($this->user && $this->user->status == "Active") {
    //         // for time zone set in ist
    //         date_default_timezone_set('Asia/kolkata');
    //         //TODO get qty,medicine id
    //         $rules = [
    //             'type' => 'required|in:current,request,received,consume',
    //             //'arogyamitra_id' => 'required|numeric|gt:0'
    //         ];
    //         if ($this->user->role == 2) {
    //             $id = [
    //                 'arogyamitra_id' => 'required|numeric|gt:0'
    //             ];
    //             $rules = array_merge($rules, $id);
    //         } else if ($this->user->role == 6) {
    //             $id = [
    //                 'stockiest_id' => 'required|numeric|gt:0'
    //             ];
    //             $rules = array_merge($rules, $id);
    //         }

    //         if ($request->get('type') == 'consume') {
    //             $stock = [
    //                 'stock' => 'nullable',
    //                 //'stock.*.medicine_id' => 'numeric|gt:0',
    //                 //'stock.*.qty' => 'nullable|numeric|gt:0'
    //             ];
    //             $rules = array_merge($rules, $stock);
    //         } else {
    //             $stock = [
    //                 'stock' => 'array|min:1',
    //                 'stock.*.qty' => 'numeric|gt:0',
    //                 'stock.*.medicine_id' => 'required|numeric|gt:0'
    //             ];
    //             $rules = array_merge($rules, $stock);
    //         }

    //         $messages = [
    //             'stock.*.medicine_id' => trans('validation.required'),
    //             'stock.*.medicine_id' => trans('validation.numeric'),
    //             'stock.*.qty' => trans('validation.numeric'),
    //             'type' => trans('validation.required'),
    //         ];
    //         $requestStock = Validator::make($request->all(), $rules, $messages);
    //         // if validation is fail so return validation msg
    //         if ($requestStock->fails()) {
    //             // set validation msg
    //             return response()->json([
    //                 'status'    => '0',
    //                 'result'    => 'failure',
    //                 'message'   => $requestStock->errors()->all()
    //             ], 422);
    //         }
    //         $requestData = $request->all();
    //         $stock = $request->get('stock');

    //         if ($this->user->role == 2) {
    //             $id = $request->get('arogyamitra_id');
    //         } else if ($this->user->role == 6) {
    //             $id = $request->get('stockiest_id');
    //         }

    //         // quality check 100000 less than or equal
    //         foreach ($stock as $qty) {
    //             if ($qty['qty'] >= 100000) {
    //                 return response()->json([
    //                     'status'    => '0',
    //                     'result'    => 'failure',
    //                     'message'   => trans('messages.qty_max_length')
    //                 ], 422);
    //             }
    //         }

    //         if ($request->get('type') == 'request') {
    //             // if request data is not empty or not less then or equal 0 and numeric
    //             foreach ($stock as $request) {
    //                 $medicineRequestArr = [
    //                     'arogyamitra_id' => $id,
    //                     'medicine_id' => $request['medicine_id'],
    //                     'qty' => $request['qty'],
    //                     'gram_id' => ($this->user->role == 2) ? self::getGramByArogyaMitraId($id)->gram_id : NULL,
    //                     'status' => '1',
    //                     'app_user_id' => ($this->user->role == 2) ? Auth::user()->id : NULL,
    //                     'app_user_name' => ($this->user->role == 2) ? Auth::user()->name : NULL,
    //                     'created_at' => Carbon::now(),
    //                     'updated_at' => Carbon::now()
    //                 ];
    //                 //medicine add in medicine request table
    //                 $medicineRequest = RequestStock::InsertGetId($medicineRequestArr);
    //             }

    //             // success msg for adding data
    //             if ($medicineRequest) {
    //                 self::latLong($requestData);

    //                 return response()->json([
    //                     'status' => '1',
    //                     'result' => 'success',
    //                     'message' => trans('messages.request_stock'),
    //                 ], 200);
    //             } else {
    //                 return response()->json([
    //                     'status' => '0',
    //                     'result' => 'failure',
    //                     'message' => trans('messages.fails'),
    //                 ], 400);
    //             }
    //         } else if ($request->get('type') == 'consume') {
    //             self::latLong($requestData);
    //             $benSave = false;
    //             if ($request->get('beneficiary')) {
    //                 $beneficiary = new Beneficiary();
    //                 $beneficiary->arogyamitra_id = $id;
    //                 $beneficiary->gram_id = self::getGramByArogyaMitraId($id)->gram_id;
    //                 $beneficiary->number_of_beneficiary = $request->get('beneficiary');
    //                 $beneficiary->created_at = Carbon::now();
    //                 $beneficiary->save();

    //                 $benSave = true;
    //             }

    //             if ($stock) {
    //                 foreach ($stock as $consume) {
    //                     $getMedicineList = MedicineStock::select('medicine_id', 'qty')
    //                         ->where(['arogyamitra_id' => $id, 'medicine_id' => $consume['medicine_id']])
    //                         ->orderBy('medicine_id', 'ASC')
    //                         ->get()->toArray();
    //                     if ($getMedicineList) {
    //                         foreach ($getMedicineList as $medicineList) {
    //                             // if medicine list of qty greater then 0 and less then or equal of qty in request data
    //                             if ($medicineList['qty'] >= $consume['qty'] && $consume['qty']) {
    //                                 if (($medicineList['medicine_id'] == $consume['medicine_id'])) {
    //                                     // get old medicine stock qty - get new stock
    //                                     $consumeStock = $medicineList['qty'] - $consume['qty'];
    //                                     // medicine consume array
    //                                     $medicineConsumeArr = [
    //                                         'qty' => $consumeStock,
    //                                         'created_at' => Carbon::now()
    //                                     ];
    //                                     //medicine add in medicine request table
    //                                     $medicineConsume = MedicineStock::where(['arogyamitra_id' => $id, 'medicine_id' => $consume['medicine_id']])->update($medicineConsumeArr);
    //                                     // medicine track array
    //                                     $medicineTrackArr = [
    //                                         'qty' => $consume['qty'],
    //                                         'arogyamitra_id' => $id,
    //                                         'gram_id' => self::getGramByArogyaMitraId($id)->gram_id,
    //                                         'medicine_id' => $consume['medicine_id'],
    //                                         'mode' => 'C',
    //                                         'created_at' => Carbon::now()
    //                                     ];
    //                                     // medicine tract table added data when consume medicine
    //                                     MedicineTrack::InsertGetId($medicineTrackArr);
    //                                 }
    //                             } else {
    //                                 // for out of stock
    //                                 return response()->json([
    //                                     'status' => '0',
    //                                     'result' => 'failure',
    //                                     'message' => trans('messages.current_stock'),
    //                                 ], 200);
    //                             }
    //                         }
    //                     } else {
    //                         return response()->json([
    //                             'status' => '0',
    //                             'result' => 'failure',
    //                             'message' => trans('messages.current_stock'),
    //                         ], 400);
    //                     }
    //                 }

    //                 if ($medicineConsume) {
    //                     return response()->json([
    //                         'status' => '1',
    //                         'result' => 'success',
    //                         'message' => trans('messages.medicine_stock'),
    //                     ], 200);
    //                 } else {
    //                     return response()->json([
    //                         'status' => '0',
    //                         'result' => 'failure',
    //                         'message' => trans('messages.current_stock'),
    //                     ], 400);
    //                 }
    //             }

    //             if ($benSave) {
    //                 return response()->json([
    //                     'status' => '1',
    //                     'result' => 'success',
    //                     'message' => trans('messages.beneficiary_save'),
    //                 ], 200);
    //             }
    //             // for out of stock
    //         } else if ($request->get('type') == 'received') { // for received stock
    //             foreach ($stock as $stocks) {
    //                 $availableStock = MedicineStock::select('medicine_id', 'qty')
    //                     ->where(['arogyamitra_id' => $id, 'medicine_id' => $stocks['medicine_id']])
    //                     ->first();
    //                 if ($availableStock) {
    //                     $receivedStock = $availableStock['qty'] + $stocks['qty'];
    //                     // medicine received array
    //                     $medicineReceivedArr = [
    //                         'qty' => $receivedStock,
    //                         'created_at' => Carbon::now(),
    //                         'updated_at' => Carbon::now()
    //                     ];

    //                     $medicineReceived = MedicineStock::where(['arogyamitra_id' => $id, 'medicine_id' => $stocks['medicine_id']])->update($medicineReceivedArr);
    //                 } else {
    //                     $medicineReceivedArr = [
    //                         'arogyamitra_id' => $id,
    //                         'medicine_id' => $stocks['medicine_id'],
    //                         'qty' => $stocks['qty'],
    //                         'gram_id' => self::getGramByArogyaMitraId($id)->gram_id,
    //                         'created_at' => Carbon::now()
    //                     ];

    //                     $medicineReceived = MedicineStock::InsertGetId($medicineReceivedArr);
    //                 }
    //             }
    //             if ($medicineReceived) {
    //                 $medicineTrackArr = [
    //                     'qty' => $stocks['qty'],
    //                     'arogyamitra_id' => $id,
    //                     'gram_id' => self::getGramByArogyaMitraId($id)->gram_id,
    //                     'medicine_id' => $stocks['medicine_id'],
    //                     'mode' => 'R', // r for received
    //                     'created_at' => Carbon::now(),
    //                     'updated_at' => Carbon::now()
    //                 ];
    //                 // medicine tract table added data when received medicine
    //                 MedicineTrack::InsertGetId($medicineTrackArr);

    //                 self::latLong($requestData);
    //                 return response()->json([
    //                     'status'    => '1',
    //                     'result'    => 'success',
    //                     'message'   => trans('messages.received_stock')
    //                 ], 200);
    //             } else {
    //                 return response()->json([
    //                     'status'    => '0',
    //                     'result'    => 'failure',
    //                     'message'   => trans('messages.fails')
    //                 ], 400);
    //             }
    //         } else {
    //             // for current stock
    //             //for multiple medicine ids request
    //             foreach ($stock as $current) {
    //                 $getMedicineList = MedicineStock::where(['arogyamitra_id' => $id, 'medicine_id' => $current['medicine_id']])->first();

    //                 if ($getMedicineList) {
    //                     $currentStockArr = [
    //                         'qty' => $current['qty'],
    //                     ];
    //                     $currentStock = $getMedicineList->update($currentStockArr);
    //                 } else {
    //                     // medicine current array
    //                     $medicineCurrentArr = [
    //                         'arogyamitra_id' => $id,
    //                         'medicine_id' => $current['medicine_id'],
    //                         'qty' => $current['qty'],
    //                         'gram_id' => ($this->user->role == 2) ? self::getGramByArogyaMitraId($id)->gram_id : NULL,
    //                         'created_at' => Carbon::now()
    //                     ];
    //                     // if medicine id and arogya mitra id is not available then insert the data in medicine stock
    //                     $currentStock = MedicineStock::InsertGetId($medicineCurrentArr);
    //                 }
    //             }
    //             // if medicine stock update success then show the message
    //             if ($currentStock) {
    //                 self::latLong($requestData);
    //                 return response()->json([
    //                     'status'    => '1',
    //                     'result'    => 'success',
    //                     'message'   => trans('messages.stock_update')
    //                 ], 200);
    //             } else {
    //                 return response()->json([
    //                     'status'    => '0',
    //                     'result'    => 'failure',
    //                     'message'   => trans('messages.fails')
    //                 ], 400);
    //             }
    //         }
    //     } else {
    //         return response()->json([
    //             'messages'  => trans('messages.unauthorized_user')
    //         ], 401);
    //     }
    // }


    public function getStockList(Request $request)
    {
        if ($this->user && $this->user->status == "Active") {
            if ($this->user->role == 2) {
                $stock = Validator::make($request->all(), [
                    'arogyamitra_id' => 'required|numeric|gt:0'
                ]);
            } else if ($this->user->role == 6) {
                $stock = Validator::make($request->all(), [
                    'stockiest_id' => 'required|numeric|gt:0'
                ]);
            }

            if ($stock->fails()) {
                return response()->json([
                    'status'    => '0',
                    'result'    => 'failure',
                    'medicine'  => $stock->errors()->all()
                ], 422);
            }

            $medicine = [];
            $deliveredQty = [];
            //Stockiest
            if ($this->user->role == 6) {

                $deliveredQty = DB::table('medicine_request as mr')
                    ->select(
                        'mr.medicine_id',
                        'm.delivered_qty',
                        DB::raw('SUM(mr.qty) as total_delivered_qty')
                    )
                    ->join('medicine as m', 'mr.medicine_id', '=', 'm.id')
                    ->where('mr.iRequestTo', $request->stockiest_id)
                    ->where('mr.status', '1')
                    ->groupBy('mr.medicine_id')
                    ->get();

                $medicine = DB::table('medicine')
                    ->where('status', '1')
                    ->select(
                        'medicine.*',
                        DB::raw("(
                        SELECT closing_stock
                        FROM medicine_track
                        WHERE medicine_track.medicine_id = medicine.id
                        AND medicine_track.arogyamitra_id = {$request->stockiest_id}
                        ORDER BY id DESC
                        LIMIT 1
                    ) as CurrentStock")
                    )
                    ->get()
                    ->map(function ($item) {
                        return (array) $item;
                    })
                    ->toArray();
                // dd($medicine);

                foreach ($medicine as $key => $val) {

                    $arrData[$key]['medicine_id'] = $val['id'] ? $val['id'] : '';
                    $arrData[$key]['medicine_name'] = $val['name'] ? $val['name'] : '';
                    $arrData[$key]['packing'] = $val['qty'] . ' ' . $val['qty_type'] ? $val['qty'] . ' ' . $val['qty_type'] : '';
                    $arrData[$key]['current_stock'] = (!is_null($val['CurrentStock'])) ? $val['CurrentStock'] . ' ' . ($val['delivered_qty'] ?? '') : '0';
                }
            } else if ($this->user->role == 2) {
                //App user
                $getGramId = self::getGramByArogyaMitraId($request->get('arogyamitra_id'));
                $getGramIds = explode(',', $getGramId->gram_id);

                $medicine = DB::table('medicine')
                    ->where('status', '1')
                    ->select(
                        'medicine.*',
                        DB::raw("(
                        SELECT closing_stock
                        FROM medicine_track
                        WHERE medicine_track.medicine_id = medicine.id
                        AND medicine_track.arogyamitra_id = {$request->arogyamitra_id}
                        ORDER BY id DESC
                        LIMIT 1
                    ) as CurrentStock")
                    )
                    ->get()
                    ->map(function ($item) {
                        return (array) $item;
                    })
                    ->toArray();
                //dd($medicine);

                foreach ($medicine as $key => $val) {

                    $arrData[$key]['medicine_id'] = $val['id'] ? $val['id'] : '';
                    $arrData[$key]['medicine_name'] = $val['name'] ? $val['name'] : '';
                    $arrData[$key]['packing'] = $val['qty'] . ' ' . $val['qty_type'] ? $val['qty'] . ' ' . $val['qty_type'] : '';
                    // $arrData[$key]['current_stock'] = $val['CurrentStock'] . ' '. $val['delivered_qty'] ? $val['CurrentStock'] .' '. $val['delivered_qty'] : '0';
                    $arrData[$key]['current_stock'] = (!is_null($val['CurrentStock'])) ? $val['CurrentStock'] . ' ' . ($val['delivered_qty'] ?? '') : '0';
                }
            }
            // to do current stock in medicine available
            if (!empty($medicine)) {
                $beneficiaryArr = [
                    'beneficiary' => 0,
                    'last_update' => "",
                ];
                if ($this->user->role == 2) {
                    $beneficiary = Beneficiary::whereIn('gram_id', $getGramIds)->orderBy('id', 'DESC')->first();
                    if ($beneficiary) {
                        $beneficiaryArr = [
                            'beneficiary' => $beneficiary->number_of_beneficiary,
                            'last_update' => date('d-m-Y', strtotime($beneficiary->created_at))
                        ];
                    }
                }

                $response = [
                    'status' => '1',
                    'result' => 'success',
                    'beneficiary'  => $beneficiaryArr,
                    'medicine' => $arrData
                ];

                if ($this->user->role == 6) {
                    $response['delivered_qty'] = $deliveredQty;
                }

                return response()->json($response, 200);

                // return response()->json([
                //     'status'    => '1',
                //     'result'    => 'success',
                //     'beneficiary'  => $beneficiaryArr,
                //     'medicine'  => $arrData
                // ], 200);
                // to do current stock in medicine Not available
            } else {
                return response()->json([
                    'status'    => '0',
                    'result'    => 'failure',
                    'message'   => trans('messages.medicine_out_stock')
                ], 200);
            }
        } else {
            return response()->json([
                'messages'  => trans('messages.unauthorized_user')
            ], 401);
        }
    }

    public function getArogyaMitraList()
    {
        if ($this->user && $this->user->status == "Active") {
            $arrData = array();
            $getList = Taluka::select(DB::raw('group_concat(id) as ids'))->where(['jilla_id' => $this->user->jilla_id, 'status' => '1'])->first();

            if ($getList) {
                $ids = explode(',', $getList->ids);
                // get gramjuth list
                $getGramjuth = Gramjuth::where('gramjuth.status', '1')
                    ->whereIn('gramjuth.taluka_id', $ids)
                    ->get()->toArray();
                $j = 0;
                foreach ($getGramjuth as $value) {
                    $getUser = User::select('users.id as arogya_mitra_id', 'users.mobile_no', 'users.name', 'users.gram_id', 'g.name as gram_name')
                        ->join('gram as g', 'g.id', 'users.gram_id')
                        ->where('users.role', '=', '3')
                        ->where('users.status', '=', 'Active')
                        ->orderBy('g.name', 'asc')
                        ->where(['g.gramjuth_id' => $value['id'], 'g.status' => '1'])
                        ->whereIn('g.id', explode(',', $this->user->gram_id))
                        ->get()->toArray();

                    if ($getUser) {
                        foreach ($getUser as $i => $val) {
                            $arrData['arogyaMitraList'][$j]['gramjuth_name'] = $value['name'];
                            $arrData['arogyaMitraList'][$j]['gram_details'][$i]['gram_name'] = $val['gram_name'];
                            $arrData['arogyaMitraList'][$j]['gram_details'][$i]['gram_id'] = (int)$val['gram_id'];
                            $arrData['arogyaMitraList'][$j]['gram_details'][$i]['arogya_mitra_id'] = $val['arogya_mitra_id'];
                            $arrData['arogyaMitraList'][$j]['gram_details'][$i]['name'] = $val['name'];
                            $arrData['arogyaMitraList'][$j]['gram_details'][$i]['mobile_no'] = $val['mobile_no'];
                        }
                        $j++;
                    }
                }
            }
            if (!empty($arrData)) {
                return response()->json([
                    'status'    => '1',
                    'result'    => 'success',
                    'response'  => $arrData
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

    public function getVillageList()
    {
        if ($this->user && $this->user->status == "Active") {
            $arrData = array();
            $getList = Taluka::select(DB::raw('group_concat(id) as ids'))->where(['jilla_id' => $this->user->jilla_id, 'status' => '1'])->first();
            if ($getList) {
                $ids = explode(',', $getList->ids);
                $getGramjuth = Gramjuth::where('gramjuth.status', '1')
                    ->whereIn('gramjuth.taluka_id', $ids)
                    ->get()->toArray();
                if ($getGramjuth) {
                    foreach ($getGramjuth as $key => $value) {
                        $id[] = $value['id'];
                    }

                    $getUser = User::select('users.id as arogya_mitra_id', 'users.mobile_no', 'users.name', 'users.gram_id', 'g.name as gram_name')
                        ->join('gram as g', 'g.id', 'users.gram_id')
                        ->where('users.role', '3')
                        ->where('users.status', 'Active')
                        ->orderBy('g.name', 'asc')
                        //->whereIn('g.gramjuth_id', $id)
                        ->whereIn('g.id', explode(',', $this->user->gram_id))
                        ->where(['g.status' => '1'])
                        ->get()->toArray();

                    if ($getUser) {
                        foreach ($getUser as $key => $val) {
                            $arrData['village'][$key]['gram_name'] = $val['gram_name'];
                            $arrData['village'][$key]['gram_id'] = (int)$val['gram_id'];
                            $arrData['village'][$key]['arogya_mitra_id'] = $val['arogya_mitra_id'];
                        }
                    }
                }
            }

            if (!empty($arrData)) {
                return response()->json([
                    'status'    => '1',
                    'result'    => 'success',
                    'response'  => $arrData
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

    public function exportArogyaMitraList()
    {
        if ($this->user && $this->user->status == "Active") {
            date_default_timezone_set('Asia/kolkata');

            $getUser = PdfTrack::select('file_name', 'created_at')->where('arogyamitra_id', $this->user->id)->first();

            $date = date('Y-m-d');
            $downloadLink = url('/assets/uploads/arogya-mitra-export') . '/' .  $date . '_' . $this->user->id . '.csv';
            $filePath = public_path('/assets/uploads/arogya-mitra-export/' . $date . '_' . $this->user->id . ".csv");

            if ($getUser) {
                $time = strtotime($getUser->created_at);
                $realtime = time();

                // check time for 24 hour
                if ($time > $realtime) {
                    return response()->json([
                        'status'    => '1',
                        'result'    => 'success',
                        'response'  => $downloadLink
                    ], 200);
                }

                $getExportData = self::getArogyaMitra();

                if ($getExportData) {
                    $handle = fopen($filePath, 'w');
                    $headers = ['Taluka', 'Gramjuth', 'Gram', 'Name', 'Mobile No.'];

                    fputcsv($handle, $headers);
                    foreach ($getExportData as $val) {
                        fputcsv($handle, $val);
                    }
                    fclose($handle);
                }

                $downloadRecordArr = [
                    'file_name' => $date . '_' . $this->user->id . ".csv",
                    'created_at' => Carbon::now()
                ];

                return response()->json([
                    'status'    => '1',
                    'result'    => 'success',
                    'response'  => $downloadLink
                ], 200);
            } else {
                $downloadRecordArr = [
                    'arogyamitra_id' => $this->user->id,
                    'file_name' => $date . '_' . $this->user->id . ".csv",
                    'created_at' => Carbon::now()
                ];

                PdfTrack::InsertGetId($downloadRecordArr);

                return response()->json([
                    'status'    => '1',
                    'result'    => 'success',
                    'response'  => $downloadLink
                ], 200);
            }
        } else {
            return response()->json([
                'messages'  => trans('messages.unauthorized_user')
            ], 401);
        }
    }

    /* public function MedicineTrack($medicineData, $type)
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
    } */

    public function getGramByArogyaMitraId($userId)
    {
        return User::select('name', 'gram_id')->where(['id' => $userId, 'status' => 'Active'])->first();
    }

    public function getArogyaMitra()
    {
        $getList = Taluka::select(DB::raw('group_concat(id) as ids'))->where(['jilla_id' => $this->user->jilla_id, 'status' => '1'])->first();
        if ($getList) {
            $ids = explode(',', $getList->ids);
            // get gramjuth list
            $getGramjuth = Gramjuth::select(DB::raw('group_concat(id) as ids'))->where('gramjuth.status', '1')
                ->whereIn('gramjuth.taluka_id', $ids)
                ->first();
            $gramIds = explode(',', $getGramjuth->ids);

            $getGram = Gram::select(DB::raw('group_concat(id) as gramIds'))->where('status', '1')
                ->whereIn('gramjuth_id', $gramIds)
                ->first();
            $gramIds = explode(',', $getGram->gramIds);

            $getExportData = Taluka::select('taluka.name as taluka', 'gj.name as gramjuth', 'g.name as gram', 'u.name as name', 'u.mobile_no as users_mobile')
                ->join('gramjuth as gj', 'gj.taluka_id', 'taluka.id')
                ->join('gram as g', 'g.gramjuth_id', 'gj.id')
                ->join('users as u', 'u.gram_id', 'g.id')
                ->orderBy('g.name', 'asc')
                ->where(['u.role' => '3', 'u.status' => 'Active'])
                // ->whereIn('u.gram_id', $gramIds)
                ->whereIn('u.gram_id', explode(',', $this->user->gram_id))
                ->get()
                ->toArray();
        }
        return $getExportData;
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
