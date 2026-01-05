<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\{
    Medicine,
    MedicineStock,
    MedicineTrack
};


class MedicineStockController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $title = 'Medicine Stock';
        $status = $request->get('status');

        $medicines = Medicine::where('status', '1')->orderBy('id', 'ASC')->get();
        foreach ($medicines as $key => $val) {
            if ($val->qty_type=='નંગ') {
                $qtyType = 'Pcs(નંગ)';
            }  elseif($val->qty_type=='ગ્રામ') {
                $qtyType = 'Grm(ગ્રામ)';
            } else {
                $qtyType = 'Ml(મી.લી.)';
            }
            $medicines[$key]['qty_type'] = $qtyType;
            $medicines[$key]['qty'] = 0;

            $stock = MedicineStock::join('users', 'users.id', 'medicine_stock.arogyamitra_id')->where(['users.id' => Auth::user()->id, 'medicine_stock.medicine_id' => $val['id']])->sum('medicine_stock.qty');

            if ($stock) {
                $medicines[$key]['qty'] = $stock;
            }
        }

        return view('medicine_stock.index', compact('medicines', 'title'));
    }

    public function store(Request $request)
    {
        $medicines = $request->get('medicine');
        $count = 0;
        foreach ($medicines as $mId => $mStock) {
            if (!is_null($mStock)) {
                $isExist = MedicineStock::where(['medicine_id' => $mId, 'arogyamitra_id' => Auth::user()->id])->first();
                if ($isExist) {
                    $qty = $isExist->qty + $mStock;
                    MedicineStock::where(['id' => $isExist->id])->update(['qty' => $qty]);
                } else {
                    $medicineStock = new MedicineStock();
                    $medicineStock->arogyamitra_id = Auth::user()->id;
                    $medicineStock->medicine_id = $mId;
                    $medicineStock->qty = $mStock;
                    $medicineStock->save();
                }
                //add records in medicinetrack table
                $medicineTrack = new MedicineTrack();
                $medicineTrack->arogyamitra_id = Auth::user()->id;
                $medicineTrack->medicine_id = $mId;
                $medicineTrack->mode = 'R';
                $medicineTrack->qty = $mStock;
                $medicineTrack->save();
            } else {
                $count++;
            }
        }

        if ($count == count($medicines)) {
            return redirect()->back()->with('error', 'Please enter at least one quantity.');
        }

        return redirect()->back()->with('success', 'Stock update successfully.');
    }
}
