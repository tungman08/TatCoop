<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use File;

class BackgroundController extends Controller
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
        $this->middleware('auth:admins', ['except' => 'getBackground']);
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
