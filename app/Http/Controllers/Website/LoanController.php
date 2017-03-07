<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\LoanType;

class LoanController extends Controller
{
    public function getIndex() {
        $loan_types = LoanType::active()->get();

        return view('website.loan.index', [
            'loan_types' => $loan_types
        ]);
    }
}
