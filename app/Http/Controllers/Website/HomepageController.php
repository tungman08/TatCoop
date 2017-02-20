<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Statistic;
use App\Carousel;
use App\News;
use App\Knowledge;

use DB;
use Storage;

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
            'carousels' => Carousel::all(),
            'news' => News::orderBy('id', 'desc')->take(6)->get(),
            'knowledges' => Knowledge::orderBy('id', 'desc')->take(8)->get(),
            'statistics' => Statistic::visitor_statistic()
        ]);
    }
}
