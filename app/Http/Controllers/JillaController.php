<?php

namespace App\Http\Controllers;

use App\Models\{
    Prant,
    Vibhag,
    Jilla
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    DB
};

class JillaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $title = 'Jilla';
        $district = Jilla::select(DB::raw("CONCAT_WS(' > ', prant.name, vibhag.name) as concat_values"), 'jilla.*', 'prant.name as prant_name')
            ->join('vibhag', 'vibhag.id', '=', 'jilla.vibhag_id')
            ->join('prant', 'prant.id', '=', 'vibhag.prant_id')
            ->orderBy('jilla.id', 'desc')
            ->get();
        return view('jilla.index', compact('district', 'title'));
    }

    public function create()
    {
        $title = 'Jilla';
        $prant = Prant::where('status', '1')->pluck('name', 'id');
        return view('jilla.create', compact('title', 'prant'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required||unique:jilla,name|min:2',
            'vibhag_id' => 'required',
            'prant_id' => 'required',
        ], [
            'name.required' => 'The name field is required *',
            'name.string' => 'The name field must be a string.',
            'name.min' => 'The name field must be at least 2 characters long.',
            'vibhag_id.required' => 'The vibhag name field is required.',
            'prant_id.required' => 'The prant name field is required.',
        ]);

        Jilla::create($request->all());
        toastr()->success('Record saved successfully!');
        return redirect()->route('jilla.index');
    }

    public function show(string $id)
    {
        $title = 'Jilla';
        $jilla = Jilla::select('v.name as vibhag_name', 'jilla.name as jilla_name', 'jilla.status')
            ->join('vibhag as v', 'v.id', 'jilla.vibhag_id')
            ->findOrFail($id);
        return view('jilla.show', compact('title', 'jilla'));
    }

    public function edit(string $id)
    {
        $title = 'Jilla';
        $vibhag = Vibhag::where('status', '1')->pluck('name', 'id');
        $jilla = Jilla::with('vibhag')->where('id', $id)->first();

        return view('jilla.edit', compact('title', 'jilla', 'vibhag'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|min:2',
            'vibhag_id' => 'required',
        ], [
            'name.required' => 'The name field is required *',
            'name.min' => 'The name field must be at least 2 characters long.',
            'vibhag_id.required' => 'The vibhag name field is required.',
        ]);

        $jilla = Jilla::findOrFail($id);
        $jilla->update($request->all());
        toastr()->success('Record updated successfully!');
        return redirect()->route('jilla.index');
    }

    public function destroy(string $id)
    {
        $delete = Jilla::where('jilla.id', $id)
            ->leftJoin('taluka as t', 'jilla.id', '=', 't.jilla_id')
            ->leftJoin('gramjuth as gj', 't.id', '=', 'gj.taluka_id')
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
            return redirect()->route('jilla.index');
        } else {
            $jilla = jilla::findOrFail($id);
            $jilla->delete();
            toastr()->success('Record deleted successfully!');
            return redirect()->back();
        }
    }

    public function deleteJilla(Request $request)
    {
        $jillaIds = $request->input('ids');
        $delete = Jilla::whereIn('jilla.id', $jillaIds)
            ->leftJoin('taluka as t', 'jilla.id', '=', 't.jilla_id')
            ->leftJoin('gramjuth as gj', 't.id', '=', 'gj.taluka_id')
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
            $jillo = jilla::whereIn('id', $jillaIds)->get();

            foreach ($jillo as $jilla) {
                $jilla->delete();
            }
            toastr()->success('Record deleted successfully!');
            return json_encode([
                'status' => '1',
                'messages' => 'Record deleted successfully.'
            ]);
        }
    }

    public function changeDistrictStatus(Request $request)
    {
        $type = $request->input('type');
        $multiStatus = $request->input('status');
        $jillaIds = $request->input('ids');

        if ($request->get('jilla_status') == '1' || ($multiStatus == 0 && $jillaIds !== '' && $type == 'Multiple')) {
            $status = '0';
            $userStatus = 'Deactive';

            if ($jillaIds) {
                Jilla::leftJoin('taluka as t', 't.jilla_id', 'jilla.id')
                    ->leftJoin('gramjuth as gj', 'gj.taluka_id', 't.id')
                    ->leftJoin('gram as g', 'g.gramjuth_id', 'gj.id')
                    ->leftJoin('users as u', 'u.gram_id', 'g.id')
                    ->whereIn('jilla.id', $jillaIds)
                    ->update(['jilla.status' => $status, 't.status' => $status, 'gj.status' => $status, 'g.status' => $status, 'u.status' => $userStatus]);
            } else {
                Jilla::leftJoin('taluka as t', 't.jilla_id', 'jilla.id')
                    ->leftJoin('gramjuth as gj', 'gj.taluka_id', 't.id')
                    ->leftJoin('gram as g', 'g.gramjuth_id', 'gj.id')
                    ->leftJoin('users as u', 'u.gram_id', 'g.id')
                    ->where('jilla.id', $request->get('jilla_id'))
                    ->update(['jilla.status' => $status, 't.status' => $status, 'gj.status' => $status, 'g.status' => $status, 'u.status' => $userStatus]);
            }
            toastr()->success('Status ' . $userStatus . ' successfully!');
            return json_encode([
                'status' => '0',
                'messages' => 'Status deactive successfully.'
            ]);
        } elseif ($request->get('jilla_status') == '0' || ($multiStatus == 1 && $jillaIds !== '' && $type == 'Multiple')) {
            $status = '1';
            $userStatus = 'Active';

            if ($jillaIds) {
                Jilla::whereIn('id', $jillaIds)->update(['status' => $status]);
            } else {
                Jilla::where('id', $request->get('jilla_id'))->update(['status' => $status]);
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
