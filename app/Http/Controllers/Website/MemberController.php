<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class MemberController extends Controller
{
    /**
     * Create a new member controller instance.
     *
     * @return void
     */
    public function __construct(){
        $this->middleware('user');
   }

   /**
    * Responds to requests to GET /member
    */
    public function getIndex() {
        return 'member';
    }
}
