<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use File;

class CarouselController extends Controller
{
    public function getCarousel($image) {
        $path = storage_path('app/carousels') . '/' . $image;

        if(!File::exists($path)) abort(404);

        $file = File::get($path);
        $header = File::mimeType($path);

        $response = response()->make($file, 200);
        $response->header("Content-Type", $header);

        return $response; 
    }
}
