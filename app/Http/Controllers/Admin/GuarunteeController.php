<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Member;
use App\Loan;

class GuarunteeController extends Controller
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

    public function getMember() {
        return view('admin.guaruntee.member');
    }

    public function index($id) {
        $member = Member::find($id);

        return view('admin.guaruntee.index', [
            'member' => $member
        ]);
    }
}
