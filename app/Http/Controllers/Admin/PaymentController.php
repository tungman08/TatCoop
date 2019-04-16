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

        $validator->after(function($validator) use ($member_id, $loan_id) {
            $member = Member::find($member_id);
            $loan = Loan::find($loan_id);

            // เงื่อนไขการปิดยอดกู้สามัญ
            if ($loan->loan_type_id == 1) {
                $period = ($loan->payments->count() / $loan->period) * 100;
                $payment = ($loan->payments->sum('principle') / $loan->outstanding) * 100;

                // เงื่อนไขปิดยอด ต้องผ่อนไปแล้ว 1 ใน 10 หรือ ชำระเงินไปแล้ว 10%
                if ($period < 10 && $payment < 10) { 
                    $validator->errors()->add('tofast', 'ต้องผ่อนไปแล้ว 1 ใน 10 ของงวดทั้งหมด หรือ ชำระเงินไปแล้ว 10% ของเงินทั้งหมด');
                }
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
                $payment->pay_date = Diamond::parse($request->input('pay_date'));
                $payment->principle = floatval(str_replace(',', '', $request->input('principle')));
                $payment->interest = floatval(str_replace(',', '', $request->input('interest')));
                $payment->save();

                $this->closeDate($loan_id);
    
                History::addAdminHistory(Auth::guard($this->guard)->id(), 'เพิ่มข้อมูล', 'ปิดยอดเงินกู้ ' . $payment->loan->code);
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
