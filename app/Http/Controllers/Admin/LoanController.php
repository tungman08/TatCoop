<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Member;

class LoanController extends Controller
{
    public function create($id) {
        return view('admin.member.loan.index', [
            'member' => Member::find($id)
        ]);
    }}
