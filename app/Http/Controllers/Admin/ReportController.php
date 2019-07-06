<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Dividend;
use App\Dividendmember;
use App\Loan;
use App\Member;
use DB;
use Diamond;
use Excel;
use stdClass;

class ReportController extends Controller
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

    public function getIndex() {
        return view('admin.report.index');
    }

    public function postExport(Request $request) {
        $date = Diamond::parse($request->input('date'));
        $reporttype = $request->input('reporttype');
        $report = $request->input('report');

        switch ($reporttype) {
            case 'annual':
                $this->annual($report, $date);
                break;
            case 'monthly':
                $this->monthly($report, $date);
                break;
            case 'today':
                $this->today($report);
                break;
        }
    }

    public function annual($report, $date) {
        switch ($report) {
            case 'diviends':
                $this->diviends($date);
                break;
        }
    }
 
    public function monthly($report, $date) {
        switch ($report) {
            case 'shareholdings':
                $this->shareholdings($date);
                break;
            case 'loans':
                $this->loans($date);
                break;
            case 'routine':
                $this->routine($date);
                break;
        }
    }

    public function today($report) {
        switch ($report) {
            case 'members':
                $this->members();
                break;
            case 'payments':
                $this->payments();
                break;
        }
    }

    private function diviends($date) {
        $year = $date->year;
        $dividend = Dividend::where('rate_year', $year)->first();
        $filename = 'เงินปันผลของสมาชิกสหกรณ์ ปี '. $date->thai_format('Y');

        if (!is_null($dividend)) {
            $members = Member::active()->get();
            $header = ['รหัสสมาชิก', 'ชื่อ-นามสกุล', 'ค่าหุ้นยกมา', 'เงินปันผล', 
            'ค่าหุ้น ม.ค.', 'เงินปันผล', 'ค่าหุ้น ก.พ.', 'เงินปันผล', 'ค่าหุ้น มี.ค.', 'เงินปันผล', 'ค่าหุ้น เม.ย.', 'เงินปันผล',
            'ค่าหุ้น พ.ค.', 'เงินปันผล', 'ค่าหุ้น มิ.ย.', 'เงินปันผล', 'ค่าหุ้น ก.ค.', 'เงินปันผล', 'ค่าหุ้น ส.ค.', 'เงินปันผล',
            'ค่าหุ้น ก.ย.', 'เงินปันผล', 'ค่าหุ้น ต.ค.', 'เงินปันผล', 'ค่าหุ้น พ.ย.','เงินปันผล', 'ค่าหุ้น ธ.ค.', 'เงินปันผล', 
            'รวมเงินปันผล', 'รวมเงินเฉลี่ยคืน', 'รวมเป็นเงินทั้งสิ้น'];

            Excel::create($filename, function($excel) use ($filename, $dividend, $header, $members) {
                // sheet
                $excel->sheet('รายละเอียดการปันผล', function($sheet) use ($filename, $dividend, $header, $members) {
                    // disable auto size for sheet
                    $sheet->setAutoSize(false);

                    // title
                    $sheet->row(1, [$filename]);

                    // rate
                    $sheet->row(2, ['อัตราเงินปันผล', $dividend->shareholding_rate / 100]);
                    $sheet->setColumnFormat([
                        'B2' => '0.00%'
                    ]);
                    $sheet->row(3, ['อัตราเงินเฉลี่ยคืน', $dividend->loan_rate / 100]);
                    $sheet->setColumnFormat([
                        'B3' => '0.00%'
                    ]);

                    // header
                    $sheet->row(5, $header);

                    // data
                    $row = 6;
                    foreach ($members as $member) {
                        $data = [];
                        $data[] = $member->memberCode;
                        $data[] = $member->profile->fullname;

                        $m_dividends = Dividendmember::where('dividend_id', $dividend->id)
                            ->where('member_id', $member->id)
                            ->get();

                        $dividend_total = 0;
                        $index = 0;
                        $column = 3;
                        foreach ($m_dividends as $m_dividend) {
                            $pointer = ($index > 0) ? $this->name_to_date($m_dividend->dividend_name)->month : 0;

                            if ($index == 0) {
                                $data[] = $m_dividend->shareholding;
                                $data[] = $m_dividend->shareholding_dividend; //"=" . $this->getExcelColumn($column) . "$row*B2";
                                $dividend_total += $m_dividend->shareholding_dividend;
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

                                $data[] = $m_dividend->shareholding;
                                $data[] = $m_dividend->shareholding_dividend; //"=" . $this->getExcelColumn($column) . "$row*B2*(" . strval(12 - Diamond::parse($m_dividend->dividend_name)->month) . "/12)";
                                $dividend_total += $m_dividend->shareholding_dividend;
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

                        $data[] = $dividend_total;
                        // $data[] = "=" . $this->getExcelColumn(4) . "$row+" . $this->getExcelColumn(6) . "$row+" .
                        //     $this->getExcelColumn(8) . "$row+" . $this->getExcelColumn(10) . "$row+" .
                        //     $this->getExcelColumn(12) . "$row+" . $this->getExcelColumn(14) . "$row+" .
                        //     $this->getExcelColumn(16) . "$row+" . $this->getExcelColumn(18) . "$row+" .
                        //     $this->getExcelColumn(20) . "$row+" . $this->getExcelColumn(22) . "$row+" .
                        //     $this->getExcelColumn(24) . "$row+" . $this->getExcelColumn(26) . "$row+" .
                        //     $this->getExcelColumn(28) . "$row";
                            
                        $data[] = $m_dividends->sum('interest_dividend');

                        $data[] = $dividend_total + $m_dividends->sum('interest_dividend'); //"=" .  $this->getExcelColumn(29) . "$row+" . $this->getExcelColumn(30) . "$row";

                        $sheet->row($row, $data);
                        $row++;
                    }

                    $sheet->setColumnFormat([
                        "C5:AE$row" => '#,##0.00'
                    ]);
                });
            })->download('xlsx');
        }
        else {
            Excel::create($filename, function($excel) use ($filename) {
                // sheet
                $excel->sheet('รายละเอียดการปันผล', function($sheet) use ($filename) {
                    // disable auto size for sheet
                    $sheet->setAutoSize(false);

                    // title
                    $sheet->row(1, [$filename]);

                    // rate
                    $sheet->row(3, ['ไม่มีข้อมูล']);
                });
            })->download('xlsx');
        }
    }

    private function shareholdings($date) {
        // เฉพาะคนที่จ่าย
        // $shareholdings = DB::select(DB::raw("select lpad(b.id, 5, '0') as member_code, c.fullname, c.employee_type, b.a_amount, b.b_amount, c.total_shareholding " .
        //     "from (" .
        //         "select a.id, sum(case when a.type_id = 1 then a.amount else 0 end) as a_amount, sum(case when a.type_id = 2 then a.amount else 0 end) as b_amount " .
        //         "from (" .
        //             "select m1.id, s1.shareholding_type_id as type_id, sum(s1.amount) as amount " .
        //             "from shareholdings s1 " .
        //             "inner join members m1 on s1.member_id = m1.id " .
        //             "where m1.leave_date is null " .
        //             "and s1.pay_date between '" . $date->format('Y-m-1') . "' and '" . $date->copy()->endOfMonth()->format('Y-m-d') . "' ".
        //             "group by m1.id, s1.shareholding_type_id" .
        //         ") a " .
        //         "group by a.id " .
        //     ") b " .
        //     "inner join (" .
        //         "select m2.id, concat(p.name, ' ', p.lastname) as fullname, et.name as employee_type, sum(s2.amount) as total_shareholding " .
        //         "from shareholdings s2 " .
        //         "inner join members m2 on s2.member_id = m2.id " .
        //         "inner join profiles p on m2.profile_id = p.id " .
        //         "inner join employees e on m2.profile_id = e.profile_id " .
        //         "inner join employee_types et on e.employee_type_id = et.id " .
        //         "where m2.leave_date is null " .
        //         "and s2.pay_date <= '" . $date->copy()->endOfMonth()->format('Y-m-d') . "' " .
        //         "group by m2.id, p.name, p.lastname, et.name " .
        //     ") c on b.id = c.id;"));

        // ทั้งหมด
        $shareholdings = DB::select(DB::raw("select lpad(b.id, 5, '0') as member_code, b.fullname, b.employee_type, " .
            "case when c.a_amount is not null then c.a_amount else 0 end as a_amount, " .
            "case when c.b_amount is not null then c.b_amount else 0 end as b_amount, " . 
            "b.total_shareholding " .
            "from (" .
                "select m2.id, concat(p.name, ' ', p.lastname) as fullname, et.name as employee_type, sum(s2.amount) as total_shareholding " .
                "from shareholdings s2 " .
                "inner join members m2 on s2.member_id = m2.id " .
                "inner join profiles p on m2.profile_id = p.id " .
                "inner join employees e on m2.profile_id = e.profile_id " .
                "inner join employee_types et on e.employee_type_id = et.id " .
                "where m2.leave_date is null " .
                "and s2.pay_date <= '" . $date->copy()->endOfMonth()->format('Y-m-d') . "' " .
                "group by m2.id, p.name, p.lastname, et.name " .
                ") b " .
            "left join (" .
                "select a.id, sum(case when a.type_id = 1 then a.amount else 0 end) as a_amount, sum(case when a.type_id = 2 then a.amount else 0 end) as b_amount " .
                "from (" .
                    "select m1.id, s1.shareholding_type_id as type_id, sum(s1.amount) as amount " .
                    "from shareholdings s1 " .
                    "inner join members m1 on s1.member_id = m1.id " .
                    "where m1.leave_date is null " .
                    "and s1.pay_date between '" . $date->format('Y-m-1') . "' and '" . $date->copy()->endOfMonth()->format('Y-m-d') . "' ".
                    "group by m1.id, s1.shareholding_type_id" .
                ") a " .
                "group by a.id " .
            ") c on b.id = c.id;"));

        $filename = 'ชำระค่าหุ้นประจำเดือน '. $date->thai_format('M Y');
        $header = ['#', 'เลขทะเบียนสมาชิก', 'ชื่อ-นามสกุล', 'ประเภทสมาชิก', 'ค่าหุ้นปกติ', 'ค่าหุ้นเงินสด', 'ค่าหุ้นสะสมรวม'];

        Excel::create($filename, function($excel) use ($filename, $header, $shareholdings) {
            // sheet
            $excel->sheet('ชำระค่าหุ้น', function($sheet) use ($filename, $header, $shareholdings) {
                // disable auto size for sheet
                $sheet->setAutoSize(false);

                // title
                $sheet->row(1, [$filename]);

                // header
                $sheet->row(3, $header);

                // data
                $row = 4;
                foreach ($shareholdings as $shareholding) {
                    $data = [];
                    $data[] = $row - 3;
                    $data[] = $shareholding->member_code;
                    $data[] = $shareholding->fullname;
                    $data[] = $shareholding->employee_type;
                    $data[] = $shareholding->a_amount;
                    $data[] = $shareholding->b_amount;
                    $data[] = $shareholding->total_shareholding;

                    $sheet->row($row, $data);
                    $row++;
                }

                $sheet->setColumnFormat([
                    "E4:G$row" => '#,##0.00'
                ]);
            });
        })->download('xlsx');
    }

    private function loans($date) {
        $payments = DB::select(DB::raw('select members.id as member_id, payments.loan_id as loan_id, profiles.name, profiles.lastname, loan_types.name as loan_types_name, loans.code as loan_code, sum(payments.principle) as principle, sum(payments.interest) as interest, loans.outstanding as outstanding, ' .
            "loans.outstanding - (select coalesce(sum(p.principle), 0) from tatcoop.payments p where p.pay_date <= date('" . Diamond::create($date->year, $date->month, $date->daysInMonth, 0, 0, 0) . "') and p.loan_id = payments.loan_id) as balance " .
            'from tatcoop.payments ' .
            'inner join loans on payments.loan_id = loans.id ' .
            'inner join loan_types on loans.loan_type_id = loan_types.id ' .
            'inner join members on loans.member_id = members.id ' .
            'inner join profiles on members.profile_id = profiles.id ' .
            "where payments.pay_date between date('" . Diamond::create($date->year, $date->month, 1, 0, 0, 0) . "') and date('" . Diamond::create($date->year, $date->month, $date->daysInMonth, 0, 0, 0) . "') " .
            'group by members.id, profiles.name, profiles.lastname, loan_types.name, loans.code;'
        ));

        $filename = 'ชำระเงินกู้ประจำเดือน '. $date->thai_format('M Y');
        $header = ['#', 'เลขทะเบียนสมาชิก', 'ชื่อ-นามสกุล', 'ประเภทเงินกู้', 'เลขที่สัญญา', 'เงินต้น', 'ดอกเบี้ย', 'รวม', 'เงินต้นคงเหลือ'];

        Excel::create($filename, function($excel) use ($filename, $header, $payments, $date) {
            // sheet
            $excel->sheet('ชำระเงินกู้', function($sheet) use ($filename, $header, $payments, $date) {
                // disable auto size for sheet
                $sheet->setAutoSize(false);

                // title
                $sheet->row(1, [$filename]);

                // header
                $sheet->row(3, $header);

                // data
                $row = 4;
                foreach ($payments as $payment) {
                    $data = [];
                    $data[] = $row - 3;
                    $data[] = str_pad($payment->member_id, 5, "0", STR_PAD_LEFT);
                    $data[] = $payment->name . ' ' . $payment->lastname;
                    $data[] = $payment->loan_types_name;
                    $data[] = $payment->loan_code;
                    $data[] = $payment->principle;
                    $data[] = $payment->interest;
                    $data[] = $payment->principle + $payment->interest;
                    $data[] = $payment->balance;

                    $sheet->row($row, $data);
                    $row++;
                }

                $sheet->setColumnFormat([
                    "F4:I$row" => '#,##0.00'
                ]);
            });
        })->download('xlsx');
    }

    private function members() {
        $members = Member::all();
        $filename = 'สมาชิกสหกรณ์ '. Diamond::today()->thai_format('j M Y');
        $header = ['#', 'รหัสพนักงาน', 'ชื่อ', 'สกุล', 'เลขประจำตัวประชาชน', 'เลขทะเบียนสมาชิก', 'ที่อยู่', 'ตำบล/แขวง', 'อำเภอ/เขต', 'จังหวัด', 'รหัสไปรษณีย์', 'จำนวนหุ้น', 'จำนวนเงินค่าหุ้น', 'วันที่เข้าเป็นสมาชิก', 'ออกจากสมาชิกเมื่อ', 'ค่าทำเนียม', 'วัดเกิด', 'อายุ', 'อีเมล', 'สถานะ'];

        Excel::create($filename, function($excel) use ($filename, $members, $header) {
            // sheet
            $excel->sheet('สมาชิกสหกรณ์', function($sheet) use ($filename, $members, $header) {
                // disable auto size for sheet
                $sheet->setAutoSize(false);

                // title
                $sheet->row(1, [$filename]);

                // header
                $sheet->row(3, $header);

                // data
                $count = 0;
                $row = 4;
                foreach ($members as $member) {
                    $sheet->row($row, [++$count, $member->profile->employee->code, $member->profile->name, $member->profile->lastname,
                        $member->profile->citizen_code, $member->memberCode, $member->profile->address, $member->profile->subdistrict->name, 
                        $member->profile->subdistrict->district->name, $member->profile->subdistrict->district->province->name, $member->profile->subdistrict->postcode->code,
                        $member->shareholding, $member->shareholdings->sum('amount'), Diamond::parse($member->start_date)->thai_format('Y-m-d'),
                        (!is_null($member->leave_date)) ? Diamond::parse($member->leave_date)->thai_format('Y-m-d') : '-', $member->fee,
                        (!is_null($member->profile->birth_date)) ? Diamond::parse($member->profile->birth_date)->thai_format('Y-m-d') : '-', (!is_null($member->profile->birth_date)) ? $member->profile->birth_date->age : '-', (!is_null($member->user)) ? $member->user->email : '-',
                        (is_null($member->leave_date)) ? $member->profile->employee->employee_type->name : 'ลาออก']);
                    $row++;
                }

                $sheet->setColumnFormat([
                    "L4:L$row" => '#,##0',
                    "M4:M$row" => '#,##0.00',
                    "P4:P$row" => '#,##0.00'
                ]);
            });
        })->download('xlsx');
    }

    private function payments() {
        $loans = Loan::whereNull('completed_at')
            ->whereNotNull('code')
            ->orderBy('loan_type_id')
            ->get();

        $count = 0;
        $collection = collect([]);
        foreach ($loans as $loan) {
            $item = new stdClass();
            $item->index = ++$count;
            $item->loantype = $loan->loanType->name;
            $item->loancode = $loan->code;
            $item->membercode = $loan->member->memberCode;
            $item->membername = $loan->member->profile->fullname;
            $item->loandate = Diamond::parse($loan->loaned_at)->format('Y-m-d');
            $item->outstanding = $loan->outstanding;
            $item->period = $loan->payments->count() . '/' . $loan->period;
            $item->priciple = $loan->outstanding - $loan->payments->sum('principle');
            $collection->push($item);
        }

        $filename = 'สัญญาเงินกู้ที่กำลังผ่อนชำระ '. Diamond::today()->thai_format('j M Y');
        $header = ['#','ประเภทเงินกู้','เลขที่สัญญา','รหัสสมาชิก','ชื่อผู้กู้','วันที่กู้','วงเงินที่กู้','ผ่อนชำระไปแล้ว','เงินต้นคงเหลือ'];

        Excel::create($filename, function($excel) use ($filename, $collection, $header) {
            // sheet
            $excel->sheet('สัญญาเงินกู้ที่กำลังผ่อนชำระ', function($sheet) use ($filename, $collection, $header) {
                // disable auto size for sheet
                $sheet->setAutoSize(false);

                // title
                $sheet->row(1, [$filename]);

                // header
                $sheet->row(3, $header);

                // data
                $row = 4;
                foreach ($collection as $data) {
                    $sheet->row($row, [$data->index, $data->loantype, $data->loancode, 
                        $data->membercode, $data->membername, $data->loandate, 
                        $data->outstanding, $data->period, $data->priciple]);
                    $row++;
                }

                $sheet->setColumnFormat([
                    "G4:G$row" => '#,##0.00',
                    "I4:I$row" => '#,##0.00',
                ]);
            });
        })->download('xlsx');
    }

    private function routine($date) {
        
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

    private function name_to_date($name) {
        $months = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
        $date = explode(" ", $name);
        $month = array_search($date[0], $months) + 1;
        $year = $date[1] - 543;

        return Diamond::parse("{$year}-{$month}-1");
    }
}
