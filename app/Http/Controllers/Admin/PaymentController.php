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

    public function create($loan_id) {
        $loan = Loan::find($loan_id);

        return view('admin.payment.create', [
            'member' => $loan->member,
            'loan' => $loan
        ]);
    }

    public function store($loan_id, Request $request) {
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
            $loan = Loan::find($loan_id);        

            DB::transaction(function() use ($request, $loan) {
                $payment = new Payment();
                $payment->loan_id = $loan->id;
                $payment->period = $request->input('period');
                $payment->pay_date = Diamond::parse($request->input('pay_date'));
                $payment->principle = floatval(str_replace(',', '', $request->input('principle')));
                $payment->interest = floatval(str_replace(',', '', $request->input('interest')));
                $payment->save();

                if (abs($loan->outstanding - $loan->payments->sum('principle')) < 0.01) {
                    // $loan->completed_at = Diamond::parse($request->input('pay_date'));
                    // $loan->save();
                    $this->closeDate($loan->id);
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

            return redirect()->action('Admin\LoanController@show', [ 'member_id' => $loan->member_id, 'id' => $loan->id ])
                ->with('flash_message', 'ข้อมูลการชำระเงินกู้ถูกป้อนเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function show($loan_id, $id) {
        $loan = Loan::find($loan_id);
        $payment = Payment::find($id);

        return view('admin.payment.show', [
            'member' => $loan->member,
            'loan' => $loan,
            'payment' => $payment
        ]);
    }

    public function edit($loan_id, $id) {
        $loan = Loan::find($loan_id);
        $payment = Payment::find($id);

        return view('admin.payment.edit', [
            'member' => $loan->member,
            'loan' => $loan,
            'payment' => $payment
        ]);
    }

    public function update($loan_id, $id, Request $request) {
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

        $validator->after(function($validator) use ($request, $id, $loan_id) {
            if (Payment::where('id', '<>', $id)->where('loan_id', $loan_id)->where('period', $request->input('period'))->count() > 0) {
                $validator->errors()->add('duplicate', 'เลขที่งวดซ้ำกับในระบบ');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        else {
            $loan = Loan::find($loan_id); 

            DB::transaction(function() use ($request, $loan, $id) {
                $payment = Payment::find($id);
                $payment->period = $request->input('period');
                $payment->pay_date = Diamond::parse($request->input('pay_date'));
                $payment->principle = floatval(str_replace(',', '', $request->input('principle')));
                $payment->interest = floatval(str_replace(',', '', $request->input('interest')));
                $payment->save();

                if (abs($loan->outstanding - $loan->payments->sum('principle')) < 0.01) {
                    $this->closeDate($loan->id);
                }
                else {
                    $loan->completed_at = null;
                    $loan->save();
                }
    
                History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'การผ่อนชำระเงินกู้ ' . $loan->code);
            });

            return redirect()->action('Admin\PaymentController@show', [ 'loan_id' => $loan->id, 'id' => $id])
                ->with('flash_message', 'แก้ไขข้อมูลการชำระเงินกู้เรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function destroy($loan_id, $id) {
        $loan = Loan::find($loan_id); 

        DB::transaction(function() use ($id, $loan) {
            $payment = Payment::find($id);
            $pay_date = $payment->pay_date;

            foreach ($payment->attachments as $attachment) {
                Storage::disk('attachments')->delete($attachment->file);
            }

            $payment->delete();

            if (abs($loan->outstanding - $loan->payments->sum('principle')) < 0.01) {
                $this->closeDate($loan->id);
            }
            else {
                $loan->completed_at = null;
                $loan->save();
            }
            
            History::addAdminHistory(Auth::guard($this->guard)->id(), 'ลบข้อมูล', 'ลบข้อมูลการผ่อนชำระเงินกู้ ' . $loan->code);
        });

        return redirect()->action('Admin\LoanController@show', [ 'member_id' => $loan->member_id, 'id' => $loan->id])
            ->with('flash_message', 'ลบข้อมูลการชำระเงินกู้เรียบร้อยแล้ว')
            ->with('callout_class', 'callout-success');
    }

    public function getClose($loan_id) {
        $loan = Loan::find($loan_id);

        return view('admin.payment.close', [
            'member' => $loan->member,
            'loan' => $loan 
        ]);
    }

    public function postClose($loan_id, Request $request) { 
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

        $validator->after(function($validator) use ($loan_id) {
            $loan = Loan::find($loan_id);
            $member = $loan->member;

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

            return redirect()->action('Admin\LoanController@show', [ 'member_id' => Loan::find($loan_id)->member_id, 'id' => $loan_id ])
                ->with('flash_message', ($request->input('remark') != '-') 
                    ? 'ข้อมูลการปิดยอดเงินกู้ถูกป้อนเรียบร้อยแล้ว (' . $request->input('remark') . ')' 
                    : 'ข้อมูลการปิดยอดเงินกู้ถูกป้อนเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function getCalculate($loan_id) {
        $loan = Loan::find($loan_id);

        return view('admin.payment.calculate', [
            'member' => $loan->member,
            'loan' => $loan
        ]);
    }

    public function getBilling($payment_id, $paydate) {
        $payment = Payment::find($payment_id);
        $loan = $payment->loan;
        $member = $loan->member;
        $billdate = Diamond::parse($paydate);

        return view('admin.payment.billing', [
            'member' => $member,
            'loan' => $loan,
            'billing' => Billing::latest()->first(),
            'payment' => $payment,
            'billno' => $billdate->thai_format('Y') . str_pad($payment->loan->id, 8, '0', STR_PAD_LEFT),
            'date' => $billdate
        ]);
    }

	public function getPrintBilling($payment_id, $pay_date) {
        $payment = Payment::find($payment_id);
        $loan = $payment->loan;
        $member = $loan->member;
        $billdate = Diamond::parse($pay_date);

		return view('admin.payment.print', [
			'member' => $member,
			'loan' => $loan,
			'billing' => Billing::latest()->first(),
			'payment' => $payment,
			'billno' => $billdate->thai_format('Y') . str_pad($payment->loan->id, 8, '0', STR_PAD_LEFT),
			'date' => $billdate
		]);
    }

    public function getPdfBilling($payment_id, $pay_date) {
        $payment = Payment::find($payment_id);
        $loan = $payment->loan;
        $member = $loan->member;
        $billdate = Diamond::parse($pay_date);

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
