<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;

use App\User;
use App\Member;
use Statistic;
use History;
use Auth;
use DB;
use Mail;

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
     * Where to redirect users after logout.
     *
     * @var string
     */
    protected $redirectAfterLogout = '/auth/login';

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
        $this->middleware($this->guestMiddleware(), ['except' => 'getLogout']);
    }

    /**
     * Assign view for login form.
     *
     * @var string
     */
    protected $loginView = 'website.auth.login';

    /**
     * Handle an authentication attempt.
     *
     * @return Response
     */
    public function postLogin(Request $request) {
        // grab credentials from the request
        $credentials = $request->only('email', 'password');

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

        $validator->after(function($validator) use ($request) {
            $user = User::where('email', strtolower($request->input('email')))->first();

            if (!is_null($user)) {
                if (!$user->confirmed) {
                    if (DB::table('user_confirmations')->where('email', $user->email)->count() == 0) {
                        $token = hash_hmac('sha256', str_random(40), config('app.key'));
                        DB::table('user_confirmations')->insert([
                            'email' => strtolower($user->email), 
                            'token' => $token
                        ]);
                    }

                    $token = DB::table('user_confirmations')->where('email', $user->email)->first()->token;

                    Mail::send('website.emails.verify', ['token' => $token], function($message) use ($user) {
                        $message->to($user->email, $user->member->profile->name . " " . $user->member->profile->lastname)
                            ->subject('ยืนยันการสมัครเข้าใช้งานระบบเว็บไซต์ www.tatcoop.com');
                    });                        

                    $validator->errors()
                        ->add('verify', 'ยังไม่ได้ทำการยืนยันข้อมูลสมาชิกนี้ โปรดตรวจสอบอีเมลที่ได้รับจากระบบ');
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }
        else {
            if (Auth::guard($this->guard)->attempt($credentials, $request->has('remember'))) {
                Statistic::user(Auth::guard($this->guard)->id());
                History::addUserHistory(Auth::guard($this->guard)->id(), 'เข้าสู่ระบบ');
                $user = User::find(Auth::guard($this->guard)->id());

                if ($user->newaccount) {
                    $user->newaccount = false;
                    $user->save();
                }

                return redirect()->action('Website\MemberController@show', [ 'id' => $user->member_id ]);
            }
            else {
                return redirect()->back()
                    ->withErrors(trans('auth.failed'))
                    ->withInput($request->except('password'));
            }
        }
    }

    /**
     * Assign view for register form.
     *
     * @var string
     */
    protected $registerView = 'website.auth.register';

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
            'member_id' => 'เลขทะเบียนสมาชิก',
        ];

        $validator = Validator::make($register, $rules);
        $validator->setAttributeNames($attributeNames);

        $validator->after(function($validator) use ($request) {
            $member = Member::find($request->input('member_id'));

            if (!is_null($member)) {
                if (str_replace('-', '', $member->profile->citizen_code) != $request->input('citizen_code')) {
                    $validator->errors()
                        ->add('citizen_code_notmatch', 'ข้อมูล เลขประจำตัวประชาชน ไม่ตรงกับข้อมูลสมาชิก');
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except(['password', 'member_id', 'terms']));
        }
        else {
            DB::transaction(function() use ($request) {
                $user = new User([
                    'email' => strtolower($request->input('email')),
                    'password' => $request->input('password')
                ]);

                $member = Member::find($request->input('member_id'));
                $member->user()->save($user);

                $id = User::where('email', strtolower($request->input('email')))->first()->id;
                History::addUserHistory($id, 'สร้างบัญชีผู้ใช้');

                $token = hash_hmac('sha256', str_random(40), config('app.key'));
                $confirm = DB::table('user_confirmations')->insert([
                                'email' => strtolower($request->input('email')), 
                                'token' => $token
                            ]);

                Mail::send('website.emails.verify', ['token' => $token], function($message) use ($user) {
                    $message->to($user->email, $user->member->profile->name . " " . $user->member->profile->lastname)
                        ->subject('ยืนยันการสมัครเข้าใช้งานระบบเว็บไซต์ www.tatcoop.com');
                });
            });

            return redirect()->back()
                ->with('registed', 'ลงทะเบียนเรียบร้อยแล้ว คุณต้องเข้ายืนยันการใช้งานจากลิงก์ที่ส่งไปยังอีเมล ' . strtolower($request->input('email')));
        }
    }

    /**
     * Responds to requests to GET /auth/verify/SeMXnmSNLPzcQvWFnoTGdmj4OucAfe2UpbbrBu28HdY=
     */
    public function getVerify($token) {
        if(!$token) {
            return redirect()->action('Website\HomeController@index');
        }

        $confirm = DB::table('user_confirmations')
            ->where('token', $token)
            ->first();

        if (is_null($confirm)) {
            return redirect()->action('Website\AuthController@getLogin')
                ->with('verified', 'คุณเคยทำการยืนยันอีเมลไปแล้ว ไม่ต้องยืนยันซ้ำอีก สามารถเข้าใช้งานระบบได้เลย')
                ->withInput(['email' => $confirm->email]);
        }

        DB::transaction(function() use ($confirm) {
            $user = User::where('email', $confirm->email)->first();
            $user->forceFill(['confirmed' => true])->save();

            DB::table('user_confirmations')
                ->where('token', $confirm->token)
                ->delete();
        });

        return redirect()->action('Website\AuthController@getLogin')
            ->with('verified', 'คุณทำการยืนยันอีเมลเรียบร้อยแล้ว')
            ->withInput(['email' => $confirm->email]);
    }
}
