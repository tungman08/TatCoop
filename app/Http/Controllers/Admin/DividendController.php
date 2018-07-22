<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Dividend;
use App\Dividendmember;
use App\Member;
use Auth;
use DB;
use Diamond;
use Excel;
use History;
use Validator;
use MemberProperty;

class DividendController extends Controller
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
        $dividend = Dividend::whereRaw('rate_year = (select max(rate_year) as rate_year from dividends)')->first();
        $year = ($dividend != null) ? $dividend->rate_year : intval(Diamond::today()->format('Y'));

        return view('admin.dividend.member', [
            'year' => $year
        ]);
    }

    public function getMemberDividend($member_id) {
        $dividend_years = Dividend::all();

        $member = Member::find($member_id);
        $dividend = $dividend_years->last();

        $dividends = Dividendmember::where('dividend_id', $dividend->id)
            ->where('member_id', $member->id)
            ->get();

        return view('admin.dividend.show', [
            'member' => $member,
            'dividend_years' => collect($dividend_years),
            'dividends' => $dividends,
        ]);
    }

    public function index() {
        return view('admin.dividend.index', [
            'dividends' => Dividend::orderBy('rate_year', 'desc')->get()
        ]);
    }

    public function create() {
        return view('admin.dividend.create');
    }

    public function store(Request $request) {
        $rules = [
            'rate_year' => 'required|digits:4|unique:dividends,rate_year', 
            'shareholding_rate' => 'required|numeric|between:0,100',
            'loan_rate' => 'required|numeric|between:0,100',
            'release_date' => 'required|date_format:Y-m-d',
        ];

        $attributeNames = [
            'rate_year' => 'ปี ค.ศ.', 
            'shareholding_rate' => 'อัตราเงินปันผล',
            'loan_rate' => 'อัตราเงินเฉลี่ยคืน',
            'release_date' => 'วันที่เผยแพร่'
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        else {
            DB::transaction(function() use ($request) {
                $dividend = new Dividend();
                $dividend->rate_year = $request->input('rate_year');
                $dividend->shareholding_rate = $request->input('shareholding_rate');
                $dividend->loan_rate = $request->input('loan_rate');
                $dividend->release_date = Diamond::parse($request->input('release_date'));
                $dividend->save();
    
                History::addAdminHistory(Auth::guard($this->guard)->id(), 'เพิ่มข้อมูล', 'ป้อนอัตราเงินปันผล ประจำปี ' . $dividend->rate_year);
            });

            return redirect()->action('Admin\DividendController@index')
                ->with('flash_message', 'ข้อมูลอัตราเงินปันผลถูกป้อนเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function edit($id) {
        return view('admin.dividend.edit', ['dividend'=>Dividend::find($id)]);
    }

    public function update(Request $request, $id) {
        $rules = [
            'shareholding_rate' => 'required|numeric|between:0,100',
            'loan_rate' => 'required|numeric|between:0,100'
        ];

        $attributeNames = [
            'shareholding_rate' => 'อัตราเงินปันผล',
            'loan_rate' => 'อัตราเงินเฉลี่ยคืน'
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
                $dividend = Dividend::find($id);
                $dividend->shareholding_rate = $request->input('shareholding_rate');
                $dividend->loan_rate = $request->input('loan_rate');
                $dividend->save();
    
                History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'แก้ไขอัตราเงินปันผล ประจำปี ' . $dividend->rate_year + 543);
            });

            return redirect()->action('Admin\DividendController@index')
                ->with('flash_message', 'แก้ไขข้อมูลอัตราเงินปันผลเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function destroy($id) {
        DB::transaction(function() use ($id) {
            $dividend = Dividend::find($id);

            History::addAdminHistory(Auth::guard($this->guard)->id(), 'ลบข้อมูล', 'ลบอัตราเงินปันผล ประจำปี ' . $dividend->rate_year + 543);

            $dividend->delete();
        });

        return redirect()->action('Admin\DividendController@index')
            ->with('flash_message', 'ลบข้อมูลอัตราเงินปันผลเรียบร้อยแล้ว')
            ->with('callout_class', 'callout-success');
    }

    public function postExport($year) {
        $dividend = Dividend::where('rate_year', $year)->first();
        $members = Member::active()->get();
        $filename = 'สรุปรายละเอียดการปันผลประจำปี '. Diamond::parse($dividend->rate_year . '-1-1')->thai_format('Y');
        $header = ['รหัสสมาชิก', 'ชื่อ-นามสกุล', 'ค่าหุ้นยกมา', 'เงินปันผล', 
            'ค่าหุ้น ม.ค.', 'เงินปันผล', 'ค่าหุ้น ก.พ.', 'เงินปันผล', 'ค่าหุ้น มี.ค.', 'เงินปันผล', 'ค่าหุ้น เม.ย.', 'เงินปันผล',
            'ค่าหุ้น พ.ค.', 'เงินปันผล', 'ค่าหุ้น มิ.ย.', 'เงินปันผล', 'ค่าหุ้น ก.ค.', 'เงินปันผล', 'ค่าหุ้น ส.ค.', 'เงินปันผล',
            'ค่าหุ้น ก.ย.', 'เงินปันผล', 'ค่าหุ้น ต.ค.', 'เงินปันผล', 'ค่าหุ้น พ.ย.','เงินปันผล', 'ค่าหุ้น ธ.ค.', 'เงินปันผล', 
            'รวมเงินปันผล', 'รวมเงินเฉลี่ยคืน', 'รวมเป็นเงินทั้งสิ้น'];

        Excel::create($filename, function($excel) use($filename, $dividend, $header, $members) {
            // sheet
            $excel->sheet('รายละเอียดการปันผล', function($sheet) use($filename, $dividend, $header, $members) {
                // disable auto size for sheet
                $sheet->setAutoSize(false);

                // title
                $sheet->row(1, [$filename]);

                // rate
                $sheet->row(2, ['อัตราเงินปันผล', ($dividend->rate / 100)]);
                $sheet->setColumnFormat(array(
                    'B2' => '0.00%'
                ));

                // header
                $sheet->row(4, $header);

                // data
                $row = 5;
                foreach ($members as $member) {
                    $data = [];
                    $data[] = $member->memberCode;
                    $data[] = $member->profile->fullName;

                    $m_dividends = Dividendmember::where('dividend_id', $dividend->id)
                        ->where('member_id', $member->id)
                        ->get();

                    $index = 0;
                    $column = 3;
                    foreach ($m_dividends as $m_dividend) {
                        $pointer = ($index > 0) ? Diamond::parse($m_dividend->dividend_name)->month : 0;

                        if ($index == 0) {
                            $data[] = $m_dividend->shareholding_dividend;
                            $data[] = "=" . $this->getExcelColumn($column) . "$row*B2";
                            $column += 2;
                        }
                        else {
                            if ($pointer > $index) {
                                for ($i = 0; $i < $pointer - $index; $i++) {
                                    $data[] = 0;
                                    $data[] = 0;

                                    $column += 2;
                                    $index++;
                                }
                            }

                            $data[] = $m_dividend->shareholding_dividend;
                            $data[] = "=" . $this->getExcelColumn($column) . "$row*B2*(" . strval(12 - Diamond::parse($m_dividend->dividend_name)->month) . "/12)";
                            $column += 2;
                        }

                        $index++;
                    }

                    while ($index < 13) {
                        $data[] = 0;
                        $data[] = 0;

                        $column += 2;
                        $index++;
                    }

                    $data[] = "=" . $this->getExcelColumn(4) . "$row+" . $this->getExcelColumn(6) . "$row+" .
                        $this->getExcelColumn(8) . "$row+" . $this->getExcelColumn(10) . "$row+" .
                        $this->getExcelColumn(12) . "$row+" . $this->getExcelColumn(14) . "$row+" .
                        $this->getExcelColumn(16) . "$row+" . $this->getExcelColumn(18) . "$row+" .
                        $this->getExcelColumn(20) . "$row+" . $this->getExcelColumn(22) . "$row+" .
                        $this->getExcelColumn(24) . "$row+" . $this->getExcelColumn(26) . "$row+" .
                        $this->getExcelColumn(28) . "$row";
                        
                    $data[] = $m_dividends->sum('interest_dividend');

                    $data[] = "=" .  $this->getExcelColumn(29) . "$row+" . $this->getExcelColumn(30) . "$row";

                    $sheet->row($row, $data);
                    $row++;
                }

                $sheet->setColumnFormat(array(
                    "C5:AC$row" => '#,##0.00'
                ));
            });
        })->download('xlsx');
    }

    private function getExcelColumn($val) {
        $first = floor(($val / 26));
        $second = $val % 26;

        if ($first > 0) {
            if ($second == 0) {
                return 'Z';
            }
            else {
                return chr(64 + $first) . chr(64 + $second);
            }
        }
        else {
            return chr(64 + $second);
        }
    }
}
