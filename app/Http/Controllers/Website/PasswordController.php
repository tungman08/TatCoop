<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Validator;

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
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Responds to requests to GET /password
     */
    public function getRecovery() {
        return view('website.password.recovery');
    }

    /**
     * Handle an user recovery password.
     *
     * @return Response
     */
    public function postRecovery(Request $request) {
        // grab inputs from the request
        $email = $request->input('email');

        $rules = [
            'email' => 'required|email|exists:users,email,deleted_at,NULL'
        ];

        $attributeNames = [
            'email' => 'อีเมล'
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        else {
            return redirect()->back()
                ->with('sent', "ส่งลิงก์สำหรับตั้งค่าหรัสผ่านไปที่ $email เรียบร้อยแล้ว");
        }
    }
}
