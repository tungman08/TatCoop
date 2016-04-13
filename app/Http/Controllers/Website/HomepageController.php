<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class HomepageController extends Controller
{
    /**
     * Responds to requests to GET /
     */
    public function getIndex() {
        return view('website.homepage.index');
    }

    /**
     * Responds to requests to GET /announce/1
     */
     public function getAnnounce($id) {
        return view('website.announce.index');
    }
}
