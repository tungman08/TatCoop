<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Administrator;
use App\Member;
use App\Shareholding;
use DB;
use Diamond;
use Mail;
use Validator;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:admins');
    }

    /**
     * Responds to requests to GET /
     */
    public function getIndex() {
        return view('admin.manage.index', [
            'member_amount' => Member::whereNull('leave_date')->count(),
            'member_shareholding' => Shareholding::all()->sum('amount')
        ]);
    }

    /**
     * Responds to requests to GET /unauthorize
     */
    public function getUnauthorize() {
        return 'unauthorize';
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function index() {
        return view('admin.administrator.index', [
            'admins' => Administrator::normal()->get()
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        return view('admin.administrator.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request) {
        $rules = [
            'name' => 'required', 
            'email' => 'required|unique:administrators,email',
            'new_password' => 'required|min:6|confirmed', 
            'new_password_confirmation' => 'required|min:6',
        ];

        $attributeNames = [
            'name' => 'อีเมลผู้ดูแลระบบ',
            'email' => 'ชื่อสำหรับแสดงผล',
            'new_password' => 'รหัสผ่าน',
            'new_password_confirmation' => 'ยืนยันรหัสผ่าน',
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except(['new_password', 'new_password_confirmation']));
        }
        else {
            DB::transaction(function() use ($request) {
                $admin = new Administrator();
                $admin->name = $request->input('name');
                $admin->email = $request->input('email');
                $admin->password = $request->input('new_password');
                $admin->save();

                Mail::send('admin.emails.newadmin', ['email' => $request->input('email'), 'password' => $request->input('new_password')], function($message) use ($admin) {
                    $message->to($admin->email, $admin->name)
                        ->subject('คุณได้รับการแต่งตั้งเป็นผู้ดูแลระบบเว็บไซต์ www.tatcoop.com');
                });
            });

            return redirect()->route('admin.administrator.index')
                ->with('flash_message', 'เพิ่มบัญชีผู้ใช้เรียบร้อยแล้ว ระบบได้ส่งอีเมลแจ้งผู้ใช้ด้วย username = \'' . $request->input('email') . '\' และ password = \'' . $request->input('new_password') . '\'')
                ->with('callout_class', 'callout-success');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        if ($id <= 1 || $id > Administrator::max('id')) {
            return redirect()->to('admin/administrator');
        }

        return view('admin.administrator.edit', [
            'admins' => Administrator::find($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id) {
        $rules = [
            'name' => 'required', 
        ];

        $attributeNames = [
            'name' => 'อีเมลผู้ดูแลระบบ',
            'email' => 'ชื่อสำหรับแสดงผล',
            'new_password' => 'รหัสผ่าน',
            'new_password_confirmation' => 'ยืนยันรหัสผ่าน',
        ];

        $validator = Validator::make($request->except('email'), $rules);
        $validator->setAttributeNames($attributeNames);

        $validator->after(function($validator) use ($request) {
            if (!empty($request->input('new_password'))) {
                if ($request->input('new_password') != $request->input('new_password_confirmation')) {
                    $validator->errors()->add('password_notmatch', 'ข้อมูล รหัสผ่าน ไม่ตรงกัน');
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except(['new_password', 'new_password_confirmation']));
        }
        else {
            DB::transaction(function() use ($request, $id) {
                $admin = Administrator::find($id);
                $admin->name = $request->input('name');

                if (!empty($request->input('new_password'))) {
                    $admin->password = $request->input('new_password');
                    $admin->password_changed = false;
                }

                $admin->save();

                if (!empty($request->input('new_password'))) {
                    Mail::send('admin.emails.updateadmin', ['email' => $request->input('email'), 'password' => $request->input('new_password')], function($message) use ($admin) {
                        $message->to($admin->email, $admin->name)
                            ->subject('บัญชีผู้ดูแลระบบ www.tatcoop.com ของคุณได้มีการแก้ไขเรียบร้อย');
                    });
                }
            });

            return redirect()->route('admin.administrator.index')
                ->with('flash_message', (!empty($request->input('new_password'))) ? 'แก้ไขข้อมูลผู้ใช้เรียบร้อยแล้ว ระบบได้ส่งอีเมลแจ้งผู้ใช้ด้วย username = \'' . $request->input('email') . '\' และ password = \'' . $request->input('new_password') . '\'' : 'แก้ไขข้อมูลผู้ใช้เรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    /**
     * Show the form for delete the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function getErase($id) {
        if ($id <= 1 || $id > Administrator::max('id')) {
            return redirect()->to('/admin/administrator');
        }

        return view('admin.administrator.erase', [
            'admins' => Administrator::find($id)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Request $request, $id) {
        $rules = [
            'email' => 'required', 
        ];

        $attributeNames = [
            'email' => 'บัญชีผู้ใช้',
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        $validator->after(function($validator) use ($request, $id) {
            $admin = Administrator::findOrFail($id);   

            if ($admin->email != $request->input('email')) {
                $validator->errors()->add('password_notmatch', 'ชื่อบัญชีผู้ใช้ที่กรอกไม่ตรงกับบัญชีผู้ที่ต้องการลบ');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        else {
            $admin = Administrator::findOrFail($id);
            $admin->delete();

            return redirect()->route('admin.administrator.index')
                ->with('flash_message', 'ลบบัญชีผู้ใช้เรียบร้อยแล้ว ยกเลิกคำสั่งให้คลิกที่นี่ ')
                ->with('callout_class', 'callout-warning')
                ->with('flash_link', '/admin/administrator/' . $id . '/undelete');
        }
    }

    /**
     * Restore a deleted this item.
     *
     * @return Response
     */
    public function getUnDelete($id) {
        $admin = Administrator::withTrashed()->findOrFail($id);
        $admin->restore();

        return redirect()->route('admin.administrator.index')
            ->with('flash_message', 'คืนค่าบัญชีผู้ใช้เรียบร้อยแล้ว')
            ->with('callout_class', 'callout-success');
    }

    /**
     * Force delete this item.
     *
     * @return Response
     */
    public function getForceDelete($id) {
        $admin = Administrator::withTrashed()->findOrFail($id);
        $admin->forceDelete();

        return redirect()->route('admin.administrator.index')
            ->with('flash_message', 'ลบบัญชีผู้ใช้อย่างถาวรเรียบร้อยแล้ว')
            ->with('callout_class', 'callout-danger');
    }

    /**
     * Show all deleted items in storage.
     *
     * @return Response
     */
    public function getRestore() {
        $admin = Administrator::onlyTrashed()->get();

        return view('admin.administrator.restore', [
            'admins' => $admin
        ]);
    }
}
