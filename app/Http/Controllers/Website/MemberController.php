<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Auth;
use App\Member;
use Diamond;

class MemberController extends Controller
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

    public function index() {
        $member = Member::find(Auth::user()->member_id);
        $startYear = Diamond::parse($member->start_date)->year > 2019 ? Diamond::parse($member->start_date)->year : 2019;

        return view('website.member.index', [
            'member' => $member,
            'histories' => Member::where('profile_id', $member->profile_id)->get(),
            'startYear' => $startYear
        ]);
    }
}
