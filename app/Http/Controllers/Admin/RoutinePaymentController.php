<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\RoutinePayment;
use App\RoutinePaymentDetail;
use App\Loan;
use App\Payment;
use Validator;
use DB;
use Diamond;

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
        $routines = RoutinePayment::where('status', false)
            ->orderBy('calculated_date', 'desc')
            ->get();

        return view('admin.routine.payment.index', [
            'routines' => $routines
        ]);
    }

    public function show($id) {
        $routine = RoutinePayment::find($id);

        return view('admin.routine.payment.show', [
            'routine' => $routine
        ]);
    }

    public function save($id) {
        $routine = RoutinePayment::find($id);

        $rules = [
            'detail_id' => 'required|numeric',
        ];

        $attributeNames = [
            'detail_id' => 'ไอดี',
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        $validator->after(function($validator) use ($routine) {
            //conflict check
            $conflict = [];
            foreach ($routine->details as $detail) {
                $payment = Payment::where('loan_id', $detail->loan_id)
                    ->whereMonth('pay_date', Diamond::parse($detail->pay_date)->month)
                    ->whereYear('pay_date', Diamond::parse($detail->pay_date)->year)
                    ->get();

                if ($payment->count() > 0) {
                    $conflict[] = $payment->loan->code . "[" . $payment->loan->loan_type_id . "]";
                } 
            }

            if (count($conflict) > 0) {
                $validator->errors()->add('conflict', 'ข้อมูลการชำระค่าเงินกู้ของสมาชิกมีความขัดแย้งกัน กรุณาตรวจสอบอีกครั้ง ข้อมูลที่ขัดแย้ง (' . implode(', ', $conflict) . ')');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        else {
            DB::transaction(function() use ($routine) {
                //save data
                foreach ($routine->details as $detail) {
                    $payment = new Payment();
                    $payment->pay_date = $detail->pay_date;
                    $payment->principle = $detail->principle;
                    $payment->interest = $detail->interest;

                    $loan = Loan::find($detail->loan_id);
                    $loan->payments()->save($payment);

                    $detail->status = true;
                    $detail->save();
                }
            });

            return redirect()->action('Admin\RoutinePaymentController@show', ['id' => $routine->id])
                ->with('flash_message', 'บันทึกข้อมูลการชำระค่าเงินกู้ทั้งหมดลงฐานข้อมูลเรียบร้อยเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function saveDetail(Request $request) {
        $detail_id = $request->input('detail_id');
        $detail = RoutinePaymentDetail::find($detail_id);

        $rules = [
            'detail_id' => 'required|numeric',
        ];

        $attributeNames = [
            'detail_id' => 'ไอดี',
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        $validator->after(function($validator) use ($detail) {
            //conflict check
            $payment = Payment::where('loan_id', $detail->loan_id)
                ->whereMonth('pay_date', Diamond::parse($detail->pay_date)->month)
                ->whereYear('pay_date', Diamond::parse($detail->pay_date)->year)
                ->get();

            if ($payment->count() > 0) {
                $validator->errors()->add('conflict', 'ข้อมูลการชำระค่าเงินกู้ของสมาชิกมีความขัดแย้งกัน กรุณาตรวจสอบอีกครั้ง');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        else {
            DB::transaction(function() use ($detail) {
                //save data
                $payment = new Payment();
                $payment->pay_date = $detail->pay_date;
                $payment->principle = $detail->principle;
                $payment->interest = $detail->interest;

                $loan = Loan::find($detail->loan_id);
                $loan->payments()->save($payment);

                $detail->status = true;
                $detail->save();
            });

            return redirect()->action('Admin\RoutinePaymentController@show', ['id' => $detail->routine->id])
                ->with('flash_message', 'บันทึกข้อมูลการชำระค่าเงินกู้ลงฐานข้อมูลเรียบร้อยเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function editDetail($id) {
        $detail = RoutinePaymentDetail::find($id);

        return view('admin.routine.payment.edit', [
            'detail' => $detail
        ]);
    }

    public function updateDetail($id, Request $request) {
        $rules = [
            'principle' => 'required',
            'interest' => 'required'
        ];

        $attributeNames = [ 
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
                $detail->principle = $request->input('principle');
                $detail->interest = $request->input('interest');
                $detail->save();
            });

            return redirect()->action('Admin\RoutinePaymentController@show', ['id' => $detail->routine->id])
                ->with('flash_message', 'แก้ไขข้อมูลการชำระเงินกู้เรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function deleteDetail($id) {
        $detail = RoutinePaymentDetail::find($id);
        $routine_id = $detail->routine->id;
        $detail->delete();

        return redirect()->action('Admin\RoutinePaymentController@show', ['id' => $routine_id])
            ->with('flash_message', 'ลบข้อมูลการชำระค่าเงินกู้เรียบร้อยแล้ว')
            ->with('callout_class', 'callout-success');
    }
}
