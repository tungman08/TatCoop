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
}