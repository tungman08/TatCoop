<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;

use App\User;
use App\Member;
use App\Libraries\Statistic;
use Auth;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/member';

    /**
     * Only user authorize to access this section.
     *
     * @var string
     */
    protected $guard = 'users';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
    }

    /**
     * Responds to requests to GET /auth/login
     */
    public function getLogin() {
        return view('website.auth.login');
    }

    /**
     * Handle an authentication attempt.
     *
     * @return Response
     */
    public function postLogin(Request $request) {
        // grab credentials from the request
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember') ? true : false;

        $rules = [
            'email' => 'required|email|exists:users,email,deleted_at,NULL',
            'password' => 'required|min:6'
        ];

        $attributeNames = [
            'email' => 'อีเมล',
            'password' => 'รหัสผ่าน',
        ];

        $validator = Validator::make($credentials, $rules);
        $validator->setAttributeNames($attributeNames);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }
        else {
            if (Auth::guard($this->guard)->attempt($credentials, $remember)) {
                Statistic::user(Auth::guard($this->guard)->id());

                return redirect()->route('website.member.index');
            }
            else {
                return redirect()->back()
                    ->withErrors(trans('auth.failed'))
                    ->withInput($request->except('password'));
            }
        }
    }

    /**
     * Responds to requests to GET /auth/register
     */
    public function getRegister() {
        return view('website.auth.register');
    }

    /**
     * Handle an user registation.
     *
     * @return Response
     */
    public function postRegister(Request $request) {
        // grab inputs from the request
        $register = $request->except('terms');

        $rules = [
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'citizen_code' => 'required|min:13|exists:profiles,citizen_code,deleted_at,NULL',
            'member_id' => 'required|exists:members,id,leave_date,NULL,deleted_at,NULL|unique:users,member_id',
        ];

        $attributeNames = [
            'email' => 'อีเมล',
            'password' => 'รหัสผ่าน',
            'citizen_code' => 'เลขประจำตัวประชาชน',
            'member_id' => 'รหัสสมาชิก',
        ];

        $validator = Validator::make($register, $rules);
        $validator->setAttributeNames($attributeNames);

        $validator->after(function($validator) use ($request) {
            $member = Member::find($request->input('member_id'));

            if (!is_null($member)) {
                if ($member->profile->citizen_code != $request->input('citizen_code')) {
                    $validator->errors()->add('citizen_code_notmatch', 'ข้อมูล เลขประจำตัวประชาชน ไม่ตรงกับข้อมูลสมาชิก');
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except(['password', 'member_id', 'terms']));
        }
        else {
            $user = new User($request->only('email', 'password'));
            $member = Member::find($request->input('member_id'));
            $member->user()->save($user);

            return redirect()->route('website.auth.login')
                ->with('registed', 'ลงทะเบียนการใช้งานบริการอิเล็กทรอนิกส์เรียบร้อยแล้ว');
        }
    }
}
