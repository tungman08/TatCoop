<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\LoanType;
use App\Member;
use App\PaymentType;

class LoanController extends Controller
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
    
    public function getCreateNormal($id) {
        $member = Member::find($id);
        $loantype = LoanType::find(1);
        $employee_type = $member->profile->employee->employee_type->id;

        if ($employee_type < 3) {
            return view('admin.loan.normal.employee.create', [
                'member' => $member,
                'loantype' => $loantype
            ]);
        }
        else {
            return view('admin.loan.normal.outsider.create', [
                'member' => $member,
                'loantype' => $loantype
            ]);
        }
    }

    public function getCreateEmerging($id) {
        $member = Member::find($id);
        $loantype = LoanType::find(2);
        $employee_type = $member->profile->employee->employee_type->id;

        if ($employee_type < 3) {
            return view('admin.loan.emerging.employee.create', [
                'member' => $member,
                'loantype' => $loantype
            ]);
        }
        else {
            return view('admin.loan.emerging.outsider.create', [
                'member' => $member,
                'loantype' => $loantype
            ]);
        }
    }

    public function getCreateSpecial($id, $loantype_id) {
        $member = Member::find($id);
        $loantype = LoanType::find($loantype_id);
        $employee_type = $member->profile->employee->employee_type->id;

        if ($employee_type < 3) {
            return view('admin.loan.special.employee.create', [
                'member' => $member,
                'loantype' => $loantype
            ]);
        }
        else {
            return view('admin.loan.special.outsider.create', [
                'member' => $member,
                'loantype' => $loantype
            ]);
        }
    }
}
