<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, Validator};

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validated_data = Validator::make(
            $request->all(),
            [
                'mobile_no' => 'required|numeric|gt:0|min:10',
                'password' => 'required'
            ],
            [
                'mobile_no.required' => 'મોબાઈલ નંબર અનિવાર્ય છે.',
                'password.required' => 'પાસવર્ડ અનિવાર્ય છે.',
                'mobile_no.numeric' => 'મોબાઈલ નંબર સંખ્યાત્મક હોવો જોઈએ.',
                'mobile_no.gt' => 'મોબાઈલ નંબર 0 થી મોટો હોવો જોઈએ.',
                'mobile_no.min' => 'મોબાઈલ નંબર 10 અંકનો હોવો જોઈએ.'
            ]
        );
        if ($validated_data->fails()) {
            return response()->json([
                'status'  => '0',
                'result'  => 'failure',
                'message' => $validated_data->errors()->all()
            ], 422);
        }

        // get user details by mobile no
        $user = User::select('users.*', 'j.name as jilla_name')->join('jilla as j', 'j.id', 'users.jilla_id')
            ->where(['users.mobile_no' => $request->get('mobile_no')])
            ->where(function ($q) {
                $q->where('users.role', '=', '2')
                    ->orWhere('users.role', '=', '6');
            })
            ->first();


        // if user is available
        if ($user) {
            if ($user->status == "Deactive") {
                return response()->json([
                    'status'  => '0',
                    'result'  => 'failure',
                    'message' => trans('messages.deactive_account')
                ], 401);
            }
            // if request password and user password match then continue
            if (Hash::check($request->get('password'), $user->password)) {
                $token = $user->createToken('api')->accessToken;
                if ($user->role == 2) {
                    $userData['arogya_mitra_id'] = $user->id;
                } else {
                    $userData['stockiest_id'] = $user->id;
                }
                $userData['name'] = $user->name;
                $userData['mobile_no'] = $user->mobile_no;
                $userData['email'] = $user->email ? $user->email : '';
                $userData['status'] = $user->status;
                $userData['jilla_id'] = $user->jilla_id;
                $userData['jilla_name'] = $user->jilla_name;
                $userData['address'] = $user->address ? $user->address : '';
                return response()->json([
                    'status'    => '1',
                    'result'    => 'success',
                    'userData'  => $userData,
                    'token'     => $token,
                    'message'   => trans('messages.welcome')
                ], 200);
            } else {
                return response()->json([
                    'status'    => '0',
                    'result'    => 'failure',
                    'message'   => trans('messages.unauthorized_user')
                ], 401);
            }
        } else {
            return response()->json([
                'status'    => '0',
                'result'    => 'failure',
                'message'   => trans('messages.mobile_no_not_found')
            ], 401);
        }
    }

    public function logout()
    {
        try {
            auth('api')->user()->token()->revoke();

            return response()->json([
                'status'   => '1',
                'result'   => 'success',
                'message'  => trans('messages.logged_out')
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status'   => '0',
                'result'   => 'failure',
                'message'  => trans('messages.fails')
            ], 400);
        }
    }
}
