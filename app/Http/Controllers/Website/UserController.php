<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use Hash;
use History;
use Validator;

class UserController extends Controller
{
    /**
     * Only user authorize to access this section.
     *
     * @var string
     */
    protected $guard = 'users';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:users');
    }

    public function getProfile() {
        return view('website.user.profile', [
            'user' => Auth::user(),
            'index' => 0,
            'count' => History::countUserHistory(Auth::guard()->id()),
            'histories' => History::user(Auth::guard()->id())
        ]);
    }

    public function getPassword() {
        return view('website.user.password', [
            'user' => Auth::user()
        ]);
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
            if (!Hash::check($request->input('password'), Auth::user()->password)) {
                $validator->errors()->add('password_notmatch', 'รหัสผ่านไม่ถูกต้อง');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        else {
            $user = Auth::user();
            $user->password = $request->input('new_password');
            $user->push();

            History::addUserHistory(Auth::guard()->id(), 'เปลี่ยนรหัสผ่าน');

            return redirect()->action('Website\UserController@getProfile')
                ->with('password_changed', 'เปลี่ยนรหัสผ่านเสร็จเรียบร้อยแล้ว!');
        }
    }
}
