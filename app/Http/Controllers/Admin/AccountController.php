<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\OldEmail;
use Auth;
use DB;
use Diamond;
use History;
use Mail;
use Validator;

class AccountController extends Controller
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

    public function index() {
        return view('admin.account.index');
    }

    public function show($id) {
        return view('admin.account.show', [
            'user' => User::where('member_id', $id)->first()
        ]);
    }

    public function edit($id) {
        return view('admin.account.edit', [
            'user' => User::where('member_id', $id)->first()
        ]);
    }

    public function update(Request $request, $id) {
        $rules = [
            'new_email' => 'required|email|max:255|unique:users,email|confirmed',
            'new_email_confirmation' => 'required|email|max:255',
            'reamrk' => 'required'
        ];

        $attributeNames = [
            'new_email' => 'อีเมลบัญชีผู้ใช้ใหม่',
            'new_email_confirmation' => 'ยืนยันอีเมลบัญชีผู้ใช้ใหม่',
            'reamrk' => 'หมายเหตุ'
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        else {
            DB::transaction(function() use ($request, $id) {
                $user = User::find($id);
                $old_email = $user->email;

                $user->email = $request->input('new_email');
                $user->newaccount = true;
                $user->save();

                $oldemail = new OldEmail();
                $oldemail->email = $old_email;
                $oldemail->canceled_at = Diamond::today();
                $oldemail->remark = $request->input('remark');
                $user->old_emails()->save($oldemail);

                History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'แก้ไขข้อมูลอีเมลบัญชีผู้ใช้ระบบ');

                Mail::send('admin.account.email', ['old_email' => $old_email, 'email' => $user->email], function($message) use ($user) {
                    $message->to($user->email, $user->member->profile->fullName)
                        ->subject('ยืนยันเปลี่ยนแปลงชื่อบัญชีผู้ใช้ www.tatcoop.com');
                });
            });

            return redirect()->action('Admin\AccountController@show', ['id' => User::find($id)->member_id])
                ->with('flash_message', 'แก้ไขอีเมลบัญชีผู้ใช้เรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }
}