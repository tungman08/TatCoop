<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Admin;
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
    protected $redirectTo = '/';

    /**
     * Only administartor authorize to access this section.
     *
     * @var string
     */
    protected $guard = 'admins';

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
        return view('admin.auth.login');
    }

    /**
     * Handle an authentication attempt.
     *
     * @return Response
     */
    public function postLogin(Request $request) {
        // grab credentials from the request
        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials)) {
            Statistic::addOfficer(Auth::id());

            return redirect()->route('admin.user.password');
        }
        else {
            return redirect()->back()->withErrors('ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง')->withInput($request->except('password'));
        }
    }
}
