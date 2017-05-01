<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Statistic;
use File;
use App\Carousel;
use App\News;
use App\Knowledge;

class HomeController extends Controller
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
        return view('website.home.index', [
            'carousels' => Carousel::all(),
            'news' => News::orderBy('id', 'desc')->take(6)->get(),
            'knowledges' => Knowledge::orderBy('id', 'desc')->take(8)->get(),
            'statistics' => Statistic::visitor_statistic()
        ]);
    }

    /**
     * Get an attach file.
     *
     * @return Response
     */
    public function getBackground($photo) {
        $path = storage_path('app/backgrounds') . '/' . $photo;

        if (!File::exists($path)) abort(404);

        $file = File::get($path);
        $header = File::mimeType($path);

        $response = response()->make($file, 200);
        $response->header("Content-Type", $header);

        return $response; 
    }
}
