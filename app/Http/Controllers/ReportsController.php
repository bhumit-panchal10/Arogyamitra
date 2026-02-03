<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    Jilla,
    MedicineStock,
    Vibhag,
    Gram,
    Taluka,
    Gramjuth,
    Prant,
    MedicineRequest,
    User
};
use Illuminate\Support\Facades\{
    DB,
    Auth
};

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function stockiestReport(Request $request)
    {
        $title = 'Stockiest Report';
        // Stockiest dropdown
        $stockiests = User::where('role', 6)
            ->where('status', 1)
            ->get();

        $hasSearch = $request->filled('stockiest_id')
            || $request->filled('from_date')
            || $request->filled('to_date');

        $dispatchData = collect();


        // Medicine master (KEEP AS IT IS ✅)
        $medicines = [
            ['medicine_id' => 1, 'name' => 'મહાસુદર્શન ટીકડી'],
            ['medicine_id' => 2, 'name' => 'સૂંઠ'],
            ['medicine_id' => 3, 'name' => 'લીંડી પિંપર'],
            ['medicine_id' => 4, 'name' => 'હરડે ટીકડી'],
            ['medicine_id' => 5, 'name' => 'બહેડા'],
            ['medicine_id' => 6, 'name' => 'આંમળા'],
            ['medicine_id' => 7, 'name' => 'ગળો'],
            ['medicine_id' => 8, 'name' => 'ગોખરું'],
            ['medicine_id' => 9, 'name' => 'વાવડીંગ'],
            ['medicine_id' => 10, 'name' => 'નાગકેસર'],
            ['medicine_id' => 11, 'name' => 'મજીઠ'],
            ['medicine_id' => 12, 'name' => 'કડાછાલ'],
            ['medicine_id' => 13, 'name' => 'કુવાડિયા બીજ ચૂર્ણ'],
            ['medicine_id' => 14, 'name' => 'ખેરછાલ'],
            ['medicine_id' => 15, 'name' => 'હિંગ્વાષ્ટક'],
            ['medicine_id' => 16, 'name' => 'કપૂરકાચલી'],
            ['medicine_id' => 17, 'name' => 'અશ્વગંધા ટીકડી'],
            ['medicine_id' => 19, 'name' => 'બિલ્વાદિ તેલ'],
            ['medicine_id' => 20, 'name' => 'ઇરિમેદાદિ તેલ'],
            ['medicine_id' => 21, 'name' => 'ષડબિંદુ તેલ'],
        ];



        if ($hasSearch) {
            $dispatchData = DB::table('medicine_dispatch as md')
                ->select(
                    'md.medicine_id',
                    'u.id as stockiest_id',
                    'u.name as stockiest_name',
                    'u.mobile_no',
                    'p.name as prant_name',
                    'v.name as vibhag_name',
                    'j.name as jilla_name',
                    DB::raw('SUM(md.qty) as total_dispatch')
                )
                ->join('users as u', 'u.id', '=', 'md.to_id')
                ->leftJoin('prant as p', 'p.id', '=', 'u.prant_id')
                ->leftJoin('vibhag as v', 'v.id', '=', 'u.vibhag_id')
                ->leftJoin('jilla as j', 'j.id', '=', 'u.jilla_id')
                ->where('u.role', 6)
                // ✅ Stockiest filter
                ->when(
                    $request->stockiest_id,
                    fn($q) =>
                    $q->where('u.id', $request->stockiest_id)
                )
                // ✅ Date filter (created_at)
                ->when(
                    $request->from_date && $request->to_date,
                    function ($q) use ($request) {
                        $q->whereBetween('md.created_at', [
                            $request->from_date . ' 00:00:00',
                            $request->to_date . ' 23:59:59',
                        ]);
                    }
                )
                ->groupBy(
                    'md.medicine_id',
                    'u.id',
                    'u.name',
                    'u.mobile_no',
                    'p.name',
                    'v.name',
                    'j.name'
                )
                ->get()
                ->keyBy('medicine_id');
        }

        return view('report.StockiestReport', compact(
            'title',
            'medicines',
            'dispatchData',
            'stockiests'
        ));
    }

    public function beneficiariesReport(Request $request)
    {
        return view('report.beneficiariesReport');
    }

    public function index(Request $request)
    {
        $title = 'Medicine Stock';
        $selPrant = $request->get('prant_id') ?? null;
        $selVibhag = $request->get('vibhag_id') ?? null;
        $selJilla = $request->get('jilla_id') ?? null;
        $selTaluka = $request->get('taluka_id') ?? null;
        $selGramjuth = $request->get('gramjuth_id') ?? null;
        $selGram = $request->get('gram_id') ?? null;
        $date = $request->start_date ? date("Y-m-d", strtotime($request->start_date)) : '';
        $prant = Prant::where(['status' => '1'])->pluck('name', 'id');
        $vibhag = Vibhag::where(['prant_id' => $selPrant, 'status' => '1'])->pluck('name', 'id');
        $jilla = Jilla::where(['vibhag_id' => $selVibhag, 'status' => '1'])->pluck('name', 'id');
        $taluka = Taluka::where(['jilla_id' => $selJilla, 'status' => '1'])->pluck('name', 'id');
        $gramjuth = Gramjuth::where(['taluka_id' => $selTaluka, 'status' => '1'])->pluck('name', 'id');
        $gram = Gram::where(['gramjuth_id' => $selGramjuth, 'status' => '1'])->pluck('name', 'id');

        $role = auth()->user()->role;
        $prant = Prant::where('status', '1')->pluck('name', 'id');

        if ($selPrant) {
            $vibhag = Vibhag::where('status', '1')->where('prant_id', $selPrant)->pluck('name', 'id');
        }
        if ($selVibhag) {
            $jilla = Jilla::where('status', '1')->where('vibhag_id', $selVibhag)->pluck('name', 'id');
        }
        if ($selTaluka) {
            $taluka = Taluka::where('taluka.status', '1')->leftJoin('jilla', 'jilla.id', '=', 'taluka.jilla_id')->where('taluka.jilla_id', $selJilla)->pluck('taluka.name', 'taluka.id');
        }
        if ($selGramjuth) {
            $gramjuth = Gramjuth::where('gramjuth.status', '1')->leftJoin('taluka', 'taluka.id', '=', 'gramjuth.taluka_id')->where('taluka_id', $selTaluka)->pluck('gramjuth.name', 'gramjuth.id');
        }
        if ($selGram) {
            $gram = Gram::where('status', '1')->where('gramjuth_id', $selGramjuth)->pluck('name', 'id');
        }
        if ($role == 1) {
            $medicinesStock = MedicineStock::select('medicine.name',  DB::raw('SUM(medicine_stock.qty) as current_stock'), 'medicine.qty', 'medicine.qty_type')
                ->join('medicine', 'medicine.id', '=', 'medicine_stock.medicine_id')
                ->join('users', 'users.id', '=', 'medicine_stock.arogyamitra_id');

            if ($selPrant) {
                $medicinesStock = $medicinesStock->where('users.prant_id', $selPrant)->whereIn('users.role', [2, 3, 4, 5, 6]);
            }
            if ($selVibhag) {
                $currentStock = $medicinesStock->where('users.vibhag_id', $selVibhag);
            }
            if ($selJilla) {
                $currentStock = $medicinesStock->where('users.jilla_id', $selJilla);
            }
            if ($selTaluka) {
                $currentStock = $medicinesStock->leftJoin('gram as gs', 'gs.id', '=', 'users.gram_id')->leftJoin('gramjuth as gm', 'gm.id', '=', 'gs.gramjuth_id')->Join('taluka', 'taluka.id', '=', 'gm.taluka_id')->where('taluka.id', $selTaluka)->whereIn('users.role', [2, 3, 4, 5]);
            }
            if ($selGramjuth) {
                $currentStock = $medicinesStock->leftJoin('gram as g', 'g.id', '=', 'users.gram_id')->where('g.gramjuth_id', $selGramjuth);
            }
            if ($selGram) {
                $currentStock = $medicinesStock->where('users.gram_id', $selGram);
            }

            if ($date) {
                $medicinesStock = $medicinesStock->whereDate('medicine_stock.created_at', $date);
            }
            //$medicinesStock = $medicinesStock->whereNotNull('medicine_stock.gram_id');

            $medicinesStock = $medicinesStock->groupBy('medicine_stock.medicine_id')->get();
        } else if ($role == 5) {
            $vibhag = Vibhag::where('status', '1')->where('prant_id', Auth::user()->prant_id)->pluck('name', 'id');

            $medicinesStock = MedicineStock::select('medicine.name',  DB::raw('SUM(medicine_stock.qty) as current_stock'), 'medicine.qty', 'medicine.qty_type')
                ->join('medicine', 'medicine.id', '=', 'medicine_stock.medicine_id')
                ->join('users', 'users.id', '=', 'medicine_stock.arogyamitra_id')
                ->where('users.prant_id', Auth::user()->prant_id);
            if ($selVibhag) {
                $medicinesStock = $medicinesStock->where('users.vibhag_id', $selVibhag);
            }
            if ($selJilla) {
                $medicinesStock = $medicinesStock->where('users.jilla_id', $selJilla);
            }
            if ($selTaluka) {
                $medicinesStock = $medicinesStock->leftJoin('gram as gs', 'gs.id', '=', 'users.gram_id')->leftJoin('gramjuth as gm', 'gm.id', '=', 'gs.gramjuth_id')->Join('taluka', 'taluka.id', '=', 'gm.taluka_id')->where('taluka.id', $selTaluka);
            }
            if ($selGramjuth) {
                $medicinesStock = $medicinesStock->leftJoin('gram as g', 'g.id', '=', 'users.gram_id')->where('g.gramjuth_id', $selGramjuth);
            }
            if ($selGram) {
                $medicinesStock = $medicinesStock->where('users.gram_id', $selGram);
            }
            if ($date) {
                $medicinesStock = $medicinesStock->whereDate('medicine_stock.created_at', $date);;
            }

            $medicinesStock = $medicinesStock->groupBy('medicine_stock.medicine_id')->whereNotNull('medicine_stock.gram_id')->get();
        } else if ($role == 4) {
            $jilla = Jilla::where('status', '1')->where('vibhag_id', Auth::user()->vibhag_id)->pluck('name', 'id');

            $medicinesStock = MedicineStock::select('medicine.name',  DB::raw('SUM(medicine_stock.qty) as current_stock'), 'medicine.qty', 'medicine.qty_type')
                ->join('medicine', 'medicine.id', '=', 'medicine_stock.medicine_id')
                ->join('users', 'users.id', '=', 'medicine_stock.arogyamitra_id')
                ->where('users.vibhag_id', Auth::user()->vibhag_id);

            if ($selJilla) {
                $medicinesStock = $medicinesStock->where('users.jilla_id', $selJilla);
            }
            if ($selTaluka) {
                $medicinesStock = $medicinesStock->leftJoin('gram as gs', 'gs.id', '=', 'users.gram_id')->leftJoin('gramjuth as gm', 'gm.id', '=', 'gs.gramjuth_id')->Join('taluka', 'taluka.id', '=', 'gm.taluka_id')->where('taluka.id', $selTaluka);
            }
            if ($selGramjuth) {
                $medicinesStock = $medicinesStock->leftJoin('gram as g', 'g.id', '=', 'users.gram_id')->where('g.gramjuth_id', $selGramjuth);
            }
            if ($selGram) {
                $medicinesStock = $medicinesStock->where('users.gram_id', $selGram);
            }

            if ($date) {
                $medicinesStock = $medicinesStock->whereDate('medicine_stock.created_at', $date);
            }

            $medicinesStock = $medicinesStock->groupBy('medicine_stock.medicine_id')->whereNotNull('medicine_stock.gram_id')->get();
        }

        return view('report.index', compact('role', 'medicinesStock', 'title',  'prant', 'vibhag', 'jilla', 'taluka', 'gramjuth', 'gram', 'selPrant', 'selVibhag', 'selJilla', 'selTaluka', 'selGramjuth', 'selGram'));
    }
}
