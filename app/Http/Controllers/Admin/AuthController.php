<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Admin;
use Statistic;
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
     * Where to redirect administartors after login / registration.
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
        $this->middleware($this->guestMiddleware(), ['except' => 'getLogout']);
    }

    /**
     * Assign view for login form.
     *
     * @var string
     */
    protected $loginView = 'admin.auth.login';

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
            'email' => 'required|email|exists:administrators,email,deleted_at,NULL',
            'password' => 'required|min:6'
        ];

        $validator = Validator::make($credentials, $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }
        else {
            if (Auth::guard($this->guard)->attempt($credentials, $remember)) {
                Statistic::administartor(Auth::guard($this->guard)->id());

                if (Auth::guard($this->guard)->user()->password_changed) {
                    return redirect()->route('admin.index');
                }
                else {
                    return redirect()->route('admin.user.password');
                }
            }
            else {
                return redirect()->back()
                    ->withErrors(trans('auth.failed'))
                    ->withInput($request->except('password'));
            }
        }
    }
}
