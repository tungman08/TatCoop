<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\RoutineShareholding;
use App\RoutineShareholdingDetail;
use App\Shareholding;
use Validator;
use DB;
use Diamond;

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
        $routines = RoutineShareholding::where('status', false)
            ->orderBy('calculated_date', 'desc')
            ->get();

        return view('admin.routine.shareholding.index', [
            'routines' => $routines
        ]);
    }
    
    public function show($id) {
        $details = RoutineShareholding::join('routine_shareholding_details', 'routine_shareholding_details.routine_shareholding_id', '=', 'routine_shareholdings.id')
            ->join('members', 'routine_shareholding_details.member_id', '=', 'members.id')
            ->join('profiles', 'profiles.id', '=', 'members.profile_id')
            ->leftJoin('shareholdings', 'shareholdings.member_id', '=', 'members.id')
            ->where('routine_shareholdings.id', $id)
            ->groupBy('routine_shareholding_details.id', 'routine_shareholding_details.status', 'members.id', 'profiles.name', 'profiles.lastname', 'routine_shareholding_details.pay_date', 'members.shareholding')
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
            'routine' => RoutineShareholding::find($id),
            'details' => $details
        ]);
    }

    public function save($id) {
        $routine = RoutineShareholding::find($id);

        $rules = [
            'detail_id' => 'required|numeric',
        ];

        $attributeNames = [
            'detail_id' => 'ไอดี',
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        $validator->after(function($validator) use ($routine) {
            //conflict check
            $conflict = [];
            foreach ($routine->details as $detail) {
                $shareholding = Shareholding::where('member_id', $detail->member_id)
                    ->whereMonth('pay_date', Diamond::parse($detail->pay_date)->month)
                    ->whereYear('pay_date', Diamond::parse($detail->pay_date)->year)
                    ->where('shareholding_type_id', 1)
                    ->get();

                if ($shareholding->count() > 0) {
                    $conflict[] = $shareholding->member->memberCode;
                } 
            }

            if (count($conflict) > 0) {
                $validator->errors()->add('conflict', 'ข้อมูลการชำระค่าหุ้นของสมาชิกมีความขัดแย้งกัน กรุณาตรวจสอบอีกครั้ง ข้อมูลที่ขัดแย้ง (' . implode(', ', $conflict) . ')');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        else {
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
}
