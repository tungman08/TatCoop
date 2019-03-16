<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB;
use App\Member;
use App\Loan;

class GuarunteeController extends Controller
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
        $this->middleware('auth:admins');
    }

    public function getMember() {
        $members = DB::table('members')
            ->leftJoin('loan_member', 'members.id', '=', 'loan_member.member_id')
            ->leftJoin('loans', 'loan_member.loan_id', '=', 'loans.id')
            ->whereNull('members.leave_date')
            //->whereNull('loans.completed_at')
            //->whereNotNull('loans.code')
            ->groupBy('members.id')
            ->select([
				DB::raw("IF(loan_member.yourself is not null, IF(SUM(IF(loan_member.yourself = 0 AND loans.code IS NOT NULL AND loans.completed_at IS NULL, 1, 0)) < 2, 2 - SUM(IF(loan_member.yourself = 0 AND loans.code IS NOT NULL AND loans.completed_at IS NULL, 1, 0)), 0), 2) as status")
            ])
            ->get();

        return view('admin.guaruntee.member', [
            'full' => count(array_filter($members, function($obj) { return $obj->status == 0; })),
            'available1' => count(array_filter($members, function($obj) { return $obj->status == 1; })),
            'available2' => count(array_filter($members, function($obj) { return $obj->status == 2; }))
        ]);
    }

    public function index($id) {
        $member = Member::find($id);

        return view('admin.guaruntee.index', [
            'member' => $member
        ]);
    }
}
