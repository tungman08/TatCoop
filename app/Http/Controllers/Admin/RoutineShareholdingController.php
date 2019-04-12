<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Member;
use App\RoutineSetting;
use App\RoutineShareholding;
use App\RoutineShareholdingDetail;
use App\Shareholding;
use Validator;
use DB;
use Diamond;
use Excel;
use PHPExcel_Shared_Date as ExcelDate;
use stdClass;

class RoutineShareholdingController extends Controller
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
        $this->middleware('auth:admins', ['except' => 'getUnauthorize']);
    }

    public function index() {
        $setting = RoutineSetting::find(1);

        if ($setting->save_status == true) {
            $this->updateStatus();
        }
        
        $routines = RoutineShareholding::orderBy('calculated_date', 'desc')
            ->get();

        return view('admin.routine.shareholding.index', [
            'routines' => $routines
        ]);
    }
    
    public function show($id) {
        $routine = RoutineShareholding::find($id);
        $details = RoutineShareholding::join('routine_shareholding_details', 'routine_shareholding_details.routine_shareholding_id', '=', 'routine_shareholdings.id')
            ->join('members', 'routine_shareholding_details.member_id', '=', 'members.id')
            ->join('profiles', 'profiles.id', '=', 'members.profile_id')
            ->leftJoin('shareholdings', 'shareholdings.member_id', '=', 'members.id')
            ->where('routine_shareholdings.id', $id)
            ->Where(function($query) use ($routine) {
                $query->whereDate('shareholdings.pay_date', '<=', $routine->calculated_date->endOfMonth())
                    ->orWhereNull('shareholdings.pay_date'); 
            })
            ->groupBy('routine_shareholding_details.id',
                'routine_shareholding_details.status',
                'members.id',
                'profiles.name',
                'profiles.lastname',
                'routine_shareholding_details.pay_date',
                'members.shareholding')
            ->orderBy('membercode')
            ->select([
                'routine_shareholding_details.id as id',
                'routine_shareholding_details.status as status',
                DB::raw("LPAD(members.id, 5, '0') as membercode"),
                DB::raw("CONCAT(profiles.name, ' ', profiles.lastname) as fullname"),
                DB::raw("DATE_FORMAT(DATE_ADD(routine_shareholding_details.pay_date, INTERVAL 543 YEAR), \"%Y-%m-%d\") as paydate"),
                DB::raw("FORMAT(members.shareholding, 0) as shareholding"),
                DB::raw("FORMAT(routine_shareholding_details.amount, 2) as amount"),
                DB::raw("FORMAT(SUM(IF(shareholdings.amount IS NOT NULL, shareholdings.amount, 0)), 2) as total")
            ])
            ->get();

        return view('admin.routine.shareholding.show', [
            'routine' => $routine,
            'details' => $details
        ]);
    }

    public function save($id) {
        $this->updateStatus();
        
        $routine = RoutineShareholding::find($id);

        DB::transaction(function() use ($routine) {
            //save data
            foreach ($routine->details as $detail) {
                $shareholding = new Shareholding();
                $shareholding->member_id = $detail->member_id;
                $shareholding->pay_date = Diamond::parse($detail->pay_date);
                $shareholding->shareholding_type_id = 1;
                $shareholding->amount = $detail->amount;
                $shareholding->remark = "ป้อนข้อมูลอัตโนมัติ";
                $shareholding->save();

                $detail->status = true;
                $detail->save();
            }

            $routine->saved_date = Diamond::today();
            $routine->status = true;
            $routine->save();
        });

        return redirect()->action('Admin\RoutinePaymentController@show', ['id' => $routine->id])
            ->with('flash_message', 'บันทึกข้อมูลการชำระค่าหุ้นเงินกู้ทั้งหมดลงฐานข้อมูลเรียบร้อยเรียบร้อยแล้ว')
            ->with('callout_class', 'callout-success');
    }

    public function saveDetail(Request $request) {
        $detail_id = $request->input('detail_id');
        $detail = RoutineShareholdingDetail::find($detail_id);

        $rules = [
            'detail_id' => 'required|numeric',
        ];

        $attributeNames = [
            'detail_id' => 'ไอดี',
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        $validator->after(function($validator) use ($detail) {
            //conflict check
            $shareholding = Shareholding::where('member_id', $detail->member_id)
                ->whereMonth('pay_date', Diamond::parse($detail->pay_date)->month)
                ->whereYear('pay_date', Diamond::parse($detail->pay_date)->year)
                ->where('shareholding_type_id', 1)
                ->get();

            if ($shareholding->count() > 0) {
                $validator->errors()->add('conflict', 'ข้อมูลหุ้นของสมาชิกมีความขัดแย้งกัน กรุณาตรวจสอบอีกครั้ง');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        else {
            DB::transaction(function() use ($detail) {
                //save data
                $shareholding = new Shareholding();
                $shareholding->member_id = $detail->member_id;
                $shareholding->pay_date = $detail->pay_date;
                $shareholding->shareholding_type_id = 1;
                $shareholding->amount = $detail->amount;
                $shareholding->remark = "ป้อนข้อมูลอัตโนมัติ";
                $shareholding->save();

                $detail->status = true;
                $detail->save();
            });

            return redirect()->action('Admin\RoutineShareholdingController@show', ['id' => $detail->routine->id])
                ->with('flash_message', 'บันทึกข้อมูลการชำระค่าหุ้นลงฐานข้อมูลเรียบร้อยเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function editDetail($id) {
        $detail = RoutineShareholdingDetail::find($id);

        return view('admin.routine.shareholding.edit', [
            'detail' => $detail
        ]);
    }

    public function updateDetail($id, Request $request) {
        $rules = [
            'amount' => 'required|numeric',
        ];

        $attributeNames = [
            'amount' => 'ค่าหุ้น',
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        else {
            $detail = RoutineShareholdingDetail::find($id);

            DB::transaction(function() use ($request, $detail) {
                $detail->amount = $request->input('amount');
                $detail->save();
            });

            return redirect()->action('Admin\RoutineShareholdingController@show', ['id' => $detail->routine->id])
                ->with('flash_message', 'แก้ไขข้อมูลการชำระค่าหุ้นเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function deleteDetail($id) {
        $detail = RoutineShareholdingDetail::find($id);
        $routine_id = $detail->routine->id;
        $detail->delete();

        return redirect()->action('Admin\RoutineShareholdingController@show', ['id' => $routine_id])
            ->with('flash_message', 'ลบข้อมูลการชำระค่าหุ้นเรียบร้อยแล้ว')
            ->with('callout_class', 'callout-success');
    }
    
    public function report($id) {
        $routine = RoutineShareholding::find($id);
        $details = RoutineShareholding::join('routine_shareholding_details', 'routine_shareholding_details.routine_shareholding_id', '=', 'routine_shareholdings.id')
            ->join('members', 'routine_shareholding_details.member_id', '=', 'members.id')
            ->join('profiles', 'profiles.id', '=', 'members.profile_id')
            ->leftJoin('shareholdings', 'shareholdings.member_id', '=', 'members.id')
            ->where('routine_shareholdings.id', $id)
            ->Where(function($query) use ($routine) {
                $query->whereDate('shareholdings.pay_date', '<=', $routine->calculated_date->endOfMonth())
                    ->orWhereNull('shareholdings.pay_date'); 
            })
            ->groupBy('routine_shareholding_details.id',
                'routine_shareholding_details.status',
                'members.id',
                'profiles.name',
                'profiles.lastname',
                'routine_shareholding_details.pay_date',
                'members.shareholding')
            ->orderBy('membercode')
            ->select([
                DB::raw("members.id as membercode"),
                DB::raw("CONCAT(profiles.name, ' ', profiles.lastname) as fullname"),
                DB::raw("routine_shareholding_details.pay_date as paydate"),
                DB::raw("members.shareholding as shareholding"),
                DB::raw("routine_shareholding_details.amount as amount"),
                DB::raw("SUM(IF(shareholdings.amount IS NOT NULL, shareholdings.amount, 0)) as total")
            ])
            ->get();

        $filename = 'ชำระค่าหุ้นปกติอัตโนมัติประจำเดือน '. Diamond::parse($routine->calculated_date)->thai_format('M Y');
        $header = ['#', 'เลขทะเบียนสมาชิก', 'ชื่อ-นามสกุล', 'วันที่จ่าย', 'จำนวนหุ้น', 'ค่าหุ้นปกติ', 'ค่าหุ้นสะสมรวม'];
        
        Excel::create($filename, function($excel) use ($filename, $header, $details) {
            // sheet
            $excel->sheet('ชำระค่าหุ้นปกติอัตโนมัติ', function($sheet) use ($filename, $header, $details) {
                // disable auto size for sheet
                $sheet->setAutoSize(false);

                // title
                $sheet->row(1, [$filename]);

                // header
                $sheet->row(3, $header);

                // data
                $row = 4;
                foreach ($details as $detail) {
                    $data = [];
                    $data[] = $row - 3;
                    $data[] = $detail->membercode;
                    $data[] = $detail->fullname;
                    $data[] = ExcelDate::PHPToExcel(Diamond::parse($detail->paydate));
                    $data[] = $detail->shareholding;
                    $data[] = $detail->amount;
                    $data[] = $detail->total;

                    $sheet->row($row, $data);
                    $row++;
                }

                $sheet->setColumnFormat([
                    "B4:B$row" => '00000',
                    "D4:D$row" => '[$-0][~buddhist]D MMM YYYY;@',
                    "E4:E$row" => '#,##0',
                    "F4:G$row" => '#,##0.00'
                ]);
            });
        })->download('xlsx');
    }

    protected function updateStatus() {
        $routines = RoutineShareholding::where('status', false)
            ->get();

        foreach ($routines as $routine) {
            $members = Member::join('profiles', 'members.profile_id', '=', 'profiles.id')
                ->join('employees', 'profiles.id', '=', 'employees.profile_id')
                ->leftJoin('routine_shareholding_details', 'members.id', '=', 'routine_shareholding_details.member_id')
                ->whereDate('members.start_date', '<=', $routine->calculated_date)
                ->where('employees.employee_type_id', 1)
                ->whereNull('members.leave_date')
                ->whereNull('routine_shareholding_details.id')
                ->groupBy('members.id', 'members.shareholding')
                ->select(
                    'members.id', 
                    'members.shareholding')
                ->get();

            foreach ($members as $member) {
                $detail = new RoutineShareholdingDetail();
                $detail->routine_shareholding_id = $routine->id;
                $detail->member_id = $member->id;
                $detail->pay_date = Diamond::parse($routine->calculated_date->endOfMonth()->format('Y-m-d'));
                $detail->amount = $member->shareholding * 10;
                $detail->save();
            }
        }

        // also would work, temporary turn off auto timestamps
        with($model = new RoutineShareholdingDetail)->timestamps = false;

        $model->join('shareholdings', function ($query) {
            $query->on('routine_shareholding_details.member_id', '=', 'shareholdings.member_id')
                 ->on('routine_shareholding_details.pay_date', '=', 'shareholdings.pay_date')
                 ->on('routine_shareholding_details.amount', '=', 'shareholdings.amount'); })
        ->where('routine_shareholding_details.status', false)
        ->where('shareholdings.shareholding_type_id', 1)
        ->update([
            'routine_shareholding_details.status' => true
        ]);
    }
}
