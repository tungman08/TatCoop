<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Prefix;
use Auth;
use DB;
use History;
use Validator;

class PrefixController extends Controller
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
        $prefixs = Prefix::all();

        return view('admin.prefix.index', [
            'prefixs' => $prefixs
        ]);
    }

    public function create() {
        return view('admin.prefix.create');
    }

    public function store(Request $request) {
        $rules = [
            'name' => 'required|unique:prefixs,name'
        ];

        $attributeNames = [
            'name' => 'คำนำหน้านาม'
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
                $prefix = new Prefix();
                $prefix->prefix = $request->input('name');
                $prefix->save();
    
                History::addAdminHistory(Auth::guard($this->guard)->id(), 'เพิ่มข้อมูล', 'เพิ่มคำนำหน้านาม');
            });

            return redirect()->action('Admin\PrefixController@index')
                ->with('flash_message', 'เพิ่มคำนำหน้านามเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function edit($id) {
        $prefix = Prefix::find($id);

        return view('admin.prefix.edit', [
            'prefix' => $prefix
        ]);
    }

    public function update($id, Request $request) {
        $rules = [
            'name' => 'required'
        ];

        $attributeNames = [
            'name' => 'คำนำหน้านาม'
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
                $prefix = Prefix::find($id);
                $prefix->name = $request->input('name');
                $prefix->save();
    
                History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'แก้ไขคำนำหน้านาม');
            });

            return redirect()->action('Admin\PrefixController@index')
                ->with('flash_message', 'แก้ไขคำนำหน้านามเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function destroy($id) { 
        $validator = Validator::make([], []);

        $validator->after(function($validator) use ($id) {
            if (Prefix::find($id)->profiles->count() > 0) {
                $validator->errors()->add('used', 'ไม่สามาลบได้เนื่องจากข้อมูลมีการใช้งานอยู่');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        else {
            DB::transaction(function() use ($id) {
                $prefix = Prefix::find($id);
                $prefix->delete();
    
                History::addAdminHistory(Auth::guard($this->guard)->id(), 'ลบข้อมูล', 'ลบคำนำหน้านาม');    
            });
    
            return redirect()->action('Admin\PrefixController@index')
                ->with('flash_message', 'ลบคำนำหน้านามเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }
}
