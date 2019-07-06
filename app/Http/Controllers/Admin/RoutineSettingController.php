<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\RoutineSetting;
use Auth;
use DB;
use History;
use Response;
use stdClass;

class RoutineSettingController extends Controller
{
    /**
     * Only administartor authorize to access this section.
     *
     * @var string
     */
    protected $guard = 'admins';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:admins');
    }

    public function index() {
        return view('admin.routine.setting.index', [
            'shareholding' => RoutineSetting::find(1),
            'payment' => RoutineSetting::find(2),
        ]);
    }

    public function update($id, Request $request) {
        $setting = RoutineSetting::find($id);
        $action = $request->input('action');
        $status = $request->input('status') === 'true';
    
        DB::transaction(function() use ($setting, $action, $status) {
            switch ($action) {
                case 'calculate':
                    $setting->calculate_status = !$status;

                    if ($status) {
                        $setting->approve_status = false;
                        $setting->save_status = false;
                    }
                    break;
                case 'approve':
                    $setting->approve_status = !$status;

                    if ($status) {
                        $setting->save_status = false;
                    }
                    break;
                default:
                    $setting->save_status = !$status;
                    break;
            }

            $setting->save();

            History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'แก้ไขการตั้งค่าการนำส่งข้อมูลหักบัญชีเงินเดือน');
        });
        
        $setting = RoutineSetting::find($id);
        $result = new stdClass();
        $result->calculate_status = boolval($setting->calculate_status);
        $result->approve_status = boolval($setting->approve_status);
        $result->save_status = boolval($setting->save_status);

        return Response::json($result);
    }
}
