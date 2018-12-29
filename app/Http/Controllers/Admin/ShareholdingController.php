<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use History;
use DB;
use Diamond;
use PDF;
use Storage;
use FileManager;
use Response;
use stdClass;
use Validator;
use App\Billing;
use App\Member;
use App\Profile;
use App\Shareholding;
use App\ShareholdingType;
use App\ShareholdingAttachment;

class ShareholdingController extends Controller
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
        return view('admin.shareholding.member');
    }

    public function index($id) {
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

        return view('admin.shareholding.index', [
            'member' => $member,
            'shareholdings' => $shareholdings
        ]);
    }

    public function create($id) {
        return view('admin.shareholding.create', [
            'member' => Member::find($id),
            'shareholding_types' => ShareholdingType::all()
        ]);
    }

    public function store(Request $request, $id) {
        $rules = [
            'pay_date' => 'required|date_format:Y-m-d', 
            'amount' => 'required|numeric'
        ];

        $attributeNames = [
            'pay_date' => 'วันที่ชำระ', 
            'amount' => 'ค่าหุ้น'
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
                $shareholding = new Shareholding();
                $shareholding->member_id = $id;
                $shareholding->pay_date = $request->input('pay_date');
                $shareholding->shareholding_type_id = $request->input('shareholding_type_id');
                $shareholding->amount = $request->input('amount');

                if (!empty($request->input('remark'))) {
                    $shareholding->remark = $request->input('remark');
                }
                    
                $shareholding->save();

                if ($request->hasFile('attachment')) {
                    $file = $request->file('attachment');
                    $display = mb_ereg_replace('\s+', ' ', basename($file->getClientOriginalName(), '.' . $file->getClientOriginalExtension()));
                    $filename = time() . uniqid() . '.' . $file->getClientOriginalExtension();
                    $path = ($file->getRealPath() != false) ? $file->getRealPath() : $file->getPathname();
                    Storage::disk('attachments')->put($filename, file_get_contents($path));
    
                    $attachment = new ShareholdingAttachment([
                        'file' => $filename,
                        'display' => $display
                    ]);
                    $shareholding->attachments()->save($attachment);
                }

                $profile = Profile::find(Member::find($id)->profile_id);

                History::addAdminHistory(Auth::guard($this->guard)->id(), 'เพิ่มข้อมูล', 'เพิ่มข้อมูลค่าหุ้นของคุณ' . $profile->name . ' ' . $profile->lastname);
            });

            return redirect()->action('Admin\ShareholdingController@index', ['id' => $id])
                ->with('flash_message', 'ป้อนข้อมูลการชำระค่าหุ้นเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function getShow($member_id, $paydate) {
        $pay_date = Diamond::parse($paydate);
		$shareholdings = Shareholding::join('shareholding_types', 'shareholdings.shareholding_type_id', '=', 'shareholding_types.id')
			->leftJoin('shareholding_attachments', 'shareholdings.id', '=', 'shareholding_attachments.shareholding_id')
			->where('shareholdings.member_id', $member_id)
			->whereYear('shareholdings.pay_date', '=', $pay_date->year)
			->whereMonth('shareholdings.pay_date', '=', $pay_date->month)
			->select(
				'shareholdings.id as id',
				DB::raw('shareholdings.pay_date as paydate'),
				DB::raw('shareholding_types.name as shareholding_type_name'),
				DB::raw('shareholdings.amount as amount'),
				DB::raw('(select sum(s.amount) from shareholdings s where s.member_id = ' . $member_id . ' and s.pay_date < shareholdings.pay_date and s.id < shareholdings.id) as total_shareholding'),
				DB::raw('case when shareholding_attachments.id is not null > 0 then \'<i class="fa fa-paperclip"></i>\' else \'&nbsp;\' end as attachment'))
			->get();
		$total_shareholding = Shareholding::where('member_id', $member_id)
			->whereDate('pay_date', '<', $pay_date)
			->sum('amount');

        return view('admin.shareholding.show', [
            'member' => Member::find($member_id),
            'shareholding_date' => $pay_date,
            'shareholdings' => $shareholdings,
			'total_shareholding' => $total_shareholding
        ]);
    }

    public function getDetail($member_id, $paydate, $id) {
        $pay_date = Diamond::parse($paydate);
		$shareholding = Shareholding::find($id);
		$total_shareholding = Shareholding::where('member_id', $member_id)
			->where('id', '<', $id)
			->whereDate('pay_date', '<', $shareholding->pay_date)
			->sum('amount');

        return view('admin.shareholding.detail', [
            'member' => Member::find($member_id),
            'shareholding' => $shareholding,
            'total_shareholding' => $total_shareholding
        ]);
    }

    public function edit($member_id, $id) {
        return view('admin.shareholding.edit', [
            'member' => Member::find($member_id),
            'shareholding' => Shareholding::find($id),
            'shareholding_types' => ShareholdingType::all()
        ]);
    }

    public function update(Request $request, $member_id, $id) {
       $rules = [
            'pay_date' => 'required|date_format:Y-m-d', 
            'amount' => 'required|numeric',
        ];

        $attributeNames = [
            'pay_date' => 'วันที่ชำระ', 
            'amount' => 'ค่าหุ้น',
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        else {
            $shareholding = Shareholding::find($id);
            $paydate = Diamond::parse($shareholding->pay_date);

            DB::transaction(function() use ($request, $shareholding) {
                $shareholding->pay_date = $request->input('pay_date');
                $shareholding->shareholding_type_id = $request->input('shareholding_type_id');
                $shareholding->amount = $request->input('amount');
                $shareholding->remark = !empty($request->input('remark')) ? $request->input('remark') : null;
                $shareholding->save();

                $profile = Profile::find(Member::find($shareholding->member_id)->profile_id);

                History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'แก้ไขข้อมูลค่าหุ้นของคุณ' . $profile->name . ' ' . $profile->lastname);
            });

            return redirect()->action('Admin\ShareholdingController@getDetail', ['member_id' => $member_id, 'paydate' => $paydate->format('Y-m-1'), 'id' => $id])
                ->with('flash_message', 'แก้ไขข้อมูลการชำระค่าหุ้นเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function destroy($member_id, $id) {
        $shareholding = Shareholding::find($id);
        $paydate = Diamond::parse($shareholding->pay_date);

        DB::transaction(function() use ($shareholding) {
            $profile = Profile::find(Member::find($shareholding->member_id)->profile_id);

            foreach ($shareholding->attachments as $attachment) {
                Storage::disk('attachments')->delete($attachment->file);
            }

            History::addAdminHistory(Auth::guard($this->guard)->id(), 'ลบข้อมูล', 'ลบข้อมูลค่าหุ้นคุณ' . $profile->name . ' ' . $profile->lastname);

            $shareholding->delete();
        });

        $shareholdings = Shareholding::where('member_id', $member_id)
            ->whereYear('pay_date', '=', $paydate->format('Y'))
            ->whereMonth('pay_date', '=', $paydate->format('m'))
            ->get();

        if ($shareholdings->count() > 0) {
            return redirect()->action('Admin\ShareholdingController@getShow', ['member_id' => $member_id, 'paydate' => $paydate->format('Y-m-1')])
                ->with('flash_message', 'ลบข้อมูลการชำระค่าหุ้นเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }

        return redirect()->action('Admin\ShareholdingController@index', ['id' => $member_id])
            ->with('flash_message', 'ลบข้อมูลการชำระค่าหุ้นเรียบร้อยแล้ว')
            ->with('callout_class', 'callout-success');
    }

    public function getAutoShareholding() {
        return view('admin.shareholding.auto');
    }

    public function postAutoShareholding(Request $request) {
        $date = Diamond::parse($request->input('month') . '-1')->endOfMonth();

        $members = Member::active()
            ->join('employees', 'members.profile_id', '=', 'employees.profile_id')
            ->where('members.shareholding', '>', 0)
            ->where('employees.employee_type_id', 1)
            ->whereDate('members.start_date', '<', $date)
            ->whereNotIn('members.id', function($query) use ($date) {
                $query->from('shareholdings')
                    ->whereMonth('pay_date', '=', $date->month)
                    ->whereYear('pay_date', '=', $date->year)
                    ->where('shareholding_type_id', 1)
                    ->select('member_id');
            })
            ->select('members.*')->get();

        DB::transaction(function() use ($members, $date) {
            foreach($members as $member) {
                $share = new Shareholding();
                $share->pay_date = $date;
                $share->shareholding_type_id = 1;
                $share->amount = $member->shareholding * 10 ;
                $share->remark = 'ป้อนข้อมูลอัตโนมัติ';
                $member->shareholdings()->save($share);
            }

            History::addAdminHistory(Auth::guard($this->guard)->id(), 'ป้อนการชำระค่าหุ้นแบบอัตโนมัติ', 'ทำรายการชำระค่าหุ้นอัตโนมัติประจำเดือน' . $date->thai_format('F Y'));
        });

        return redirect()->action('Admin\ShareholdingController@getMember')
            ->with('flash_message', 'ทำรายการชำระค่าหุ้นอัตโนมัติประจำเดือน' . $date->thai_format('F Y') . ' จำนวน ' . $members->count() . ' คน เรียบร้อยแล้ว')
            ->with('callout_class', 'callout-success');
    }

    function getBilling($member_id, $paydate, $id) {
        $pay_date = Diamond::parse($paydate);
		$shareholding = Shareholding::find($id);
		$total_shareholding = Shareholding::where('member_id', $member_id)
			->where('id', '<', $id)
			->whereDate('pay_date', '<', $shareholding->pay_date)
			->sum('amount');

        return view('admin.shareholding.billing', [
            'member' => Member::find($member_id),
            'shareholding' => $shareholding,
            'total_shareholding' => $total_shareholding,
            'billno' => Diamond::parse($shareholding->pay_date)->thai_format('Y') . str_pad($shareholding->id, 8, '0', STR_PAD_LEFT),
			'billing' => Billing::latest()->first()
        ]);
    }
   
    public function getPrintBilling($member_id, $paydate, $id) {
        $pay_date = Diamond::parse($paydate);
		$shareholding = Shareholding::find($id);
		$total_shareholding = Shareholding::where('member_id', $member_id)
			->where('id', '<', $id)
			->whereDate('pay_date', '<', $shareholding->pay_date)
			->sum('amount');

        return view('admin.shareholding.print', [
            'member' => Member::find($member_id),
            'shareholding' => $shareholding,
            'total_shareholding' => $total_shareholding,
            'billno' => Diamond::parse($shareholding->pay_date)->thai_format('Y') . str_pad($shareholding->id, 8, '0', STR_PAD_LEFT),
			'billing' => Billing::latest()->first()
        ]);
     }

     public function getPdfBilling($member_id, $paydate, $id) {
        $pay_date = Diamond::parse($paydate);
		$shareholding = Shareholding::find($id);
		$total_shareholding = Shareholding::where('member_id', $member_id)
			->where('id', '<', $id)
			->whereDate('pay_date', '<', $shareholding->pay_date)
			->sum('amount');

        $data = [
            'member' => Member::find($member_id),
            'shareholding' => $shareholding,
            'total_shareholding' => $total_shareholding,
            'billno' => Diamond::parse($shareholding->pay_date)->thai_format('Y') . str_pad($shareholding->id, 8, '0', STR_PAD_LEFT),
			'billing' => Billing::latest()->first()
        ];

        return PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
            ->loadView('admin.shareholding.pdf', $data)->download('ใบเสร็จรับเงินค่าหุ้นเดือน-' . Diamond::parse($shareholding->pay_date)->thai_format('M-Y') . '.pdf');
     }

     public function postUploadFile(Request $request) {
		$id = $request->input('shareholding_id');
		$file = $request->file('file');

		$display = mb_ereg_replace('\s+', ' ', basename($file->getClientOriginalName(), '.' . $file->getClientOriginalExtension()));
        $filename = time() . uniqid() . '.' . $file->getClientOriginalExtension();
		$path = ($file->getRealPath() != false) ? $file->getRealPath() : $file->getPathname();

        Storage::disk('attachments')->put($filename, file_get_contents($path));
    
        $attachment = new ShareholdingAttachment([
            'file' => $filename,
            'display' => $display
        ]);

		$shareholding = Shareholding::find($id);
        $shareholding->attachments()->save($attachment);

		$data = new stdClass();
        $data->id = ShareholdingAttachment::where('file',$filename)->first()->id;
		$data->href = FileManager::get('attachments', $filename);
        $data->display = $display;

        return Response::json($data);
     }

	 public function postDeleteFile(Request $request) {
		$id = $request->input('id');
		$attachment = ShareholdingAttachment::find($id);
		$shareholding_id = $attachment->shareholding_id;

		Storage::disk('attachments')->delete($attachment->file);
        $attachment->delete();

		$shareholding = Shareholding::find($shareholding_id);
		$data = new stdClass();
		$data->id = $id;
        $data->count = $shareholding->attachments->count();

        return Response::json($data);
     }
}
