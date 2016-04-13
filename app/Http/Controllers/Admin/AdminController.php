<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    /**
     * Create a new administartor controller instance.
     *
     * @return void
     */
    public function __construct(){
        $this->middleware('admin', ['except' => 'getUnauthorize']);
   }

   /**
    * Responds to requests to GET /
    */
    public function getIndex() {
        return 'admin';
    }

    /**
     * Responds to requests to GET /unauthorize
     */
     public function getUnauthorize() {
         return 'unauthorize';
     }
}
