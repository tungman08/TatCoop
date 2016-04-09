<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class AnnounceController extends Controller
{
    public function showDocs() {
        return view('announce.docs');
    }
}
