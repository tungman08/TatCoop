<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Member;
use App\Shareholding;

class HomeController extends Controller
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

    /**
     * Responds to requests to GET /
     */
    public function getIndex() {
        return view('admin.home.index', [
            'member_amount' => Member::whereNull('leave_date')->count(),
            'member_shareholding' => Shareholding::all()->sum('amount')
        ]);
    }
}
