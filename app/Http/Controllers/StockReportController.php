<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    Gram,
    Medicine,
    MedicineStock,
    MedicineTrack,
    User
};

class StockReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $title = 'Backend Report';
        $status = $request->get('status');
        $stockiestId = User::where('role', 1)->pluck('id', 'name')->toArray();
        $role = auth()->user()->role;

        if ($role == 1) {
            $medicines = Medicine::select('medicine.id', 'medicine.name', 'medicine.qty_type', 'medicine_stock.qty as stock_qty')->leftJoin('medicine_stock', 'medicine_stock.medicine_id', '=', 'medicine.id')->where('medicine_stock.arogyamitra_id', '=', $stockiestId)->orderBy('medicine.id', 'asc')->get();

            foreach ($medicines as $key => $val) {
                if ($val->qty_type == 'નંગ') {
                    $qtyType = 'Pcs(નંગ)';
                } elseif ($val->qty_type == 'ગ્રામ') {
                    $qtyType = 'Grm(ગ્રામ)';
                } else {
                    $qtyType = 'Ml(મી.લી.)';
                }
                $medicines[$key]['qty_type'] = $qtyType;
                $medicines[$key]['qty'] = 0;


                // Set quantity value if $stock is defined
                if ($val->stock_qty) {
                    $medicines[$key]['qty'] = $val->stock_qty;
                }
            }
        }

        return view('stock_report.index', compact('medicines', 'title'));
    }

    public function show(Request $request, $id)
    {
        $title = 'Backend Report';
        $status = $request->get('status');
        $ids = Medicine::find($id);
        $role = auth()->user()->role;
        $start_date = $end_date = '';
        $start_date = date('d-m-Y', strtotime('-30 day'));
        $end_date = date('d-m-Y');

        $date = $request->get('date_range');
        if ($request->isMethod('get') && $date) {
            $dateRange = explode('to', $request->get('date_range'));

            if (count($dateRange) == 1) {
                $start_date = $end_date = date('d-m-Y', strtotime(rtrim($dateRange[0])));
            } else {
                if (isset($dateRange[0]) && isset($dateRange[1])) {
                    $start_date = date('d-m-Y', strtotime(rtrim($dateRange[0])));
                    $end_date = date('d-m-Y', strtotime(ltrim($dateRange[1])));
                }
            }
        }

        $userIds = User::select('id')->where('role', 6)->pluck('id');

        $medicines = MedicineTrack::select('medicine.id', 'medicine.name', 'medicine.qty_type', 'medicine_track.qty as medicine_qty', 'medicine_track.mode', 'users.name as uname', 'medicine_track.created_at as track_date')
            ->leftJoin('medicine', 'medicine_track.medicine_id', '=', 'medicine.id')
            ->leftJoin('users', 'users.id', '=', 'medicine_track.arogyamitra_id')
            ->where(function ($query) use ($userIds) {
                $query->whereIn('medicine_track.arogyamitra_id', $userIds)
                    ->where('medicine_track.mode', 'C')
                    ->orWhere(function ($query) {
                        $query->where('medicine_track.arogyamitra_id', 1)
                            ->where('medicine_track.mode', 'R');
                    });
            })->where('medicine_track.medicine_id', $id);

        if ($start_date && $end_date) {
            $medicines->whereDate('medicine_track.created_at', '>=', date('Y-m-d', strtotime($start_date)))
                ->whereDate('medicine_track.created_at', '<=', date('Y-m-d', strtotime($end_date)));
        }

        $medicines = $medicines->orderBy('medicine_track.created_at', 'desc')->get();

        foreach ($medicines as $key => $val) {
            if ($val->qty_type == 'નંગ') {
                $qtyType = 'Pcs(નંગ)';
            } elseif ($val->qty_type == 'ગ્રામ') {
                $qtyType = 'Grm(ગ્રામ)';
            } else {
                $qtyType = 'Ml(મી.લી.)';
            }
            $medicines[$key]['qty_type'] = $qtyType;
            $medicines[$key]['qty'] = $val->medicine_qty;
        }

        return view('stock_report.show', ['id' => $id], compact('medicines', 'title', 'start_date', 'end_date'));
    }

    public function stockiestStock(Request $request)
    {
        $title = 'Stockiest Report';
        $role = auth()->user()->role;
        $medicines = [];
        $stockiestIds = User::where('role', 6)->pluck('id')->toArray();
        $selStockiest = $request->get('stockiest_id') ?? null;
        $stockiest = User::whereIn('id', $stockiestIds)->pluck('name', 'id');

        if ($role == 1 && $selStockiest && $request->isMethod('post')) {
            $medicines =  Medicine::select('medicine.id', 'medicine.qty_type', 'medicine.name', 'medicine_stock.qty AS stock_qty')
                ->leftJoin('medicine_stock', 'medicine.id', '=', 'medicine_stock.medicine_id')
                ->where('medicine_stock.arogyamitra_id', $selStockiest)
                ->orderBy('medicine.id', 'asc')->get();

            foreach ($medicines as $key => $val) {
                if ($val->qty_type == 'નંગ') {
                    $qtyType = 'Pcs(નંગ)';
                } elseif ($val->qty_type == 'ગ્રામ') {
                    $qtyType = 'Grm(ગ્રામ)';
                } else {
                    $qtyType = 'Ml(મી.લી.)';
                }
                $medicines[$key]['qty_type'] = $qtyType;
                $medicines[$key]['qty'] = 0;

                if ($val->stock_qty) {
                    $medicines[$key]['qty'] = $val->stock_qty;
                }
            }
        }

        return view('stock_report.stockiest', compact('medicines', 'title', 'role', 'selStockiest', 'stockiest'));
    }

    public function stockiestShow(Request $request, $id, $stockiest)
    {
        $title = 'Stockiest Report';
        $selStockiest = $request->get('stockiest_id') ?? null;
        $medicinesIds = $request->get('id', 'name') ?? [];
        $ids = Medicine::find($id);
        $stockiestUser = User::find($stockiest);
        $medicineName = ($ids) ? $ids->name : '';
        $stockiestName = ($stockiestUser) ? $stockiestUser->name : '';
        $role = auth()->user()->role;
        $start_date = date('d-m-Y', strtotime('-30 day'));
        $end_date = date('d-m-Y');

        $date = $request->get('date_range');
        if ($request->isMethod('post') && $date) {
            $dateRange = explode('to', $request->get('date_range'));

            if (count($dateRange) == 1) {
                $start_date = $end_date = date('d-m-Y', strtotime(rtrim($dateRange[0])));
            } else {
                if (isset($dateRange[0]) && isset($dateRange[1])) {
                    $start_date = date('d-m-Y', strtotime(rtrim($dateRange[0])));
                    $end_date = date('d-m-Y', strtotime(ltrim($dateRange[1])));
                }
            }
        }

        $appUserIds = $this->getAppUsersByStockiestId($stockiest);

        if ($role == 1) {
            $medicines = MedicineTrack::select(
                    'm.name',
                    'users.name  as uname',
                    'm.qty_type',
                    'medicine_track.qty',
                    'medicine_track.mode',
                    'medicine_track.created_at'
                )
                ->leftJoin('users', 'users.id', '=', 'medicine_track.arogyamitra_id')
                ->leftJoin('medicine as m', 'm.id', '=', 'medicine_track.medicine_id')
                ->where('medicine_track.medicine_id', $id)
                ->where(function ($query) use ($stockiest, $appUserIds) {
                    $query->where(function ($query1) use ($stockiest) {
                        $query1->where('medicine_track.mode', '=', 'R')
                            ->where('users.id', '=', $stockiest);
                    });
                    $query->orWhere(function ($query2) use ($appUserIds) {
                        $query2->where('medicine_track.mode', '=', 'C')
                            ->whereIn('users.id', $appUserIds);
                    });
                });

            if ($start_date && $end_date !== '') {
                $medicines->whereDate('medicine_track.created_at', '>=', date('Y-m-d', strtotime($start_date)))
                    ->whereDate('medicine_track.created_at', '<=', date('Y-m-d', strtotime($end_date)));
            }

            $medicines = $medicines->orderBy('medicine_track.created_at', 'desc')->get();

            foreach ($medicines as $key => $val) {
                if ($val->qty_type == 'નંગ') {
                    $qtyType = 'Pcs(નંગ)';
                } elseif ($val->qty_type == 'ગ્રામ') {
                    $qtyType = 'Grm(ગ્રામ)';
                } else {
                    $qtyType = 'Ml(મી.લી.)';
                }
                $medicines[$key]['qty_type'] = $qtyType;
                $medicines[$key]['qty'] = $val->qty;
            }
        }

        //$filters = Session::get('stock_report_filters');
        //$selUser = $filters['stockiest_id'] ?? null;

        return view('stock_report.stockiest_show', compact('medicines', 'title', 'start_date', 'end_date', 'medicineName', 'stockiestName', 'id', 'stockiest'));
    }

    public function appUsersReport(Request $request)
    {
        $title = 'App User Report';
        $gramName = $medicines = [];
        //get user id and gram id from request
        $selUser = $request->get('app_user_id') ?? null;
        $selectedGram = $request->get('id') ?? null;

        if ($request->isMethod('post')) {
            if ($selUser && $selectedGram) {
                /* $medicines = MedicineTrack::select('medicine.id as medicines_id', 'medicine.name', 'medicine.qty_type', 'ms.qty', 'medicine_track.mode', 'medicine_track.qty as track_qty', 'medicine_track.created_at')
                ->leftJoin('medicine', 'medicine.id', '=', 'medicine_track.medicine_id')
                ->leftJoin('medicine_stock as ms', function($join){
                    $join->on('ms.medicine_id', '=', 'medicine_track.medicine_id');
                    $join->on('ms.arogyamitra_id', '=', 'medicine_track.arogyamitra_id');
                })
                //->leftJoin('medicine_stock', 'medicine_track.medicine_id', '=', 'medicine_stock.medicine_id')
                ->where('medicine_track.gram_id', $selectedGram)
                ->orderBy('medicine_track.created_at', 'desc')
                ->groupBy('medicine.id')
                ->get(); */

                $medicines = MedicineStock::select('medicine.id as medicines_id', 'medicine.name', 'medicine.qty_type', 'medicine_stock.qty')
                    ->leftJoin('medicine', 'medicine.id', '=', 'medicine_stock.medicine_id')
                    ->where('medicine_stock.gram_id', $selectedGram)
                    ->orderBy('medicine_stock.created_at', 'desc')
                    ->get();

                /* $medicines = MedicineStock::select('medicine.id as medicines_id', 'medicine.name', 'medicine_stock.qty', 'medicine.qty_type', 'medicine_stock.gram_id', 'medicine_stock.arogyamitra_id')
                    ->leftJoin('medicine', 'medicine.id', '=', 'medicine_stock.medicine_id')
                    ->where('medicine_stock.arogyamitra_id', $selUser)
                    ->where('medicine_stock.gram_id', $selectedGram)->get(); */
                // check whether data found or not in stock table
                if ($medicines) {
                    // loop through each medicine name, quantity and quantity type
                    foreach ($medicines as $key => $val) {
                        if ($val->qty_type == 'નંગ') {
                            $qtyType = 'Pcs(નંગ)';
                        } elseif ($val->qty_type == 'ગ્રામ') {
                            $qtyType = 'Grm(ગ્રામ)';
                        } else {
                            $qtyType = 'Ml(મી.લી.)';
                        }
                        $medicines[$key]['qty_type'] = $qtyType;
                        $medicines[$key]['qty'] = $val->qty ? : 0;
                        $medicines[$key]['date'] = date('d-m-Y', strtotime($val->created_at));
                    }
                }

                $grams = User::select('gram_id')->where('id', $selUser)->first();
                if ($grams) {
                    $gramIds = explode(',', $grams['gram_id']);
                    foreach ($gramIds as $gramId) {
                        $gram = Gram::find($gramId);
                        if ($gram && $gram->status) {
                            $gramName[$gramId] = $gram->name;
                        }
                    }
                }
            }
        }

        $user = User::where('role', 2)->pluck('name', 'id');

        return view('stock_report.app_users_report', compact('title', 'selUser', 'user', 'selectedGram', 'medicines', 'gramName'));
    }

    public function appUsersMedicineTrack(Request $request, $mId, $gramId)
    {
        $title = 'App User Report';
        $medicine = Medicine::find($mId);
        $gram = Gram::find($gramId);
        $medicineName = ($medicine) ? $medicine->name : '';
        $gramName = $gram->name;

        $role = auth()->user()->role;

        $start_date = date('d-m-Y', strtotime('-30 day'));
        $end_date = date('d-m-Y');

        $date = $request->get('date_range');
        if ($request->isMethod('post') && $date) {
            $dateRange = explode('to', $request->get('date_range'));

            if (count($dateRange) == 1) {
                $start_date = $end_date = date('d-m-Y', strtotime(rtrim($dateRange[0])));
            } else {
                if (isset($dateRange[0]) && isset($dateRange[1])) {
                    $start_date = date('d-m-Y', strtotime(rtrim($dateRange[0])));
                    $end_date = date('d-m-Y', strtotime(ltrim($dateRange[1])));
                }
            }
        }

        $medicineTrack = MedicineTrack::select('medicine_track.mode', 'medicine_track.qty', 'medicine_track.created_at', 'medicine.qty_type')
            ->leftJoin('medicine', 'medicine.id', '=', 'medicine_track.medicine_id')
            ->where('medicine_track.gram_id', $gramId)
            ->where('medicine.id', $mId);
            if ($start_date && $end_date) {
                $medicineTrack->whereDate('medicine_track.created_at', '>=', date('Y-m-d', strtotime($start_date)))
                    ->whereDate('medicine_track.created_at', '<=', date('Y-m-d', strtotime($end_date)));
            }

        $medicineTrack = $medicineTrack->orderBy('medicine_track.created_at', 'desc')->get();

        return view('stock_report.appUser_show', compact('medicineTrack', 'title', 'start_date', 'end_date', 'medicineName', 'gramName', 'mId', 'gramId'));
    }

    public function getArogyamitraUsers($appUser)
    {
        $query = User::select('gram_id')->where('id', $appUser)->first();
        $appUsers = array();
        if ($query) {
            //separate gram id
            $gramIds = explode(',', $query['gram_id']);
            //loop through each gram_id
            foreach ($gramIds as $gram) {
                //find arogyamitra whose gram id is equal to app user gram id
                $app_users = User::select('id', 'name')->whereRaw("FIND_IN_SET(?, gram_id)", [$gram])->where('users.role', 3)->get();
                //loop through each record if found data
                if ($app_users) {
                    foreach ($app_users as $user) {
                        // store id of app users into array where key and values are same
                        $appUsers[$user->id] = $user->id;
                    } // end loop

                } //endif
            } //end loop
        } //endif
        //return array of app users
        return $appUsers;
    }

    public function getUserGramList(Request $request)
    {
        $selUser = $request->get('userId') ?? null;
        $gramName = [];
        $grams = User::select('gram_id')->where('id', $selUser)->first();
        //check whether gram ids exist or not
        if ($grams) {
            //split gram ids
            $gramIds = explode(',', $grams['gram_id']);
            foreach ($gramIds as $gramId) {
                $gram = Gram::find($gramId);
                if ($gram) {
                    $gramName[$gramId] = $gram->name;
                }
            }
        }
        return $gramName;
    }

    public function getAppUsersByStockiestId($stockiest)
    {
        //get gram_ids of stockiest
        $query = User::select('gram_id')->where('id', $stockiest)->first();
        $appUsers = array();
        if ($query) {
            //separate gram id
            $gramIds = explode(',', $query['gram_id']);
            //loop through each gram_id
            foreach ($gramIds as $gram) {
                //find app users whose gram id is equal to stockist gram id
                $app_users = User::select('id')->whereRaw("FIND_IN_SET(?, gram_id)", [$gram])->where('users.role', 2)->get();
                //loop through each record if found data
                if ($app_users) {
                    foreach ($app_users as $user) {
                        // store id of app users into array where key and values are same
                        $appUsers[$user->id] = $user->id;
                    } // end loop

                } //endif
            } //end loop
        } //endif
        //return array of app users
        return $appUsers;
    }
}