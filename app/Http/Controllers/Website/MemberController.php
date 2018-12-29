<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use History;
use DB;
use Diamond;
use MemberProperty;
use PDF;
use Validator;
use Route;
use stdClass;
use App\Member;
use App\Billing;
use App\Dividend;
use App\Dividendmember;
use App\Prefix;
use App\Province;
use App\District;
use App\Loan;
use App\LoanType;
use App\Subdistrict;
use App\Postcode;
use App\Profile;
use App\Employee;
use App\EmployeeType;
use App\Shareholding;
use App\Payment;

class MemberController extends Controller
{
    /**
     * Only user authorize to access this section.
     *
     * @var string
     */
    protected $guard = 'users';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:users', ['except' => [ 'index', 'getUnauthorize' ]]);
        $this->middleware("user", ['except' => [ 'index', 'getUnauthorize' ]]);
    }

    public function getUnauthorize() {
        return 'unauthorize';
    }

    public function index() {
        return redirect('/auth/login');
    }

   /**
    * Responds to requests to GET /member
    */
   public function show($id) {
        $member = Member::find($id);

        return view('website.member.show', [
            'member' => $member,
            'histories' => Member::where('profile_id', $member->profile_id)->get(),
        ]);
   }

   /**
    * Responds to requests to GET /member/edit
    */
   public function edit($id) {
		$member = Member::find(Auth::user()->member_id);
		$provinces = Province::orderBy('name')->get();
		$districts = District::where('province_id', $member->profile->province_id)->orderBy('name')->get();
		$subdistricts = Subdistrict::where('district_id', $member->profile->district_id)->orderBy('name')->get();

		return view('website.member.edit', [
			'member' => $member,
			'prefixs' => Prefix::all(),
			'provinces' => $provinces,
			'districts' => $districts,
			'subdistricts' => $subdistricts,
		]);
   }

   public function update($id, Request $request) {
        $rules = [
            'profile.birth_date' => 'required|date_format:Y-m-d', 
            'profile.address' => 'required', 
        ];

        $attributeNames = [
            'profile.birth_date' => 'วันเกิด',
            'profile.address' => 'ที่อยู่', 
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
                $member = Member::find($id);
                $profile = Profile::find($member->profile_id);
                $profile->address = $request->input('profile')['address'];
                $profile->province_id = $request->input('profile')['province_id'];
                $profile->district_id = $request->input('profile')['district_id'];
                $profile->subdistrict_id = $request->input('profile')['subdistrict_id'];
                $profile->postcode_id = Postcode::where('code', $request->input('profile')['postcode']['code'])->first()->id;
                $profile->birth_date = Diamond::parse($request->input('profile')['birth_date']);
                $profile->save();

                History::addUserHistory(Auth::guard()->id(), 'แก้ไขข้อมูล', 'แก้ไขข้อมูลส่วนตัว');
            });

            return redirect()->action('Website\MemberController@show', ['id' => $id])
                ->with('flash_message', 'แก้ไขข้อมูลเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
   }

   public function getShareholding($id) {
        $member = Member::find($id);
        $shareholdings = Shareholding::where('member_id', $member->id)
            ->select(
                DB::raw('concat(year(pay_date), \'-\', month(pay_date), \'-1\') as paydate'),
                DB::raw('str_to_date(concat(\'1/\', month(pay_date), \'/\', year(pay_date)), \'%d/%m/%Y\') as name'),
                DB::raw('(sum(if(member_id = ' . $member->id . ' and shareholding_type_id = 1, amount, 0))) as amount'),
                DB::raw('(sum(if(member_id = ' . $member->id . ' and shareholding_type_id = 2, amount, 0))) as amount_cash'),
                DB::raw('(select sum(s.amount) from shareholdings s where s.member_id = ' . $member->id . ' and s.pay_date < paydate) as total_shareholding'))
            ->groupBy(DB::raw('year(pay_date)'), DB::raw('month(pay_date)'))
            ->get();

        return view('website.member.shareholding', [
            'member' => $member,
            'shareholdings' => $shareholdings
        ]);
   }

      public function getShowShareholding($id, $month) {
        $pay_date = Diamond::parse($month);
		$shareholdings = Shareholding::join('shareholding_types', 'shareholdings.shareholding_type_id', '=', 'shareholding_types.id')
			->leftJoin('shareholding_attachments', 'shareholdings.id', '=', 'shareholding_attachments.shareholding_id')
			->where('shareholdings.member_id', $id)
			->whereYear('shareholdings.pay_date', '=', $pay_date->year)
			->whereMonth('shareholdings.pay_date', '=', $pay_date->month)
			->select(
				'shareholdings.id as id',
				DB::raw('shareholdings.pay_date as paydate'),
				DB::raw('shareholding_types.name as shareholding_type_name'),
				DB::raw('shareholdings.amount as amount'),
				DB::raw('(select sum(s.amount) from shareholdings s where s.member_id = ' . $id . ' and s.pay_date < shareholdings.pay_date and s.id < shareholdings.id) as total_shareholding'),
				DB::raw('case when shareholding_attachments.id is not null > 0 then \'<i class="fa fa-paperclip"></i>\' else \'&nbsp;\' end as attachment'))
			->get();
		$total_shareholding = Shareholding::where('member_id', $id)
			->whereDate('pay_date', '<', $pay_date)
			->sum('amount');

        return view('website.member.showshareholding', [
            'member' => Member::find($id),
            'shareholding_date' => $pay_date,
            'shareholdings' => $shareholdings,
			'total_shareholding' => $total_shareholding
        ]);
   }

   public function getLoan($id) {
        $member = Member::find($id);

        return view('website.member.loan', [
            'member' => $member,
            'loans' => Loan::where('member_id', $member->id)->whereNotNull('code')->orderBy('id', 'desc')->get(),
            'loantypes' => LoanType::active()->get()
        ]);
   }

   public function getShowLoan($id, $loan_id) {
        $member = Member::find($id);
        $loan = Loan::find($loan_id);

        return view('website.member.showloan', [
            'member' => $member,
            'loan' => $loan,  
        ]);
   }

   public function getDividend($id) {
        $member = Member::find($id);
        $dividend_years = Dividend::all();

        return view('website.member.dividend', [
            'member' => $member,
            'dividend_years' => collect($dividend_years),
            'dividends' => Dividendmember::where('dividend_id', $dividend_years->last()->id)
                ->where('member_id', $member->id)
                ->get()
        ]);
   }

   public function getGuaruntee($id) {
        $member = Member::find($id);

        return view('website.member.guaruntee', [
            'member' => $member
        ]);
   }

   public function getShareholdingBilling($id, $shareholding_id, $date) {
        $pay_date = Diamond::parse($date);
		$shareholding = Shareholding::find($shareholding_id);
		$total_shareholding = Shareholding::where('member_id', $id)
			->where('id', '<', $shareholding_id)
			->whereDate('pay_date', '<', $shareholding->pay_date)
			->sum('amount');

        return view('website.member.shareholding.billing', [
            'member' => Member::find($id),
            'shareholding' => $shareholding,
            'total_shareholding' => $total_shareholding,
            'billno' => Diamond::parse($shareholding->pay_date)->thai_format('Y') . str_pad($shareholding->id, 8, '0', STR_PAD_LEFT),
			'billing' => Billing::latest()->first()
        ]);
   }

   public function getPrintShareholdingBilling($id, $shareholding_id, $date) {
        $pay_date = Diamond::parse($date);
		$shareholding = Shareholding::find($shareholding_id);
		$total_shareholding = Shareholding::where('member_id', $id)
			->where('id', '<', $shareholding_id)
			->whereDate('pay_date', '<', $shareholding->pay_date)
			->sum('amount');

        History::addUserHistory(Auth::guard()->id(), 'นำข้อมูลออก', 'นำข้อมูลใบเสร็จออกจากระบบ');

        return view('website.member.shareholding.print', [
            'member' => Member::find($id),
            'shareholding' => $shareholding,
            'total_shareholding' => $total_shareholding,
            'billno' => Diamond::parse($shareholding->pay_date)->thai_format('Y') . str_pad($shareholding->id, 8, '0', STR_PAD_LEFT),
			'billing' => Billing::latest()->first()
        ]);
   }

   public function getPdfShareholdingBilling($id, $shareholding_id, $date) {
        $pay_date = Diamond::parse($date);
		$shareholding = Shareholding::find($shareholding_id);
		$total_shareholding = Shareholding::where('member_id', $id)
			->where('id', '<', $shareholding_id)
			->whereDate('pay_date', '<', $shareholding->pay_date)
			->sum('amount');

        $data = [
            'member' => Member::find($id),
            'shareholding' => $shareholding,
            'total_shareholding' => $total_shareholding,
            'billno' => Diamond::parse($shareholding->pay_date)->thai_format('Y') . str_pad($shareholding->id, 8, '0', STR_PAD_LEFT),
			'billing' => Billing::latest()->first()
        ];

        History::addUserHistory(Auth::guard()->id(), 'นำข้อมูลออก', 'นำข้อมูลใบเสร็จออกจากระบบ');

        return PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
            ->loadView('website.member.shareholding.pdf', $data)->download('ใบเสร็จรับเงินค่าหุ้นเดือน-' . Diamond::parse($shareholding->pay_date)->thai_format('M-Y') . '.pdf');
   }

   public function getLoanBilling($id, $loan_id, $payment_id, $date) {
        $billdate = Diamond::parse($date);
        $member = Member::find($id);
        $loan = Loan::find($loan_id);
        $payment = Payment::find($payment_id);

        return view('website.member.loan.billing', [
            'member' => $member,
            'loan' => $loan,
            'billing' => Billing::latest()->first(),
            'payment' => $payment,
            'billno' => $billdate->thai_format('Y') . str_pad($payment->loan->id, 8, '0', STR_PAD_LEFT),
            'date' => $billdate
        ]);
   }

   public function getPrintLoanBilling($id, $loan_id, $payment_id, $date) {
        $billdate = Diamond::parse($date);
        $member = Member::find($id);
        $loan = Loan::find($loan_id);
        $payment = Payment::find($payment_id);

        History::addUserHistory(Auth::guard()->id(), 'นำข้อมูลออก', 'นำข้อมูลใบเสร็จออกจากระบบ');

        return view('website.member.loan.print', [
            'member' => $member,
            'loan' => $loan,
            'billing' => Billing::latest()->first(),
            'payment' => $payment,
            'billno' => $billdate->thai_format('Y') . str_pad($payment->loan->id, 8, '0', STR_PAD_LEFT),
            'date' => $billdate
        ]);
   }

   public function getPdfLoanBilling($id, $loan_id, $payment_id, $date) {
        $billdate = Diamond::parse($date);
        $member = Member::find($id);
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

        History::addUserHistory(Auth::guard()->id(), 'นำข้อมูลออก', 'นำข้อมูลใบเสร็จออกจากระบบ');

        return PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
            ->loadView('website.member.loan.pdf', $data)->download('ใบเสร็จรับเงินค่างวด สัญญาเลขที่ ' . $loan->code . ' เดือน-' . $billdate->thai_format('M-Y') . '.pdf');
   }
}
