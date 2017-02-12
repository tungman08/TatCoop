<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB;
use Validator;
use App\Member;
use App\Shareholding;
use App\ShareholdingType;

class ShareholdingController extends Controller
{
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
            });

            return redirect()->route('admin.member.tab', ['id' => $member_id, 'tab' => 1])
                ->with('flash_message', 'แก้ไขข้อมูลการชำระค่าหุ้นเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function getErase($member_id, $id) {
        DB::transaction(function() use ($id) {
            $shareholding = Shareholding::find($id);
            $shareholding->delete();
        });

        return redirect()->route('admin.member.tab', ['id' => $member_id, 'tab' => 1])
            ->with('flash_message', 'ลบข้อมูลการชำระค่าหุ้นเรียบร้อยแล้ว')
            ->with('callout_class', 'callout-success');
    }
}
