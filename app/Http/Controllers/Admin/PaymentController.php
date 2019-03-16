<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Billing;
use App\Member;
use App\Loan;
use App\Payment;
use App\PaymentAttachment;
use LoanCalculator;
use Validator;
use FileManager;
use DB;
use Diamond;
use History;
use Auth;
use PDF;
use Response;
use Storage;
use stdClass;

class PaymentController extends Controller
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

    public function create($member_id, $loan_id) {
        return view('admin.payment.create', [
            'member' => Member::find($member_id),
            'loan' => Loan::find($loan_id)
        ]);
    }

    public function store($member_id, $loan_id, Request $request) {
        $rules = [
            'pay_date' => 'required',
            'principle' => 'required', 
            'interest' => 'required'
        ];

        $attributeNames = [
            'pay_date' => 'วันที่ชำระ', 
            'principle' => 'เงินต้น',
            'interest' => 'ดอกเบี้ย'
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        $validator->after(function($validator) use ($request, $loan_id) {
            if (Payment::where('loan_id', $loan_id)->where('period', '>=', $request->input('period'))->count() > 0) {
                $validator->errors()->add('duplicate', 'เลขที่งวดซ้ำหรือน้อยกว่าในระบบ');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        else {
            DB::transaction(function() use ($request, $loan_id) {
                $payment = new Payment();
                $payment->loan_id = $loan_id;
                $payment->period = $request->input('period');
                $payment->pay_date = Diamond::parse($request->input('pay_date'));
                $payment->principle = floatval(str_replace(',', '', $request->input('principle')));
                $payment->interest = floatval(str_replace(',', '', $request->input('interest')));
                $payment->save();
    
                $loan = Loan::find($loan_id);        
            
                if (abs($loan->outstanding - $loan->payments->sum('principle')) < 0.01) {
                    // $loan->completed_at = Diamond::parse($request->input('pay_date'));
                    // $loan->save();
                    $this->closeDate($loan_id);
                }

                if ($request->hasFile('attachment')) {
                    $file = $request->file('attachment');
                    $display = mb_ereg_replace('\s+', ' ', basename($file->getClientOriginalName(), '.' . $file->getClientOriginalExtension()));
                    $filename = time() . uniqid() . '.' . $file->getClientOriginalExtension();
                    $path = ($file->getRealPath() != false) ? $file->getRealPath() : $file->getPathname();
                    Storage::disk('attachments')->put($filename, file_get_contents($path));
    
                    $attachment = new PaymentAttachment([
                        'file' => $filename,
                        'display' => $display
                    ]);
                    $payment->attachments()->save($attachment);
                }

                History::addAdminHistory(Auth::guard($this->guard)->id(), 'เพิ่มข้อมูล', 'ป้อนการชำระเงินกู้ ' . $loan->code);
            });

            return redirect()->action('Admin\LoanController@show', [ 'member' => $member_id, 'loan' => $loan_id ])
                ->with('flash_message', 'ข้อมูลการชำระเงินกู้ถูกป้อนเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function show($member_id, $loan_id, $payment_id) {
        $loan = Loan::find($loan_id);
        $payment = Payment::find($payment_id);

        return view('admin.payment.show', [
            'member' => $loan->member,
            'loan' => $loan,
            'payment' => $payment
        ]);
    }

    public function getClose($member_id, $loan_id) {
        return view('admin.payment.close', [
            'member' => Member::find($member_id),
            'loan' => Loan::find($loan_id)
        ]);
    }

    public function postClose($member_id, $loan_id, Request $request) { 
        $rules = [
            'pay_date' => 'required',
            'principle' => 'required', 
            'interest' => 'required'
        ];

        $attributeNames = [
            'pay_date' => 'วันที่ชำระ', 
            'principle' => 'เงินต้น',
            'interest' => 'ดอกเบี้ย'
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        else {
            DB::transaction(function() use ($request, $loan_id) {
                $payment = new Payment();
                $payment->loan_id = $loan_id;
                $payment->pay_date = Diamond::parse($request->input('pay_date'));
                $payment->principle = floatval(str_replace(',', '', $request->input('principle')));
                $payment->interest = floatval(str_replace(',', '', $request->input('interest')));
                $payment->save();

                // $loan = Loan::find($loan_id); 
                // $loan->completed_at = Diamond::parse($request->input('pay_date'));
                // $loan->save();
                $this->closeDate($loan_id);
    
                History::addAdminHistory(Auth::guard($this->guard)->id(), 'เพิ่มข้อมูล', 'ปิดยอดเงินกู้ ' . $loan->code);
            });

            return redirect()->action('Admin\LoanController@show', [ 'member' => $member_id, 'loan' => $loan_id ])
                ->with('flash_message', ($request->input('remark') != '-') 
                    ? 'ข้อมูลการปิดยอดเงินกู้ถูกป้อนเรียบร้อยแล้ว (' . $request->input('remark') . ')' 
                    : 'ข้อมูลการปิดยอดเงินกู้ถูกป้อนเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function getCalculate($member_id, $loan_id) {
        return view('admin.payment.calculate', [
            'member' => Member::find($member_id),
            'loan' => Loan::find($loan_id)
        ]);
    }

    // public function getAutoPayment() {
    //    return view('admin.payment.auto');
    // }

    // public function postAutoPayment(Request $request) {
    //    $date = Diamond::parse($request->input('month') . '-1')->endOfMonth();
	
    //    $members = Db::table('members')
    //        ->join('profiles', 'members.profile_id', '=', 'profiles.id')
    //        ->join('employees', 'profiles.id', '=', 'employees.profile_id')
    //        ->join('employee_types', 'employees.employee_type_id', '=', 'employee_types.id')
    //        ->join('loans', 'members.id', '=', 'loans.member_id')
    //        ->where('employees.employee_type_id', 1)
    //        ->whereDate('members.start_date', '<', $date)
    //        ->whereNull('loans.completed_at')
    //        ->groupBy('members.id')
    //        ->select('members.id')
    //        ->get();
	
    //    DB::transaction(function() use ($members, $date) {
    //        foreach($members as $member) {
    //            $loans = Loan::where('member_id', $member->id)
    //                ->whereNull('completed_at')
    //                ->get();
	
    //            foreach($loans as $loan) {
    //                $pay = LoanCalculator::monthly_payment($loan, $date);
	
    //                if ($pay->principle > 0 && $pay->interest > 0) {
    //                    $payment = new Payment();
    //                    $payment->pay_date = $date;
    //                    $payment->principle = $pay->principle;
    //                    $payment->interest = $pay->interest;
    
    //                    $loan->payments()->save($payment);
    //                }
    //            }
    //        }
	
    //        History::addAdminHistory(Auth::guard($this->guard)->id(), 'ป้อนการชำระเงินกู้แบบอัตโนมัติ', 'ทำรายการชำระเงินกู้อัตโนมัติประจำเดือน' . $date->thai_format('F Y'));
    //    });
	
    //    return redirect()->action('Admin\LoanController@getMember')
    //        ->with('flash_message', 'ทำรายการชำระเงินกู้อัตโนมัติประจำเดือน' . $date->thai_format('F Y') . ' จำนวน ' . $members->count() . ' คน เรียบร้อยแล้ว')
    //        ->with('callout_class', 'callout-success');
    // }

    public function edit($member_id, $loan_id, $payment_id) {
        return view('admin.payment.edit', [
            'member' => Member::find($member_id),
            'loan' => Loan::find($loan_id),
            'payment' => Payment::find($payment_id)
        ]);
    }

    public function update($member_id, $loan_id, $payment_id, Request $request) {
        $rules = [
            'pay_date' => 'required',
            'principle' => 'required',
            'interest' => 'required'
        ];

        $attributeNames = [
            'pay_date' => 'วันที่ชำระ', 
            'principle' => 'เงินต้น',
            'interest' => 'ดอกเบี้ย'
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        $validator->after(function($validator) use ($request, $payment_id, $loan_id) {
            if (Payment::where('id', '<>', $payment_id)->where('loan_id', $loan_id)->where('period', $request->input('period'))->count() > 0) {
                $validator->errors()->add('duplicate', 'เลขที่งวดซ้ำกับในระบบ');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        else {
            DB::transaction(function() use ($request, $loan_id, $payment_id) {
                $payment = Payment::find($payment_id);
                $payment->period = $request->input('period');
                $payment->pay_date = Diamond::parse($request->input('pay_date'));
                $payment->principle = floatval(str_replace(',', '', $request->input('principle')));
                $payment->interest = floatval(str_replace(',', '', $request->input('interest')));
                $payment->save();

                $loan = Loan::find($loan_id); 
                if (abs($loan->outstanding - $loan->payments->sum('principle')) < 0.01) {
                    // $loan->completed_at = Diamond::parse($request->input('pay_date'));
                    // $loan->save();
                    $this->closeDate($loan_id);
                }
                else {
                    $loan->completed_at = null;
                    $loan->save();
                }
    
                History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'การผ่อนชำระเงินกู้ ' . $loan->code);
            });

            return redirect()->action('Admin\PaymentController@show', [ 'member' => $member_id, 'loan' => $loan_id, 'payment' => $payment_id])
                ->with('flash_message', 'แก้ไขข้อมูลการชำระเงินกู้เรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function destroy($member_id, $loan_id, $payment_id) {
        DB::transaction(function() use ($payment_id, $loan_id) {
            $payment = Payment::find($payment_id);
            $pay_date = $payment->pay_date;

            foreach ($payment->attachments as $attachment) {
                Storage::disk('attachments')->delete($attachment->file);
            }

            $payment->delete();

            $loan = Loan::find($loan_id); 
            if (abs($loan->outstanding - $loan->payments->sum('principle')) < 0.01) {
                // $loan->completed_at = $pay_date;
                // $loan->save();
                $this->closeDate($loan_id);
            }
            else {
                $loan->completed_at = null;
                $loan->save();
            }
            
            History::addAdminHistory(Auth::guard($this->guard)->id(), 'ลบข้อมูล', 'ลบข้อมูลการผ่อนชำระเงินกู้ ' . $loan->code);
        });

        return redirect()->action('Admin\LoanController@show', [ 'member' => $member_id, 'loan' => $loan_id])
            ->with('flash_message', 'ลบข้อมูลการชำระเงินกู้เรียบร้อยแล้ว')
            ->with('callout_class', 'callout-success');
    }

    
    public function getBilling($member_id, $loan_id, $payment_id, $paydate) {
        $billdate = Diamond::parse($paydate);
        $member = Member::find($member_id);
        $loan = Loan::find($loan_id);
        $payment = Payment::find($payment_id);

        return view('admin.payment.billing', [
            'member' => $member,
            'loan' => $loan,
            'billing' => Billing::latest()->first(),
            'payment' => $payment,
            'billno' => $billdate->thai_format('Y') . str_pad($payment->loan->id, 8, '0', STR_PAD_LEFT),
            'date' => $billdate
        ]);
    }

	public function getPrintBilling($member_id, $loan_id, $payment_id, $paydate) {
		$billdate = Diamond::parse($paydate);
		$member = Member::find($member_id);
		$loan = Loan::find($loan_id);
		$payment = Payment::find($payment_id);

		return view('admin.payment.print', [
			'member' => $member,
			'loan' => $loan,
			'billing' => Billing::latest()->first(),
			'payment' => $payment,
			'billno' => $billdate->thai_format('Y') . str_pad($payment->loan->id, 8, '0', STR_PAD_LEFT),
			'date' => $billdate
		]);
    }

    public function getPdfBilling($member_id, $loan_id, $payment_id, $paydate) {
        $billdate = Diamond::parse($paydate);
        $member = Member::find($member_id);
        $loan = Loan::find($loan_id);
        $payment = Payment::find($payment_id);

        $data = [
            'member' => $member,
            'loan' => $loan,
            'billing' => Billing::latest()->first(),
            'payment' => $payment,
            'billno' => $billdate->thai_format('Y') . str_pad($payment->loan->id, 8, '0', STR_PAD_LEFT),
            'date' => $billdate
        ];

        return PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
            ->loadView('admin.payment.pdf', $data)->download('ใบเสร็จรับเงินค่างวด สัญญาเลขที่ ' . $loan->code . ' เดือน-' . $billdate->thai_format('M-Y') . '.pdf');
    }

    public function postUploadFile(Request $request) {
		$id = $request->input('payment_id');
		$file = $request->file('file');

		$display = mb_ereg_replace('\s+', ' ', basename($file->getClientOriginalName(), '.' . $file->getClientOriginalExtension()));
        $filename = time() . uniqid() . '.' . $file->getClientOriginalExtension();
		$path = ($file->getRealPath() != false) ? $file->getRealPath() : $file->getPathname();

        Storage::disk('attachments')->put($filename, file_get_contents($path));
    
        $attachment = new PaymentAttachment([
            'file' => $filename,
            'display' => $display
        ]);

		$payment = Payment::find($id);
        $payment->attachments()->save($attachment);

		$data = new stdClass();
        $data->id = PaymentAttachment::where('file',$filename)->first()->id;
		$data->href = FileManager::get('attachments', $filename);
        $data->display = $display;

        return Response::json($data);
    }

	public function postDeleteFile(Request $request) {
		$id = $request->input('id');
		$attachment = PaymentAttachment::find($id);
		$payment_id = $attachment->payment_id;

		Storage::disk('attachments')->delete($attachment->file);
        $attachment->delete();

		$payment = Payment::find($payment_id);
		$data = new stdClass();
		$data->id = $id;
        $data->count = $payment->attachments->count();

        return Response::json($data);
    }

    protected function closeDate($loan_id) {
        $loan = Loan::find($loan_id);
        $last_pay_date = $loan->payments->max('pay_date');

        $loan->completed_at = Diamond::parse($last_pay_date);
        $loan->save(); 
    }
}
