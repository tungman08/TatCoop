<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\DocumentType;
use App\Carousel;
use Storage;
use File;

class CarouselController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:admins', ['except' => 'getCarousel']);
    }

    public function index() {
        $carousels = Carousel::all();
        $types = DocumentType::where('id', '<>', 3)->get();

        return view('admin.carousel.index', [
            'document_types' => $types,
            'carousels' => $carousels
        ]);
    }

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