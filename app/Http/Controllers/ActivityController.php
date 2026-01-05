<?php

namespace App\Http\Controllers;

use App\Models\LogHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    public function index()
    {
        if (in_array(Auth()->user()->role, ['1', '4', '5'])) {
            $activity = LogHistory::orderBy('id', 'desc')->get();
            $title = 'Active';
            return view('activity.index', ['activity' => $activity, 'title' => $title]);
        } else {
            abort(403, 'Unauthorized action.');
        }
    }
}
