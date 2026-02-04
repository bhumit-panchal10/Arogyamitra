<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\{
    Medicine,
    MedicineRequest,
    MedicineStock,
    MedicineTrack,
    PdfTrack,
    User,
    MedicineDispatch,
    Beneficiary
};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    Auth,
    DB,
    Validator,
    File
};
use App\Traits\GlobalHelper;

class StockiestController extends Controller
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

    public function beneficiariesadd(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'arogyamitra_id' => 'required|numeric|gt:0',
            'gram_id' => 'required|numeric|gt:0',
            'request_date' => 'required|date',
            'number_of_beneficiary' => 'required|numeric|gt:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        Beneficiary::create([
            'arogyamitra_id' => $request->arogyamitra_id,
            'gram_id'       => $request->gram_id,
            'request_date'       => $request->request_date,
            'number_of_beneficiary'  => $request->number_of_beneficiary,
        ]);

        return response()->json([
            'status'  => '1',
            'success' => true,
            'message' => 'Beneficiary Add successfully'
        ], 200);
    }

    public function getAppUser(Request $request)
    {

        if ($this->user && $this->user->status == "Active") {
            $stock = Validator::make($request->all(), [
                'stockiest_id' => 'required|numeric|gt:0'
            ]);

            if ($stock->fails()) {
                return response()->json([
                    'status'    => '0',
                    'result'    => 'failure',
                    'medicine'  => $stock->errors()->all()
                ], 422);
            }

            $getUser = $this->getAppUserList($this->user);

            // to do current stock in medicine available
            if (!empty($getUser)) {
                if ($request->get('export') == 'csv') {
                    foreach ($getUser as $key => $val) {
                        unset($getUser[$key]['id']);
                        unset($getUser[$key]['is_medicine_request']);
                    }

                    $generatePdf = self::exportPdfForAppUser($getUser, $this->user->id);

                    if ($generatePdf) {
                        return response()->json([
                            'status'    => '1',
                            'result'    => 'success',
                            'response'  => $generatePdf
                        ], 200);
                    } else {
                        return response()->json([
                            'status'    => '0',
                            'result'    => 'failure',
                            'response'  => 'messages.pdf_not_generate'
                        ], 400);
                    }
                }
                return response()->json([
                    'status'    => '1',
                    'result'    => 'success',
                    'users'  => $getUser
                ], 200);
                // to do current stock in medicine Not available
            } else {
                return response()->json([
                    'status'    => '0',
                    'result'    => 'failure',
                    'message'   => trans('messages.user_not_found')
                ], 200);
            }
        } else {
            return response()->json([
                'messages'  => trans('messages.unauthorized_user')
            ], 401);
        }
    }

    public function rejectMedicineRequest(Request $request)
    {
        if (!$this->user || $this->user->status !== "Active") {
            return response()->json([
                'success' => false,
                'message' => trans('messages.unauthorized_user')
            ], 401);
        }

        // 🔹 Validation
        $validator = Validator::make($request->all(), [
            'medicine_request_id' => 'required|numeric|gt:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $medicineRequest = MedicineRequest::find($request->medicine_request_id);

        if (!$medicineRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Medicine request not found'
            ], 404);
        }

        if ((int) $medicineRequest->status === 3) {
            return response()->json([
                'success' => false,
                'message' => 'Delivered request cannot be rejected'
            ], 409);
        }

        if ((int) $medicineRequest->status === 4) {
            return response()->json([
                'success' => false,
                'message' => 'Request already rejected'
            ], 409);
        }

        // 🔹 Update status
        $medicineRequest->update([
            'status' => 4
        ]);

        return response()->json([
            'status'  => '1',
            'success' => true,
            'message' => 'Medicine request rejected successfully'
        ], 200);
    }

    public function getMedicineRequest(Request $request)
    {

        if (!$this->user || $this->user->status !== "Active") {
            return response()->json([
                'messages' => trans('messages.unauthorized_user')
            ], 401);
        }

        // Validation
        $validator = Validator::make($request->all(), [
            'app_user_id' => 'required|numeric|gt:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'   => '0',
                'result'   => 'failure',
                'message'  => $validator->errors()->all()
            ], 422);
        }

        // Fetch medicine requests
        // $medicineRequest = DB::table('medicine_request as mr')
        //     ->join('medicine as m', 'mr.medicine_id', '=', 'm.id')
        //     ->where('mr.iRequestTo', $request->app_user_id)
        //     ->where('mr.status', '1')
        //     ->select(
        //         'mr.id',
        //         'mr.medicine_id',
        //         'mr.qty',
        //         'mr.app_user_id',
        //         'mr.status',
        //         'm.name',
        //         'm.qty_type'
        //     )
        //     ->get();

        $medicineRequest = DB::table('medicine_request as mr')
            ->join('medicine as m', 'mr.medicine_id', '=', 'm.id')
            ->join('users as u', 'mr.arogyamitra_id', '=', 'u.id')
            ->where('mr.iRequestTo', $request->app_user_id)
            ->where('mr.status', '1')
            ->select(
                'mr.id',
                'u.name as arogyamitra_name', // ✅ correct name
                'mr.medicine_id',
                'mr.qty',
                'mr.app_user_id',
                'mr.status',
                'm.name',
                'm.qty_type'
            )
            ->selectRaw('
        (
            SELECT mt.closing_stock
            FROM medicine_track mt
            WHERE mt.medicine_id = m.id
              AND mt.arogyamitra_id = ?
            ORDER BY mt.id DESC
            LIMIT 1
        ) as CurrentStock
        ', [$request->app_user_id])
            ->orderBy('mr.app_user_id')
            ->orderBy('mr.id')
            ->get();

        $arrData = [];

        foreach ($medicineRequest as $row) {
            $arrData[] = [
                'medicine_request_id'   => $row->id,
                'medicine_id'   => $row->medicine_id,
                'qty'           => $row->qty . ' ' . $row->qty_type,
                'status'        => $row->status,
                'medicine_name' => $row->name,
                'app_user_id' => $row->app_user_id ?? '',
                'user_name' => $row->arogyamitra_name ?? '',
                'CurrentStock' => $row->CurrentStock ?? ''

            ];
        }

        if (!empty($arrData)) {
            return response()->json([
                'status'   => '1',
                'result'   => 'success',
                'medicine' => $arrData
            ], 200);
        }

        return response()->json([
            'status'  => '0',
            'result'  => 'failure',
            'message' => trans('messages.medicine_out_stock')
        ], 200);
    }

    public function receivestock(Request $request)
    {

        if (!$this->user || $this->user->status !== "Active") {
            return response()->json([
                'messages' => trans('messages.unauthorized_user')
            ], 401);
        }
        // Validation
        $validator = Validator::make($request->all(), [
            'login_user_id' => 'required|numeric|gt:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'   => '0',
                'result'   => 'failure',
                'message'  => $validator->errors()->all()
            ], 422);
        }
        if (Auth::user()->role == '6') {
            $enter_by = '1';
        } elseif (Auth::user()->role == '2') {
            $enter_by = '6';
        }

        // Fetch medicine requests
        $medicineRequest = DB::table('medicine_dispatch as md')
            ->join('medicine as m', 'md.medicine_id', '=', 'm.id')
            ->join('users as u', 'md.to_id', '=', 'u.id')
            ->selectRaw('
            md.medicine_id,
            m.name as medicine_name,
            m.qty_type,
            u.name as stockiest_name,
            SUM(md.qty) as qty
        ')
            ->where('md.to_id', $request->login_user_id)
            ->groupBy(
                'md.medicine_id',
                'm.name',
                'm.qty_type',
                'u.name'
            )
            ->get();


        $arrData = [];

        foreach ($medicineRequest as $row) {
            $arrData[] = [
                'medicine_id'     => $row->medicine_id,
                'medicine_name'   => $row->medicine_name,
                'qty'             => $row->qty . ' ' . $row->qty_type,
                'stockiest_name'  => $row->stockiest_name,
            ];
        }


        if (!empty($arrData)) {
            return response()->json([
                'status'   => '1',
                'result'   => 'success',
                'medicine' => $arrData
            ], 200);
        }

        return response()->json([
            'status'  => '0',
            'result'  => 'failure',
            'message' => trans('messages.medicine_out_stock')
        ], 200);
    }

    public function getmedicinedelivered(Request $request)
    {
        if (!$this->user || $this->user->status !== "Active") {
            return response()->json([
                'messages' => trans('messages.unauthorized_user')
            ], 401);
        }

        // 🔹 Validation
        $validator = Validator::make($request->all(), [
            'iRequestTo' => 'required|numeric|gt:0',
            'items' => 'required|array|min:1',
            'items.*.medicine_id' => 'required|numeric|gt:0',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.medicine_request_id' => 'required|numeric|gt:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => '0',
                'result' => 'failure',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::transaction(function () use ($request) {

            foreach ($request->items as $item) {

                $medicineRequest = MedicineRequest::findOrFail($item['medicine_request_id']);

                // 🔹 Sender Last Stock
                $lastTrack = MedicineTrack::where([
                    'medicine_id'    => $item['medicine_id'],
                    'arogyamitra_id' => $request->iRequestTo
                ])
                    ->orderByDesc('id')
                    ->first();

                $openingStock = $lastTrack ? $lastTrack->closing_stock : 0;
                $closingStock = $openingStock - $item['qty'];

                // 🔹 Sender Credit (C)
                MedicineTrack::create([
                    'arogyamitra_id' => $request->iRequestTo,
                    'medicine_id'    => $item['medicine_id'],
                    'opening_stock'  => $openingStock,
                    'qty'            => $item['qty'],
                    'closing_stock'  => $closingStock,
                    'mode'           => 'C',
                    'gram_id'        => $medicineRequest->gram_id,
                ]);

                // 🔹 Receiver Last Stock
                $receiverLastTrack = MedicineTrack::where([
                    'medicine_id'    => $item['medicine_id'],
                    'arogyamitra_id' => $medicineRequest->arogyamitra_id
                ])
                    ->orderByDesc('id')
                    ->first();

                $receiverOpening = $receiverLastTrack ? $receiverLastTrack->closing_stock : 0;
                $receiverClosing = $receiverOpening + $item['qty'];

                // 🔹 Dispatch Entry
                MedicineDispatch::create([
                    'from_id' => $request->iRequestTo,
                    'to_id'       => $medicineRequest->arogyamitra_id,
                    'medicine_id'  => $item['medicine_id'],
                    'qty'          => $item['qty'],
                    'Entery_By'    => Auth::user()->role,
                ]);

                // 🔹 Receiver Entry (R)
                MedicineTrack::create([
                    'arogyamitra_id' => $medicineRequest->arogyamitra_id,
                    'medicine_id'    => $item['medicine_id'],
                    'opening_stock'  => $receiverOpening,
                    'qty'            => $item['qty'],
                    'closing_stock'  => $receiverClosing,
                    'mode'           => 'R',
                    'gram_id'        => $medicineRequest->gram_id,
                ]);

                // 🔹 Update Request Status
                MedicineRequest::where('id', $item['medicine_request_id'])
                    ->update([
                        'status' => '3',
                        'delivered_quantity' => $item['qty']
                    ]);
            }
        });

        return response()->json([
            'status'    => '1',
            'result' => 'success',
            'message' => 'Medicines Delivered Successfully'
        ], 200);
    }

    // public function getmedicinedelivered(Request $request)
    // {
    //     if (!$this->user || $this->user->status !== "Active") {
    //         return response()->json([
    //             'messages' => trans('messages.unauthorized_user')
    //         ], 401);
    //     }

    //     // ðŸ”¹ Validation

    //         $stock = Validator::make($request->all(), [
    //             'iRequestTo'         => 'required|numeric|gt:0',
    //             'medicine_id'          => 'required|numeric',
    //             'qty'                  => 'required|numeric|min:1',
    //             'medicine_request_id'  => 'required|numeric'
    //         ]);


    //     if ($stock->fails()) {
    //         return response()->json([
    //             'status'   => '0',
    //             'result'   => 'failure',
    //             'medicine' => $stock->errors()->all()
    //         ], 422);
    //     }
    //     //iRequestFrom Request from
    //     //iRequestTo : person given the request
    //     DB::transaction(function () use ($request) {

    //         // ðŸ”¹ Dispatch Entry
    //         MedicineDispatch::create([
    //             'Stockiest_id' => $request->iRequestTo ?? $request->iRequestTo,
    //             'medicine_id'  => $request->medicine_id,
    //             'qty'          => $request->qty,
    //             'Entery_By'    => Auth::user()->role,
    //         ]);

    //         $medicineRequest = MedicineRequest::findOrFail($request->medicine_request_id);
    //         //$Arogyamitra = MedicineRequest::where('arogyamitra_id',medicineRequest->arogyamitra_id)->first();


    //         // ðŸ”¹ Last Stock
    //       // ðŸ”¹ Last Stock
    //         $lastTrack = MedicineTrack::where([
    //                 'medicine_id'    => $request->medicine_id,
    //                 'arogyamitra_id' => $request->iRequestTo
    //             ])
    //             ->orderByDesc('id')
    //             ->first();


    //         $openingStock = $lastTrack ? $lastTrack->closing_stock : 0;
    //         $closingStock = $openingStock - $request->qty;

    //         // ðŸ”¹ Stockist / ArogyaMitra Credit
    //         MedicineTrack::create([
    //             'arogyamitra_id' => $request->iRequestTo ?? $request->stockiest_id,
    //             'medicine_id'    => $request->medicine_id,
    //             'opening_stock'  => $openingStock,
    //             'qty'            => $request->qty,
    //             'closing_stock'  => $closingStock,
    //             'mode'           => 'C',
    //             'gram_id'        => $medicineRequest->gram_id,
    //         ]);
    //         $lastTrack = MedicineTrack::where(['medicine_id'=> $request->medicine_id,'arogyamitra_id'=>$medicineRequest->arogyamitra_id])
    //             ->orderByDesc('id')
    //             ->first();
    //             $openingStock = $lastTrack ? $lastTrack->closing_stock : 0;
    //         $closingStock = $openingStock+$request->qty;
    //         // ðŸ”¹ Receiver Entry
    //         MedicineTrack::create([
    //             'arogyamitra_id' => $medicineRequest->arogyamitra_id,
    //             'medicine_id'    => $request->medicine_id,
    //             'opening_stock'  => $openingStock,
    //             'qty'            => $request->qty,
    //             'closing_stock'  => $closingStock,
    //             'mode'           => 'R',
    //             'gram_id'        => $medicineRequest->gram_id,
    //         ]);

    //         // ðŸ”¹ Update Request Status
    //         MedicineRequest::where('id', $request->medicine_request_id)
    //             ->update([
    //                 'status' => '3',
    //                 'delivered_quantity' => $request->qty
    //             ]);
    //     });

    //     return response()->json([
    //         'result'  => 'success',
    //         'message' => 'Medicine Delivered Successfully'
    //     ], 200);
    // }

    public function usednote(Request $request)
    {

        if (!$this->user || $this->user->status !== "Active") {
            return response()->json([
                'messages' => trans('messages.unauthorized_user')
            ], 401);
        }

        // Validation
        $validator = Validator::make($request->all(), [
            'login_user_id' => 'required|numeric|gt:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'   => '0',
                'result'   => 'failure',
                'message'  => $validator->errors()->all()
            ], 422);
        }

        // Fetch medicine requests
        $medicineRequest = DB::table('medicine_request as mr')
            ->join('medicine as m', 'mr.medicine_id', '=', 'm.id')
            ->where('mr.iRequestTo', $request->login_user_id)
            ->join('users as u', 'mr.iRequestTo', '=', 'u.id')
            ->where('mr.status', '3')
            ->select(
                'mr.id',
                'mr.medicine_id',
                'mr.qty',
                'u.name As username',
                'mr.app_user_id',
                'mr.app_user_name',
                'mr.status',
                'm.name',
                'm.qty_type'
            )
            ->get();

        $arrData = [];

        foreach ($medicineRequest as $row) {
            $arrData[] = [
                'medicine_request_id'   => $row->id,
                'medicine_id'   => $row->medicine_id,
                'qty'           => $row->qty . ' ' . $row->qty_type,
                'app_user_name'        => $row->app_user_name,
                'status'        => $row->status,
                'medicine_name' => $row->name,
                'app_user_id' => $row->app_user_id ?? ''
            ];
        }

        if (!empty($arrData)) {
            return response()->json([
                'status'   => '1',
                'result'   => 'success',
                'medicine' => $arrData
            ], 200);
        }

        return response()->json([
            'status'  => '0',
            'result'  => 'failure',
            'message' => trans('messages.medicine_out_stock')
        ], 200);
    }
    public function updateStock(Request $request)
    {
        if ($this->user && $this->user->status == "Active") {
            // for time zone set in ist
            date_default_timezone_set('Asia/kolkata');
            //TODO get qty,medicine id
            $rules = [
                'type' => 'required',
                'stockiest_id' => 'required|numeric|gt:0',
                'stock' => 'array|min:1',
                'stock.*.qty' => 'numeric|gt:0',
                'stock.*.medicine_id' => 'required|numeric|gt:0'
            ];

            $messages = [
                'stock.*.medicine_id' => trans('validation.required'),
                'stock.*.medicine_id' => trans('validation.numeric'),
                'stock.*.qty' => trans('validation.numeric')
            ];
            $requestStock = Validator::make($request->all(), $rules, $messages);

            if ($requestStock->fails()) {
                return response()->json([
                    'status'    => '0',
                    'result'    => 'failure',
                    'message'   => $requestStock->errors()->all()
                ], 422);
            }

            $stock = $request->get('stock');
            $id = $request->get('stockiest_id');

            // quality check 100000 less than or equal
            foreach ($stock as $qty) {
                if ($qty['qty'] >= 100000) {
                    return response()->json([
                        'status'    => '0',
                        'result'    => 'failure',
                        'message'   => trans('messages.qty_max_length')
                    ], 422);
                }
            }

            if ($stock) {
                $type = $request->get('type');
                if ($type == 'received') {
                    $medicineReceived = self::receivedStock($stock, $id, $request->all());
                    if ($medicineReceived) {
                        return response()->json([
                            'status'    => '1',
                            'result'    => 'success',
                            'message'   => trans('messages.received_stock')
                        ], 200);
                    } else {
                        return response()->json([
                            'status'    => '0',
                            'result'    => 'failure',
                            'message'   => trans('messages.fails')
                        ], 400);
                    }
                } else if ($type == 'consume' || $type == 'medicine-request-consume') {
                    $medicineConsume = self::consumeStock($stock, $id, $type, $request->all());

                    if ($medicineConsume) {
                        return response()->json([
                            'status' => '1',
                            'result' => 'success',
                            'message' => trans('messages.medicine_stock'),
                        ], 200);
                    } else {
                        return response()->json([
                            'status' => '0',
                            'result' => 'failure',
                            'message' => trans('messages.current_stock'),
                        ], 400);
                    }
                }
            }
        } else {
            return response()->json([
                'messages'  => trans('messages.unauthorized_user')
            ], 401);
        }
    }

    public function getRequestStock(Request $request)
    {
        if ($this->user && $this->user->status == "Active") {
            $stock = Validator::make($request->all(), [
                'stockiest_id' => 'required|numeric|gt:0'
            ]);

            if ($stock->fails()) {
                return response()->json([
                    'status'    => '0',
                    'result'    => 'failure',
                    'medicine'  => $stock->errors()->all()
                ], 422);
            }

            $medicine = $appUserIds = [];
            //Stockiest
            //$getGramIds = $this->getGramStockiest($this->user);

            $getUser = $this->getAppUserList($this->user);

            if ($getUser) {
                foreach ($getUser as $val) {
                    $appUserIds[] = $val['id'];
                }
            }

            // To display current stock of medicine
            $stockiestId = $request->get('stockiest_id');
            $medicine = Medicine::select('ms.qty as current_stock', 'medicine.qty_type', 'medicine.name AS medicine_name', 'medicine.id as medicine_id', DB::raw("CONCAT(medicine.qty,' ',medicine.qty_type) AS packing"))
                //->leftJoin('medicine_stock as ms', 'ms.medicine_id', 'medicine.id')
                ->leftJoin('medicine_stock as ms', function ($join) use ($stockiestId) {
                    $join->on('medicine.id', '=', 'ms.medicine_id')
                        ->where('ms.arogyamitra_id', $stockiestId);
                })
                ->where(['medicine.status' => '1'])
                ->orderBy('medicine.id', 'ASC')
                ->groupBy('medicine.id')
                ->get();

            foreach ($medicine as $key => $value) {
                $arrData[$key]['medicine_id'] = $value['medicine_id'] ? $value['medicine_id'] : '';
                $arrData[$key]['medicine_name'] = $value['medicine_name'] ? $value['medicine_name'] : '';
                $arrData[$key]['packing'] = $value['packing'] ? $value['packing'] : '';

                //$stock = MedicineStock::
                $arrData[$key]['current_stock'] = $value['current_stock'] ? $value['current_stock'] : '0';

                $medicineRequest = MedicineRequest::select(DB::raw("SUM(qty) as qty"))
                    ->where(['medicine_id' => $value['medicine_id'], 'status' => '1'])
                    ->whereIn('app_user_id', $appUserIds)
                    ->first();

                $arrData[$key]['requested_stock'] = ($medicineRequest && $medicineRequest->qty) ? (string) $medicineRequest->qty : '';
            }
            // to do current stock in medicine available
            if (!empty($medicine)) {
                return response()->json([
                    'status'    => '1',
                    'result'    => 'success',
                    'medicine'  => $arrData
                ], 200);
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

    public function consumeStock($stock, $id, $type, $requestData)
    {
        foreach ($stock as $consume) {
            $currentStock = MedicineStock::select('medicine_id', 'qty')
                ->where(['arogyamitra_id' => $id, 'medicine_id' => $consume['medicine_id']])
                ->orderBy('medicine_id', 'ASC')
                ->first();

            if ($currentStock) {
                if ($currentStock['qty'] >= $consume['qty'] && $consume['qty']) {
                    $consumeStock = $currentStock['qty'] - $consume['qty'];

                    $medicineConsumeArr = [
                        'qty' => $consumeStock,
                        'created_at' => Carbon::now()
                    ];

                    $medicineConsume = MedicineStock::where(['arogyamitra_id' => $id, 'medicine_id' => $consume['medicine_id']])->update($medicineConsumeArr);

                    $medicineTrackArr = [
                        'qty' => $consume['qty'],
                        'arogyamitra_id' => $requestData['app_user_id'],
                        'medicine_id' => $consume['medicine_id'],
                        'mode' => 'C',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                    MedicineTrack::InsertGetId($medicineTrackArr);

                    if ($type == 'medicine-request-consume') {
                        /* $getUser = $this->getAppUserList($this->user);
                        if ($getUser) {
                            foreach ($getUser as $val) {
                                $appUserIds[] = $val['id'];
                            }
                        } */

                        MedicineRequest::where(['medicine_id' => $consume['medicine_id'], 'status' => '1'])
                            //->whereIn('app_user_id', $appUserIds)
                            ->where('app_user_id', $requestData['app_user_id'])
                            ->update(['status' => '2']);
                        //MedicineRequest::where(['app_user_id' => $id, 'medicine_id' => $consume['medicine_id']])->update(['status' => '2']);
                    }
                } else {
                    // for out of stock
                    return response()->json([
                        'status' => '0',
                        'result' => 'failure',
                        'message' => trans('messages.current_stock'),
                    ], 200);
                }
            } else {
                return response()->json([
                    'status' => '0',
                    'result' => 'failure',
                    'message' => trans('messages.current_stock'),
                ], 400);
            }
        }

        if ($medicineConsume) {
            $this->latLong($requestData);

            return true;
            /* return response()->json([
                'status' => '1',
                'result' => 'success',
                'message' => trans('messages.medicine_stock'),
            ], 200); */
        } else {
            return false;
            /* return response()->json([
                'status' => '0',
                'result' => 'failure',
                'message' => trans('messages.current_stock'),
            ], 400); */
        }
    }

    public function receivedStock($stock, $id, $requestData)
    {
        foreach ($stock as $receive) {
            $currentStock = MedicineStock::select('medicine_id', 'qty')
                ->where(['arogyamitra_id' => $id, 'medicine_id' => $receive['medicine_id']])
                ->orderBy('medicine_id', 'ASC')
                ->first();

            if ($currentStock) {
                $receivedStock = $currentStock['qty'] + $receive['qty'];

                $medicineReceivedArr = [
                    'qty' => $receivedStock,
                    'updated_at' => Carbon::now()
                ];

                $medicineReceived = MedicineStock::where(['arogyamitra_id' => $id, 'medicine_id' => $receive['medicine_id']])->update($medicineReceivedArr);

                $medicineTrackArr = [
                    'qty' => $receive['qty'],
                    'arogyamitra_id' => $id,
                    'medicine_id' => $receive['medicine_id'],
                    'mode' => 'R', // r for received
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
                // medicine tract table added data when received medicine
                MedicineTrack::InsertGetId($medicineTrackArr);
            } else {
                $medicineReceivedArr = [
                    'arogyamitra_id' => $id,
                    'medicine_id' => $receive['medicine_id'],
                    'qty' => $receive['qty'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

                $medicineReceived = MedicineStock::InsertGetId($medicineReceivedArr);

                $medicineTrackArr = [
                    'qty' => $receive['qty'],
                    'arogyamitra_id' => $id,
                    'medicine_id' => $receive['medicine_id'],
                    'mode' => 'R', // r for received
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
                // medicine tract table added data when received medicine
                MedicineTrack::InsertGetId($medicineTrackArr);
            }
        }

        if ($medicineReceived) {
            $this->latLong($requestData);

            return true;
            /* return response()->json([
                'status'    => '1',
                'result'    => 'success',
                'message'   => trans('messages.received_stock')
            ], 200); */
        } else {
            return false;
            /* return response()->json([
                'status'    => '0',
                'result'    => 'failure',
                'message'   => trans('messages.fails')
            ], 400); */
        }
    }

    public function syncData(Request $request)
    {
        $stock = $request->get('stock');
        if ($this->user) {
            date_default_timezone_set('Asia/kolkata');
            $rules = [
                'stock' => 'array|min:1',
                'stock.*.qty' => 'numeric|gt:0',
                'stock.*.medicine_id' => 'required|numeric|gt:0',
                'stock.*.type' => 'required|in:current,request,received,consume,medicine-request-consume',
                'stock.*.stockiest_id' => 'required|numeric|gt:0'
            ];
            $messages = [
                'stock.*.medicine_id' => trans('validation.required'),
                'stock.*.medicine_id' => trans('validation.numeric'),
                'stock.*.qty' => trans('validation.numeric'),
                'stock.*.type' => trans('validation.required'),
                'stock.*.stockiest_id' => trans('validation.required'),
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
            if (!$stock) {
                return response()->json([
                    'status'    => '0',
                    'result'    => 'failure',
                    'message'   => trans('messages.no_found')
                ], 200);
            }
            $response = self::syncDataByType($stock);

            if ($response) {
                $activeMedicine = Medicine::where('status', '1')->orderBy('name', 'asc')->get()->toArray();
                $inactiveMedicine = Medicine::where('status', '0')->orderBy('name', 'asc')->get()->toArray();
                $arrData['user'] = $this->user->status;
                $arrData['activeMedicine'] = $activeMedicine;
                $arrData['inactiveMedicine'] = $inactiveMedicine;
                return response()->json([
                    'status'    => '1',
                    'result'    => 'success',
                    'message'   => trans('messages.syn_data'),
                    'medicine'  => $arrData
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
        $response = '';
        foreach ($stock as $value) {
            if ($value['type'] == 'request') {
                $medicineRequestArr = [
                    'arogyamitra_id' => $value['stockiest_id'],
                    'medicine_id' => $value['medicine_id'],
                    'qty' => $value['qty'],
                    'gram_id' => NULL,
                    'status' => '1',
                    'app_user_id' => NULL,
                    'app_user_name' => NULL,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
                //medicine add in medicine request table
                $response = MedicineRequest::InsertGetId($medicineRequestArr);
            } else if ($value['type'] == 'current') {
                $isMedicineStockAvailable = MedicineStock::where(['arogyamitra_id' => $value['stockiest_id'], 'medicine_id' => $value['medicine_id']])
                    ->first();

                if ($isMedicineStockAvailable) {
                    $currentArr = [
                        'qty' => $value['qty'],
                        'updated_at' => Carbon::now()
                    ];
                    $response = $isMedicineStockAvailable->update($currentArr);
                } else {
                    $currentArr = [
                        'arogyamitra_id' => $value['stockiest_id'],
                        'medicine_id' => $value['medicine_id'],
                        'qty' => $value['qty'],
                        'gram_id' => NULL,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                    $response = MedicineStock::InsertGetId($currentArr);
                }
            } else if ($value['type'] == 'received') {
                $isMedicineStockAvailable = MedicineStock::where(['arogyamitra_id' => $value['stockiest_id'], 'medicine_id' => $value['medicine_id']])->first();
                if ($isMedicineStockAvailable) {
                    $newStock = $isMedicineStockAvailable['qty'] + $value['qty'];
                    $updateArr = [
                        'qty' => $newStock,
                        'updated_at' => Carbon::now()
                    ];
                    $response = $isMedicineStockAvailable->update($updateArr);
                } else {
                    $receivedArr = [
                        'arogyamitra_id' => $value['stockiest_id'],
                        'medicine_id' => $value['medicine_id'],
                        'qty' => $value['qty'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                    $response = MedicineStock::InsertGetId($receivedArr);
                }
                self::medicineTrack($value, "R");
            } else if ($value['type'] == 'consume' || $value['type'] == 'medicine-request-consume') {
                $isMedicineStockAvailable = MedicineStock::where(['arogyamitra_id' => $value['stockiest_id'], 'medicine_id' => $value['medicine_id']])->first();

                if ($isMedicineStockAvailable && ($isMedicineStockAvailable['qty'] >= $value['qty'] && $value['qty'])) {
                    $newStock = $isMedicineStockAvailable['qty'] - $value['qty'];

                    $currentArr = [
                        'qty' => $newStock ?: 0,
                        'updated_at' => Carbon::now(),
                    ];
                    $response = $isMedicineStockAvailable->update($currentArr);
                    self::medicineTrack($value, "C");

                    if ($value['type'] == 'medicine-request-consume') {
                        /* $getUser = $this->getAppUserList($this->user);
                        if ($getUser) {
                            foreach ($getUser as $val) {
                                $appUserIds[] = $val['id'];
                            }
                        } */

                        MedicineRequest::where(['medicine_id' => $value['medicine_id'], 'status' => '1'])
                            //->whereIn('app_user_id', $appUserIds)
                            ->where('app_user_id', $value['app_user_id'])
                            ->update(['status' => '2']);
                    }
                }
            }
        }

        return $response;
    }

    public function medicineTrack($medicineData, $type)
    {
        $ReceivedArr = [
            'arogyamitra_id' => $medicineData['stockiest_id'],
            'medicine_id' => $medicineData['medicine_id'],
            'qty' => $medicineData['qty'],
            'mode' => $type,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ];

        MedicineTrack::InsertGetId($ReceivedArr);
    }

    public function exportPdfForAppUser($appUser, $stockiestId)
    {
        date_default_timezone_set('Asia/kolkata');

        $fileAvailable = PdfTrack::select('file_name', 'created_at')
            ->where('arogyamitra_id', $stockiestId)
            ->whereDate('created_at', date('Y-m-d'))
            ->first();

        $date = date('Y-m-d');
        $downloadLink = url('/assets/uploads/app-user-export') . '/' .  $date . '_' . $stockiestId . '.csv';
        $filePath = public_path('/assets/uploads/app-user-export/' . $date . '_' . $stockiestId . ".csv");

        if (empty($fileAvailable)) {
            try {
                $path = public_path('/assets/uploads/app-user-export');
                if (!File::isDirectory($path)) {
                    File::makeDirectory($path, 0777, true, true);
                }

                $handle = fopen($filePath, 'w');
                $headers = ['Name', 'Mobile No.'];

                fputcsv($handle, $headers);
                foreach ($appUser as $val) {
                    fputcsv($handle, $val);
                }
                fclose($handle);


                $insertArr = [
                    'arogyamitra_id' => $stockiestId,
                    'file_name' => $date . '_' . $stockiestId . ".csv",
                    'created_at' => Carbon::now()
                ];

                PdfTrack::InsertGetId($insertArr);

                return $downloadLink;
            } catch (\Throwable $th) {
                return false;
            }
        } else {
            return $downloadLink;
        }
    }
}
