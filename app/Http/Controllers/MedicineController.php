<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicine;
use App\Models\MedicineRequest;
use App\Models\MedicineStock;
use App\Models\MedicineTrack;
use App\Models\Vibhag;

class MedicineController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $title = 'Medicine';
        $medicines = Medicine::orderBy('id', 'ASC')->get();

        return view('medicine.index', compact('medicines', 'title'));
    }

    public function create()
    {
        $title = 'Medicine';
        return view('medicine.create', compact('title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:medicine,name',
            'qty' => 'required|numeric|max:9999|gt:0',
            'qty_type' => 'required',
        ], [
            'name.required' => 'The medicine name field is required',
            'qty.required' => 'The quantity field is required.',
            'qty.max' => 'The quantity field must be greater than 4 characters long',
            'qty.numeric' => 'The quantity must be a numeric value.',
            'qty_type.required' => 'The quantity type field is required.',
        ]);

        Medicine::create($request->all());
        toastr()->success('Record saved successfully!');
        return redirect()->route('medicines.index');
    }

    public function show($id)
    {
        $title = 'Medicine';
        $medicine = Medicine::findOrFail($id);
        return view('medicine.show', compact('medicine', 'title'));
    }

    public function edit($id)
    {
        $title = 'Medicine';
        $medicine = Medicine::findOrFail($id);
        return view('medicine.edit', compact('medicine', 'title'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|min:2|max:50',
            'qty' => 'required|numeric|max:9999|gt:0',
            'qty_type' => 'required|string',
        ], [
            'name.required' => 'The medicine name field is required',
            'name.min' => 'The medicine name field must be at least 2 characters long.',
            'name.max' => 'The medicine name field may not be greater than 50 characters long.',
            'qty.required' => 'The quantity field is required.',
            'qty.max' => 'The quantity field must be at greater 4 characters long',
            'qty.numeric' => 'The quantity must be a numeric value.',
            'qty_type.required' => 'The quantity type field is required.',
        ]);

        $medicine = Medicine::findOrFail($id);
        $medicine->update($request->all());
        toastr()->success('Record updated successfully!');
        return redirect()->route('medicines.index');
    }

    public function destroy($id)
    {
        $medicine = Medicine::findOrFail($id);
        if ($medicine) {
            $medicine_stock = MedicineStock::where('medicine_id', $id)->first();
            $medicine_track = MedicineTrack::where('medicine_id', $id)->first();
            $medicine_request = MedicineRequest::where('medicine_id', $id)->first();

            if (!$medicine_stock && !$medicine_track  && !$medicine_request) {
                $medicine->delete();
                toastr()->success('Record deleted successfully!');
            } else {
                toastr()->info("This record can't deleted because medicine stock is available.");
            }
            return redirect()->route('medicines.index');
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $medicine = Medicine::findOrFail($id);
        $status = $request->status == 1 ? '1' : '0';

        $medicine->status = $status;
        $medicine->save();
        toastr()->success('Record updated successfully!');
        return redirect()->route('medicines.index');
    }

    public function deleteMedicine(Request $request)
    {
        $ids = $request->input('ids');
        $delete = Medicine::whereIn('medicine.id', $ids)
            ->leftJoin('medicine_stock as ms', 'medicine.id', '=', 'ms.medicine_id')
            ->leftJoin('medicine_request as mr', 'medicine.id', '=', 'mr.medicine_id')
            ->leftJoin('medicine_track as mt', 'medicine.id', '=', 'mt.medicine_id')
            ->selectRaw('COUNT(DISTINCT ms.medicine_id) + COUNT(DISTINCT mr.medicine_id) + COUNT(DISTINCT mt.medicine_id) as count')
            ->first();

        $count = $delete->count;

        if ($count > 0) {
            toastr()->info("This record can't deleted because medicine records is available.");
            return json_encode([
                'status' => '0',
                'messages' => "This record can't deleted because medicine records is available."
            ]);
        } else {
            $medicineRecord = Medicine::whereIn('id', $ids)->get();
            foreach ($medicineRecord as $medicines) {
                $medicines->delete();
            }
            toastr()->success('Record deleted successfully!');
            return json_encode([
                'status' => '1',
                'messages' => 'You have deleted all selected medicines!.'
            ]);
        }
    }

    public function changeMedicineStatus(Request $request)
    {
        //multiselect medicine changeStatus
        $multiStatus = $request->input('status');
        $medicineIds = $request->input('ids');

        if ($request->get('Medicine_status') == '0' || $multiStatus == 0 && count($medicineIds) > 0) {
            $status = '0';
            $medicineStatus = 'Deactive';
            $flag = false;
            if ($status == 0) {
                $flag = true;
            }
            Medicine::whereIn('id', $medicineIds)->update(['status' => $status]);
            if ($flag) {
                toastr()->success('Status ' . $medicineStatus . ' successfully!');
                return json_encode([
                    'status' => $flag,
                    'messages' => 'Status deactivated successfully.'
                ]);
            } else {
                toastr()->error('Something went wrong!');
                return json_encode([
                    'status' => false,
                    'messages' => 'Status error.'
                ]);
            }
        } elseif ($request->get('Medicine_status') == '1' || ($multiStatus == 1 && count($medicineIds) > 0)) {
            $status = '1';
            $medicineStatus = 'Active';
            $flag = false;
            if ($status == 1) {
                $flag = true;
            }
            Medicine::whereIn('id', $medicineIds)->update(['status' => $status]);
            if ($flag) {
                toastr()->success('Status ' . $medicineStatus . ' successfully!');
                return json_encode([
                    'status' => true,
                    'messages' => 'Status activated successfully.'
                ]);
            } else {
                toastr()->error('Something went wrong!');
                return json_encode([
                    'status' => false,
                    'messages' => 'Status error.'
                ]);
            }
        } else {
            toastr()->info('Status changed failed!');
            return json_encode([
                'messages' => 'Status changed failed.'
            ]);
        }
    }
}
