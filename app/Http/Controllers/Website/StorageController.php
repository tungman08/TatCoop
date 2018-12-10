<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use File;

class StorageController extends Controller
{
    public function getFile($directory, $filename) {
        $path = storage_path('app/' . $directory) . '/' . $filename;

        if(!File::exists($path)) 
            abort(404);

        $response = response()->make(File::get($path), 200);
        $response->header("Content-Type", File::mimeType($path));

        return $response; 
    }

    public function getDownload($directory, $filename, $displayname) {
        $ext = File::extension($displayname);
        $path = storage_path('app/' . $directory) . '/' . $filename . '.' . $ext;

        if (!File::exists($path)) 
            abort(404);

        return response()->download($path, $displayname, ['Content-Type' => File::mimeType($path)]); 
    }
}
