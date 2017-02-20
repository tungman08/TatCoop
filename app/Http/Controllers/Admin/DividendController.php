<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Dividend;
use Auth;
use History;
use DB;
use Validator;

class DividendController extends Controller
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
        return view('admin.dividend.index', ['dividends'=>Dividend::all()]);
    }

    public function create() {
        return view('admin.dividend.create');
    }

    public function store(Request $request) {
        $rules = [
            'rate_year' => 'required|digits:4|unique:dividends,rate_year', 
            'rate' => 'required|numeric|between:0,100',
        ];

        $attributeNames = [
            'rate_year' => 'ปี ค.ศ.', 
            'rate' => 'อัตราเงินปันผล',
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
                $dividend = new Dividend();
                $dividend->rate_year = $request->input('rate_year');
                $dividend->rate = $request->input('rate');
                $dividend->save();
    
                History::addAdminHistory(Auth::guard($this->guard)->id(), 'เพิ่มข้อมูล', 'ป้อนอัตราเงินปันผล ประจำปี ' . $dividend->rate_year + 543);
            });

            return redirect()->route('admin.dividend.index')
                ->with('flash_message', 'ข้อมูลอัตราเงินปันผลถูกป้อนเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function edit($id) {
        return view('admin.dividend.edit', ['dividend'=>Dividend::find($id)]);
    }

    public function update(Request $request, $id) {
        $rules = [
            'rate' => 'required|numeric|between:0,100',
        ];

        $attributeNames = [
            'rate' => 'ตราเงินปันผล',
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
                $dividend = Dividend::find($id);
                $dividend->rate = $request->input('rate');
                $dividend->save();
    
                History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'แก้ไขอัตราเงินปันผล ประจำปี ' . $dividend->rate_year + 543);
            });

            return redirect()->route('admin.dividend.index')
                ->with('flash_message', 'แก้ไขข้อมูลอัตราเงินปันผลเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function getErase($id) {
        DB::transaction(function() use ($id) {
            $dividend = Dividend::find($id);

            History::addAdminHistory(Auth::guard($this->guard)->id(), 'ลบข้อมูล', 'ลบอัตราเงินปันผล ประจำปี ' . $dividend->rate_year + 543);

            $dividend->delete();
        });

        return redirect()->route('admin.dividend.index')
            ->with('flash_message', 'ลบข้อมูลอัตราเงินปันผลเรียบร้อยแล้ว')
            ->with('callout_class', 'callout-success');
    }
}
