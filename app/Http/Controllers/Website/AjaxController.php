<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use Bing;
use App\Theme;

class AjaxController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:users', ['except' => 'getBackground']);
    }

    /**
     * Get Bing photo of the day.
     *
     * @param  Request
     * @return Response
     */
    public function getBackground(Request $request) {

        return response()->json(Bing::setArgs(['date'=>$request->input('date')])->getImage());
    }

    public function postSkin(Request $request) {
        $skin = $request->input('skin');
                $skins = Theme::all();

                $user = Auth::user();
                $user->theme_id = Theme::where('code', $skin)->first()->id;
                $user->push();

        return $skins;
    }
}
