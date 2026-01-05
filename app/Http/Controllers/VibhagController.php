<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    Vibhag,
    Prant
};

class VibhagController extends Controller
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
        $vibhag = Vibhag::with('prant')->orderBy('id', 'desc')->get();
        $title = 'Vibhag';
        return view('vibhag.index', ['vibhag' => $vibhag, 'title' => $title]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Vibhag';
        $prant = Prant::where('status', '1')->pluck('name', 'id');
        return view('vibhag.create', compact('title', 'prant'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:vibhag,name',
            'prant_id' => 'required',
        ], [
            'name.required' => 'The name field is required *',
            'prant_id.required' => 'The prant name field is required.',
        ]);
        Vibhag::create($request->all());
        toastr()->success('Record saved successfully!');
        return redirect()->route('vibhag.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $vibhag = Vibhag::findOrFail($id);
        $title = 'Vibhag';
        return view('vibhag.show', ['vibhag' => $vibhag, 'title' => $title]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $title = 'Vibhag';
        $vibhag = Vibhag::findOrFail($id);
        $prant = Prant::where('status', '1')->pluck('name', 'id');
        return view('vibhag.edit', compact('title', 'prant', 'vibhag'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'prant_id' => 'required',
        ], [
            'name.required' => 'The name field is required *',
            'prant_id.required' => 'The prant name field is required.',
        ]);

        $vibhag = Vibhag::findOrFail($id);
        $vibhag->update($request->all());
        toastr()->success('Record updated successfully!');
        return redirect()->route('vibhag.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $delete = Vibhag::where('vibhag.id', $id)
            ->leftJoin('jilla as j', 'vibhag.id', '=', 'j.vibhag_id')
            ->leftJoin('taluka as t', 'j.id', '=', 't.jilla_id')
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
            return redirect()->route('vibhag.index');
        } else {
            $vibhag = Vibhag::find($id);
            $vibhag->delete();
            toastr()->success('Record deleted successfully!');
            return redirect()->route('vibhag.index');
        }
    }

    public function deleteVibhag(Request $request)
    {
        $vibhagIds = $request->input('ids');
        $delete = Vibhag::whereIn('vibhag.id', $vibhagIds)
            ->leftJoin('jilla as j', 'vibhag.id', '=', 'j.vibhag_id')
            ->leftJoin('taluka as t', 'j.id', '=', 't.jilla_id')
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
            $vibhags = vibhag::whereIn('id', $vibhagIds)->get();

            foreach ($vibhags as $vibhag) {
                $vibhag->delete();
            }
            toastr()->success('Record deleted successfully!');
            return json_encode([
                'status' => '1',
                'messages' => 'Record deleted successfully.'
            ]);
        }
    }

    public function changeVibhagStatus(Request $request)
    {
        $type = $request->input('type');
        $multiStatus = $request->input('status');
        $vibhagIds = $request->input('ids');

        if ($request->get('vibhag_status') == '1' || ($multiStatus == 0 && $vibhagIds !== '' && $type == 'Multiple')) {
            $status = '0';
            $userStatus = 'Deactive';

            if ($vibhagIds) {
                Vibhag::leftJoin('jilla as j', 'j.vibhag_id', 'vibhag.id')
                    ->leftJoin('taluka as t', 't.jilla_id', 'j.id')
                    ->leftJoin('gramjuth as gj', 'gj.taluka_id', 't.id')
                    ->leftJoin('gram as g', 'g.gramjuth_id', 'gj.id')
                    ->leftJoin('users as u', 'u.gram_id', 'g.id')
                    ->whereIn('vibhag.id', $vibhagIds)
                    ->update(['vibhag.status' => $status, 'j.status' => $status, 't.status' => $status, 'gj.status' => $status, 'g.status' => $status, 'u.status' => $userStatus]);
            } else {
                Vibhag::leftJoin('jilla as j', 'j.vibhag_id', 'vibhag.id')
                    ->leftJoin('taluka as t', 't.jilla_id', 'j.id')
                    ->leftJoin('gramjuth as gj', 'gj.taluka_id', 't.id')
                    ->leftJoin('gram as g', 'g.gramjuth_id', 'gj.id')
                    ->leftJoin('users as u', 'u.gram_id', 'g.id')
                    ->where('vibhag.id', $request->get('vibhag_id'))
                    ->update(['vibhag.status' => $status, 'j.status' => $status, 't.status' => $status, 'gj.status' => $status, 'g.status' => $status, 'u.status' => $userStatus]);
            }
            toastr()->success('Status ' . $userStatus . ' successfully!');
            return json_encode([
                'status' => '0',
                'messages' => 'Status deactive successfully.'
            ]);
        } elseif ($request->get('vibhag_status') == '0' || ($multiStatus == 1 && $vibhagIds !== '' && $type == 'Multiple')) {
            $status = '1';
            $userStatus = 'Active';

            if ($vibhagIds) {
                Vibhag::whereIn('id', $vibhagIds)->update(['status' => $status]);
            } else {

                Vibhag::where('id', $request->get('vibhag_id'))->update(['status' => $status]);
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
