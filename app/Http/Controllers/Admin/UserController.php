<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use Validator;
use Hash;

class UserController extends Controller
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

    public function getProfile() {
        return view('admin.user.profile');
    }

    public function getPassword() {
        $user = Auth::guard($this->guard)->user();

        return view('admin.user.password', [
            'user' => $user
        ]);
    }

    public function getAlert() {
        return view('admin.user.alert');
    }

    public function getMessage() {
        return view('admin.user.message');
    }
    
    public function getNotice() {
        return view('admin.user.notice');
    }

    /**
     * Update user's password.
     *
     * @param  Request  $request
     * @return Response
     */
    public function postPassword(Request $request) {
        $rules = [
            'password' => 'required|min:6', 
            'new_password' => 'required|min:6|confirmed',
            'new_password_confirmation' => 'required|min:6'
        ];

        $attributeNames = [
            'password' => 'รหัสผ่าน',
            'new_password' => 'รหัสผ่านใหม่',
            'new_password_confirmation' => 'ยืนยันรหัสผ่านใหม่',
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        $validator->after(function($validator) use ($request) {
            if (!Hash::check($request->input('password'), Auth::guard($this->guard)->user()->password)) {
                $validator->errors()->add('password_notmatch', 'รหัสผ่านไม่ถูกต้อง');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        else {
            $admin = Auth::guard('admins')->user();
            $admin->password = $request->input('new_password');
            $admin->password_changed = true;
            $admin->push();

            return redirect()->route('admin.user.profile')
                ->with('password_changed', 'เปลี่ยนรหัสผ่านเสร็จเรียบร้อยแล้ว!');
        }
    }
}