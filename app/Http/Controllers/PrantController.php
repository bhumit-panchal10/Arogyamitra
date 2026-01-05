<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prant;

class PrantController extends Controller
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
        $prant = Prant::orderBy('id', 'desc')->get();
        $title = 'Prant';
        return view('prant.index', ['prant' => $prant, 'title' => $title]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Prant';
        return view('prant.create', ['title' => $title]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:prant,name',
        ], [
            'name.required' => 'The name field is required *',
        ]);
        Prant::create($request->all());
        toastr()->success('Record saved successfully!');
        return redirect()->route('prant.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $prant = Prant::findOrFail($id);
        $title = 'Prant';
        return view('prant.show', ['prant' => $prant, 'title' => $title]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $prant = Prant::findOrFail($id);
        $title = 'Prant';
        return view('prant.edit', ['prant' => $prant, 'title' => $title]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
        ], [
            'name.required' => 'The name field is required *',
        ]);

        $prant = Prant::findOrFail($id);
        $prant->update($request->all());
        toastr()->success('Record updated successfully!');
        return redirect()->route('prant.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $delete = Prant::where('prant.id', $id)
            ->leftJoin('vibhag as v', 'prant.id', '=', 'v.prant_id')
            ->leftJoin('jilla as j', 'v.id', '=', 'j.vibhag_id')
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
            return redirect()->route('prant.index');
        } else {
            $prant = Prant::find($id);
            $prant->delete();
            toastr()->success('Record deleted successfully!');
            return redirect()->route('prant.index');
        }
    }

    public function deletePrant(Request $request)
    {
        $prantIds = $request->input('ids');
        $delete = Prant::whereIn('prant.id', $prantIds)
            ->leftJoin('vibhag as v', 'prant.id', '=', 'v.prant_id')
            ->leftJoin('jilla as j', 'v.id', '=', 'j.vibhag_id')
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
            $prants = prant::whereIn('id', $prantIds)->get();

            foreach ($prants as $prant) {
                $prant->delete();
            }
            toastr()->success('Record deleted successfully!');
            return json_encode([
                'status' => '1',
                'messages' => 'Record deleted successfully.'
            ]);
        }
    }

    public function changePrantStatus(Request $request)
    {
        $type = $request->input('type');
        $multiStatus = $request->input('status');
        $prantIds = $request->input('ids');

        if ($request->get('prant_status') == '1' || ($multiStatus == 0 && $prantIds !== '' && $type == 'Multiple')) {
            $status = '0';
            $userStatus = 'Deactive';

            if ($prantIds) {
                Prant::leftJoin('vibhag as v', 'v.prant_id', 'prant.id')
                    ->leftJoin('jilla as j', 'j.vibhag_id', 'v.id')
                    ->leftJoin('taluka as t', 't.jilla_id', 'j.id')
                    ->leftJoin('gramjuth as gj', 'gj.taluka_id', 't.id')
                    ->leftJoin('gram as g', 'g.gramjuth_id', 'gj.id')
                    ->leftJoin('users as u', 'u.gram_id', 'g.id')
                    ->whereIn('prant.id', $prantIds)
                    ->update(['prant.status' => $status, 'v.status' => $status, 'j.status' => $status, 't.status' => $status, 'gj.status' => $status, 'g.status' => $status, 'u.status' => $userStatus]);
            } else {
                Prant::leftJoin('vibhag as v', 'v.prant_id', 'prant.id')
                    ->leftJoin('jilla as j', 'j.vibhag_id', 'v.id')
                    ->leftJoin('taluka as t', 't.jilla_id', 'j.id')
                    ->leftJoin('gramjuth as gj', 'gj.taluka_id', 't.id')
                    ->leftJoin('gram as g', 'g.gramjuth_id', 'gj.id')
                    ->leftJoin('users as u', 'u.gram_id', 'g.id')
                    ->where('prant.id', $request->get('prant_id'))
                    ->update(['prant.status' => $status, 'v.status' => $status, 'j.status' => $status, 't.status' => $status, 'gj.status' => $status, 'g.status' => $status, 'u.status' => $userStatus]);
            }
            toastr()->success('Status ' . $userStatus . ' successfully!');
            return json_encode([
                'status' => '0',
                'messages' => 'Status deactive successfully.'
            ]);
        } elseif ($request->get('prant_status') == '0' || ($multiStatus == 1 && $prantIds !== '' && $type == 'Multiple')) {
            $status = '1';
            $userStatus = 'Active';

            if ($prantIds) {
                Prant::whereIn('id', $prantIds)->update(['status' => $status]);
            } else {
                Prant::where('id', $request->get('prant_id'))->update(['status' => $status]);
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
