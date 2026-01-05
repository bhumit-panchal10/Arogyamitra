<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    Gramjuth,
    Jilla,
    Taluka,
    Prant
};
use Illuminate\Support\Facades\{
    DB
};

class GramjuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $gramjuth = Gramjuth::select(
            DB::raw("CONCAT_WS(' > ', prant.name, vibhag.name, jilla.name, taluka.name) as  concat_values"),
            'gramjuth.*',
            'prant.name as prant_name',
            'vibhag.name as vibhag_name',
            'jilla.name as jilla_name',
        )
            ->join('taluka', 'taluka.id', '=', 'gramjuth.taluka_id')
            ->join('jilla', 'jilla.id', '=', 'taluka.jilla_id')
            ->join('vibhag', 'vibhag.id', '=',  'jilla.vibhag_id')
            ->join('prant', 'prant.id', '=', 'vibhag.prant_id')
            ->orderBy('gramjuth.id', 'DESC')
            ->get();


        $title = 'Gramjuth';
        return view('gramjuth.index', ['gramjuth' => $gramjuth, 'title' => $title]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Gramjuth';
        $prant = Prant::where('status', '1')->pluck('name', 'id');
        return view('gramjuth.create', ['title' => $title, 'prant' => $prant]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:2|unique:gramjuth,name',
            'vibhag_id' => 'required',
            'jilla_id' => 'required',
            'taluka_id' => 'required',
            'prant' => 'required',
        ], [
            'name.required' => 'The name field is required',
            'name.min' => 'The name field must be at least 2 characters long.',
            'prant.required' => 'The prant name field is required.',
            'vibhag_id.required' => 'The vibhag name field is required.',
            'jilla_id.required' => 'The jilla name field is required.',
            'taluka_id.required' => 'The taluka name field is required.',
            'name.regex' => 'The name field must not contain numbers.',
        ]);
        Gramjuth::create($request->all());
        toastr()->success('Record saved successfully!');
        return redirect()->route('gramjuth.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $title = 'Gramjuth';
        $result = Gramjuth::select('t.name as taluka_name', 'gramjuth.*')
            ->join('taluka as t', 'gramjuth.taluka_id', '=', 't.id')
            ->where('gramjuth.id', $id)
            ->first();
        return view('gramjuth.show', ['result' => $result, 'title' => $title]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $gramjuth = Gramjuth::findOrFail($id);
        $taluka = Taluka::where('status', '1')->pluck('name', 'id');
        $title = 'Gramjuth';
        return view('gramjuth.edit', ['gramjuth' => $gramjuth, 'title' => $title, 'taluka' => $taluka]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|min:2|max:50',
            'taluka_id' => 'required'
        ], [
            'name.required' => 'The name field is required *',
            'name.string' => 'The name field must be a string.',
            'name.min' => 'The gramjuth name field must be at least 2 characters.',
            'name.max' => 'The gramjuth name field may not be greater than 50 characters.',
            'taluka_id.required' => 'The taluka name field is required.',
        ]);
        $gramjuth = Gramjuth::findOrFail($id);
        $gramjuth->update($request->all());
        toastr()->success('Record updated successfully!');
        return redirect()->route('gramjuth.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $delete = Gramjuth::where('gramjuth.id', $id)
            ->leftJoin('gram as g',  'gramjuth.id', '=', 'g.gramjuth_id')
            ->leftJoin('users as u',  'g.id', '=', 'u.gram_id')
            ->leftJoin('medicine_stock as ms', 'u.id', '=', 'ms.arogyamitra_id')
            ->leftJoin('medicine_request as mr', 'u.id', '=', 'mr.arogyamitra_id')
            ->leftJoin('medicine_track as mt', 'u.id', '=', 'mt.arogyamitra_id')
            ->selectRaw('COUNT(DISTINCT ms.arogyamitra_id) + COUNT(DISTINCT mr.arogyamitra_id) + COUNT(DISTINCT mt.arogyamitra_id) as count')
            ->first();

        $count = $delete->count;

        if ($count > 0) {
            toastr()->info("This record can't deleted because medicine records is available.");
            return redirect()->route('gramjuth.index');
        } else {
            $gramjuth = Gramjuth::find($id);
            $gramjuth->delete();
            toastr()->success('Record deleted successfully!');
            return redirect()->back();
        }
    }

    public function deleteGramjuth(Request $request)
    {
        $gramjuthIds = $request->input('ids');
        $delete = Gramjuth::whereIn('gramjuth.id', $gramjuthIds)
            ->leftJoin('gram as g',  'gramjuth.id', '=', 'g.gramjuth_id')
            ->leftJoin('users as u',  'g.id', '=', 'u.gram_id')
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
            $gramuths = Gramjuth::whereIn('id', $gramjuthIds)->get();

            foreach ($gramuths as $gramjuth) {
                $gramjuth->delete();
            }
            toastr()->success('Record deleted successfully!');
            return json_encode([
                'status' => '1',
                'messages' => 'Record deleted successfully.'
            ]);
        }
    }

    public function changeGramjuthStatus(Request $request)
    {
        $type = $request->input('type');
        $multiStatus = $request->input('status');
        $gramjuthIds = $request->input('ids');

        if ($request->get('gramjuth_status') == '1' || ($multiStatus == 0 && $gramjuthIds !== '' && $type == 'Multiple')) {
            $status = '0';
            $userStatus = 'Deactive';

            if ($gramjuthIds) {
                Gramjuth::leftJoin('gram as g', 'g.gramjuth_id', 'gramjuth.id')
                    ->leftJoin('users as u', 'u.gram_id', 'g.id')
                    ->whereIn('gramjuth.id', $gramjuthIds)
                    ->update(['gramjuth.status' => $status, 'g.status' => $status, 'u.status' => $userStatus]);
            } else {

                Gramjuth::leftJoin('gram as g', 'g.gramjuth_id', 'gramjuth.id')
                    ->leftJoin('users as u', 'u.gram_id', 'g.id')
                    ->where('gramjuth.id', $request->get('gramjuth_id'))
                    ->update(['gramjuth.status' => $status, 'g.status' => $status, 'u.status' => $userStatus]);
            }
            toastr()->success('Status ' . $userStatus . ' successfully!');
            return json_encode([
                'status' => '0',
                'messages' => 'Status deactive successfully.'
            ]);
        } elseif ($request->get('gramjuth_status') == '0' || ($multiStatus == 1 && $gramjuthIds !== '' && $type == 'Multiple')) {
            $status = '1';
            $userStatus = 'Active';

            if ($gramjuthIds) {
                Gramjuth::whereIn('id', $gramjuthIds)->update(['status' => $status]);
            } else {
                Gramjuth::where('id', $request->get('gramjuth_id'))->update(['status' => $status]);
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

    public function getJillaOptions(Request $request)
    {
        $vibhagId = $request->input('vibhag_id');

        // Retrieve the Jilla options based on the provided vibhag_id
        $jillaOptions = Jilla::where('vibhag_id', $vibhagId)
            ->where('status', '1')
            ->pluck('name', 'id');

        // Return the options as a JSON response
        return response()->json([
            'options' => $jillaOptions
        ]);
    }
}
