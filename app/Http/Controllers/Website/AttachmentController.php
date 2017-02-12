<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use File;

class AttachmentController extends Controller
{
    /**
     * Get an attach file.
     *
     * @return Response
     */
    public function getAttachment($file) {
        $path = storage_path('app/attachments') . '/' . $file;

        if(!File::exists($path)) abort(404);

        $file = File::get($path);
        $header = File::mimeType($path);

        $response = response()->make($file, 200);
        $response->header("Content-Type", $header);

        return $response; 
    }
}
