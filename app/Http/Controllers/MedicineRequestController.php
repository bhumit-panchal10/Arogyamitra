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
    MedicineTrack
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    Auth,
    DB
};
use App\Exports\MedicineRequestExport;
use Maatwebsite\Excel\Facades\Excel;


class MedicineRequestController extends Controller
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
        $vibhags = Vibhag::where('status', '1')->pluck('name', 'id');
        $jilla = Jilla::where('status', '1')->pluck('name', 'id');
        $taluka = Taluka::where('status', '1')->pluck('name', 'id');
        $gramjuth = Gramjuth::where('status', '1')->pluck('name', 'id');
        $gram = Gram::where('status', '1')->pluck('name', 'id');
        $user = User::select('users.role', 'users.*');
        $dNone = '';
        if (Auth::user()->role == 4) {
            $select = MedicineRequest::select('medicine_request.id as mrId', 'medicine_request.medicine_id', 'm.id', 'medicine_request.status', 'medicine_request.created_at', 'medicine_request.app_user_id', 'j.name as jilla_name', 'medicine_request.app_user_name as app_user_name', 'medicine_request.qty as request_qty', 'm.name as medicine_name', 'm.qty', 'm.qty_type', DB::raw('SUM(medicine_request.qty) as total_request'), 'v.name as vibhag_name');
            $query = $select
                ->join('medicine as m', 'm.id', 'medicine_request.medicine_id')
                ->join('users as u', 'u.id', 'medicine_request.app_user_id')
                ->join('gram as g', 'g.id', 'medicine_request.gram_id')
                ->join('gramjuth as gj', 'gj.id', 'g.gramjuth_id')
                ->join('taluka as t', 't.id', 'gj.taluka_id')
                ->join('jilla as j', 'j.id', 'u.jilla_id')
                ->join('vibhag as v', 'v.id', 'j.vibhag_id')
                ->where('v.id', Auth::user()->vibhag_id);
            if (!is_null($status)) {
                if ($status == 2 || $status == 0) {
                    $dNone = "d-none";
                }
                $query->where('medicine_request.status', $status);
            }
            $medicineRequest = $query->orderBy('medicine_request.created_at', 'DESC')->groupBy('medicine_request.medicine_id', 'app_user_id', 'status', 'medicine_request.updated_at')->get()->toArray();
            $totalMedicinePending = MedicineRequest::join('users as u', 'u.id', 'medicine_request.app_user_id')
                ->where(['medicine_request.status' => '1', 'u.id' => Auth::user()->vibhag_id])
                ->count();
        } elseif (Auth::user()->role == 5) {
            $select = ['medicine_request.id as mrId', 'medicine_request.medicine_id', 'm.id', 'medicine_request.status', 'medicine_request.created_at', 'medicine_request.app_user_id', 'j.name as jilla_name', 'medicine_request.app_user_name as app_user_name', 'medicine_request.qty as request_qty', 'm.name as medicine_name', 'm.qty', 'm.qty_type', DB::raw('SUM(medicine_request.qty) as total_request'), 'v.name as vibhag_name',];
            $query = MedicineRequest::select($select)
                ->join('medicine as m', 'm.id', 'medicine_request.medicine_id')
                ->join('users as u', 'u.id', 'medicine_request.app_user_id')
                ->join('gram as g', 'g.id', 'medicine_request.gram_id')
                ->join('gramjuth as gj', 'gj.id', 'g.gramjuth_id')
                ->join('taluka as t', 't.id', 'gj.taluka_id')
                ->join('jilla as j', 'j.id', 'u.jilla_id')
                ->join('vibhag as v', 'v.id', 'j.vibhag_id')
                ->join('prant as p', 'p.id', 'v.prant_id')
                ->where('p.id', Auth::user()->prant_id);
            if (!is_null($status)) {
                if ($status == 2 || $status == 0) {
                    $dNone = "d-none";
                }
                $query->where('medicine_request.status', $status);
            }
            $medicineRequest = $query->orderBy('medicine_request.created_at', 'DESC')->groupBy('medicine_request.medicine_id', 'app_user_id', 'status', 'medicine_request.updated_at')->get()->toArray();
            $totalMedicinePending = MedicineRequest::join('users as u', 'u.id', 'medicine_request.app_user_id')
                ->where(['medicine_request.status' => '1', 'u.id' => Auth::user()->prant_id])->where('medicine_request.status', '1')
                ->count();
        } else {
            $select = ['medicine_request.id as mrId', 'medicine_request.medicine_id', 'm.id', 'medicine_request.status', 'medicine_request.created_at',  'medicine_request.app_user_id', 'j.name as jilla_name', 'medicine_request.app_user_name as app_user_name', 'medicine_request.qty as request_qty', 'm.name as medicine_name', 'm.qty', 'm.qty_type', DB::raw('SUM(medicine_request.qty) as total_request')];
            $query = MedicineRequest::select($select)
                ->join('medicine as m', 'm.id', 'medicine_request.medicine_id')
                ->join('users as u', 'u.id', 'medicine_request.app_user_id')
                ->join('jilla as j', 'j.id', 'u.jilla_id');
            if (!is_null($status)) {
                if ($status == 2 || $status == 0) {
                    $dNone = "d-none";
                }
                $query->where('medicine_request.status', $status)->orderBy('medicine_request.created_at', 'DESC');
            }
            $medicineRequest = $query->groupBy('medicine_request.medicine_id', 'medicine_request.app_user_id', 'medicine_request.status', 'medicine_request.updated_at')->orderBy('medicine_request.created_at', 'DESC')->get()->toArray();
            $totalMedicinePending = MedicineRequest::where('status', '1')->count();
        }
        return view('medicine_request.index', compact('title', 'medicineRequest', 'totalMedicinePending', 'dNone'));
    }

    public function updateRequestStatus(Request $request)
    {
        $medicineIds = MedicineRequest::select(DB::raw('GROUP_CONCAT(id) as ids'))
            ->where(['medicine_id' => $request->medicine_id, 'status' => '1', 'app_user_id' => $request->request->get('app_user_id')])
            ->first()->ids;
        if ($request->status == '2') {
            $message = 'Medicine request has been accepted!';
        } else {
            $message = 'Medicine request has been rejected!';
        }
        $medicine = MedicineRequest::whereIn('id', explode(',', $medicineIds))->update(['status' => $request->status]);

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
        $ids = implode(',', $request->ids);
        $toArray = explode(',', $ids);
        $medicineIds = MedicineRequest::select(DB::raw('GROUP_CONCAT(id) as ids'))->whereIn('id', $toArray)->first();

        if ($request->status == '2') {
            $message = 'Medicine request has been accepted!';
        } elseif ($request->status == '0') {
            $message = 'Medicine request has been rejected!';
        }
        $medicine = MedicineRequest::whereIn('id', explode(',', $medicineIds->ids))->update(['status' => $request->status]);

        if ($medicine) {
            return response()->json(['status' => 1, 'success' => true, 'message' => $message]);
        } else {
            return response()->json(['status' => 0, 'success' => false, 'message' => 'Action Failed!']);
        }
    }

    public function bulkAccept(Request $request)
    {
        foreach ($request->requests as $req) {

            MedicineRequest::where([
                'medicine_id' => $req['medicine_id'],
                'arogyamitra_id' => $req['arogyamitra_id'],
                'status' => 2
            ])->update(['status' => 3]);
        }

        return response()->json([
            'message' => 'Selected requests accepted successfully'
        ]);
    }

    public function export($status)
    {
        return Excel::download(
            new MedicineRequestExport($status),
            'medicine_requests.xlsx'
        );
    }

    public function deliver(Request $request)
    {


        $request->validate([
            'medicine_id' => 'required',
            'delivered_quantity'  => 'required|numeric|min:1',
        ]);


        DB::transaction(function () use ($request) {

            // 1️⃣ Get Medicine Request
            $medicineRequest = MedicineRequest::findOrFail($request->medicine_id);

            $medicineId  = $medicineRequest->medicine_id;
            $qtyDelivered = $request->delivered_quantity;

            $arogyamitra_id = $request->arogyamitra_id;
            // 2️⃣ Get last stock entry for this medicine
            $lastTrack = MedicineTrack::where('medicine_id', $medicineId)
                ->orderBy('id', 'desc')
                ->first();

            $openingStock = $lastTrack ? $lastTrack->closing_stock : 0;
            $closingStock = $openingStock - $qtyDelivered;

            // if ($closingStock < 0) {
            //     throw new \Exception('Insufficient stock');
            // }


            // 3️⃣ Insert medicine_track entry (REDUCE)
            MedicineTrack::create([
                'arogyamitra_id' => $arogyamitra_id ?? '',
                'medicine_id'    => $medicineId,
                'opening_stock'  => $openingStock,
                'qty'            => $qtyDelivered,
                'closing_stock'  => $closingStock,
                'mode'           => 'R', // Reduce
                'gram_id'        => $medicineRequest->gram_id,
            ]);


            // Update medicine_request
            DB::table('medicine_request')
                ->where('id', $request->medicine_id)
                ->update([
                    'delivered_quantity' => $qtyDelivered,
                    'status'             => '3', // must be string for ENUM
                ]);
        });

        toastr()->success('Medicine delivered & stock updated successfully');
        return back();
    }
}
