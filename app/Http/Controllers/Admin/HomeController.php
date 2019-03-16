<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Dashboard;

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
        $info = Dashboard::info();

        return view('admin.home.index', [
            'info' => $info
        ]);
    }

    public function postDashboard(Request $request) {
        $year = $request->input('year');
        $summary = Dashboard::summary($year);
        $chart = Dashboard::chart($year);

        return compact('summary', 'chart');
    }
}
