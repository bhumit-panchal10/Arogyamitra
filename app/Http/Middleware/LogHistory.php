<?php

namespace App\Http\Middleware;

use App\Models\LogHistory as ModelsLogHistory;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogHistory
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $url = request()->route()->getName();
        $model = substr($url, 0, strpos($url, "."));
        $userId = $request->user_id;
        // Determine the action based on the HTTP method and route

        if ($model == 'activeLog') {
            $logMessage = 'Active Log';
        } elseif ($url == 'home') {
            $action = 'Dashboard';
            $logMessage = $model . ' ' . $action;
        } elseif ($model == 'profile') {
            $action = 'Open my';
            $logMessage = $action . ' ' . $model;
        } elseif ($model == 'users') {
            if ($request->isMethod('post')) {
                switch ($request->status) {
                    case 'Active':
                        switch ($request->role) {
                            case '1':
                                $action = 'backend Active filtered';
                                break;
                            case '2':
                                $action = 'App Active filtered';
                                break;
                            case '3':
                                $action = 'Arogyamitra Active filtered';
                                break;
                            case '4':
                                $action = 'vibhag Active filtered';
                                break;
                            case '5':
                                $action = 'prant Active filtered';
                                break;
                            default:
                                $action = 'Active filtered';
                        }
                        break;
                    case 'Deactive':
                        switch ($request->role) {
                            case '1':
                                $action = 'backend Deactive filtered';
                                break;
                            case '2':
                                $action = 'App Deactive filtered';
                                break;
                            case '3':
                                $action = 'Arogyamitra Deactive filtered';
                                break;
                            case '4':
                                $action = 'vibhag Deactive filtered';
                                break;
                            case '5':
                                $action = 'prant Deactive filtered';
                                break;
                            default:
                                $action = 'Deactive filtered';
                        }
                        break;
                    default:
                        if ($request->role == '') {
                            $action = 'click submit button';
                        } else {
                            if ($request->role == '1') {
                                $roleUser = 'backend';
                            } elseif ($request->role == '2') {
                                $roleUser = 'app';
                            } elseif ($request->role == '3') {
                                $roleUser = 'Arogyamitra';
                            } elseif ($request->role == '4') {
                                $roleUser = 'vibhag';
                            } elseif ($request->role == '5') {
                                $roleUser = 'prant';
                            }
                            $action = ucfirst($roleUser) . ' filtered';
                        }
                }
            } elseif ($request->isMethod('get')) {
                if ($url == 'users.create') {
                    $action = 'create';
                } elseif ($url == 'users.store') {
                    $action = 'Record added!';
                } elseif ($url == 'users.edit') {
                    $action = 'edit';
                } elseif ($url == 'users.show') {
                    $action = 'id ' . $userId . ' show';
                } else {
                    $action = 'list';
                }
            } elseif ($request->isMethod('delete')) {
                $action = 'Record Deleted successfully!';
            } elseif ($request->isMethod('put') || $request->isMethod('patch')) {
                $action = 'id ' . $userId . ' update';
            }
            // Create a log message based on the action
            $logMessage = $model . ' ' . $action;
        } elseif ($model == 'medicines' || $model == 'report') {
            if ($request->isMethod('post')) {
                if ($request->filterType == '' && $request->prant_id == '' && $request->vibhag_id == '' && $request->jilla_id == '' && $request->taluka_id == '' && $request->gramjuth_id == '' && $request->gram_id == '') {
                    $action = 'in clicked submit button';
                } elseif (in_array($request->filterType, ['prant', 'vibhag', 'jilla', 'taluka', 'gramjuth', 'gram', 'datepicker'])) {
                    if ($request->filterType == 'prant' && $request->prant_id != '') {
                        $action = ', medicines stock is filtered under prant';
                    } elseif ($request->filterType == 'vibhag' && $request->prant_id != '' && $request->vibhag_id != '') {
                        $action = ', medicines stock is filtered under vibhag';
                    } elseif ($request->filterType == 'jilla' && $request->prant_id != '' && $request->vibhag_id != '' && $request->jilla_id != '') {
                        $action = ', medicines stock is filtered under jilla';
                    } elseif ($request->filterType == 'taluka' && $request->prant_id != '' && $request->vibhag_id != '' && $request->jilla_id != '' && $request->taluka_id != '') {
                        $action = ', medicines stock is filtered under taluka';
                    } elseif ($request->filterType == 'gramjuth' && $request->prant_id != '' && $request->vibhag_id != '' && $request->jilla_id != '' && $request->taluka_id != '' && $request->gramjuth_id != '') {
                        $action = ', medicines stock is filtered under gramjuth';
                    } elseif ($request->filterType == 'gram' && $request->prant_id != '' && $request->vibhag_id != '' && $request->jilla_id != '' && $request->taluka_id != '' && $request->gramjuth_id != '' && $request->gram_id != '') {
                        $action = ', medicines stock is filtered under gram';
                    } elseif ($request->filterType == 'datepicker' && $request->prant_id == '' && $request->vibhag_id == '' && $request->jilla_id == '' && $request->taluka_id == '' && $request->gramjuth_id == '' && $request->gram_id == '') {
                        $action = ', medicines stock is filtered under datepicker';
                    } elseif ($request->filterType == 'datepicker' && $request->prant_id == '' && $request->vibhag_id == '' && $request->jilla_id == '' && $request->taluka_id == '' && $request->gramjuth_id == '' && $request->gram_id == '') {
                        $action = ', medicines stock is filtered under date wise gram';
                    } elseif ($request->filterType == 'datepicker' && $request->prant_id == '' && $request->vibhag_id == '' && $request->jilla_id == '' && $request->taluka_id == '' && $request->gramjuth_id == '') {
                        $action = ', medicines stock is filtered under date wise gramjuth';
                    } elseif ($request->filterType == 'datepicker' && $request->prant_id == '' && $request->vibhag_id == '' && $request->jilla_id == '' && $request->taluka_id == '') {
                        $action = ', medicines stock is filtered under date wise taluka';
                    } elseif ($request->filterType == 'datepicker' && $request->prant_id == '' && $request->vibhag_id == '' && $request->jilla_id == '') {
                        $action = ', medicines stock is filtered under date wise jilla';
                    } elseif ($request->filterType == 'datepicker' && $request->prant_id == '' && $request->vibhag_id == '') {
                        $action = ', medicines stock is filtered under date wise vibhag';
                    } elseif ($request->filterType == 'datepicker' && $request->prant_id != '') {
                        $action = ', medicines stock is filtered under date wise prant';
                    }
                } else {
                    $action = 'Record added!';
                }
            } elseif ($request->isMethod('get')) {
                if ($url == 'medicines.create') {
                    $action = 'create form';
                } elseif ($url == 'medicines.edit') {
                    $action = 'edit';
                } elseif ($url == 'medicines.show') {
                    $action = ' show';
                } else {
                    if ($model == 'report') {
                        $action = 'medicines stock list';
                    } else {
                        $action = 'list';
                    }
                }
            } elseif ($request->isMethod('delete')) {
                $action = 'deleted successfully!';
            } elseif ($request->isMethod('put') || $request->isMethod('patch')) {
                if ($request->status == '0') {
                    $action = 'status Deactive';
                } elseif ($request->status == '1') {
                    $action = 'status Active';
                } else {
                    $action = 'update';
                }
            }
            // Create a log message based on the action
            $logMessage = $model . ' ' . $action;
        } elseif ($model == 'prant' || $model == 'vibhag' || $model == 'jilla' || $model == 'taluka' || $model == 'gramjuth' || $model == 'grams') {
            if ($request->isMethod('post')) {
                $action = 'Record added!';
            } elseif ($request->isMethod('get')) {
                if ($url == $model . '.create') {
                    $action = 'create form';
                } elseif ($url == $model . '.edit') {
                    $action = 'edit';
                } elseif ($url == $model . '.show') {
                    $action = ' show';
                } else {
                    $action = 'list';
                }
            } elseif ($request->isMethod('delete')) {
                $action = 'deleted successfully!';
            } elseif ($request->isMethod('put') || $request->isMethod('patch')) {
                $action = 'update';
            }
            // Create a log message based on the action
            $logMessage = $model . ' ' . $action;
        } elseif ($model == 'medicines-stock') {
            if ($request->role == '') {
                $action = 'click submit button';
            } else {
                if ($request->role == '1') {
                    $roleUser = 'backend';
                } elseif ($request->role == '2') {
                    $roleUser = 'app';
                } elseif ($request->role == '3') {
                    $roleUser = 'Arogyamitra';
                } elseif ($request->role == '4') {
                    $roleUser = 'vibhag';
                } elseif ($request->role == '5') {
                    $roleUser = 'prant';
                }
                $action = ucfirst($roleUser) . ' filtered';
            }
        } elseif ($model == 'medicineRequest') {
            if ($request->status == '2' && $request->app_user_id != '' && $request->medicine_id != '') {
                $logMessage = 'click Accept button';
            } elseif ($request->status == '0' && $request->app_user_id != '' && $request->medicine_id != '') {
                $logMessage = 'click Reject button';
            } elseif ($request->status == '0') {
                $logMessage = 'Rejected ' . $model . ' filtered';
            } elseif ($request->status == '1') {
                $logMessage = 'Pending ' . $model . ' filtered';
            } elseif ($request->status == '2') {
                $logMessage = 'Accepted ' . $model . ' filtered';
            } else {
                $logMessage = $model . ' list';
            }
        } else {
            $logMessage = json_encode($request->all());
        }

        $LogHistory = [
            'method' => $request->server('REQUEST_METHOD'),
            'request_para' => $logMessage, // Use json_encode to store parameters as JSON
            'request_url' => $request->server('REDIRECT_URL'),
            'ip_address' => $request->ip(), // Capture the client's IP address
            'user_agent' => $request->header('User-Agent'), // Capture the user agent (browser)
            'created_at' => now(), // Add current timestamp
            'updated_at' => now(), // Add current timestamp
            'user_id' => Auth::user()->role,
        ];

        // Delete records older than 30 days
        \App\Models\LogHistory::where('created_at', '<', now()->subDays(30))->delete();

        // Insert the current activity log entry
        ModelsLogHistory::InsertGetId($LogHistory);
        return $next($request);
    }
}
