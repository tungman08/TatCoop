<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class MemberController extends Controller
{
   /**
    * Responds to requests to GET /member
    */
    public function getIndex() {
        return 'member';
    }

    /**
     * Responds to requests to GET /member/admin
     */
     public function getAdmin() {
         return 'admin';
     }
}
