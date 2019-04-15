<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Auth;
use App\Member;

class GuarunteeController extends Controller
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
        $id = Auth::user()->member_id;
        $member = Member::find($id);

        return view('website.guaruntee.index', [
            'member' => $member
        ]);
    }
}
