<?php

namespace App\Http\Controllers;

use App\Models\MedicineRequest;
use App\Models\Prant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $title = 'Dashboard';
        if (Auth::user()->role == '1') {
            $prantId = Prant::where('status', '1')->pluck('id');
            $selectFields = ['medicine_request.status', 'medicine_request.app_user_name as arogyamitra_name', 'medicine_request.qty as request_qty', 'm.name as medicine_name', DB::raw('SUM(medicine_request.qty) as qty'), 'm.qty_type', 'g.name as gram_name', 'gj.name as gramjuth_name', 't.name as taluka_name', 'j.name as jilla_name', 'v.name as vibhag_name', DB::raw('SUM(medicine_request.qty) as qty')];
            $query = MedicineRequest::select($selectFields)
                ->join('medicine as m', 'm.id', 'medicine_request.medicine_id')
                ->join('users as u', 'u.id', 'medicine_request.app_user_id')
                ->join('gram as g', 'g.id', 'medicine_request.gram_id')
                ->join('gramjuth as gj', 'gj.id', 'g.gramjuth_id')
                ->join('taluka as t', 't.id', 'gj.taluka_id')
                ->join('jilla as j', 'j.id', 'u.jilla_id')
                ->join('vibhag as v', 'v.id', 'j.vibhag_id')
                ->join('prant as p', 'p.id', 'v.prant_id')
                ->whereIn('p.id', $prantId)
                ->take(5)
                ->orderBy('medicine_request.created_at', 'DESC')
                ->groupBy('medicine_request.medicine_id');
            $medicineRequest = $query->get();
        } elseif (Auth::user()->role == '5') {
            $selectFields = ['medicine_request.status', 'medicine_request.app_user_name as arogyamitra_name', 'medicine_request.qty as request_qty', 'm.name as medicine_name', DB::raw('SUM(medicine_request.qty) as qty'), 'm.qty_type', 'g.name as gram_name', 'gj.name as gramjuth_name', 't.name as taluka_name', 'j.name as jilla_name', 'v.name as vibhag_name', DB::raw('SUM(medicine_request.qty) as qty')];
            $query = MedicineRequest::select($selectFields)
                ->join('medicine as m', 'm.id', 'medicine_request.medicine_id')
                ->join('users as u', 'u.id', 'medicine_request.app_user_id')
                ->join('gram as g', 'g.id', 'medicine_request.gram_id')
                ->join('gramjuth as gj', 'gj.id', 'g.gramjuth_id')
                ->join('taluka as t', 't.id', 'gj.taluka_id')
                ->join('jilla as j', 'j.id', 'u.jilla_id')
                ->join('vibhag as v', 'v.id', 'j.vibhag_id')
                ->join('prant as p', 'p.id', 'v.prant_id')
                ->where('p.id', Auth::user()->prant_id)
                ->take(5)
                ->orderBy('medicine_request.created_at', 'DESC')
                ->groupBy('medicine_request.medicine_id');
            $medicineRequest = $query->get();
        } elseif (Auth::user()->role == '4') {
            $selectFields = ['medicine_request.status', 'medicine_request.app_user_name as arogyamitra_name', 'medicine_request.qty as request_qty', 'm.name as medicine_name', DB::raw('SUM(medicine_request.qty) as qty'), 'm.qty_type', 'g.name as gram_name', 'gj.name as gramjuth_name', 't.name as taluka_name', 'j.name as jilla_name', 'v.name as vibhag_name', DB::raw('SUM(medicine_request.qty) as qty')];
            $query = MedicineRequest::select($selectFields)
                ->join('medicine as m', 'm.id', 'medicine_request.medicine_id')
                ->join('users as u', 'u.id', 'medicine_request.app_user_id')
                ->join('gram as g', 'g.id', 'medicine_request.gram_id')
                ->join('gramjuth as gj', 'gj.id', 'g.gramjuth_id')
                ->join('taluka as t', 't.id', 'gj.taluka_id')
                ->join('jilla as j', 'j.id', 'u.jilla_id')
                ->join('vibhag as v', 'v.id', 'j.vibhag_id')
                ->where('v.id', Auth::user()->vibhag_id)
                ->take(5)
                ->orderBy('medicine_request.created_at', 'DESC')
                ->groupBy('medicine_request.medicine_id');
            $medicineRequest = $query->get();
        } else {
            $selectFields = ['medicine_request.*', 'medicine_request.app_user_name as arogyamitra_name', 'medicine_request.qty as request_qty', 'm.name as medicine_name', 'm.qty', 'm.qty_type', 'g.name as gram_name', 'gj.name as gramjuth_name', 't.name as taluka_name', 'j.name as jilla_name', 'v.name as vibhag_name'];
            $query = MedicineRequest::select($selectFields)
                ->join('medicine as m', 'm.id', 'medicine_request.medicine_id')
                ->join('users as u', 'u.id', 'medicine_request.arogyamitra_id')
                ->join('gram as g', 'g.id', 'u.gram_id')
                ->join('gramjuth as gj', 'gj.id', 'g.gramjuth_id')
                ->join('taluka as t', 't.id', 'gj.taluka_id')
                ->join('jilla as j', 'j.id', 't.jilla_id')
                ->join('vibhag as v', 'v.id', 'j.vibhag_id')
                ->orderBy('medicine_request.created_at', 'DESC')
                ->where('u.vibhag_id', Auth::user()->vibhag_id);
            $medicineRequest = $query->get()->toArray();
        }
        return view('home', compact('title', 'medicineRequest'));
    }

    public function privacyPolicy()
    {
        $title = 'Privacy Policy';
        return view('privacyPolicy',  compact('title'));
    }
}
