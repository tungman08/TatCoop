<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Response;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\RoutineSetting;
use App\RoutinePayment;
use App\RoutinePaymentDetail;
use App\Member;
use App\Loan;
use App\Payment;
use Validator;
use DB;
use Diamond;
use stdClass;
use LoanCalculator;

class RoutinePaymentController extends Controller
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
        $this->middleware('auth:admins', ['except' => 'getUnauthorize']);
    }

    public function index() {
        $setting = RoutineSetting::find(2);

        // if ($setting->save_status == true) {
        //     $this->updateStatus();
        // }

        $routines = RoutinePayment::orderBy('calculated_date', 'desc')
            ->get();

        return view('admin.routine.payment.index', [
            'routines' => $routines
        ]);
    }

    public function show($id) {
        $routine = RoutinePayment::find($id);
        $details = RoutinePayment::join('routine_payment_details', 'routine_payment_details.routine_payment_id', '=', 'routine_payments.id')
            ->join('loans', 'routine_payment_details.loan_id', '=', 'loans.id')
            ->join('loan_types', 'loans.loan_type_id', '=', 'loan_types.id')
            ->join('members', 'loans.member_id', '=', 'members.id')
            ->join('profiles', 'profiles.id', '=', 'members.profile_id')
            ->where('routine_payments.id', $id)
            ->select([
                'routine_payment_details.id as id',
                'routine_payment_details.status as status',
                'loans.code as loancode',
                'loan_types.name as loantypename',
                DB::raw("LPAD(members.id, 5, '0') as membercode"),
                DB::raw("CONCAT(profiles.name, ' ', profiles.lastname) as fullname"),
                DB::raw("FORMAT(routine_payment_details.period, 0) as period"),
                DB::raw("FORMAT(routine_payment_details.principle, 2) as principle"),
                DB::raw("FORMAT(routine_payment_details.interest, 2) as interest"),
                DB::raw("FORMAT(routine_payment_details.principle + routine_payment_details.interest, 2) as total")
            ])
            ->get();

        return view('admin.routine.payment.show', [
            'routine' => $routine,
            'details' => $details
        ]);
    }

    // public function save($id) {
    //     $this->updateStatus();
        
    //     $routine = RoutinePayment::find($id);

    //     DB::transaction(function() use ($routine) {
    //         //save data
    //         foreach ($routine->details as $detail) {
    //             $payment = new Payment();
    //             $payment->pay_date = $detail->pay_date;
    //             $payment->period = $detail->period;
    //             $payment->principle = $detail->principle;
    //             $payment->interest = $detail->interest;

    //             $loan = Loan::find($detail->loan_id);
    //             $loan->payments()->save($payment);

    //             $detail->status = true;
    //             $detail->save();
    //         }

    //         $routine->status = true;
    //         $routine->saved_date = Diamond::today();
    //         $routine->save();
    //     });

    //     return redirect()->action('Admin\RoutinePaymentController@show', ['id' => $routine->id])
    //         ->with('flash_message', 'บันทึกข้อมูลการชำระค่าเงินกู้ทั้งหมดลงฐานข้อมูลเรียบร้อยเรียบร้อยแล้ว')
    //         ->with('callout_class', 'callout-success');
    // }

    public function createDetail($routine_id) {
        $routine = RoutinePayment::find($routine_id);

        return view('admin.routine.payment.create', [
            'routine' => $routine
        ]);
    }

    public function storeDetail($routine_id, Request $request) {
        $rules = [
            'loan_id' => 'required|numeric',
            'pay_date' => 'required|date_format:Y-m-d', 
            'period' => 'required|numeric',
            'principle' => 'required|numeric',
            'interest' => 'required|numeric',
        ];

        $attributeNames = [
            'loan_id' => 'รหัสสมาชิก',
            'pay_date' => 'วันที่ชำระ', 
            'period' => 'งวดที่',
            'principle' => 'จำนวนเงินต้น',
            'interest' => 'จำนวนดอกเบี้ย',
        ];
 
        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        $validator->after(function ($validator)use ($request, $routine_id) {
            if (RoutinePaymentDetail::where('routine_payment_id', $routine_id)
                ->where('loan_id', $request->input('loan_id'))->count() > 0) {
                $validator->errors()->add('error', 'สัญญาเงินกู้นี้มีข้อมูลอยู่แล้ว');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        else {
            DB::transaction(function() use ($request, $routine_id) {
                $detail = new RoutinePaymentDetail();
                $detail->routine_payment_id = $routine_id;
                $detail->loan_id = $request->input('loan_id');
                $detail->pay_date = Diamond::parse($request->input('pay_date'));
                $detail->period = $request->input('period');
                $detail->principle = $request->input('principle');
                $detail->interest = $request->input('interest');
                $detail->save();
            });

            return redirect()->action('Admin\RoutinePaymentController@show', ['routine_id' => $routine_id])
                ->with('flash_message', 'เพิ่มข้อมูลการชำระเงินกู้เรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }
    
    // public function saveDetail(Request $request) {
    //     $detail_id = $request->input('detail_id');
    //     $detail = RoutinePaymentDetail::find($detail_id);

    //     $rules = [
    //         'detail_id' => 'required|numeric',
    //     ];

    //     $attributeNames = [
    //         'detail_id' => 'ไอดี',
    //     ];

    //     $validator = Validator::make($request->all(), $rules);
    //     $validator->setAttributeNames($attributeNames);

    //     $validator->after(function($validator) use ($detail) {
    //         //conflict check
    //         $payment = Payment::where('loan_id', $detail->loan_id)
    //             ->whereMonth('pay_date', Diamond::parse($detail->pay_date)->month)
    //             ->whereYear('pay_date', Diamond::parse($detail->pay_date)->year)
    //             ->get();

    //         if ($payment->count() > 0) {
    //             $validator->errors()->add('conflict', 'ข้อมูลการชำระค่าเงินกู้ของสมาชิกมีความขัดแย้งกัน กรุณาตรวจสอบอีกครั้ง');
    //         }
    //     });

    //     if ($validator->fails()) {
    //         return redirect()->back()->withErrors($validator);
    //     }
    //     else {
    //         DB::transaction(function() use ($detail) {
    //             //save data
    //             $payment = new Payment();
    //             $payment->pay_date = $detail->pay_date;
    //             $payment->period = $detail->period;
    //             $payment->principle = $detail->principle;
    //             $payment->interest = $detail->interest;

    //             $loan = Loan::find($detail->loan_id);
    //             $loan->payments()->save($payment);

    //             $detail->status = true;
    //             $detail->save();
    //         });

    //         return redirect()->action('Admin\RoutinePaymentController@show', ['id' => $detail->routine->id])
    //             ->with('flash_message', 'บันทึกข้อมูลการชำระค่าเงินกู้ลงฐานข้อมูลเรียบร้อยเรียบร้อยแล้ว')
    //             ->with('callout_class', 'callout-success');
    //     }
    // }

    public function editDetail($routine_id, $id) {
        $detail = RoutinePaymentDetail::find($id);

        return view('admin.routine.payment.edit', [
            'detail' => $detail
        ]);
    }

    public function updateDetail($routine_id, $id, Request $request) {
        $rules = [
            'period' => 'required',
            'principle' => 'required',
            'interest' => 'required'
        ];

        $attributeNames = [ 
            'period' => 'งวดที่',
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
            $detail = RoutinePaymentDetail::find($id);

            DB::transaction(function() use ($request, $detail) {
                $detail->period = $request->input('period');
                $detail->principle = $request->input('principle');
                $detail->interest = $request->input('interest');
                $detail->save();
            });

            return redirect()->action('Admin\RoutinePaymentController@show', ['id' => $detail->routine->id])
                ->with('flash_message', 'แก้ไขข้อมูลการชำระเงินกู้เรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function deleteDetail($routine_id, $id) {
        $detail = RoutinePaymentDetail::find($id);
        $routine_id = $detail->routine->id;
        $detail->delete();

        return redirect()->action('Admin\RoutinePaymentController@show', ['id' => $routine_id])
            ->with('flash_message', 'ลบข้อมูลการชำระค่าเงินกู้เรียบร้อยแล้ว')
            ->with('callout_class', 'callout-success');
    }

    // public function report($id) {
    //     $routine = RoutinePayment::find($id);
    //     $details = RoutinePayment::join('routine_payment_details', 'routine_payment_details.routine_payment_id', '=', 'routine_payments.id')
    //         ->join('loans', 'routine_payment_details.loan_id', '=', 'loans.id')
    //         ->join('loan_types', 'loans.loan_type_id', '=', 'loan_types.id')
    //         ->join('members', 'loans.member_id', '=', 'members.id')
    //         ->join('profiles', 'profiles.id', '=', 'members.profile_id')
    //         ->where('routine_payments.id', $id)
    //         ->select([
    //             'loans.code as loancode',
    //             'loan_types.name as loantypename',
    //             DB::raw("members.id as membercode"),
    //             DB::raw("CONCAT(profiles.name, ' ', profiles.lastname) as fullname"),
    //             DB::raw("routine_payment_details.period as period"),
    //             DB::raw("routine_payment_details.pay_date as paydate"),
    //             DB::raw("routine_payment_details.principle as principle"),
    //             DB::raw("routine_payment_details.interest as interest")
    //         ])
    //         ->get();

    //     $filename = 'ชำระเงินกู้ปกติอัตโนมัติประจำเดือน '. Diamond::parse($routine->calculated_date)->thai_format('M Y');
    //     $header = ['#', 'เลขทะเบียนสมาชิก', 'ชื่อ-นามสกุล', 'เลขที่สัญญา', 'ประเภทเงินกู้', 'งวดที่', 'วันที่จ่าย', 'เงินต้น', 'ดอกเบี้ย', 'รวม'];

    //     Excel::create($filename, function($excel) use ($filename, $header, $details) {
    //         // sheet
    //         $excel->sheet('ชำระเงินกู้ปกติอัตโนมัติ', function($sheet) use ($filename, $header, $details) {
    //             // disable auto size for sheet
    //             $sheet->setAutoSize(false);

    //             // title
    //             $sheet->row(1, [$filename]);

    //             // header
    //             $sheet->row(3, $header);

    //             // data
    //             $row = 4;
    //             foreach ($details as $detail) {
    //                 $data = [];
    //                 $data[] = $row - 3;
    //                 $data[] = $detail->membercode;
    //                 $data[] = $detail->fullname;
    //                 $data[] = $detail->loancode;
    //                 $data[] = $detail->loantypename;
    //                 $data[] = $detail->period;
    //                 $data[] = ExcelDate::PHPToExcel(Diamond::parse($detail->paydate));
    //                 $data[] = $detail->principle;
    //                 $data[] = $detail->interest;
    //                 $data[] = $detail->principle + $detail->interest;

    //                 $sheet->row($row, $data);
    //                 $row++;
    //             }

    //             $sheet->setColumnFormat([
    //                 "B4:B$row" => '00000',
    //                 "F4:F$row" => '#,##0',
    //                 "G4:F$row" => '[$-0][~buddhist]D MMM YYYY;@',
    //                 "H4:J$row" => '#,##0.00'
    //             ]);
    //         });
    //     })->download('xlsx');
    // }

    public function ajaxcalculate(Request $request) {
        $member = Member::find($request->input('member_id'));

        if (is_null($member)) {
            return Response::json(['error' => 'ไม่พบรหัสสมาชิกนี้']);
        }
        else if ($member->profile->employee->employee_type_id != 1) {
            return Response::json(['error' => 'ต้องเป็นสมาชิกประเภทพนักงาน/ลูกจ้าง ททท. เท่านั้น']);
        }
        else if ($member->loans->count() == 0) {
            return Response::json(['error' => 'สมาชิกไม่มีสัญญาเงินกู้']);
        }
        else if ($member->loans->filter(function ($item) { return !is_null($item['code']) && is_null($item['completed_at']); })->count() == 0) {
            return Response::json(['error' => 'สมาชิกไม่มีสัญญาเงินกู้']);
        }

        $loans = [];
        foreach ($member->loans->filter(function ($item) { return !is_null($item['code']) && is_null($item['completed_at']); }) as $loan) {
            $loans[$loan->id] =  'เลขที่: ' . $loan->code .' : ' . $loan->loanType->name;
        }

        $result = new stdClass();
        $result->fullname = $member->profile->fullname;
        $result->loans = $loans;

        return Response::json($result);
    }

    public function ajaxpayment(Request $request) {
        $loan = Loan::find($request->input('loan_id'));
        $pay_date = Diamond::parse($request->input('pay_date'));
        $payment = LoanCalculator::monthly_payment($loan, $pay_date);

        $result = new stdClass();
        $result->period = ($loan->payments->count() > 0) ? $loan->payments->max('period') + 1 : 1;
        $result->principle = $payment->principle;
        $result->interest = $payment->interest;

        return Response::json($result);
    }

    // protected function updateStatus() {
    //     $routines = RoutinePayment::where('status', false)
    //         ->get();

    //     foreach ($routines as $routine) {
            
    //     }

    //     // also would work, temporary turn off auto timestamps
    //     with($model = new RoutinePaymentDetail)->timestamps = false;

    //     $model->join('payments', function ($query) {
    //         $query->on('routine_payment_details.loan_id', '=', 'payments.loan_id')
    //             ->on('routine_payment_details.pay_date', '=', 'payments.pay_date'); })
    //     ->where('routine_payment_details.status', false)
    //     ->update([
    //         'routine_payment_details.status' => true
    //     ]);
    // }
}
