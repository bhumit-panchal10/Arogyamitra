<?php

namespace App\Http\Controllers;

use App\Models\{
    Gram,
    Gramjuth,
    Jilla,
    MedicineRequest,
    MedicineStock,
    MedicineTrack,
    Taluka,
    Vibhag,
    Prant,
    User
};
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\{
    DB,
    Auth,
    Hash
};
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $selPrant = $request->get('prant') ?? null;
        $selVibhag = $request->get('vibhag') ?? null;
        $selJilla = $request->get('jilla') ?? null;
        $selTaluka = $request->get('taluka') ?? null;
        $selGramjuth = $request->get('gramjuth') ?? null;
        $selGram = $request->get('gram') ?? null;
        $prantList = Prant::select('name', 'id')->where('status', '1')->get();

        $query = User::select('users.*')->whereNull('users.deleted_at');
        if (Auth::user()->role == 1) {
            $query = $query->where('users.id', '!=', Auth::user()->id);

            if ($selPrant) {
                $query = $query->where('users.prant_id', '=', $selPrant);
            }
            if ($selVibhag) {
                $query = $query->where('users.vibhag_id', '=', $selVibhag);
            }
        } elseif (Auth::user()->role == 5) {
            $query = $query->where('users.prant_id', Auth::user()->prant_id)->whereIn('role', [2, 3]);

            if ($selVibhag) {
                $query = $query->where('users.vibhag_id', '=', $selVibhag);
            }
        } elseif (Auth::user()->role == 4) {
            $query = $query->where('users.vibhag_id', Auth::user()->vibhag_id)->whereIn('role', [2, 3]);
        }

        if ($selJilla) {
            $query = $query->where('users.jilla_id', '=', $selJilla);
        }
        if ($selTaluka) {
            $query = $query->leftJoin('gram as gs', 'gs.id', '=', 'users.gram_id')->leftJoin('gramjuth as gm', 'gm.id', '=', 'gs.gramjuth_id')->Join('taluka', 'taluka.id', '=', 'gm.taluka_id')->where('taluka.id', '=', $selTaluka);
        }
        if ($selGramjuth) {
            $query = $query->leftJoin('gram as g', 'g.id', '=', 'users.gram_id')->where('g.gramjuth_id', '=', $selGramjuth);
        }
        if ($selGram) {
            $query = $query->where('users.gram_id', '=', $selGram);
        }

        if (!empty($request->get('status'))) {
            $query->where('users.status', $request->get('status'));
        }
        if (!empty($request->get('role'))) {
            $query->where('users.role', $request->get('role'));
        }
        $users = $query->orderBy('users.id', 'DESC')->get();

        foreach ($users as $key => $value) {
            if ($value->role == 1) {
                $name = $role = 'Admin User';
            } elseif ($value->role == 2 || $value->role == 6) {
                $role = $value->role == 2 ? 'App User' : 'Stockiest User';
                $jilla = Jilla::select(DB::raw("CONCAT_WS(' > ', prant.name, vibhag.name, jilla.name) as concat_values"))
                    ->join('vibhag', 'vibhag.id', '=', 'jilla.vibhag_id')
                    ->join('prant', 'prant.id', '=', 'vibhag.prant_id')
                    ->where('jilla.id', '=', $value->jilla_id)
                    ->first();
                $allotGram = Gram::select(DB::raw("GROUP_CONCAT(name) as gram_list"))->whereIn('id', explode(',', $value->gram_id))->first();
                $gList = explode(",", $allotGram->gram_list);
                $addSpace = implode(' ,  ', $gList);
                if ($value->gram_id) {
                    $name = $jilla->concat_values ? $jilla->concat_values . ' > ' . $addSpace : '-';
                } else {
                    $name = $jilla->concat_values ? $jilla->concat_values : '-';
                }
            } elseif ($value->role == 3) {
                $role = 'Arogya Mitra';
                $gram = Gram::select(DB::raw("CONCAT_WS(' > ', prant.name, vibhag.name, jilla.name, taluka.name , gramjuth.name, gram.name) as  concat_values"))
                    ->join('gramjuth', 'gramjuth.id', '=', 'gram.gramjuth_id')
                    ->join('taluka', 'taluka.id', '=', 'gramjuth.taluka_id')
                    ->join('jilla', 'jilla.id', '=', 'taluka.jilla_id')
                    ->join('vibhag', 'vibhag.id', '=',  'jilla.vibhag_id')
                    ->join('prant', 'prant.id', '=', 'vibhag.prant_id')
                    ->where('gram.id', '=', $value->gram_id)
                    ->first();
                $name = isset($gram->concat_values) ? $gram->concat_values : 'No data found';
            } elseif ($value->role == 4) {
                $role = 'Vibhag User';
                $vibhag = Vibhag::select(DB::raw("CONCAT_WS(' > ', prant.name, vibhag.name) as concat_values"))
                    ->join('prant', 'prant.id', '=', 'vibhag.prant_id')
                    ->where('vibhag.id', '=', $value->vibhag_id)
                    ->first();
                $name = $vibhag->concat_values ? $vibhag->concat_values : '-';
            } elseif ($value->role == 5) {
                $role = 'Prant User';
                $prant = Prant::where('id', '=', $value->prant_id)->first();
                $name = $prant->name ? $prant->name : '-';
            } else {
                $role = '-';
                $name = '-';
            }
            $users[$key]->location_name = $name;
            $users[$key]->role_name = $role;
        }

        $title = 'Users';

        return view('users.index', compact('users', 'title', 'prantList', 'selPrant', 'selVibhag', 'selJilla', 'selTaluka', 'selGramjuth', 'selGram'));
    }

    /**
     * Show the form for creating new User.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Users';
        if (Auth::user()->role == 1) {
            $role = [
                '1' => 'Backend User',
                '2' => 'App User',
                '3' => 'Arogya Mitra User',
                '4' => 'Vibhag User',
                '5' => 'Prant User',
                '6' => 'Stockiest User'
            ];
        } else {
            $role = [
                '2' => 'App User',
                '3' => 'Arogya Mitra User'
            ];
        }

        $prant = Prant::where('status', '1')->pluck('name', 'id');
        $vibhags = Vibhag::where('status', '1')->pluck('name', 'id');
        $jilla = Jilla::where('status', '1')->pluck('name', 'id');
        $taluka = Taluka::where('status', '1')->pluck('name', 'id');
        $gramjuth = Gramjuth::where('status', '1')->pluck('name', 'id');
        $gram = Gram::where('status', '1')->pluck('name', 'id');
        $vibhagField = Auth::user()->prant_id ? Vibhag::where('prant_id', Auth::user()->prant_id)->where('status', '1')->pluck('name', 'id') : [];
        $jillaField = Auth::user()->vibhag_id ? Jilla::where('vibhag_id', Auth::user()->vibhag_id)->where('status', '1')->pluck('name', 'id') : [];
        $filledField = session('filled_values', []);
        $display = 'd-none';

        return view('users.create', compact('role', 'title', 'prant', 'vibhags', 'jilla', 'taluka', 'gramjuth', 'gram', 'vibhagField', 'jillaField', 'display'));
    }

    /**
     * Store a newly created User in storage.
     *
     * @param  \App\Http\Requests\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required',
            'mobile_no'     => ['required', 'numeric', 'digits:10'],
            'role'          => ['required', 'in:1,2,3,4,5,6']
        ], [
            'name.required'         => 'The name field is required.',
            'mobile_no.required'    => 'The mobile number field is required.',
            'mobile_no.digits'      => 'The mobile number should be 10 digits.',
            'role.required'         => 'The role field is required.',
        ]);

        if ($request->role == '1') {
            $request->validate([
                'email'    => [
                    'required',
                    'email',
                    Rule::unique('users', 'email')->ignore($request->user_id)->whereNull('deleted_at'),
                ],
                'password' => ['required', 'min:8', 'string', 'regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/'],
            ], [
                'email.required'    => 'The email field is required.',
                'email.email'       => 'The email address is invalid.',
                'password.required' => 'The password field is required.'
            ]);
        } else if ($request->role == '2' || $request->role == '6') {
            $request->validate([
                'prant_id'      => 'required',
                'vibhag_id'     => 'required',
                'jilla_id'      => 'required',
                'gramId'        => 'required',
                'password'      => ['required', 'min:8', 'string', 'regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/']
            ], [
                'prant_id.required'      => 'The prant field is required.',
                'vibhag_id.required'     => 'The vibhag field is required.',
                'jilla_id.required'      => 'The jilla field is required.',
                'password.required'      => 'The password field is required.'
            ]);
        } else if ($request->role == '3') {
            $request->validate([
                'prant_id'      => 'required',
                'vibhag_id'     => 'required',
                'jilla_id'      => 'required',
                'taluka_id'     => 'required',
                'gramjuth_id'   => 'required',
                'gram_id'       => 'required'
            ], [
                'prant_id.required'      => 'The prant field is required.',
                'vibhag_id.required'     => 'The vibhag field is required.',
                'jilla_id.required'      => 'The jilla field is required.',
                'taluka_id.required'     => 'The taluka field is required.',
                'gramjuth_id.required'   => 'The gramjuth field is required.',
                'gram_id.required'       => 'The gram field is required.'
            ]);
        } else if ($request->role == '4') {
            $request->validate([
                'prant_id'          => 'required',
                'vibhag_id'         => 'required',
                'email'        => [
                    'required',
                    'email',
                    Rule::unique('users', 'email')->ignore($request->user_id)->whereNull('deleted_at')
                ],
                'password'          => ['required', 'min:8', 'string', 'regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/']
            ], [
                'prant_id.required'      => 'The prant field is required.',
                'vibhag_id.required'     => 'The vibhag field is required.',
                'email.required'         => 'The email field is required.',
                'email.email'            => 'The email address is invalid.',
                'password.required'      => 'The password field is required.'
            ]);
        } else if ($request->role == '5') {
            $request->validate(
                [
                    'prant_id'         => 'required',
                    'email'    => [
                        'required',
                        'email',
                        Rule::unique('users', 'email')->ignore($request->user_id)->whereNull('deleted_at')
                    ],
                    'password'         => ['required', 'min:8', 'string', 'regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/']
                ],
                [
                    'prant_id.required' => 'The parent field is required.',
                    'email.required'    => 'The email field is required.',
                    'email.email'       => 'The email address is invalid.',
                    'password.required' => 'The password field is required.'
                ]
            );
        }

        $userArr = [];
        if ($request->get('role') == '1') {
            $userArr = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'mobile_no' => $request->mobile_no,
                'address' => $request->address,
                'password' => $request->password,
                'status' => $request->status
            ];
        } else if ($request->get('role') == '2' || $request->get('role') == '6') {
            $gram = implode(',', $request->gramId);
            $userArr = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'mobile_no' => $request->mobile_no,
                'address' => $request->address,
                'password' => $request->password,
                'prant_id' => $request->prant_id,
                'vibhag_id' => $request->vibhag_id,
                'jilla_id' => $request->jilla_id,
                'gram_id' => ltrim($gram, ','),
                'status' => $request->status
            ];
        } else if ($request->get('role') == '3') {
            $uniqueUser = User::where(['gram_id' => $request->gram_id, 'status' => 'Active', 'role' => '3'])->onlyTrashed('deleted_at')->count();
            if ($uniqueUser) {
                toastr()->error('The arogya mitra user already exists.');
                return Redirect()->back();
            }
            $userArr = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'mobile_no' => $request->mobile_no,
                'address' => $request->address,
                'password' => $request->password,
                'jilla_id' => $request->jilla_id,
                'gram_id' => $request->gram_id,
                'prant_id' => $request->prant_id,
                'vibhag_id' => $request->vibhag_id,
                'status' => $request->status
            ];
        } else if ($request->get('role') == '4') {
            $userArr = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'mobile_no' => $request->mobile_no,
                'address' => $request->address,
                'password' => $request->password,
                'prant_id' => $request->prant_id,
                'vibhag_id' => $request->vibhag_id,
                'status' => $request->status
            ];
        } else if ($request->get('role') == '5') {
            $userArr = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'mobile_no' => $request->mobile_no,
                'address' => $request->address,
                'password' => $request->password,
                'prant_id' => $request->prant_id,
                'status' => $request->status
            ];
        }

        $user = User::create($userArr);
        toastr()->success('Record saved successfully!');

        return redirect()->route('users.index', compact('user'));
    }

    /**
     * Show the form for editing User.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::user()->role != 1) {
            abort(404);
        }
        $usersList = User::findOrFail($id);
        if (is_null($usersList)) {
            abort(404);
        }
        $title = 'Users';
        if (Auth::user()->role == 1) {
            $role = [
                '1' => 'Backend User',
                '2' => 'App User',
                '3' => 'Arogya Mitra User',
                '4' => 'Vibhag User',
                '5' => 'Prant User',
                '6' => 'Stockiest User'
            ];
        } else {
            $role = [
                '2' => 'App User',
                '3' => 'Arogya Mitra User'
            ];
        }
        $prant = Prant::where('status', '1')->pluck('name', 'id');
        $vibhags = Vibhag::where('status', '1')->pluck('name', 'id');
        $jilla = Jilla::where('status', '1')->pluck('name', 'id');
        $taluka = Taluka::where('status', '1')->pluck('name', 'id');
        $gramjuth = Gramjuth::where('status', '1')->pluck('name', 'id');
        $gram = Gram::where('status', '1')->pluck('name', 'id');
        $display = 'd-none';
        $usersList = User::find($id);
        $emailId = '';
        if ($usersList->role == 2 || $usersList->role == 3 || $usersList->role == 6) {
            $emailId = 'd-none';
        }
        if ($usersList) {
            if ($usersList->role == '3') {
                $users = User::select('g.name as gam_name', 'gm.id as gramjuth_id', 't.id as taluka_id', 'j.id as jila_id', 'v.id as vibhag_id', 'users.*')
                    ->join('gram as g', 'g.id', 'users.gram_id')
                    ->join('gramjuth as gm', 'gm.id', 'g.gramjuth_id')
                    ->join('taluka as t', 't.id', 'gm.taluka_id')
                    ->join('jilla as j', 'j.id', 't.jilla_id')
                    ->join('vibhag as v', 'v.id', 'j.vibhag_id')->where('users.id', $id)->first();
            } elseif ($usersList->role == '2') {
                $users = User::where('users.id', $id)->first();
            } elseif ($usersList->role == '4') {
                $users = User::select('users.*', 'p.id as prant_id')
                    ->join('vibhag as v', 'v.id', 'users.vibhag_id')
                    ->join('prant as p', 'p.id', 'v.prant_id')
                    ->where('users.id', $id)->first();
            } elseif ($usersList->role == '1') {
                $users = User::where('users.id', $id)->first();
            } elseif ($usersList->role == '5') {
                $users = User::where('users.id', $id)->first();
            } else {
                $users = User::where('users.id', $id)->first();
            }
        }

        return view('users.edit', compact('role', 'gram', 'jilla', 'emailId', 'title', 'vibhags', 'taluka', 'gramjuth', 'users', 'display', 'prant'));
    }

    /**
     * Update User in storage.
     *
     * @param  \App\Http\Requests\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'name'                  => 'required',
            'mobile_no'             => ['required', 'numeric', 'digits:10'],
            'role'                  => ['required', 'in:1,2,3,4,5,6']
        ], [
            'name.required'         => 'The name field is required.',
            'mobile_no.required'    => 'The mobile number field is required.',
            'mobile_no.digits'      => 'The mobile number should be 10 digits.'
        ]);

        if ($request->role == '1') {
            $request->validate([
                'email'    => [
                    'required',
                    'email',
                    Rule::unique('users', 'email')->ignore($request->user_id)->whereNull('deleted_at'),
                ]
            ], [
                'email.required' => 'The email field is required.'
            ]);
        } else if ($request->role == '2' || $request->role == '6') {
            $request->validate([
                'prant_id'             => 'required',
                'vibhag_id'            => 'required',
                'jilla_id'             => 'required',
                'gramId'               => 'required'
            ], [
                'prant_id.required'    => 'The prant field is required.',
                'vibhag_id.required'   => 'The vibhag field is required.',
                'jilla_id.required'    => 'The jilla field is required.',
                'gramId.required'      => 'The gram field is required.'
            ]);
        } else if ($request->role == '3') {
            $request->validate([
                'prant_id'             => 'required',
                'vibhag_id'            => 'required',
                'jilla_id'             => 'required',
                'taluka_id'            => 'required',
                'gramjuth_id'          => 'required',
                'gram_id'              => 'required'
            ], [
                'prant_id.required'    => 'The prant field is required.',
                'vibhag_id.required'   => 'The vibhag field is required.',
                'jilla_id.required'    => 'The jilla field is required.',
                'taluka_id.required'   => 'The taluka field is required.',
                'gramjuth_id.required' => 'The gramjuth field is required.',
                'gram_id.required'     => 'The gram field is required.',
                'email.required'    => 'The email field is required.'
            ]);
        } elseif ($request->role == '4') {
            $request->validate([
                'prant_id'     => 'required',
                'vibhag_id'    => 'required',
                'email'        => [
                    'required',
                    'email',
                    Rule::unique('users', 'email')->ignore($request->user_id)->whereNull('deleted_at')
                ]
            ], [
                'prant_id.required'     => 'The prant field is required.',
                'vibhag_id.required'    => 'The vibhag field is required.',
                'role.required'         => 'The role field is required.',
                'email.required'        => 'The email field is required.'
            ]);
        } else if ($request->role == '5') {
            $request->validate([
                'prant_id' => 'required',
                'email'    => [
                    'required',
                    'email',
                    Rule::unique('users', 'email')->ignore($request->user_id)->whereNull('deleted_at')
                ]
            ], [
                'prant_id.required' => 'The prant field is required.',
                'email.required'    => 'The email field is required.'
            ]);
        }

        if ($request->get('role') == '1') {
            $userArr = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'mobile_no' => $request->mobile_no,
                'address' => $request->address,
                'status' => $request->status
            ];
        } else if ($request->get('role') == '2' || $request->get('role') == '6') {
            $gram = implode(',', $request->gramId);
            $userArr = [
                'name' => $request->name,
                'role' => $request->role,
                'mobile_no' => $request->mobile_no,
                'address' => $request->address,
                'prant_id' => $request->prant_id,
                'vibhag_id' => $request->vibhag_id,
                'jilla_id' => $request->jilla_id,
                'gram_id' => ltrim($gram, ','),
                'status' => $request->status
            ];
        } else if ($request->get('role') == '3') {
            $role = User::where(['role' => '3', 'gram_id' => $request->gram_id, 'status' => 'Active'])->where('id', '!=', $request->get('user_id'))->count();
            if ($role) {
                $request->validate(['gram_id' => 'unique:users,gram_id']);
                return redirect()->route('users.edit', ['user' => $request->get('user_id')])->with(['gram_id' => 'The gram field is already exists']);
            }
            $userArr = [
                'name' => $request->name,
                'role' => $request->role,
                'mobile_no' => $request->mobile_no,
                'address' => $request->address,
                'prant_id' => $request->prant_id,
                'vibhag_id' => $request->vibhag_id,
                'jilla_id' => $request->jilla_id,
                'gram_id' => $request->gram_id,
                'status' => $request->status
            ];
        } else if ($request->get('role') == '4') {
            $userArr = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'mobile_no' => $request->mobile_no,
                'address' => $request->address,
                'prant_id' => $request->prant_id,
                'vibhag_id' => $request->vibhag_id,
                'status' => $request->status
            ];
        } else if ($request->get('role') == '5') {
            $userArr = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'mobile_no' => $request->mobile_no,
                'address' => $request->address,
                'prant_id' => $request->prant_id,
                'status' => $request->status
            ];
        }

        User::where('id', $request->get('user_id'))->update($userArr);
        toastr()->success('Record update successfully!');

        return redirect()->route('users.index');
    }

    /**
     * Display User.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $title = 'Users';
        $userList = User::find($id);
        $gramArray = [];
        if ($userList) {
            if ($userList->role == '3') {
                $user = User::select('p.name as p_name', 'g.name as gam_name', 'gm.name as grm_name', 't.name as tk_name', 'j.name as jila_name', 'v.name as vi_name', 'users.status', 'users.*')
                    ->join('gram as g', 'g.id', 'users.gram_id')
                    ->join('gramjuth as gm', 'gm.id', 'g.gramjuth_id')
                    ->join('taluka as t', 't.id', 'gm.taluka_id')
                    ->join('jilla as j', 'j.id', 't.jilla_id')
                    ->join('vibhag as v', 'v.id', 'j.vibhag_id')
                    ->join('prant as p', 'p.id', 'v.prant_id')
                    ->where('users.id', $id)->first();
            } elseif ($userList->role == '2' || $userList->role == '6') {
                $user = User::select('p.name as p_name', 'v.name as vi_name', 'j.name as jila_name', 'users.*')
                    ->join('jilla as j', 'j.id', 'users.jilla_id')
                    ->join('vibhag as v', 'v.id', 'j.vibhag_id')
                    ->join('prant as p', 'p.id', 'v.prant_id')
                    ->where('users.id', $id)->first();
                $gramArray = Gram::select('name')->whereIn('id', explode(',', $userList->gram_id))->get()->toArray();
            } elseif ($userList->role == '4') {
                $user = User::select('p.name as p_name', 'v.name as v_name', 'users.*')
                    ->join('vibhag as v', 'v.id', 'users.vibhag_id')
                    ->join('prant as p', 'p.id', 'users.prant_id')
                    ->where('users.id', $id)->first();
            } elseif ($userList->role == '5') {
                $user = User::select('p.name as p_name', 'users.*')
                    ->join('prant as p', 'p.id', 'users.prant_id')
                    ->where('users.id', $id)->first();
            } else {
                $user = User::where('users.id', $id)->first();
            }
            return view('users.show', compact('user', 'title', 'gramArray'));
        }
    }

    /**
     * Remove User from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user) {
            $medicine_request = MedicineRequest::where('arogyamitra_id', $id)->count();
            $medicine_stock = MedicineStock::where('arogyamitra_id', $id)->count();
            $medicine_track = MedicineTrack::where('arogyamitra_id', $id)->count();

            if ($medicine_request == 0 && $medicine_stock == 0 && $medicine_track == 0) {
                User::where('id', $id)->update(['deleted_at' => Carbon::now(), 'status' => 'Deactive']);
                toastr()->success('Record Deleted successfully!');
                return redirect()->route('users.index');
            } else {
                toastr()->info("This record can't deleted because medicine records is available.");
                return redirect()->back();
            }
        }
    }

    public function password()
    {
        $title = 'Change Password';
        return view('users.password', compact('title'));
    }

    public function storePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|same:confirm_password|string|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            'confirm_password' => 'required|min:8',
        ], [
            'current_password.required' => 'The password field is required.',
            'new_password.required' => 'The password field is required.',
            'confirm_password.required' => 'The password field is required.',
        ]);

        $user = User::find(Auth::id());
        if (!Hash::check($request->get('current_password'), $user->password)) {
            toastr()->error('Invalid old password!');
            return redirect()->back();
        } elseif ($request->get('current_password') == $request->get('new_password')) {
            toastr()->error('Current and new password should not be the same!');
            return redirect()->back();
        } else {
            $user->update(['password' => Hash::make($request->get('new_password'))]);
            $user->save();
            toastr()->success('Password changed successfully!');
            return redirect()->route('home');
        }
    }

    public function profile()
    {
        $title = 'Users Profile';
        $userRole = Auth::user()->role;
        $id = Auth::user()->id;
        if ($userRole == '4') {
            $user = User::select('v.name as v_name', 'users.*')
                ->join('vibhag as v', 'v.id', 'users.vibhag_id')
                ->where('users.id', $id)->first();
        } elseif ($userRole == '5') {
            $user = User::select('p.name as p_name', 'users.*')
                ->join('prant as p', 'p.id', 'users.prant_id')
                ->where('users.id', $id)->first();
        } else {
            $user = User::select('users.*')->where('users.id', $id)->first();
        }
        return view('users.profile', compact('title', 'user'));
    }

    public function getVibhagList(Request $request)
    {
        return Vibhag::where(['prant_id' => $request->query('prantId'), 'status' => '1'])->get()->toArray();
    }

    public function getJillaList(Request $request)
    {
        return Jilla::where('vibhag_id', $request->query('vibhagId'))->where('status', '1')->get()->toArray();
    }

    public function getTalukaList(Request $request)
    {
        return Taluka::where('jilla_id', $request->query('jillaId'))->where('status', '1')->get()->toArray();
    }

    public function getGramjuthList(Request $request)
    {
        return Gramjuth::where('taluka_id', $request->query('talukaId'))->where('status', '1')->get()->toArray();
    }

    public function getGramList(Request $request)
    {
        return Gram::where('gramjuth_id', $request->query('gramjuthId'))->where('status', '1')->get()->toArray();
    }

    public function getJillaGramList(Request $request)
    {
        return Gram::select('gram.name', 'gram.id')
            ->where('jilla.id', $request->jillaId)
            ->where('gram.status', '1')
            ->join('gramjuth', 'gramjuth.id', '=', 'gram.gramjuth_id')
            ->join('taluka', 'taluka.id', '=', 'gramjuth.taluka_id')
            ->join('jilla', 'jilla.id', '=', 'taluka.jilla_id')
            ->get()->toArray();
    }

    public function checkExistsEmail(Request $request)
    {
        if ($request->mode == "update") {
            $userEmail = User::where(['email' => $request->get('user_email'), 'status' => 'Active'])->where('id', '!=', $request->userIds)->whereNull('deleted_at')->count();
        } else {
            $userEmail = User::where(['email' => $request->get('user_email'), 'status' => 'Active'])->whereNull('deleted_at')->count();
        }

        if ($userEmail) {
            return json_encode([
                'status' => '1',
                'messages' => 'User email already exists, please enter another email.'
            ]);
        } else {
            return json_encode([
                'status' => '0',
                'messages' => ''
            ]);
        }
    }

    public function checkExistsMobile(Request $request)
    {
        if ($request->mode == "update") {
            $userMobile = User::where(['mobile_no' => $request->get('user_mobile'), 'status' => 'Active'])->where('id', '!=', $request->userIds)->whereNull('deleted_at')->count();
        } else {
            $userMobile = User::where(['mobile_no' => $request->get('user_mobile'), 'status' => 'Active'])->whereNull('deleted_at')->count();
        }

        if ($userMobile) {
            return json_encode([
                'status' => '1',
                'messages' => 'User mobile already exists, please enter another mobile.'
            ]);
        } else {
            return json_encode([
                'status' => '0',
                'messages' => 'error'
            ]);
        }
    }

    public function changeUserStatus(Request $request)
    {
        $gram_id = $request->gram_id;
        if ($request->get('user_status') == 'Active') {
            $status = 'Deactive';
        } else {
            $status = 'Active';
            if ($gram_id) {
                $activeGramId = User::where('gram_id', $gram_id)->where('status',  $status)->count();
                if ($activeGramId) {
                    toastr()->info('gram status is already active. Status change failed.');
                    return json_encode([
                        'status' => '1'
                    ]);
                }
            }
        }
        $userStatus = User::where('id', $request->get('user_id'))->update(['status' => $status]);
        if ($userStatus) {
            toastr()->success('Status ' . strtolower($status) . ' successfully!');
            return json_encode([
                'status' => '1'
            ]);
        } else {
            toastr()->info('Status changed failed!');
            return json_encode([
                'status' => '0'
            ]);
        }
    }

    public function submitForm(Request $request)
    {
        $prantList = $jillaField = [];

        return view('your.form.view', [
            'prantList' => $prantList,
            'jillaField' => $jillaField,
            'selectedRole' => $request->input('role'),
            'selectedPrant' => $request->input('prant_id'),
            'selectedVibhag' => $request->input('vibhag_id'),
            'selectedJilla' => $request->input('jilla_id'),
        ]);
    }
}
