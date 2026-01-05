<?php

namespace App\Http\Controllers;

use App\Models\{
    Prant,
    Jilla,
    Taluka
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    DB
};

class TalukaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $title = 'Taluka';
        $subDistrict = Taluka::with('jilla')->orderBy('id', 'desc')->get();
        $subDistrict = Taluka::select(DB::raw("CONCAT_WS(' > ', prant.name, vibhag.name, jilla.NAME) as concat_values"), 'taluka.*')
            ->join('jilla', 'jilla.id', '=', 'taluka.jilla_id')
            ->join('vibhag', 'vibhag.id', '=',  'jilla.vibhag_id')
            ->join('prant', 'prant.id', '=', 'vibhag.prant_id')
            ->orderBy('taluka.id', 'desc')
            ->get();


        return view('taluka.index', compact('subDistrict', 'title'));
    }

    public function create()
    {
        $title = 'Taluka';
        $prant = Prant::where('status', '1')->pluck('name', 'id');
        return view('taluka.create', compact('title', 'prant'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:taluka,name',
            'jilla_id' => 'required',
            'vibhag_id' => 'required',
            'prant' => 'required',
        ], [
            'name.required' => 'The name field is required',
            'name.unique' => 'The name has already been taken.',
            'jilla_id.required' => 'The jilla name field is required.',
            'prant.required' => 'The prant name field is required.',
            'vibhag_id.required' => 'The vibhag name field is required.',
            'name.regex' => 'The name field must not contain numbers.',
        ]);

        Taluka::create($request->all());
        toastr()->success('Record saved successfully!');
        return redirect()->route('taluka.index');
    }

    public function show(string $id)
    {
        $title = 'Taluka';
        $taluka = Taluka::select('j.name as jilla_name', 'taluka.*')
            ->join('jilla as j', 'taluka.jilla_id', '=', 'j.id')
            ->where('taluka.id', $id)
            ->first();

        return view('taluka.show', compact('title', 'taluka'));
    }
    public function edit(string $id)
    {
        $title = 'Taluka';
        $taluka = Taluka::with('jilla')->where('id', $id)->first();
        $jilla = Jilla::select('id', 'name')->pluck('name', 'id');
        return view('taluka.edit', compact('title', 'jilla', 'taluka'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|min:2|max:50|regex:/^[^0-9]+$/',
            'jilla_id' => 'required',
        ], [
            'name.required' => 'The name field is required',
            'name.string' => 'The name field must be a string.',
            'name.min' => 'The name field must be at least 2 characters long.',
            'name.max' => 'The name field may not be greater than 50 characters long.',
            'jilla_id.required' => 'The jilla name field is required.',
            'name.regex' => 'The name field must not contain numbers.',
        ]);
        $taluka = Taluka::findOrFail($id);
        $taluka->update($request->all());
        toastr()->success('Record updated successfully!');
        return redirect()->route('taluka.index');
    }

    public function destroy(string $id)
    {
        $delete = Taluka::where('taluka.id', $id)
            ->leftJoin('gramjuth as gj', 'taluka.id', '=', 'gj.taluka_id')
            ->leftJoin('gram as g', 'gj.id', '=', 'g.gramjuth_id')
            ->leftJoin('users as u', 'g.id', '=', 'u.gram_id')
            ->leftJoin('medicine_stock as ms', 'u.id', '=', 'ms.arogyamitra_id')
            ->leftJoin('medicine_request as mr', 'u.id', '=', 'mr.arogyamitra_id')
            ->leftJoin('medicine_track as mt', 'u.id', '=', 'mt.arogyamitra_id')
            ->selectRaw('COUNT(DISTINCT ms.arogyamitra_id) + COUNT(DISTINCT mr.arogyamitra_id) + COUNT(DISTINCT mt.arogyamitra_id) as count')
            ->first();

        $count = $delete->count;

        if ($count > 0) {
            toastr()->info("This record can't deleted because medicine records is available.");
            return redirect()->route('taluka.index');
        } else {
            $jilla = Taluka::findOrFail($id);
            $jilla->delete();
            toastr()->success('Record deleted successfully!');
            return redirect()->route('taluka.index');
        }
    }

    public function deleteTaluka(Request $request)
    {
        $talukaIds = $request->input('ids');
        $delete = Taluka::whereIn('taluka.id', $talukaIds)
            ->leftJoin('gramjuth as gj', 'taluka.id', '=', 'gj.taluka_id')
            ->leftJoin('gram as g', 'gj.id', '=', 'g.gramjuth_id')
            ->leftJoin('users as u', 'g.id', '=', 'u.gram_id')
            ->leftJoin('medicine_stock as ms', 'u.id', '=', 'ms.arogyamitra_id')
            ->leftJoin('medicine_request as mr', 'u.id', '=', 'mr.arogyamitra_id')
            ->leftJoin('medicine_track as mt', 'u.id', '=', 'mt.arogyamitra_id')
            ->selectRaw('COUNT(DISTINCT ms.arogyamitra_id) + COUNT(DISTINCT mr.arogyamitra_id) + COUNT(DISTINCT mt.arogyamitra_id) as count')
            ->first();

        $count = $delete->count;

        if ($count > 0) {
            toastr()->info("This record can't deleted because medicine records is available.");
            return json_encode([
                'status' => '0',
                'messages' => "This record can't deleted because medicine records is available."
            ]);
        } else {
            $talukas = Taluka::whereIn('id', $talukaIds)->get();

            foreach ($talukas as $taluka) {
                $taluka->delete();
            }
            toastr()->success('Record deleted successfully!');
            return json_encode([
                'status' => '1',
                'messages' => 'Record deleted successfully.'
            ]);
        }
    }

    public function changeSubDistrictStatus(Request $request)
    {
        $type = $request->input('type');
        $multiStatus = $request->input('status');
        $talukaIds = $request->input('ids');

        if ($request->get('taluka_status') == '1' || ($multiStatus == 0 && $talukaIds !== '' && $type == 'Multiple')) {
            $status = '0';
            $userStatus = 'Deactive';

            if ($talukaIds) {
                Taluka::leftJoin('gramjuth as gj', 'gj.taluka_id', 'taluka.id')
                    ->leftJoin('gram as g', 'g.gramjuth_id', 'gj.id')
                    ->leftJoin('users as u', 'u.gram_id', 'g.id')
                    ->whereIn('taluka.id', $talukaIds)
                    ->update(['taluka.status' => $status, 'gj.status' => $status, 'g.status' => $status, 'u.status' => $userStatus]);
            } else {
                Taluka::leftJoin('gramjuth as gj', 'gj.taluka_id', 'taluka.id')
                    ->leftJoin('gram as g', 'g.gramjuth_id', 'gj.id')
                    ->leftJoin('users as u', 'u.gram_id', 'g.id')
                    ->where('taluka.id', $request->get('taluka_id'))
                    ->update(['taluka.status' => $status, 'gj.status' => $status, 'g.status' => $status, 'u.status' => $userStatus]);
            }
            toastr()->success('Status ' . $userStatus . ' successfully!');
            return json_encode([
                'status' => '0',
                'messages' => 'Status deactive successfully.'
            ]);
        } elseif ($request->get('taluka_status') == '0' || ($multiStatus == 1 && $talukaIds !== '' && $type == 'Multiple')) {
            $status = '1';
            $userStatus = 'Active';
            if ($talukaIds) {
                Taluka::whereIn('id', $talukaIds)->update(['status' => $status]);
            } else {
                Taluka::where('id', $request->get('taluka_id'))->update(['status' => $status]);
            }
            toastr()->success('Status ' . $userStatus . ' successfully!');
            return json_encode([
                'status' => '1',
                'messages' => 'Status active successfully.'
            ]);
        }
        toastr()->info('Status changed failed!');
        return json_encode([
            'messages' => 'Status changed failed.'
        ]);
    }
}
