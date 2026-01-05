<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    Medicine,
    MedicineRequest,
    Vibhag,
    Jilla,
    Prant
};
use Illuminate\Support\Facades\{
    DB,
    Auth
};

class MedicineOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $title = 'Medicine Order';
        $selPrant = $selVibhag = $selJilla = '';
        $prant = $vibhag = $jilla = [];
        $start_date = $end_date = '';
        $start_date = date('d-m-Y', strtotime('-30 day'));
        $end_date = date('d-m-Y', strtotime('now'));

        if ($request->isMethod('post')) {
            $dateRange = explode('to', $request->get('date_range'));
            $selPrant = $request->get('prant_id') ?: '';
            $selVibhag = $request->get('vibhag_id') ?: '';
            $selJilla = $request->get('jilla_id') ?: '';

            if (count($dateRange) == 1) {
                $start_date = $end_date = date('d-m-Y', strtotime(rtrim($dateRange[0])));
            } else {
                if (isset($dateRange[0]) && isset($dateRange[1])) {
                    $start_date = date('d-m-Y', strtotime(rtrim($dateRange[0])));
                    $end_date = date('d-m-Y', strtotime(ltrim($dateRange[1])));
                }
            }
        }

        $filterType = $request->filterType;
        $role = auth()->user()->role;
        $medicines = Medicine::where('status', '1')->orderBy('id', 'desc')->get();
        $prant = Prant::where('status', '1')->pluck('name', 'id');

        if ($role == 1) { //Backend user
            if ($selPrant) {
                $vibhag = Vibhag::where('status', '1')->where('prant_id', $selPrant)->pluck('name', 'id');
            }
            if ($selVibhag) {
                $jilla = Jilla::where('status', '1')->where('vibhag_id', $selVibhag)->pluck('name', 'id');
            }
            $medicinesRequest = MedicineRequest::select('medicine.name',  DB::raw('SUM(medicine_request.qty) as request_medicine'), 'medicine.qty', 'medicine.qty_type')
                ->join('medicine', 'medicine.id', '=', 'medicine_request.medicine_id')
                ->join('users', 'users.id', '=', 'medicine_request.app_user_id');

            if ($selPrant) {
                $medicinesReq = $medicinesRequest->where('users.prant_id', $selPrant);
            }
            if ($selVibhag) {
                $medicinesReq = $medicinesRequest->where('users.vibhag_id', $selVibhag);
            }
            if ($selJilla) {
                $medicinesReq = $medicinesRequest->where('users.jilla_id', $selJilla);
            }

            if ($start_date && $end_date) {
                $medicinesReq = $medicinesRequest->whereDate('medicine_request.created_at', '>=', date('Y-m-d', strtotime($start_date)))->whereDate('medicine_request.created_at', '<=', date('Y-m-d', strtotime($end_date)));
            }
            $medicinesReq = $medicinesRequest->groupBy('medicine_request.medicine_id')->orderBy('medicine_request.created_at', 'DESC')->get();
        } else if ($role == 5) { // prant user
            $vibhag = Vibhag::where('status', '1')->where('prant_id', Auth::user()->prant_id)->pluck('name', 'id');
            $medicinesRequest = MedicineRequest::select('medicine.name',  DB::raw('SUM(medicine_request.qty) as request_medicine'), 'medicine.qty', 'medicine.qty_type')
                ->join('medicine', 'medicine.id', '=', 'medicine_request.medicine_id')
                ->join('users', 'users.id', '=', 'medicine_request.arogyamitra_id')
                ->where('users.prant_id', Auth::user()->prant_id);

            if ($selVibhag) {
                $medicinesReq = $medicinesRequest->where('users.vibhag_id', $selVibhag);
            }
            if ($selJilla) {
                $medicinesReq = $medicinesRequest->where('users.jilla_id', $selJilla);
            }
            if ($start_date && $end_date) {
                $medicinesReq = $medicinesRequest->whereDate('medicine_request.created_at', '>=', date('Y-m-d', strtotime($start_date)))->whereDate('medicine_request.created_at', '<=', date('Y-m-d', strtotime($end_date)));
            }
            $medicinesReq = $medicinesRequest->groupBy('medicine_request.medicine_id')->get();
        } else if ($role == 4) { //vibhag user
            $jilla = Jilla::where('status', '1')->where('vibhag_id', Auth::user()->vibhag_id)->pluck('name', 'id');
            $medicinesRequest = MedicineRequest::select('medicine.name',  DB::raw('SUM(medicine_request.qty) as request_medicine'), 'medicine.qty', 'medicine.qty_type')
                ->join('medicine', 'medicine.id', '=', 'medicine_request.medicine_id')
                ->join('users', 'users.id', '=', 'medicine_request.arogyamitra_id')
                ->where('users.vibhag_id', Auth::user()->vibhag_id);

            if ($selJilla) {
                $medicinesReq = $medicinesRequest->where('users.jilla_id', $selJilla);
            }
            if ($start_date && $end_date) {
                $medicinesReq = $medicinesRequest->whereDate('medicine_request.created_at', '>=', date('Y-m-d', strtotime($start_date)))->whereDate('medicine_request.created_at', '<=', date('Y-m-d', strtotime($end_date)));
            }
            $medicinesReq = $medicinesRequest->groupBy('medicine_request.medicine_id')->get();
        }

        return view('medicine_order.index', compact('medicines', 'title', 'prant', 'vibhag', 'jilla', 'selPrant', 'selVibhag', 'selJilla', 'medicinesReq', 'start_date', 'end_date'));
    }
}
