<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use History;
use DB;
use Diamond;
use Validator;
use App\Member;
use App\Shareholding;
use App\ShareholdingType;

class ShareholdingController extends Controller
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

    public function create($id) {
        return view('admin.member.shareholding.create', [
            'member' => Member::find($id),
            'shareholding_types' => ShareholdingType::all()
        ]);
    }

    public function store(Request $request, $id) {
        $rules = [
            'pay_date' => 'required|date_format:Y-m-d', 
            'amount' => 'required|numeric'
        ];

        $attributeNames = [
            'pay_date' => 'วันที่ชำระ', 
            'amount' => 'ค่าหุ้น'
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        else {
            DB::transaction(function() use ($request, $id) {
                $shareholding = new Shareholding();
                $shareholding->member_id = $id;
                $shareholding->pay_date = $request->input('pay_date');
                $shareholding->shareholding_type_id = $request->input('shareholding_type_id');
                $shareholding->amount = $request->input('amount');

                if (!empty($request->input('remark')))
                    $shareholding->remark = $request->input('remark');
                    
                $shareholding->save();

                History::addAdminHistory(Auth::guard($this->guard)->id(), 'เพิ่มข้อมูล', 'เพิ่มข้อมูลค่าหุ้นแก่สมาชิกรหัส ' . str_pad($id, 5, "0", STR_PAD_LEFT));
            });

            return redirect()->route('admin.member.tab', ['id' => $id, 'tab' => 1])
                ->with('flash_message', 'ป้อนข้อมูลการชำระค่าหุ้นเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function edit($member_id, $id) {
        return view('admin.member.shareholding.edit', [
            'member' => Member::find($member_id),
            'shareholding' => Shareholding::find($id),
            'shareholding_types' => ShareholdingType::all()
        ]);
    }

    public function update(Request $request, $member_id, $id) {
       $rules = [
            'pay_date' => 'required|date_format:Y-m-d', 
            'amount' => 'required|numeric',
        ];

        $attributeNames = [
            'pay_date' => 'วันที่ชำระ', 
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
            DB::transaction(function() use ($request, $id) {
                $shareholding = Shareholding::find($id);
                $shareholding->pay_date = $request->input('pay_date');
                $shareholding->shareholding_type_id = $request->input('shareholding_type_id');
                $shareholding->amount = $request->input('amount');

                if (!empty($request->input('remark')))
                    $shareholding->remark = $request->input('remark');
                
                $shareholding->save();

                History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'แก้ไขข้อมูลค่าหุ้นแก่สมาชิกรหัส ' . str_pad($shareholding->member_id, 5, "0", STR_PAD_LEFT));
            });

            return redirect()->route('admin.member.tab', ['id' => $member_id, 'tab' => 1])
                ->with('flash_message', 'แก้ไขข้อมูลการชำระค่าหุ้นเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function getErase($member_id, $id) {
        DB::transaction(function() use ($id) {
            $shareholding = Shareholding::find($id);

            History::addAdminHistory(Auth::guard($this->guard)->id(), 'ลบข้อมูล', 'ลบข้อมูลค่าหุ้นแก่สมาชิกรหัส ' . str_pad($shareholding->member_id, 5, "0", STR_PAD_LEFT));

            $shareholding->delete();
        });

        return redirect()->route('admin.member.tab', ['id' => $member_id, 'tab' => 1])
            ->with('flash_message', 'ลบข้อมูลการชำระค่าหุ้นเรียบร้อยแล้ว')
            ->with('callout_class', 'callout-success');
    }

    public function getAutoShareholding() {
        return view('admin.shareholding.auto');
    }

    public function postAutoShareholding(Request $request) {
        $date = Diamond::parse($request->input('month') . '-1')->endOfMonth();

        $members = Member::active()
            ->join('employees', 'members.profile_id', '=', 'employees.profile_id')
            ->where('members.shareholding', '>', 0)
            ->where('employees.employee_type_id', '<', 3)
            ->whereDate('members.start_date', '<', $date)
            ->whereNotIn('members.id', function($query) use ($date) {
                $query->from('shareholdings')
                    ->whereMonth('pay_date', '=', $date->month)
                    ->whereYear('pay_date', '=', $date->year)
                    ->where('shareholding_type_id', 1)
                    ->select('member_id');
            })
            ->select('members.*')->get();

        DB::transaction(function() use ($members, $date) {
            foreach($members as $member) {
                $share = new Shareholding();
                $share->pay_date = $date;
                $share->shareholding_type_id = 1;
                $share->amount = $member->shareholding * 10 ;
                $share->remark = 'ป้อนข้อมูลอัตโนมัติ';
                $member->shareholdings()->save($share);
            }

            History::addAdminHistory(Auth::guard($this->guard)->id(), 'ป้อนการชำระค่าหุ้นแบบอัตโนมัติ', 'ทำรายการชำระค่าหุ้นอัตโนมัติประจำเดือน' . $date->thai_format('F Y'));
        });

        return redirect()->route('admin.member.index')
            ->with('flash_message', 'ทำรายการชำระค่าหุ้นอัตโนมัติประจำเดือน' . $date->thai_format('F Y') . ' จำนวน ' . $members->count() . ' คน เรียบร้อยแล้ว')
            ->with('callout_class', 'callout-success');
    }
}
