<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Auth;
use History;
use DB;
use Validator;
use App\Bailsman;

class BailsmanController extends Controller
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
        return view('admin.bailsman.index', [
            'bailsmans' => Bailsman::all()
        ]);
    }

    public function edit($id) {
        return view('admin.bailsman.edit', [
            'bailsman' => Bailsman::find($id)
        ]);  
    }

    public function update($id, Request $request) {
        $rules = [
            'self_rate' => 'required|numeric|between:1,100',
            'self_maxguaruntee' => 'required|numeric',
            'self_netsalary' => 'required|numeric',
            'other_rate' => 'required|numeric|between:1,100',
            'other_maxguaruntee' => 'required|numeric', 
            'other_netsalary' => 'required|numeric',
        ];

        $attributeNames = [
            'self_rate' => ($request->input('self_type') == 'shareholding') ? 'จำนวนหุ้นที่ต้องใช้' : 'จำนวนเงินเดือนที่สามารถค้ำได้',
            'self_maxguaruntee' => 'วงเงินสูงสุดที่สามารถค้ำได้',
            'self_netsalary' => 'เงินเดือนสุทธิต่ำสุดหลังหักค่างวด',
            'other_rate' => ($request->input('other_type') == 'shareholding') ? 'จำนวนหุ้นที่ต้องใช้' : 'จำนวนเงินเดือนที่สามารถค้ำได้',
            'other_maxguaruntee' => 'วงเงินสูงสุดที่สามารถค้ำได้', 
            'other_netsalary' => 'เงินเดือนสุทธิต่ำสุดหลังหักค่างวด',
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
                $bailsman = Bailsman::find($id);
                $bailsman->self_rate = ($request->input('self_type') == 'shareholding') ? $request->input('self_rate') / 100 : $request->input('self_rate');
                $bailsman->self_maxguaruntee = $request->input('self_maxguaruntee');
                $bailsman->self_netsalary = $request->input('self_netsalary');
                $bailsman->other_rate = ($request->input('other_type') == 'shareholding') ? $request->input('other_rate') / 100 : $request->input('other_rate');
                $bailsman->other_maxguaruntee = $request->input('other_maxguaruntee');
                $bailsman->other_netsalary = $request->input('other_netsalary');
                $bailsman->save();

                History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'แก้ไขข้อมูลเงื่อนไขการค้ำประกัน');
            });
        }

        return redirect()->action('Admin\BailsmanController@index')
            ->with('flash_message', 'แก้ไขข้อมูลเงื่อนไขการค้ำประกันเรียบร้อยแล้ว' )
            ->with('callout_class', 'callout-success');
    }

    public function getAvailable() {
        return view('admin.bailsman.available');
    }
}
