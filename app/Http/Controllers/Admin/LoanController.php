<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\LoanType;
use App\Member;
use App\Payment;
use App\PaymentType;
use App\Loan;
use App\LoanAttachment;
use App\Shareholding;
use DB;
use Auth;
use Diamond;
use History;
use PDF;
use LoanManager;
use Storage;
use Response;
use Validator;
use LoanCalculator;

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

    public function getMember() {
        return view('admin.loan.member', [
            'loans' => Loan::whereNull('completed_at')->count(),
            'highest_loan' => Member::join('profiles', 'members.profile_id', '=', 'profiles.id')
                ->join('prefixs', 'profiles.prefix_id', '=', 'prefixs.id')
                ->join('loans', 'members.id', '=', 'loans.member_id')
                ->join(DB::raw('(SELECT loans.member_id as member_id, SUM(payments.principle) as principle FROM loans INNER JOIN payments ON loans.id = payments.loan_id WHERE loans.code IS NOT NULL AND loans.completed_at IS NULL GROUP BY loans.member_id) as l'), 'members.id', '=', 'l.member_id')
                ->whereNull('members.leave_date')
                ->whereNull('loans.completed_at')
                ->whereNotNull('loans.code')
                ->groupBy('members.id', 'prefixs.name', 'profiles.name', 'profiles.lastname')
                ->select(
                    DB::raw("CONCAT(LPAD(members.id, 5, '0'), ' - ', prefixs.name, ' ', profiles.name, ' ', profiles.lastname) as fullname"),
                    DB::raw("SUM(loans.outstanding) - l.principle as balance"))
                ->orderByRaw('(SUM(loans.outstanding) - l.principle) desc')
                ->first()
        ]);
    }

    public function index($member_id) {
        $member = Member::find($member_id);

        return view('admin.loan.index', [
            'member' => $member,
            'loans' => Loan::where('member_id', $member->id)->whereNotNull('code')->orderBy('loaned_at', 'desc')->orderBy('completed_at', 'asc')->get(),
            'loantypes' => LoanType::active()->get()
        ]);
    }

    public function show($member_id, $id) {
        $member = Member::find($member_id);
        $loan = Loan::find($id);
        $payments = Payment::where('loan_id', $id)
            ->orderBy('pay_date', 'desc')
            ->get();

         return view('admin.loan.show', [
            'member' => $member,
            'loan' => $loan,
            'payments' => $payments
        ]);       
    }

    public function getCreateLoan($member_id, $loantype_id) {
        LoanManager::delete_incomplete($member_id);

        switch ($loantype_id) {
            case 1: // กู้สามัญ
                return $this->createNormalLoan($member_id);
                break;
            case 2: // กู้ฉุกเฉิน
                return $this->createEmergingLoan($member_id);
                break;
            default: // กู้เฉพาะกิจ
                return $this->createSpecialLoan($member_id, $loantype_id);
                break;
        }
    }

    protected function createNormalLoan($member_id) {
        $member = Member::find($member_id);
        $loans = Loan::where('member_id', $member_id)
            ->where('loan_type_id', 1)
            ->whereNotNull('code')
            ->whereNull('completed_at')
            ->count();

        if ($loans == 0) { // กู้ใหม่
            if ($member->profile->employee->employee_type_id == 1) { // พนักงาน/ลูกจ้าง ททท.
                return redirect()->route('service.loan.create.normal.employee', [
                    'member_id' => $member->id,
                    'loan_id' => 0,
                    'step' => 1
                ]);
            }
            else { // บุคคลภายนอก
                return redirect()->route('service.loan.create.normal.outsider', [
                    'member_id' => $member->id,
                    'loan_id' => 0,
                    'step' => 1
                ]);
            }
        }
        else { // ไม่สามารถกู้ได้   
            return redirect()->back()
                ->with('flash_message', 'ไม่สามารถกู้ได้ กรุณาปิดยอดสัญญาเงินกู้สามัญก่อน')
                ->with('callout_class', 'callout-danger');
        }
    }

    protected function createEmergingLoan($member_id) {
        $member = Member::find($member_id);
        $loans = Loan::where('member_id', $member_id)
            ->where('loan_type_id', 2)
            ->whereNotNull('code')
            ->whereNull('completed_at')
            ->count();

        if ($loans == 0) { // กู้ใหม่
            if ($member->profile->employee->employee_type_id == 1) { // พนักงาน/ลูกจ้าง ททท.
                return redirect()->route('service.loan.create.emerging.employee', [
                    'member_id' => $member->id,
                    'loan_id' => 0,
                    'step' => 1
                ]);
            }
            else { // บุคคลภายนอก
                return redirect()->route('service.loan.create.emerging.outsider', [
                    'member_id' => $member->id,
                    'loan_id' => 0,
                    'step' => 1
                ]);
            }
        }
        else { // ไม่สามารถกู้ได้   
            return redirect()->back()
                ->with('flash_message', 'ไม่สามารถกู้ได้ กรุณาปิดยอดสัญญาเงินกู้ฉุกเฉินก่อน')
                ->with('callout_class', 'callout-danger');
        }
    }

    protected function createSpecialLoan($member_id, $loantype_id) {
        $member = Member::find($member_id);
        $loans = Loan::where('member_id', $member_id)
            ->where('loan_type_id', $loantype_id)
            ->whereNotNull('code')
            ->whereNull('completed_at')
            ->count();

        if ($loans == 0) { // กู้ใหม่
            if ($member->profile->employee->employee_type_id == 1) { // พนักงาน/ลูกจ้าง ททท.
                return redirect()->route('service.loan.create.special.employee', [
                    'member_id' => $member->id,
                    'loantype_id' => $loantype_id,
                    'loan_id' => 0,
                    'step' => 1
                ]);
            }
            else { // บุคคลภายนอก
                return redirect()->route('service.loan.create.special.outsider', [
                    'member_id' => $member->id,
                    'loantype_id' => $loantype_id,
                    'loan_id' => 0,
                    'step' => 1
                ]);
            }
        }
        else { // ไม่สามารถกู้ได้  
            return redirect()->back()
                ->with('flash_message', 'ไม่สามารถกู้ได้ กรุณาปิดยอดสัญญาเงินกู้เฉพาะกิจก่อน')
                ->with('callout_class', 'callout-danger');
        }
    }

    public function edit($member_id, $id) {
        $member = Member::find($member_id);
        $loan = Loan::find($id);

        return view('admin.loan.edit', [
            'member' => $member,
            'loan' => $loan,
        ]);  
    }

    public function update($member_id, $id, Request $request) {
        $rules = [
            'code' => 'required',
            'loaned_at' => 'required|date_format:Y-m-d'
        ];
        
        $attributeNames = [
            'code' => 'เลขที่สัญญา',
            'loaned_at' => 'วันที่ทำสัญญา'
        ];    

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        $validator->after(function($validator) use ($request, $id) {
            $loan_type_id = Loan::find($id)->loan_type_id;

            if (Loan::where('id', '<>', $id)
                ->where('loan_type_id', $loan_type_id)
                ->where('code', $request->input('code'))
                ->count() > 0) {
                    
                $validator->errors()->add('duplicate', 'เลขที่สัญญาซ้ำ');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        else {
            DB::transaction(function() use ($request, $id) {
                $loan = Loan::find($id);
                $loan->code = $request->input('code');
                $loan->loaned_at = Diamond::parse($request->input('loaned_at'));
                $loan->save();

                History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'แก้ไขข้อมูลสัญญาเงินกู้เลขที่ ' . $request->input('code'));
            });

            return redirect()->action('Admin\LoanController@show', ['member_id' => $member_id, 'id' => $id])
                ->with('flash_message', 'แก้ไขสัญญาเงินกู้เลขที่ ' . $request->input('code') . ' เรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function destroy($member_id, $id) {
        $loan = Loan::find($id); 

        DB::transaction(function() use ($loan) {
            foreach ($loan->attachments as $attachment) {
                Storage::disk('loans')->delete($attachment->file);
            }

            $loan_code = $loan->code;
            $loan->delete();

            History::addAdminHistory(Auth::guard($this->guard)->id(), 'ลบข้อมูล', 'ลบข้อมูลสัญญาเงินกู้เลขที่ ' . $loan_code);
        });

        return redirect()->action('Admin\LoanController@index', [ 'member_id' => $loan->member_id])
            ->with('flash_message', 'ลบข้อมูลสัญญาเงินกู้เรียบร้อยแล้ว')
            ->with('callout_class', 'callout-success');
    }

    public function getCalSurety($member_id) {
        $member = Member::find($member_id);

        return view('admin.loan.calsurety', [
            'member' => $member
        ]);
    }

    public function getEditSureties($member_id, $loan_id) {
        $member = Member::find($member_id);
        $loan = Loan::find($loan_id);

        return view('admin.loan.sureties', [
            'member' => $member,
            'loan' => $loan,
        ]);  
    }

    public function getLoanList() {
        return view('admin.loan.loanlist');
    }

    public function getAvailable() {
        return view('admin.loan.available');
    }

    public function getDebt($member_id, Request $request) {
        $member = Member::find($member_id);
        $year = $request->query('year');
        $shareholdings = Shareholding::where('member_id', $member_id)
            ->get();
        $loans = Loan::where('member_id', $member_id)
            ->whereNotNull('code')
            //->whereNull('completed_at')
            ->where(function ($query) {
                $query->whereNull('completed_at')
                    ->orWhere(function ($sub) {
                        $sub->whereYear('completed_at', '=', \Carbon\Carbon::today()->year);
                    });
            })->get();

        return view('admin.loan.debt', [
            'member' => $member,
            'selected_year' => $year,
            'shareholdings' => $shareholdings,
            'loans' => $loans
        ]);
    }

    public function getDebtPrint($member_id, Request $request) {
        $member = Member::find($member_id);
        $year = $request->query('year');
        $shareholdings = Shareholding::where('member_id', $member_id)
            ->get();
        $loans = Loan::where('member_id', $member_id)
            ->whereNotNull('code')
            //->whereNull('completed_at')
            ->where(function ($query) {
                $query->whereNull('completed_at')
                    ->orWhere(function ($sub) {
                        $sub->whereYear('completed_at', '=', \Carbon\Carbon::today()->year);
                    });
            })->get();

        return view('admin.loan.debtprint', [
            'member' => $member,
            'selected_year' => $year,
            'shareholdings' => $shareholdings,
            'loans' => $loans
        ]);
    }

    public function getDebtPdf($member_id, Request $request) {
        $member = Member::find($member_id);
        $year = $request->query('year');
        $shareholdings = Shareholding::where('member_id', $member_id)
            ->get();
        $loans = Loan::where('member_id', $member_id)
            ->whereNotNull('code')
            //->whereNull('completed_at')
            ->where(function ($query) {
                $query->whereNull('completed_at')
                    ->orWhere(function ($sub) {
                        $sub->whereYear('completed_at', '=', \Carbon\Carbon::today()->year);
                    });
            })->get();

        $data = [
            'member' => Member::find($member_id),
            'selected_year' => $year,
            'shareholdings' => $shareholdings,
            'loans' => $loans
        ];

        return PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
            ->loadView('admin.loan.debtpdf', $data)->download('ทะเบียนหนี้-' . $member->profile->name . '-' . $member->profile->lastname . '-' . Diamond::today()->thai_format('j-M-Y') . '.pdf');
    }

    public function postShowFiles(Request $request) {
        $id = $request->input('id');
        $loan = Loan::find($id);

        return $loan->attachments;
    }

    public function postUploadFile(Request $request) {
        $file = $request->file('file');
        $loan_id = $request->input('loan_id');

        $display = mb_ereg_replace('\s+', ' ', basename($file->getClientOriginalName(), '.' . $file->getClientOriginalExtension()));
        $filename = time() . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = ($file->getRealPath() != false) ? $file->getRealPath() : $file->getPathname();
        Storage::disk('loans')->put($filename, file_get_contents($path));

        LoanAttachment::create([
            'loan_id' => $loan_id,
            'display' => $display,
            'file' => $filename
        ]);

        $loan = Loan::find($loan_id);
        History::addAdminHistory(Auth::guard($this->guard)->id(), 'เพิ่มข้อมูล', 'เพิ่มเอกสารเงินกู้' . $loan->loanType->name . ' เลขที่ ' . $loan->code);

        return Response::json(true);
    }
    
    public function postDeleteFile(Request $request) {
        $id = $request->input('id');
        $document = LoanAttachment::find($id);
        $loan = Loan::find($document->loan_id);
        History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'ลบเอกสารเงินกู้' . $loan->loanType->name . ' เลขที่ ' . $loan->code);

        Storage::disk('loans')->delete($document->file);
        $document->delete();

        return Response::json(true);
    }

    public function getPmt($member_id, $id) {
        $member = Member::find($member_id);
        $loan = Loan::find($id);

        return view('admin.loan.pmt', [
            'member' => $member,
            'loan' => $loan
        ]);
    }

    public function postPmt($member_id, $id, Request $request) {
        $rules = [
            'pmt' => 'required|numeric', 
        ];

        $attributeNames = [
            'pmt' => 'PMT', 
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        else {
            DB::transaction(function() use ($request, $id) {
                $loan = Loan::find($id);
                $loan->pmt = ($request->input('pmt') == LoanCalculator::pmt($loan->rate, $loan->outstanding, $loan->period)) ? 0 : $request->input('pmt');
                $loan->save();

                History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'แก้ไขค่า PMT ของ สัญญาเงินกู้ประเภท ' . $loan->loanType->name . ' เลขที่ ' . $loan->code . ' ของ ' . $loan->member->profile->fullname);
            });

            return redirect()->action('Admin\LoanController@show', ['member_id' => $member_id, 'id' => $id])
                ->with('flash_message', 'ข้อมูลค่า PMT ถูกแก้ไขเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }
}
