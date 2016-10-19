<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB;
use Diamond;
use Validator;
use App\LoanType;

class LoanTypeController extends Controller
{
    public function index() {
        return view('admin.loantype.index', [
            'loantypes' => LoanType::active()->get()
        ]);
    }

    public function create() {
      return view('admin.loantype.create');
    }

    public function store(Request $request) {
        $rules = [
            'name' => 'required',
            'cash_limit' => 'required|numeric', 
            'installment_limit' => 'required|numeric', 
            'start_date' => 'required|date_format:Y-m-d', 
            'expire_date' => 'required|date_format:Y-m-d'
        ];

        $attributeNames = [
            'name' => 'ชื่อประเภทเงินกู้พิเศษ',
            'cash_limit' => 'วงเงินกู้สูงสุด', 
            'installment_limit' => 'ระยะเวลาผ่อนชำระสูงสุด', 
            'start_date' => 'วันที่เริ่มใช้', 
            'expire_date' => 'วันที่หมดอายุ'
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        else {
            DB::transaction(function() use ($request) {
                $loanType = new LoanType();
                $loanType->name = $request->input('name');
                $loanType->cash_limit = $request->input('cash_limit');
                $loanType->installment_limit = $request->input('installment_limit');
                $loanType->start_date = Diamond::parse($request->input('start_date'));
                $loanType->expire_date = Diamond::parse($request->input('expire_date'));
                $loanType->save();
            });

            return redirect()->route('admin.loantype.index')
                ->with('flash_message', 'สร้างประเภทเงินกู้พิเศษชื่อ ' . $request->input('name') . ' เรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function show($id) {
    }

    public function getExpire() {
        return view('admin.loantype.expire', [
            'loantypes' => LoanType::expired()->get()
        ]);
    }
}
