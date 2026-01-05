<?php

namespace App\Http\Controllers;

use App\Models\{
    Beneficiary,
    Gram,
    Gramjuth,
    Jilla,
    Prant,
    Taluka,
    User,
    Vibhag
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BeneficiariesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $title = 'Beneficiaries';
        $startDate = date('d-m-Y', strtotime('-30 day'));
        $endDate = date('d-m-Y', strtotime('now'));

        if ($request->isMethod('post')) {
            $dateRange = explode('to', $request->get('date_range'));
            if (count($dateRange) == 1) {
                $startDate = $endDate = date('d-m-Y', strtotime(rtrim($dateRange[0])));
            } else {
                if (isset($dateRange[0]) && isset($dateRange[1])) {
                    $startDate = date('d-m-Y', strtotime(rtrim($dateRange[0])));
                    $endDate = date('d-m-Y', strtotime(ltrim($dateRange[1])));
                }
            }
        }

        $selPrant = $request->get('prant_id') ?? null;
        $selVibhag = $request->get('vibhag_id') ?? null;
        $selJilla = $request->get('jilla_id') ?? null;
        $selTaluka = $request->get('taluka_id') ?? null;
        $selGramjuth = $request->get('gramjuth_id') ?? null;
        $role = Auth::user()->role;

        $vibhags = $prant = [];
        if ($role == 1) {
            $prant = Prant::where(['status' => '1'])->pluck('name', 'id');
            $vibhags = Vibhag::where(['status' => '1', 'prant_id' => $selPrant])->pluck('name', 'id');
            $jilla = Jilla::where(['vibhag_id' => $selVibhag, 'status' => '1'])->pluck('name', 'id');
        } elseif ($role == 4) { // vibhag user
            $vibhagId = Auth::user()->vibhag_id;
            $jilla = Jilla::where(['status' => '1', 'vibhag_id' => $vibhagId])->pluck('name', 'id');
        } else { // prant user
            $prantId = Auth::user()->prant_id;
            $vibhags = Vibhag::where(['status' => '1', 'prant_id' => $prantId])->pluck('name', 'id');
            $jilla = Jilla::where(['vibhag_id' => $selVibhag, 'status' => '1'])->pluck('name', 'id');
        }

        $taluka = Taluka::where(['jilla_id' => $selJilla, 'status' => '1'])->pluck('name', 'id');
        $gramjuth = Gramjuth::where(['taluka_id' => $selTaluka, 'status' => '1'])->pluck('name', 'id');
        $patientResp = $response = [];
        $type = $request->get('filterType');
        $total = 0;
        //$numDays = User::dateDiffInDays($startDate, $endDate);

        switch ($type) {
            case 'vibhag':
                $getData = Vibhag::select('id', 'name')->where(['prant_id' => $selPrant, 'status' => '1'])->get()->toArray();
                foreach ($getData as $key => $data) {
                    $beneficiaryCount = User::getArogyaMitraIdsByFilterType("vibhag", $startDate, $endDate, $selPrant, $data['id'], null, null, null, null);
                    //$beneficiary = $beneficiaryCount ? ($numDays ? round($beneficiaryCount / $numDays, 2) : $beneficiaryCount) : 0;
                    $beneficiary = $beneficiaryCount ? : 0;
                    $patientResp[$key]['name'] = $data['name'];
                    $patientResp[$key]['number_of_beneficiary'] = $beneficiary;
                    $total += $patientResp[$key]['number_of_beneficiary'];
                }
                $response['location_name'] = 'Vibhag Name';
                break;
            case 'jilla':
                $getData = Jilla::select('id', 'name')->where(['vibhag_id' => $selVibhag, 'status' => '1'])->get()->toArray();
                foreach ($getData as $key => $data) {
                    $beneficiaryCount = User::getArogyaMitraIdsByFilterType("jilla", $startDate, $endDate, null, $selVibhag, $data['id'], null, null, null);
                    //$beneficiary = $beneficiaryCount ? ($numDays ? round($beneficiaryCount / $numDays, 2) : $beneficiaryCount) : 0;
                    $beneficiary = $beneficiaryCount ? : 0;
                    $patientResp[$key]['name'] = $data['name'];
                    $patientResp[$key]['number_of_beneficiary'] = $beneficiary;
                    $total += $patientResp[$key]['number_of_beneficiary'];
                }
                $response['location_name'] = 'Jilla Name';
                break;
            case 'taluka':
                $getData = Taluka::select('id', 'name')->where(['jilla_id' => $selJilla, 'status' => '1'])->get()->toArray();
                foreach ($getData as $key => $data) {
                    $beneficiaryCount = User::getArogyaMitraIdsByFilterType("taluka", $startDate, $endDate, null, null, $selJilla, $data['id'], null, null);
                    //$beneficiary = $beneficiaryCount ? ($numDays ? round($beneficiaryCount / $numDays, 2) : $beneficiaryCount) : 0;
                    $beneficiary = $beneficiaryCount ? : 0;
                    $patientResp[$key]['name'] = $data['name'];
                    $patientResp[$key]['number_of_beneficiary'] = $beneficiary;
                    $total += $patientResp[$key]['number_of_beneficiary'];
                }
                $response['location_name'] = 'Taluka Name';
                break;
            case 'gramjuth':
                $getData = Gramjuth::select('id', 'name')->where(['taluka_id' => $selTaluka, 'status' => '1'])->get()->toArray();
                foreach ($getData as $key => $data) {
                    $beneficiaryCount = User::getArogyaMitraIdsByFilterType("gramjuth", $startDate, $endDate, null, null, null, $selTaluka, $data['id'], null);
                    //$beneficiary = $beneficiaryCount ? ($numDays ? round($beneficiaryCount / $numDays, 2) : $beneficiaryCount) : 0;
                    $beneficiary = $beneficiaryCount ? : 0;
                    $patientResp[$key]['name'] = $data['name'];
                    $patientResp[$key]['number_of_beneficiary'] = $beneficiary;
                    $total += $patientResp[$key]['number_of_beneficiary'];
                }
                $response['location_name'] = 'Gramjuth Name';
                break;
            case 'gram':
                $getData = Gram::select('id', 'name')->where(['gramjuth_id' => $selGramjuth, 'status' => '1'])->get()->toArray();
                foreach ($getData as $key => $data) {
                    $beneficiaryCount = User::getArogyaMitraIdsByFilterType("gram", $startDate, $endDate, null, null, null, null, $selGramjuth, $data['id']);
                    //$beneficiary = $beneficiaryCount ? ($numDays ? round($beneficiaryCount / $numDays, 2) : $beneficiaryCount) : 0;
                    $beneficiary = $beneficiaryCount ? : 0;
                    $patientResp[$key]['name'] = $data['name'];
                    $patientResp[$key]['number_of_beneficiary'] = $beneficiary;
                    $total += $patientResp[$key]['number_of_beneficiary'];
                }
                $response['location_name'] = 'Gram Name';
                break;
            default:
                if ($role == 1) { // admin user
                    $getData = Prant::select('id', 'name')->where(['status' => '1'])->get()->toArray();
                    foreach ($getData as $key => $data) {
                        $beneficiaryCount = User::getArogyaMitraIds($startDate, $endDate, $role, $data['id'], null, null);
                        //$beneficiary = $beneficiaryCount ? ($numDays ? round($beneficiaryCount / $numDays, 2) : $beneficiaryCount) : 0;
                        $beneficiary = $beneficiaryCount ? : 0;
                        $patientResp[$key]['name'] = $data['name'];
                        $patientResp[$key]['number_of_beneficiary'] = $beneficiary;
                        $total += $patientResp[$key]['number_of_beneficiary'];
                    }
                    $response['location_name'] = 'Prant Name';
                    break;
                } elseif ($role == 4) { // vibhag user
                    $getData = Jilla::select('id', 'name')->where(['status' => '1', 'vibhag_id' => $vibhagId])->get()->toArray();
                    foreach ($getData as $key => $data) {
                        $beneficiaryCount = User::getArogyaMitraIds($startDate, $endDate, $role, null, $vibhagId, $data['id']);
                        //$beneficiary = $beneficiaryCount ? ($numDays ? round($beneficiaryCount / $numDays, 2) : $beneficiaryCount) : 0;
                        $beneficiary = $beneficiaryCount ? : 0;
                        $patientResp[$key]['name'] = $data['name'];
                        $patientResp[$key]['number_of_beneficiary'] = $beneficiary;
                        $total += $patientResp[$key]['number_of_beneficiary'];
                    }
                    $response['location_name'] = 'Jilla Name';
                    break;
                } else { // prant user
                    $getData = Vibhag::select('id', 'name')->where(['status' => '1', 'prant_id' => $prantId])->get()->toArray();
                    foreach ($getData as $key => $data) {
                        $beneficiaryCount = User::getArogyaMitraIds($startDate, $endDate, $role, $prantId, $data['id'], null);
                        //$beneficiary = $beneficiaryCount ? ($numDays ? round($beneficiaryCount / $numDays, 2) : $beneficiaryCount) : 0;
                        $beneficiary = $beneficiaryCount ? : 0;
                        $patientResp[$key]['name'] = $data['name'];
                        $patientResp[$key]['number_of_beneficiary'] = $beneficiary;
                        $total += $patientResp[$key]['number_of_beneficiary'];
                    }
                    $response['location_name'] = 'Vibhag Name';
                    break;
                }
        }

        return view('beneficiaries.index', compact('title', 'prant', 'vibhags', 'jilla', 'taluka', 'gramjuth', 'selPrant', 'selVibhag', 'selJilla', 'selTaluka', 'selGramjuth', 'patientResp', 'response', 'startDate', 'endDate', 'type', 'total'));
    }
}
