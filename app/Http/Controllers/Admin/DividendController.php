<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Dividend;
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
        return view('admin.dividend.member');
    }

    public function index() {
        return view('admin.dividend.index', ['dividends'=>Dividend::orderBy('rate_year', 'desc')->get()]);
    }

    public function create() {
        return view('admin.dividend.create');
    }

    public function store(Request $request) {
        $rules = [
            'rate_year' => 'required|digits:4|unique:dividends,rate_year', 
            'rate' => 'required|numeric|between:0,100',
        ];

        $attributeNames = [
            'rate_year' => 'ปี ค.ศ.', 
            'rate' => 'อัตราเงินปันผล',
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
                $dividend->rate = $request->input('rate');
                $dividend->save();
    
                History::addAdminHistory(Auth::guard($this->guard)->id(), 'เพิ่มข้อมูล', 'ป้อนอัตราเงินปันผล ประจำปี ' . $dividend->rate_year + 543);
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
            'rate' => 'required|numeric|between:0,100',
        ];

        $attributeNames = [
            'rate' => 'ตราเงินปันผล',
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
                $dividend->rate = $request->input('rate');
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

    public function getExport($id) {
        $dividend = Dividend::find($id);
        $members = Member::active()->get();
        $filename = 'สรุปรายละเอียดการปันผลประจำปี '. Diamond::parse($dividend->rate_year . '-1-1')->thai_format('Y');
        $header = ['รหัสสมาชิก', 'ชื่อ-นามสกุล', 'ค่าหุ้นยกมา', 'เงินปันผล', 
            'ค่าหุ้น ม.ค.', 'เงินปันผล', 'ค่าหุ้น ก.พ.', 'เงินปันผล', 'ค่าหุ้น มี.ค.', 'เงินปันผล', 
            'ค่าหุ้น เม.ย.', 'เงินปันผล', 'ค่าหุ้น พ.ค.', 'เงินปันผล', 'ค่าหุ้น มิ.ย.', 'เงินปันผล', 
            'ค่าหุ้น ก.ค.', 'เงินปันผล', 'ค่าหุ้น ส.ค.', 'เงินปันผล', 'ค่าหุ้น ก.ย.', 'เงินปันผล',
            'ค่าหุ้น ต.ค.', 'เงินปันผล', 'ค่าหุ้น พ.ย.', 'เงินปันผล', 'ค่าหุ้น ธ.ค.', 'เงินปันผล', 'รวมเงินปันผล'];

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

                    $m_dividends = MemberProperty::getDividend($member->id, $dividend->rate_year);

                    $index = 0;
                    $column = 3;
                    foreach ($m_dividends as $m_dividend) {
                        $pointer = ($index > 0) ? Diamond::parse($m_dividend->name)->month : 0;

                        if ($index == 0) {
                            $data[] = $m_dividend->amount;
                            $data[] = "=" . MemberProperty::getExcelColumn($column) . "$row*B2";
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

                            $data[] = $m_dividend->amount;
                            $data[] = "=" . MemberProperty::getExcelColumn($column) . "$row*B2*(" . strval(12 - Diamond::parse($m_dividend->name)->month) . "/12)";
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

                    $data[] = "=" . MemberProperty::getExcelColumn(4) . "$row+" . MemberProperty::getExcelColumn(6) . "$row+" .
                        MemberProperty::getExcelColumn(8) . "$row+" . MemberProperty::getExcelColumn(10) . "$row+" .
                        MemberProperty::getExcelColumn(12) . "$row+" . MemberProperty::getExcelColumn(14) . "$row+" .
                        MemberProperty::getExcelColumn(16) . "$row+" . MemberProperty::getExcelColumn(18) . "$row+" .
                        MemberProperty::getExcelColumn(20) . "$row+" . MemberProperty::getExcelColumn(22) . "$row+" .
                        MemberProperty::getExcelColumn(24) . "$row+" . MemberProperty::getExcelColumn(26) . "$row+" .
                        MemberProperty::getExcelColumn(28) . "$row";

                    $sheet->row($row, $data);
                    $row++;
                }

                $sheet->setColumnFormat(array(
                    "C5:AC$row" => '#,##0.00'
                ));
            });
        })->download('xlsx');
    }
}
