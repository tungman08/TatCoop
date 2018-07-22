<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Billing;
use Auth;
use DB;
use History;
use Validator;

class BillingController extends Controller
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
        return view('admin.billing.index', [
            'billing' => Billing::all()->last()
        ]);
    }

    public function create() {
        return view('admin.billing.create');  
    }

    public function store(Request $request) {
        $rules = [
            'manager' => 'required',
            'treasurer' => 'required'
        ];

        $attributeNames = [
            'manager' => 'ผู้จัดการ',
            'treasurer' => 'เหรัญญิก'
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
                $billing = new Billing();
                $billing->manager = $request->input('manager');
                $billing->treasurer = $request->input('treasurer');
                $billing->save();

                History::addAdminHistory(Auth::guard($this->guard)->id(), 'เพิ่มข้อมูล', 'เพิ่มชื่อผู้จัดการและเหรัญญิก');
            });

            return redirect()->action('Admin\BillingController@index')
                ->with('flash_message', 'เพิ่มชื่อผู้จัดการและเหรัญญิกเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function edit($id) {
        return view('admin.billing.edit', [
            'billing' => Billing::find($id)
        ]);
    }

    public function update($id, Request $request) {
        $rules = [
            'manager' => 'required',
            'treasurer' => 'required'
        ];

        $attributeNames = [
            'manager' => 'ผู้จัดการ',
            'treasurer' => 'เหรัญญิก'
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        else {
            DB::transaction(function() use ($id, $request) {
                $billing = Billing::find($id);
                $billing->manager = $request->input('manager');
                $billing->treasurer = $request->input('treasurer');
                $billing->save();

                History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'แก้ไขชื่อผู้จัดการและเหรัญญิก');
            });

            return redirect()->action('Admin\BillingController@index')
                ->with('flash_message', 'แก้ไขชื่อผู้จัดการและเหรัญญิกเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }
}
