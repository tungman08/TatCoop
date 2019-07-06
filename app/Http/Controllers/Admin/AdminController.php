<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Administrator;
use App\Role;
use App\Shareholding;
use Auth;
use DB;
use Diamond;
use History;
use Mail;
use Validator;

class AdminController extends Controller
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
        return view('admin.officer.index', [
            'admins' => Administrator::admin()->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        return view('admin.officer.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request) {
        $rules = [
            'name' => 'required', 
            'lastname' => 'required', 
            'email' => 'required|unique:administrators,email',
            'new_password' => 'required|min:6|confirmed', 
            'new_password_confirmation' => 'required|min:6',
        ];

        $attributeNames = [
            'name' => 'ชื่อ',
            'lastname' => 'นามสกุล',
            'email' => 'อีเมลเจ้าหน้าที่สหกรณ์',
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
                $role = Role::find(2);

                $admin = new Administrator();
                $admin->name = $request->input('name');
                $admin->lastname = $request->input('lastname');
                $admin->email = strtolower($request->input('email'));
                $admin->password = $request->input('new_password');
                $role->admins()->save($admin);

                History::addAdminHistory(Auth::guard($this->guard)->id(), 'เพิ่มข้อมูล', 'เพิ่มบัญชี ' . $admin->fullname . ' (' . $admin->email . ') เป็นเจ้าหน้าที่สหกรณ์');

                Mail::send('admin.emails.newadmin', ['email' => $request->input('email'), 'password' => $request->input('new_password')], function($message) use ($admin) {
                    $message->to($admin->email, $admin->fullname)
                        ->subject('คุณได้รับการแต่งตั้งเป็นเจ้าหน้าที่สหกรณ์เว็บไซต์ www.tatcoop.com');
                });
            });

            return redirect()->action('Admin\AdminController@index')
                ->with('flash_message', 'เพิ่มบัญชีผู้ใช้เรียบร้อยแล้ว ระบบได้ส่งอีเมลแจ้งผู้ใช้ด้วย username = \'' . $request->input('email') . '\' และ password = \'' . $request->input('new_password') . '\'')
                ->with('callout_class', 'callout-success');
        }
    }

    public function show($id) {
        $user = Administrator::find($id);

        if ($user->role_id != 2) {
            return redirect()->action('Admin\AdminController@index');
        }

        return view('admin.officer.show', [
            'user' => $user
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        $user = Administrator::find($id);

        if ($user->role_id != 2) {
            return redirect()->action('Admin/AdminController@index');
        }

        return view('admin.officer.edit', [
            'user' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, Request $request) {
        $rules = [
            'name' => 'required', 
            'lastname' => 'required', 
        ];

        $attributeNames = [
            'name' => 'ชื่อ',
            'lastname' => 'นามสกุล',
            'email' => 'อีเมลเจ้าหน้าที่สหกรณ์',
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
                $admin->lastname = $request->input('lastname');

                if (!empty($request->input('new_password'))) {
                    $admin->password = $request->input('new_password');
                    $admin->password_changed = false;
                }

                $admin->save();

                History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'แก้ไขบัญชีเจ้าหน้าที่สหกรณ์ชื่อ ' . $admin->fullname . ' (' . $admin->email . ')');

                if (!empty($request->input('new_password'))) {
                    Mail::send('admin.emails.updateadmin', ['email' => $request->input('email'), 'password' => $request->input('new_password')], function($message) use ($admin) {
                        $message->to($admin->email, $admin->fullname)
                            ->subject('บัญชีเจ้าหน้าที่สหกรณ์ www.tatcoop.com ของคุณได้มีการแก้ไขเรียบร้อย');
                    });
                }
            });

            return redirect()->action('Admin\AdminController@show', ['id' => $id])
                ->with('flash_message', (!empty($request->input('new_password'))) ? 'แก้ไขข้อมูลผู้ใช้เรียบร้อยแล้ว ระบบได้ส่งอีเมลแจ้งผู้ใช้ด้วย username = \'' . $request->input('email') . '\' และ password = \'' . $request->input('new_password') . '\'' : 'แก้ไขข้อมูลผู้ใช้เรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id, Request $request) {
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

            History::addAdminHistory(Auth::guard($this->guard)->id(), 'ลบข้อมูล', 'ลบบัญชีเจ้าหน้าที่สหกรณ์ชื่อ ' . $admin->fullname . ' (' . $admin->email . ')');

            $admin->delete();

            return redirect()->action('Admin\AdminController@index')
                ->with('flash_message', 'ลบบัญชีผู้ใช้เรียบร้อยแล้ว ยกเลิกคำสั่งให้คลิกที่นี่ ')
                ->with('callout_class', 'callout-warning')
                ->with('flash_link', '/admin/administrator/' . $id . '/undelete');
        }
    }

    /**
     * Show the form for delete the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function getDelete($id) {
        $user = Administrator::find($id);

        if ($user->role_id != 2) {
            return redirect()->action('Admin/AdminController@index');
        }

        return view('admin.officer.delete', [
            'user' => $user
        ]);
    }

    /**
     * Show all deleted items in storage.
     *
     * @return Response
     */
    public function getInactive() {
        $admins = Administrator::onlyTrashed()
            ->where('role_id', 2)
            ->get();

        return view('admin.officer.inactive', [
            'admins' => $admins
        ]);
    }

    /**
     * Restore a deleted this item.
     *
     * @return Response
     */
    public function postRestore($id) {
        $admin = Administrator::withTrashed()->findOrFail($id);
        $admin->restore();

        History::addAdminHistory(Auth::guard($this->guard)->id(), 'คืนสภาพข้อมูล', 'คืนสภาพบัญชีเจ้าหน้าที่สหกรณ์ชื่อ ' . $admin->fullname . ' (' . $admin->email . ')');

        return redirect()->action('Admin\AdminController@index')
            ->with('flash_message', 'คืนค่าบัญชีผู้ใช้เรียบร้อยแล้ว')
            ->with('callout_class', 'callout-success');
    }

    /**
     * Force delete this item.
     *
     * @return Response
     */
    public function postForceDelete($id) {
        $admin = Administrator::withTrashed()->findOrFail($id);

        History::addAdminHistory(Auth::guard($this->guard)->id(), 'ลบข้อมูลอย่างถาวร', 'ลบบัญชีเจ้าหน้าที่สหกรณ์ชื่อ ' . $admin->fullname . ' (' . $admin->email . ') ออกจากระบบอย่างถาวร');

        $admin->forceDelete();

        return redirect()->action('Admin\AdminController@index')
            ->with('flash_message', 'ลบบัญชีผู้ใช้อย่างถาวรเรียบร้อยแล้ว')
            ->with('callout_class', 'callout-danger');
    }
}
