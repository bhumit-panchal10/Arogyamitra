<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    Gram,
    Gramjuth,
    Prant
};
use Illuminate\Support\Facades\{
    DB
};
use Illuminate\Validation\Rule;

class GramController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $grams = Gram::select(
            DB::raw("CONCAT_WS(' > ', prant.name, vibhag.name, jilla.name, taluka.name , gramjuth.name) as  concat_values"),
            'gram.*',
            'prant.name as prant_name',
            'vibhag.name as vibhag_name',
            'jilla.name as jilla_name',
            'taluka.name as taluka_name',
        )
            ->join('gramjuth', 'gramjuth.id', '=', 'gram.gramjuth_id')
            ->join('taluka', 'taluka.id', '=', 'gramjuth.taluka_id')
            ->join('jilla', 'jilla.id', '=', 'taluka.jilla_id')
            ->join('vibhag', 'vibhag.id', '=',  'jilla.vibhag_id')
            ->join('prant', 'prant.id', '=', 'vibhag.prant_id')
            ->orderBy('gram.id', 'desc')->get();

        $title = 'Gram';

        return view('gram.index', compact('grams', 'title'));
    }

    public function create()
    {
        $title = 'Gram';
        $prant = Prant::where('status', '1')->pluck('name', 'id');
        return view('gram.create', compact('title', 'prant'));
    }

    public function store(Request $request)
    {

        $request->validate([
            //'name'          => 'required|unique:gram,name,gramjuth_id',
            'prant'         => 'required',
            'vibhag_id'     => 'required',
            'jilla_id'      => 'required',
            'taluka_id'     => 'required',
            'gramjuth_id'   => 'required',
            'name'          => [
                'required',
                Rule::unique('gram', 'name')->where('gramjuth_id', $request->gramjuth_id)
            ],
        ], [
            'name.required' => 'The name field is required.',
            'name.unique' => 'The name has already been taken.',
            'prant.required' => 'The prant field is required.',
            'vibhag_id.required' => 'The vibhag field is required.',
            'jilla_id.required' => 'The jilla field is required.',
            'taluka_id.required' => 'The taluka field is required.',
            'gramjuth_id.required' => 'The gramjuth field is required.',
        ]);
        Gram::create($request->all());
        toastr()->success('Record saved successfully!');
        return redirect()->route('grams.index');
    }

    public function show($id)
    {
        $title = 'Gram';
        $gram = Gram::with('gramjuth')->where('id', $id)->first();
        return view('gram.show', compact('gram', 'title'));
    }

    public function edit($id)
    {
        $gram = Gram::select('gram.id', 'gram.name', 'gramjuth.id as gramjuth_id', 'gramjuth.name as gramjuth_name', 'gram.status'/* , 'taluka.id as taluka_id' */)
            ->join('gramjuth', 'gramjuth.id', '=', 'gram.gramjuth_id')
            //->join('taluka', 'taluka.id', '=', 'gramjuth.taluka_id')
            ->where('gram.id', $id)
            ->first();

        $grams = Gramjuth::select('gramjuth.id as gramjuth_id', 'gramjuth.name as gramjuth_name', 'taluka.id as taluka_id', 'gramjuth.status')
            ->join('taluka', 'taluka.id', '=', 'gramjuth.taluka_id')
            //->where('taluka.id', $gram->taluka_id) // if only show gramjuth for created gram taluka
            ->get();
        $title = 'Gram';
        return view('gram.edit', compact('gram', 'grams', 'title'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'gramjuth_id'   => 'required',
            'name'          => [
                'required',
                Rule::unique('gram', 'name')->ignore($request->gram_id)->where('gramjuth_id', $request->gramjuth_id)
            ],
        ], [
            'name.required' => 'The name field is required.',
            'name.unique' => 'The name has already been taken.',
            'gramjuth_id.required' => 'The gramjuth field is required.',
        ]);

        $gram = Gram::findOrFail($id);
        $gram->update($request->all());
        toastr()->success('Record updated successfully!');
        return redirect()->route('grams.index');
    }

    public function destroy($id)
    {
        $delete = Gram::where('gram.id', $id)
            ->leftJoin('users as u', 'gram.id', '=', 'u.gram_id')
            ->leftJoin('medicine_stock as ms', 'u.id', '=', 'ms.arogyamitra_id')
            ->leftJoin('medicine_request as mr', 'u.id', '=', 'mr.arogyamitra_id')
            ->leftJoin('medicine_track as mt', 'u.id', '=', 'mt.arogyamitra_id')
            ->selectRaw('COUNT(DISTINCT ms.arogyamitra_id) + COUNT(DISTINCT mr.arogyamitra_id) + COUNT(DISTINCT mt.arogyamitra_id) as count')
            ->first();

        $count = $delete->count;

        if ($count > 0) {
            toastr()->info("This record can't deleted because medicine records is available.");
            return redirect()->route('grams.index');
        } else {
            $gram = Gram::findOrFail($id);
            $gram->delete();
            toastr()->success('Record deleted successfully!');
            return redirect()->back();
        }
    }

    public function deleteGram(Request $request)
    {
        $gramIds = $request->input('ids');
        $delete = Gram::whereIn('gram.id', $gramIds)
            ->leftJoin('users as u', 'gram.id', '=', 'u.gram_id')
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
            $grams = Gram::whereIn('id', $gramIds)->get();

            foreach ($grams as $gram) {
                $gram->delete();
            }
            toastr()->success('Record deleted successfully!');
            return json_encode([
                'status' => '1',
                'messages' => 'Record deleted successfully.'
            ]);
        }
    }

    public function changeGramStatus(Request $request)
    {
        $type = $request->input('type');
        $multiStatus = $request->input('status');
        $gramIds = $request->input('ids');

        if ($request->get('gram_status') == '1' || ($multiStatus == 0 && $gramIds !== '' && $type == 'Multiple')) {
            $status = '0';
            $userStatus = 'Deactive';

            if ($gramIds) {
                Gram::leftJoin('users as u', 'u.gram_id', 'gram.id')
                    ->whereIn('gram.id', $gramIds)
                    ->update(['gram.status' => $status, 'u.status' => $userStatus]);
            } else {
                Gram::leftJoin('users as u', 'u.gram_id', 'gram.id')
                    ->where('gram.id', $request->get('gram_id'))
                    ->update(['gram.status' => $status, 'u.status' => $userStatus]);
            }
            toastr()->success('Status ' . $userStatus . ' successfully!');
            return json_encode([
                'status' => '0',
                'messages' => 'Status deactive successfully.'
            ]);
        } elseif ($request->get('gram_status') == '0' || ($multiStatus == 1 && $gramIds !== '' && $type == 'Multiple')) {
            $status = '1';
            $userStatus = 'Active';

            if ($gramIds) {
                Gram::whereIn('id', $gramIds)->update(['status' => $status]);
            } else {
                Gram::where('id', $request->get('gram_id'))->update(['status' => $status]);
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
