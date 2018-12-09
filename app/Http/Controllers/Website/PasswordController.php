<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use App\User;
use Password;
use History;

class PasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Assign send email view for reset password.
     *
     * @var string
     */
    protected $linkRequestView = 'website.password.email';

    /**
     * Assign reset password form.
     *
     * @var string
     */
    protected $resetView = 'website.password.reset';

    /**
     * Where to redirect users after reset password.
     *
     * @var string
     */
    protected $redirectTo = '/auth/login';

    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email'], [], ['email' => 'อีเมล']);

        $broker = $this->getBroker();

        $response = Password::broker($broker)->sendResetLink(
            $request->only('email'), $this->resetEmailBuilder()
        );

        switch ($response) {
            case Password::RESET_LINK_SENT:
                return (!$this->isNewAccount($request->input('email'))) ? 
                    redirect()->back()->with('email', 'ส่งอีเมลสำหรับตั้งค่ารหัสผ่านแล้ว') : 
                    redirect()->back()->withErrors(['email' => 'ไม่สามารถส่งอีเมลสำหรับตั้งค่ารหัสผ่าน']);;

            case Password::INVALID_USER:
            default:
                return redirect()->back()->withErrors(['email' => 'ไม่สามารถส่งอีเมลสำหรับตั้งค่ารหัสผ่าน']);;
        }
    }

     /**
     * Get the password reset validation custom attributes.
     *
     * @return array
     */    
    protected function getResetValidationCustomAttributes() {
        $validator = [
            'email' => 'อีเมล',
            'password' => 'รหัสผ่าน',
        ];

        return $validator;
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->forceFill(['password' => $password])->save();

        History::addUserHistory($user->id, 'ตั้งค่ารหัสผ่านใหม่');
    }

    protected function isNewAccount($email) {
        $user = User::where('email', $email)->first();

        return $user->newaccount;
    }
}
