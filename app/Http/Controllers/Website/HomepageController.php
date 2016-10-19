<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Statistic;

class HomepageController extends Controller
{
    /**
     * Create a new homepage controller instance.
     *
     * @return void
     */
    public function __construct() {
       Statistic::visitor();
    }

    /**
     * Responds to requests to GET /
     */
    public function getIndex() {
        return view('website.homepage.index', [
            'statistics' => Statistic::visitor_statistic()
        ]);
    }

    /**
     * Responds to requests to GET /announce/1
     */
     public function getAnnounce($key) {
        return view('website.announce.index', [
            'key' => $key,
        ]);
    }
}
