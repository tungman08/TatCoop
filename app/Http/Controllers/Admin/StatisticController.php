<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Diamond;
use Statistic;

class StatisticController extends Controller
{
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
        $website = Statistic::visitor_statistic();
        $webuser = Statistic::user_statistic();
        $webapp = Statistic::administrator_statistic();

        return view('admin.statistic.index', [
            'date' => Diamond::today(),
            'website' => $website,
            'webuser' => $webuser,
            'webapp' => $webapp
        ]);
    }
}
