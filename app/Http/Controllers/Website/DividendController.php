<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Auth;
use Diamond;
use App\Member;
use App\Dividend;
use App\Dividendmember;

class DividendController extends Controller
{
    /**
     * Only user authorize to access this section.
     *
     * @var string
     */
    protected $guard = 'users';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:users');
    }

    public function index() {
        $id = Auth::user()->member_id;
        $member = Member::find($id);
        $dividend_years = Dividend::whereDate('release_date', '<=', Diamond::today())->get();

        return view('website.dividend.index', [
            'member' => $member,
            'dividend_years' => collect($dividend_years),
            'dividends' => Dividendmember::where('dividend_id', $dividend_years->last()->id)
                ->where('member_id', $member->id)
                ->orderBy('dividend_date')
                ->get()
        ]);
   }
}
