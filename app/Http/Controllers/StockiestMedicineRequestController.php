<?php

namespace App\Http\Controllers;

use App\Models\{
    MedicineRequest,
    User,
    Vibhag,
    Jilla,
    Taluka,
    Gramjuth,
    Gram,
    MedicineStock,
    MedicineTrack,
    Prant
};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    Auth,
    DB
};

class StockiestMedicineRequestController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->get('status');
        $title = 'Medicine Request';
        $Prant = Prant::where('status', '1')->get();
        $vibhags = Vibhag::where('status', '1')->pluck('name', 'id');
        $jilla = Jilla::where('status', '1')->pluck('name', 'id');
        $taluka = Taluka::where('status', '1')->pluck('name', 'id');
        $gramjuth = Gramjuth::where('status', '1')->pluck('name', 'id');
        $gram = Gram::where('status', '1')->pluck('name', 'id');
        $user = User::select('users.role', 'users.*');
        $dNone = '';
        if (Auth::user()->role == 4) {

            $select = ['medicine_request.id as mrId', 'm.id', 'medicine_request.status', 'medicine_request.created_at', 'medicine_request.arogyamitra_id', 'j.name as jilla_name', 'u.name as name', 'medicine_request.qty as request_qty', 'm.name as medicine_name', DB::raw('SUM(medicine_request.qty) as total_request')];

            $query = MedicineRequest::select($select)
                ->join('medicine as m', 'm.id', 'medicine_request.medicine_id')
                ->join('users as u', 'u.id', 'medicine_request.arogyamitra_id')
                ->join('jilla as j', 'j.id', 'u.jilla_id')
                ->join('vibhag as v', 'v.id', 'j.vibhag_id')
                ->where('v.id', Auth::user()->vibhag_id);
            if (!is_null($status)) {
                if ($status == 2 || $status == 0) {
                    $dNone = "d-none";
                }
                $query->where('medicine_request.status', $status);
            }
            $medicineRequest = $query->where('u.role', 6)
                ->groupBy('medicine_request.medicine_id', 'medicine_request.status', 'u.id', DB::raw('Date(medicine_request.updated_at)'))
                ->orderBy('medicine_request.updated_at', 'DESC')
                ->get()
                ->toArray();

            $totalMedicinePending = MedicineRequest::join('users as u', 'u.id', 'medicine_request.app_user_id')
                ->where(['medicine_request.status' => '1', 'u.id' => Auth::user()->vibhag_id])
                ->count();
        } elseif (Auth::user()->role == 5) {

            $select = ['medicine_request.id as mrId', 'm.id', 'medicine_request.status', 'medicine_request.created_at', 'medicine_request.arogyamitra_id', 'j.name as jilla_name', 'u.name as name', 'medicine_request.qty as request_qty', 'm.name as medicine_name', DB::raw('SUM(medicine_request.qty) as total_request'), 'v.name as vibhag_name'];
            $query = MedicineRequest::select($select)
                ->join('medicine as m', 'm.id', 'medicine_request.medicine_id')
                ->join('users as u', 'u.id', 'medicine_request.arogyamitra_id')
                ->join('jilla as j', 'j.id', 'u.jilla_id')
                ->join('vibhag as v', 'v.id', 'u.vibhag_id');
            if (!is_null($status)) {
                if ($status == 2 || $status == 0) {
                    $dNone = "d-none";
                }
                $query->where('medicine_request.status', $status);
            }

            $medicineRequest = $query->where('u.prant_id', Auth::user()->prant_id)
                ->where('u.role', 6)
                ->groupBy('medicine_request.medicine_id', 'medicine_request.status', 'u.id', DB::raw('Date(medicine_request.updated_at)'))
                ->orderBy('medicine_request.updated_at', 'DESC')
                ->get()
                ->toArray();


            $totalMedicinePending = MedicineRequest::join('users as u', 'u.id', 'medicine_request.app_user_id')
                ->where(['medicine_request.status' => '1', 'u.id' => Auth::user()->prant_id])->where('medicine_request.status', '1')
                ->count();
        } else {

            $select = ['medicine_request.id as mrId', 'm.id', 'medicine_request.status', 'medicine_request.created_at',  'medicine_request.arogyamitra_id', 'j.name as jilla_name', 'u.name as name', 'medicine_request.qty as request_qty', 'm.name as medicine_name', DB::raw('SUM(medicine_request.qty) as total_request')];
            $query = MedicineRequest::select($select)
                ->join('medicine as m', 'm.id', 'medicine_request.medicine_id')
                ->join('users as u', 'u.id', 'medicine_request.arogyamitra_id')
                ->join('jilla as j', 'j.id', 'u.jilla_id');
            if (!is_null($status)) {
                if ($status == 2 || $status == 0) {
                    $dNone = "d-none";
                }
                $query->where('medicine_request.status', $status);
            }
            $medicineRequest = $query->where('u.role', 6)
                ->groupBy('medicine_request.medicine_id', 'medicine_request.status', 'u.id', DB::raw('Date(medicine_request.updated_at)'))
                ->orderBy('medicine_request.updated_at', 'DESC')
                ->get()
                ->toArray();

            $totalMedicinePending = MedicineRequest::where('status', '1')->count();
        }
        return view('medicine_request.index', compact('Prant', 'title', 'medicineRequest', 'totalMedicinePending', 'dNone'));
    }

    public function updateRequestStatus(Request $request)
    {
        //single
        $medicine = MedicineRequest::where(['medicine_id' => $request->medicine_id, 'status' => '1', 'arogyamitra_id' => $request->arogyamitra_id])
            ->update(['status' => $request->status]);

        if ($request->status == '2') {
            $message = 'Medicine request has been accepted!';
        } else {
            $message = 'Medicine request has been rejected!';
        }

        if ($medicine) {
            toastr()->success($message);
        } else {
            toastr()->info('Action Failed!');
        }
        return redirect()->route('medicineRequest.index');
    }

    //current status changed for multiple
    public function updateStatus(Request $request)
    {
        $ids = implode(',', $request->ids); // array with group concat value
        $toArray = explode(',', $ids);
        if ($toArray) {
            $medicine = MedicineRequest::whereIn('id', $toArray)->update(['status' => $request->status]);
            $medicineIds = MedicineRequest::whereIn('id', $toArray)->groupBy('medicine_id')->get();
        }

        if ($request->status == '2') {
            $message = 'Medicine request has been accepted!';
        } elseif ($request->status == '0') {
            $message = 'Medicine request has been rejected!';
        }

        if ($medicine) {
            $userId = Auth::user()->id;
            foreach ($medicineIds as $key => $val) {
                $medicineStock = MedicineStock::where(['arogyamitra_id' => $userId, 'medicine_id' => $val['medicine_id']])->first();
                $qtyAftConsume = $medicineStock->qty - $request->qty;
                $medicineStock->update(['qty' => $qtyAftConsume]);

                $medicineTrackArr = [
                    'qty' => $request->qty,
                    'arogyamitra_id' => $userId,
                    'medicine_id' => $val['medicine_id'],
                    'mode' => 'C',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
                // medicine tract table added data when consume medicine
                MedicineTrack::InsertGetId($medicineTrackArr);
            }

            return response()->json(['status' => 1, 'success' => true, 'message' => $message]);
        } else {
            return response()->json(['status' => 0, 'success' => false, 'message' => 'Action Failed!']);
        }
    }

    public function acceptStock(Request $request)
    {
        if (!is_numeric($request->qty)) {
            return response()->json(['status' => 0, 'success' => false, 'message' => 'Please enter only numeric value!']);
        }

        $userId = Auth::user()->id;
        $medicineStock = MedicineStock::where(['arogyamitra_id' => $userId, 'medicine_id' => $request->medicine_id])->first();
        if ($medicineStock && is_numeric($request->qty)) {
            if ($medicineStock->qty <= $request->qty) {
                return response()->json(['status' => 0, 'success' => false, 'message' => 'Request quantity should be less than or equal to current quantity!']);
            }

            $medicine = MedicineRequest::where(['medicine_id' => $request->medicine_id, 'status' => '1', 'arogyamitra_id' => $request->arogyamitra_id])
                ->update(['status' => $request->status]);

            if ($medicine) {
                //$userId = Auth::user()->id;
                //$medicineStock = MedicineStock::where(['arogyamitra_id' => $userId, 'medicine_id' => $request->medicine_id])->first();
                $qtyAftConsume = $medicineStock->qty - $request->qty;
                $medicineStock->update(['qty' => $qtyAftConsume]);

                $medicineTrackArr = [
                    'qty' => $request->qty,
                    'arogyamitra_id' => $request->arogyamitra_id,
                    'medicine_id' => $request->medicine_id,
                    'mode' => 'C',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
                // medicine tract table added data when consume medicine
                MedicineTrack::InsertGetId($medicineTrackArr);

                $message = 'Medicine request has been accepted!';
                return response()->json(['status' => 1, 'success' => true, 'message' => $message]);
            } else {
                return response()->json(['status' => 0, 'success' => false, 'message' => 'Action Failed!']);
            }
        } else {
            return response()->json(['status' => 0, 'success' => false, 'message' => 'Stock is not available!']);
        }
    }
}
