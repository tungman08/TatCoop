<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Member;
use App\Shareholding;
use App\Billing;
use Auth;
use DB;
use Diamond;
use History;
use PDF;

class ShareholdingController extends Controller
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
        $this->middleware('auth:users');
    }

    public function index() {
        $member = Member::find(Auth::user()->member_id);
        $shareholdings = Shareholding::where('member_id', $member->id)
            ->select(
                DB::raw('concat(year(pay_date), \'-\', month(pay_date), \'-1\') as paydate'),
                DB::raw('str_to_date(concat(\'1/\', month(pay_date), \'/\', year(pay_date)), \'%d/%m/%Y\') as name'),
                DB::raw('(sum(if(member_id = ' . $member->id . ' and shareholding_type_id = 1, amount, 0))) as amount'),
                DB::raw('(sum(if(member_id = ' . $member->id . ' and shareholding_type_id = 2, amount, 0))) as amount_cash'),
                DB::raw('(select sum(s.amount) from shareholdings s where s.member_id = ' . $member->id . ' and s.pay_date < paydate) as total_shareholding'))
            ->groupBy(DB::raw('year(pay_date)'), DB::raw('month(pay_date)'))
            ->orderBy('total_shareholding', 'desc')
            ->get();

        return view('website.shareholding.index', [
            'member' => $member,
            'shareholdings' => $shareholdings
        ]);
    }

    public function show($id) {
        $member = Member::find(Auth::user()->member_id);
        $pay_date = Diamond::parse($id);
		$shareholdings = Shareholding::join('shareholding_types', 'shareholdings.shareholding_type_id', '=', 'shareholding_types.id')
			->leftJoin('shareholding_attachments', 'shareholdings.id', '=', 'shareholding_attachments.shareholding_id')
			->where('shareholdings.member_id', $member->id)
			->whereYear('shareholdings.pay_date', '=', $pay_date->year)
			->whereMonth('shareholdings.pay_date', '=', $pay_date->month)
			->select(
				'shareholdings.id as id',
				DB::raw('shareholdings.pay_date as paydate'),
				DB::raw('shareholding_types.name as shareholding_type_name'),
				DB::raw('shareholdings.amount as amount'),
				DB::raw('(select sum(s.amount) from shareholdings s where s.member_id = ' . $member->id . ' and s.pay_date < shareholdings.pay_date and s.id < shareholdings.id) as total_shareholding'),
                DB::raw('case when shareholding_attachments.id is not null > 0 then \'<i class="fa fa-paperclip"></i>\' else \'&nbsp;\' end as attachment'))
            ->orderBy('paydate', 'desc')
			->get();
		$total_shareholding = Shareholding::where('member_id', $member->id)
			->whereDate('pay_date', '<', $pay_date)
			->sum('amount');

        return view('website.shareholding.show', [
            'member' => $member,
            'shareholding_date' => $pay_date,
            'shareholdings' => $shareholdings,
			'total_shareholding' => $total_shareholding
        ]);
    }

   
    public function getBilling($shareholding_id, $date) {
        $id = Auth::user()->member_id;
        $pay_date = Diamond::parse($date);
        $shareholding = Shareholding::find($shareholding_id);
        $total_shareholding = Shareholding::where('member_id', $id)
            ->where('id', '<', $shareholding_id)
            ->whereDate('pay_date', '<', $shareholding->pay_date)
            ->sum('amount');

        return view('website.shareholding.billing', [
            'member' => Member::find($id),
            'shareholding' => $shareholding,
            'total_shareholding' => $total_shareholding,
            'billno' => Diamond::parse($shareholding->pay_date)->thai_format('Y') . str_pad($shareholding->id, 8, '0', STR_PAD_LEFT),
            'billing' => Billing::latest()->first()
        ]);
    }

    public function getPrint($shareholding_id, $date) {
        $id = Auth::user()->member_id;
        $pay_date = Diamond::parse($date);
        $shareholding = Shareholding::find($shareholding_id);
        $total_shareholding = Shareholding::where('member_id', $id)
            ->where('id', '<', $shareholding_id)
            ->whereDate('pay_date', '<', $shareholding->pay_date)
            ->sum('amount');

        History::addUserHistory(Auth::guard()->id(), 'นำข้อมูลออก', 'นำข้อมูลใบเสร็จออกจากระบบ');

        return view('website.shareholding.print', [
            'member' => Member::find($id),
            'shareholding' => $shareholding,
            'total_shareholding' => $total_shareholding,
            'billno' => Diamond::parse($shareholding->pay_date)->thai_format('Y') . str_pad($shareholding->id, 8, '0', STR_PAD_LEFT),
            'billing' => Billing::latest()->first()
        ]);
    }

    public function getPdf($shareholding_id, $date) {
        $id = Auth::user()->member_id;
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
            ->loadView('website.shareholding.pdf', $data)->download('ใบเสร็จรับเงินค่าหุ้นเดือน-' . Diamond::parse($shareholding->pay_date)->thai_format('M-Y') . '.pdf');
    }
}
