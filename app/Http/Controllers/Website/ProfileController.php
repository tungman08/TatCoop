<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Member;
use App\Subdistrict;
use App\Province;
use App\District;
use App\Prefix;
use App\Postcode;
use App\Profile;
use Auth;
use DB;
use Diamond;
use Hash;
use History;
use Validator;

class ProfileController extends Controller
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

    public function getIndex() {
        $member = Member::find(Auth::user()->member_id);

        return view('website.profile.index', [
            'member' => $member,
            'member_histories' => Member::where('profile_id', $member->profile_id)->get(),
            'user' => Auth::user(),
            'index' => 0,
            'count' => History::countUserHistory(Auth::guard()->id()),
            'histories' => History::user(Auth::guard()->id())
        ]);
    }

    public function getEdit() {
		$member = Member::find(Auth::user()->member_id);
		$provinces = Province::orderBy('name')->get();
		$districts = District::where('province_id', $member->profile->province_id)->orderBy('name')->get();
		$subdistricts = Subdistrict::where('district_id', $member->profile->district_id)->orderBy('name')->get();

		return view('website.profile.edit', [
			'member' => $member,
			'prefixs' => Prefix::all(),
			'provinces' => $provinces,
			'districts' => $districts,
			'subdistricts' => $subdistricts,
		]);
    }

    public function postUpdate(Request $request) {
        $id = Auth::user()->member_id;

        $rules = [
            'profile.birth_date' => 'required|date_format:Y-m-d', 
            'profile.address' => 'required', 
        ];

        $attributeNames = [
            'profile.birth_date' => 'วันเกิด',
            'profile.address' => 'ที่อยู่', 
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
                $member = Member::find($id);
                $profile = Profile::find($member->profile_id);
                $profile->address = $request->input('profile')['address'];
                $profile->province_id = $request->input('profile')['province_id'];
                $profile->district_id = $request->input('profile')['district_id'];
                $profile->subdistrict_id = $request->input('profile')['subdistrict_id'];
                $profile->postcode_id = Postcode::where('code', $request->input('profile')['postcode']['code'])->first()->id;
                $profile->birth_date = Diamond::parse($request->input('profile')['birth_date']);
                $profile->save();

                History::addUserHistory(Auth::guard()->id(), 'แก้ไขข้อมูล', 'แก้ไขข้อมูลส่วนตัว');
            });

            return redirect()->action('Website\ProfileController@getIndex')
                ->with('flash_message', 'แก้ไขข้อมูลเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function getPassword() {
        return view('website.profile.password', [
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

            return redirect()->action('Website\ProfileController@getIndex')
                ->with('password_changed', 'เปลี่ยนรหัสผ่านเสร็จเรียบร้อยแล้ว!');
        }
    }
}
