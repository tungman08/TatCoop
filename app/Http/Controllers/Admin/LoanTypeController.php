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
use App\Loan;
use App\LoanType;
use App\LoanTypeLimit;

class LoanTypeController extends Controller
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
            'rate' => 'required|numeric|between:0,100',
            'start_date' => 'required|date_format:Y-m-d', 
            'expire_date' => 'required|date_format:Y-m-d',
            'limits.*.cash_begin' => 'required|numeric|min:1',
            'limits.*.cash_end' => 'required|numeric',
            'limits.*.shareholding' => 'required|numeric|min:0',
            'limits.*.surety' => 'required',
            'limits.*.period' => 'required|numeric|min:1',
        ];

        $attributeNames = [
            'name' => 'ชื่อประเภทเงินกู้พิเศษ',
            'rate' => 'อัตราดอกเบี้ย',
            'start_date' => 'วันที่เริ่มใช้', 
            'expire_date' => 'วันที่หมดอายุ',
            'limits.*.cash_begin' => 'วงเงินกู้เริ่มต้น',
            'limits.*.cash_end' => 'วงเงินกู้สูงสุด',
            'limits.*.shareholding' => 'จำนวนหุ้นที่ใช้ขอกู้',
            'limits.*.surety' => 'จำนวนผู้ค้ำประกัน',
            'limits.*.period' => 'จำนวนงวดผ่อนชำระสูงสุด',
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        $limits = collect($request->input('limits'));

        $validator->after(function($validator) use ($limits) {
            $limits->each(function ($item, $key) use ($validator) {
                if ($item['cash_begin'] > $item['cash_end']) {
                    $validator->errors()->add('lessthan', 'วงเงินกู้สูงสุดต้องมากกว่าวงเงินกู้เริ่มต้น');
                }
            });
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        else {
            DB::transaction(function() use ($request) {
                $loanType = new LoanType();
                $loanType->name = $request->input('name');
                $loanType->rate = $request->input('rate');
                $loanType->start_date = Diamond::parse($request->input('start_date'));
                $loanType->expire_date = Diamond::parse($request->input('expire_date'));
                $loanType->save();

                foreach ($request->input('limits') as $item) {
                    $limit = new LoanTypeLimit();
                    $limit->cash_begin = $item['cash_begin'];
                    $limit->cash_end = $item['cash_end'];
                    $limit->shareholding = $item['shareholding'];
                    $limit->surety = $item['surety'];
                    $limit->period = $item['period'];
                    $loanType->limits()->save($limit);
                }

                History::addAdminHistory(Auth::guard($this->guard)->id(), 'เพิ่มข้อมูล', 'เพิ่มข้อมูลประเภทเงินกู้');
            });

            return redirect()->action('Admin\LoanTypeController@index')
                ->with('flash_message', 'สร้างประเภทเงินกู้ชื่อ ' . $request->input('name') . ' เรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function show($id) {
        return view('admin.loantype.show', [
            'loantype' => LoanType::find($id)
        ]);
    }

    public function edit($id) {
        return view('admin.loantype.edit', [
            'loantype' => LoanType::find($id)
        ]);
    }

    public function update(Request $request, $id) {
        $rules = [
            'rate' => 'required|numeric|between:0,100'
        ];

        $attributeNames = [
            'rate' => 'อัตราดอกเบี้ย'        
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
                $loanType = LoanType::find($id);
                $loanType->rate = $request->input('rate');
                $loanType->save();

                History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'แก้ไขข้อมูลประเภทเงินกู้');
            });

            return redirect()->action('Admin\LoanTypeController@show', [ 'loantype' => LoanType::find($id) ])
                ->with('flash_message', 'แก้ไขประเภทเงินกู้ชื่อ ' . $request->input('name') . ' เรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function destroy($id) {
        $loantype = LoanType::find($id);

        if ($loantype->loans->count() > 0) {
            return redirect()->back()
                ->withErrors(['error' => 'ไม่สามารถลบประเภทเงินกู้นี้ได้ เนื่องจากมีการใช้งานไปแล้ว']);
        }
        else {
            History::addAdminHistory(Auth::guard($this->guard)->id(), 'ลบข้อมูล', 'ลบประเภทเงินกู้ชื่อ ' . $loantype->name);

            $loantype->delete();

            return redirect()->action('Admin\LoanTypeController@index')
                ->with('flash_message', 'ลบประเภทเงินกู้ชื่อ ' . $loantype->name . ' เรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function getFinished($id) {
        return view('admin.loantype.finished', [
            'loantype' => LoanType::find($id),
            'loans' => Loan::finished()->where('loan_type_id', $id)->get()
        ]);
    }

    public function getExpired() {
        return view('admin.loantype.expired', [
            'loantypes' => LoanType::expired()->get()
        ]);
    }

    public function getInactive() {
        return view('admin.loantype.inactive', [
            'loantypes' => LoanType::deletedType()->get()
        ]);
    }

    public function postForceDelete($id) {
        $loanType = LoanType::withTrashed()->findOrFail($id);

        History::addAdminHistory(Auth::guard($this->guard)->id(), 'ลบข้อมูลอย่างถาวร', 'ลบประเภทเงินกู้ชื่อ ' . $loanType->name . ' ออกจากระบบอย่างถาวร');

        $loanType->forceDelete();

        return redirect()->action('Admin\LoanTypeController@index')
            ->with('flash_message', 'ลบประเภทเงินกู้อย่างถาวรเรียบร้อยแล้ว')
            ->with('callout_class', 'callout-danger');
    }

    public function postRestore($id) {
        $loanType = LoanType::withTrashed()->findOrFail($id);
        $loanType->restore();

        History::addAdminHistory(Auth::guard($this->guard)->id(), 'คืนสภาพข้อมูล', 'คืนสภาพประเภทเงินกู้ชื่อชื่อ ' . $loanType->name);

        return redirect()->action('Admin\LoanTypeController@index')
            ->with('flash_message', 'คืนค่าประเภทเงินกู้เรียบร้อยแล้ว')
            ->with('callout_class', 'callout-success');
    }
}
