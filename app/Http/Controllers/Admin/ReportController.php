<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Dividend;
use App\Dividendmember;
use App\Loan;
use App\RoutineShareholding;
use App\RoutinePayment;
use App\Member;
use DB;
use Diamond;
use Excel;
use PHPExcel_Shared_Date as ExcelDate;
use PHPExcel_Style_Conditional as ExcelConditional;
use PHPExcel_Style_Fill as ExcelFill;
use PHPExcel_Style_Color as ExcelColor;
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
                "where (m2.leave_date is null or if(m2.leave_date is null, FROM_DAYS(1), m2.leave_date) >= '" . $date->format('Y-m-1') . "') " .
                "and s2.pay_date <= '" . $date->copy()->endOfMonth()->format('Y-m-d') . "' " .
                "group by m2.id, p.name, p.lastname, et.name " .
                ") b " .
            "left join (" .
                "select a.id, sum(case when a.type_id = 1 then a.amount else 0 end) as a_amount, sum(case when a.type_id = 2 then a.amount else 0 end) as b_amount " .
                "from (" .
                    "select m1.id, s1.shareholding_type_id as type_id, sum(s1.amount) as amount " .
                    "from shareholdings s1 " .
                    "inner join members m1 on s1.member_id = m1.id " .
                    "where (m1.leave_date is null or if(m1.leave_date is null, FROM_DAYS(1), m1.leave_date) >= '" . $date->format('Y-m-1') . "') " .
                    "and s1.pay_date between '" . $date->format('Y-m-1') . "' and '" . $date->copy()->endOfMonth()->format('Y-m-d') . "' " .
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
        $filename = 'ชำระเงินกู้ประจำเดือน '. $date->thai_format('M Y');
        $header = ['#', 'เลขทะเบียนสมาชิก', 'ชื่อ-นามสกุล', 'ประเภทเงินกู้', 'เลขที่สัญญา', 'เงินต้น', 'ดอกเบี้ย', 'รวม', 'เงินต้นคงเหลือ'];
        $start_date = $date->format('Y-m-1');
        $end_date = $date->copy()->endOfMonth()->format('Y-m-d');

        $loan_types = DB::select(DB::raw('select lt.id, lt.name ' .
            'from payments p ' .
            'inner join loans l on p.loan_id = l.id  ' .
            'inner join loan_types lt on l.loan_type_id = lt.id  ' .
            "where p.pay_date between '" . $start_date . "' and '" . $end_date . "' " . 
            'group by lt.id, lt.name'));

        Excel::create($filename, function($excel) use ($filename, $header, $loan_types, $start_date, $end_date) {
            if (count($loan_types) > 0) {
                foreach ($loan_types as $index => $loan_type) {
                    $payments = DB::select(DB::raw('select members.id as member_id, payments.loan_id as loan_id, profiles.name, ' .
                        'profiles.lastname, loan_types.name as loan_types_name, loans.code as loan_code, ' .
                        'sum(payments.principle) as principle, sum(payments.interest) as interest, loans.outstanding as outstanding, ' .
                        "loans.outstanding - (select coalesce(sum(p.principle), 0) from tatcoop.payments p where p.pay_date <= date('" . $end_date . "') and p.loan_id = payments.loan_id) as balance " .
                        'from tatcoop.payments ' .
                        'inner join loans on payments.loan_id = loans.id ' .
                        'inner join loan_types on loans.loan_type_id = loan_types.id ' .
                        'inner join members on loans.member_id = members.id ' .
                        'inner join profiles on members.profile_id = profiles.id ' .
                        "where payments.pay_date between date('" . $start_date . "') and date('" . $end_date. "') " .
                        'and loan_types.id = ' . $loan_type->id . ' ' .
                        'group by members.id, profiles.name, profiles.lastname, loan_types.name, loans.code;'));
    
                    // sheet
                    $excel->sheet("ชำระเงินกู้ " . ($index + 1), function($sheet) use ($filename, $header, $payments, $loan_type) {
                        // disable auto size for sheet
                        $sheet->setAutoSize(false);
    
                        // title
                        $sheet->row(1, [$filename . ' ' . $loan_type->name]);
    
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
                }    
            }
            else {
                $excel->sheet("ชำระเงินกู้", function($sheet) use ($filename, $header) {
                    // disable auto size for sheet
                    $sheet->setAutoSize(false);

                    // title
                    $sheet->row(1, $filename);

                    // header
                    $sheet->row(3, $header);
                });
            }
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
        $filename = 'รายการนำส่งตัดบัญชีเงินเดือน ประจำเดือน '. $date->thai_format('M Y');

        Excel::create($filename, function($excel) use ($date) {
            $previous = $date->copy()->subMonth();
            $previous_shareholding = $this->routine_shareholding($previous);
            $previous_payment = $this->routine_payment($previous);
            $shareholding = $this->routine_shareholding($date);
            $payment = $this->routine_payment($date);
            $rows = [
                'previous_shareholding' => $previous_shareholding->count(),
                'previous_payment' => $previous_payment->count(),
                'shareholding' => !is_array($shareholding) ? $shareholding->count() : 0,
                'payment' => !is_array($payment) ? $payment->count() : 0
            ];

            // sheet
            $excel->sheet("รายการนำส่ง {$date->thai_format('M y')}", function($sheet) use ($date, $previous, $rows) {
                // disable auto size for sheet
                $sheet->setAutoSize(false);

                // title
                $title = ['รายการนำส่งตัดบัญชีเงินเดือน ประจำเดือน '. $date->thai_format('M Y')];
                $sheet->row(1, $title);

                // header
                $header = ['#', 'เลขทะเบียนสมาชิก', 'ชื่อ-นามสกุล', "จำนวนหุ้นรวม {$previous->thai_format('M y')}",
                    "จำนวนชำระเงินกู้รวม {$previous->thai_format('M y')}", "จำนวนหุ้นรวม {$date->thai_format('M y')}",
                    "จำนวนชำระเงินกู้รวม {$date->thai_format('M y')}", "ยอดรวม {$previous->thai_format('M y')}",
                    "ยอดรวม {$date->thai_format('M y')}", 'ผลต่าง'];
                $sheet->row(3, $header);

                // data
                $row = 4;
                $details = $this->routine_member($date);
                foreach ($details as $detail) {
                    $data = [];
                    $data[] = $row - 3;
                    $data[] = $detail->membercode;
                    $data[] = $detail->fullname;

                    $sheet->row($row, $data);
                    $sheet->setCellValue("D$row", "=IF(ISNA(VLOOKUP(B$row,'ค่าหุ้น {$previous->thai_format('M y')}'!" .
                        '$B$4:$G$' . ($rows['previous_shareholding'] + 3) . ",5,0)),0,VLOOKUP(B$row,'ค่าหุ้น {$previous->thai_format('M y')}'!" . 
                        '$B$4:$G$' . ($rows['previous_shareholding'] + 3) . ",5,0))");
                    $sheet->setCellValue("E$row", "=IF(ISNA(VLOOKUP(B$row,'เงินกู้ {$previous->thai_format('M y')}'!" .
                        '$D$4:$K$' . ($rows['previous_payment'] + 3) . ",8,0)),0,VLOOKUP(B$row,'เงินกู้ {$previous->thai_format('M y')}'!" . 
                        '$D$4:$K$' . ($rows['previous_payment'] + 3) . ",8,0))");
                    $sheet->setCellValue("F$row", "=IF(ISNA(VLOOKUP(B$row,'ค่าหุ้น {$date->thai_format('M y')}'!" .
                        '$B$4:$G$' . ($rows['shareholding'] + 3) . ",5,0)),0,VLOOKUP(B$row,'ค่าหุ้น {$date->thai_format('M y')}'!" . 
                        '$B$4:$G$' . ($rows['shareholding'] + 3) . ",5,0))");
                    $sheet->setCellValue("G$row", "=IF(ISNA(VLOOKUP(B$row,'เงินกู้ {$date->thai_format('M y')}'!" .
                        '$D$4:$K$' . ($rows['payment'] + 3) . ",8,0)),0,VLOOKUP(B$row,'เงินกู้ {$date->thai_format('M y')}'!" . 
                        '$D$4:$K$' . ($rows['payment'] + 3) . ",8,0))");
                    $sheet->setCellValue("H$row", "=D$row+E$row");
                    $sheet->setCellValue("I$row", "=F$row+G$row");
                    $sheet->setCellValue("J$row", "=I$row-H$row");
                    $row++;
                }

                $sheet->setColumnFormat([
                    "B4:B$row" => '00000',
                    "D4:D$row" => '#,##0.00',
                    "E4:E$row" => '#,##0.00',
                    "F4:F$row" => '#,##0.00',
                    "G4:G$row" => '#,##0.00',
                    "H4:H$row" => '#,##0.00',
                    "I4:I$row" => '#,##0.00',
                    "J4:J$row" => '#,##0.00'
                ]);

                $greater = new ExcelConditional();
                $greater->setConditionType(ExcelConditional::CONDITION_CELLIS);
                $greater->setOperatorType(ExcelConditional::OPERATOR_GREATERTHAN); 
                $greater->addCondition('0.001');
                $greater->getStyle()->getFill()->setFillType(ExcelFill::FILL_SOLID)->getEndColor()->setARGB(ExcelColor::COLOR_GREEN);

                $less = new ExcelConditional();
                $less->setConditionType(ExcelConditional::CONDITION_CELLIS);
                $less->setOperatorType(ExcelConditional::OPERATOR_LESSTHAN); 
                $less->addCondition('-0.001');
                $less->getStyle()->getFill()->setFillType(ExcelFill::FILL_SOLID)->getEndColor()->setARGB(ExcelColor::COLOR_RED);

                $conditionalStyles = $sheet->getStyle("J4:J$row")->getConditionalStyles();
                array_push($conditionalStyles, $greater);
                array_push($conditionalStyles, $less);
                $sheet->getStyle("J4:J$row")->setConditionalStyles($conditionalStyles);
            });

            // sheet
            $excel->sheet("ค่าหุ้น {$previous->thai_format('M y')}", function($sheet) use ($previous, $previous_shareholding) {
                // disable auto size for sheet
                $sheet->setAutoSize(false);

                // title
                $title = ['รายละเอียดการนำส่งค่าหุ้น ประจำเดือน '. $previous->thai_format('M Y')];
                $sheet->row(1, $title);

                // header
                $header = ['#', 'เลขทะเบียนสมาชิก', 'ชื่อ-นามสกุล', 'วันที่จ่าย', 'จำนวนหุ้น', 'ค่าหุ้นปกติ', 'ค่าหุ้นสะสมรวม'];
                $sheet->row(3, $header);

                // data
                $row = 4;
                $details = $previous_shareholding;
                foreach ($details as $detail) {
                    $data = [];
                    $data[] = $row - 3;
                    $data[] = $detail->membercode;
                    $data[] = $detail->fullname;
                    $data[] = ExcelDate::PHPToExcel(Diamond::parse($detail->paydate));
                    $data[] = $detail->shareholding;
                    $data[] = $detail->amount;
                    $data[] = $detail->total;

                    $sheet->row($row, $data);
                    $row++;
                }

                $sheet->setColumnFormat([
                    "B4:B$row" => '00000',
                    "D4:D$row" => '[$-th-TH,107]d mmm yy;@',
                    "E4:E$row" => '#,##0',
                    "F4:G$row" => '#,##0.00'
                ]);
            });

            // sheet
            $excel->sheet("เงินกู้ {$previous->thai_format('M y')}", function($sheet) use ($previous, $previous_payment) {
                // disable auto size for sheet
                $sheet->setAutoSize(false);

                // title
                $title = ['รายละเอียดการนำส่งค่าชำระเงินกู้ ประจำเดือน '. $previous->thai_format('M Y')];
                $sheet->row(1, $title);

                // header
                $header = ['#', 'ประเภทเงินกู้', 'เลขที่', 'เลขทะเบียนสมาชิก', 'ชื่อ-นามสกุล', 'งวดที่', 'วันที่จ่าย', 'จำนวนเงินต้น', 'จำนวนดอกเบี้ย', 'รวม', 'รวมทุกสัญญา'];
                $sheet->row(3, $header);

                // data
                $row = 4;
                $details = $previous_payment;
                foreach ($details as $detail) {
                    $data = [];
                    $data[] = $row - 3;
                    $data[] = $detail->loantypename;
                    $data[] = $detail->loancode;
                    $data[] = $detail->membercode;
                    $data[] = $detail->fullname;
                    $data[] = $detail->period;
                    $data[] = ExcelDate::PHPToExcel(Diamond::parse($detail->paydate));
                    $data[] = $detail->principle;
                    $data[] = $detail->interest;

                    $sheet->row($row, $data);
                    $sheet->setCellValue("J$row", "=H$row+I$row");
                    $sheet->setCellValue("K$row", "=IF(E$row=E" . ($row - 1) . ",\"\",SUMIF(E:E,E$row,J:J))");
                    $row++;
                }

                $sheet->setColumnFormat([
                    "D4:D$row" => '00000',
                    "F4:F$row" => '#,##0',
                    "G4:G$row" => '[$-th-TH,107]d mmm yy;@',
                    "H4:H$row" => '#,##0.00',
                    "I4:I$row" => '#,##0.00',
                    "J4:J$row" => '#,##0.00',
                    "K4:K$row" => '#,##0.00'
                ]);
            });

            // sheet
            $excel->sheet("ค่าหุ้น {$date->thai_format('M y')}", function($sheet) use ($date, $shareholding) {
                // disable auto size for sheet
                $sheet->setAutoSize(false);

                // title
                $title = ['รายละเอียดการนำส่งค่าหุ้น ประจำเดือน '. $date->thai_format('M Y')];
                $sheet->row(1, $title);

                // header
                $header = ['#', 'เลขทะเบียนสมาชิก', 'ชื่อ-นามสกุล', 'วันที่จ่าย', 'จำนวนหุ้น', 'ค่าหุ้นปกติ', 'ค่าหุ้นสะสมรวม'];
                $sheet->row(3, $header);

                // data
                $row = 4;
                $details = $shareholding;
                foreach ($details as $detail) {
                    $data = [];
                    $data[] = $row - 3;
                    $data[] = $detail->membercode;
                    $data[] = $detail->fullname;
                    $data[] = ExcelDate::PHPToExcel(Diamond::parse($detail->paydate));
                    $data[] = $detail->shareholding;
                    $data[] = $detail->amount;
                    $data[] = $detail->total;

                    $sheet->row($row, $data);
                    $row++;
                }

                $sheet->setColumnFormat([
                    "B4:B$row" => '00000',
                    "D4:D$row" => '[$-th-TH,107]d mmm yy;@',
                    "E4:E$row" => '#,##0',
                    "F4:G$row" => '#,##0.00'
                ]);
            });

            // sheet
            $excel->sheet("เงินกู้ {$date->thai_format('M y')}", function($sheet) use ($date, $payment) {
                // disable auto size for sheet
                $sheet->setAutoSize(false);

                // title
                $title = ['รายละเอียดการนำส่งค่าชำระเงินกู้ ประจำเดือน '. $date->thai_format('M Y')];
                $sheet->row(1, $title);

                // header
                $header = ['#', 'ประเภทเงินกู้', 'เลขที่', 'เลขทะเบียนสมาชิก', 'ชื่อ-นามสกุล', 'งวดที่', 'วันที่จ่าย', 'จำนวนเงินต้น', 'จำนวนดอกเบี้ย', 'รวม', 'รวมทุกสัญญา'];
                $sheet->row(3, $header);

                // data
                $row = 4;
                $details = $payment;
                foreach ($details as $detail) {
                    $data = [];
                    $data[] = $row - 3;
                    $data[] = $detail->loantypename;
                    $data[] = $detail->loancode;
                    $data[] = $detail->membercode;
                    $data[] = $detail->fullname;
                    $data[] = $detail->period;
                    $data[] = ExcelDate::PHPToExcel(Diamond::parse($detail->paydate));
                    $data[] = $detail->principle;
                    $data[] = $detail->interest;

                    $sheet->row($row, $data);
                    $sheet->setCellValue("J$row", "=H$row+I$row");
                    $sheet->setCellValue("K$row", "=IF(E$row=E" . ($row - 1) . ",\"\",SUMIF(E:E,E$row,J:J))");
                    $row++;
                }

                $sheet->setColumnFormat([
                    "D4:D$row" => '00000',
                    "F4:F$row" => '#,##0',
                    "G4:G$row" => '[$-th-TH,107]d mmm yy;@',
                    "H4:H$row" => '#,##0.00',
                    "I4:I$row" => '#,##0.00',
                    "J4:J$row" => '#,##0.00',
                    "K4:K$row" => '#,##0.00'
                ]);
            });

            $excel->setActiveSheetIndex(0);
        })->download('xlsx');
    }

    private function routine_member($date) {
        $members = DB::table('members')
            ->join('profiles', 'members.profile_id', '=', 'profiles.id')
            ->whereRaw("IF(members.leave_date is null, '3000-01-01', members.leave_date) > '" . $date->copy()->endOfMonth()->format('Y-m-d') . "'")
            ->select([
                'members.id as membercode',
                DB::raw("CONCAT(profiles.name, ' ', profiles.lastname) as fullname")
            ])
            ->get();

        return $members;
    }

    private function routine_shareholding($date) {
        $routine = RoutineShareholding::whereDate('calculated_date', '=', $date)->first();
        $details = [];

        if ($routine != null) {
            $details = RoutineShareholding::join('routine_shareholding_details', 'routine_shareholding_details.routine_shareholding_id', '=', 'routine_shareholdings.id')
                ->join('members', 'routine_shareholding_details.member_id', '=', 'members.id')
                ->join('profiles', 'profiles.id', '=', 'members.profile_id')
                ->leftJoin('shareholdings', 'shareholdings.member_id', '=', 'members.id')
                ->where('routine_shareholdings.id', $routine->id)
                ->where(function($query) use ($routine) {
                    $query->whereDate('shareholdings.pay_date', '<=', $routine->calculated_date->endOfMonth())
                        ->orWhereNull('shareholdings.pay_date'); 
                })
                ->groupBy('routine_shareholding_details.id',
                    'routine_shareholding_details.status',
                    'members.id',
                    'profiles.name',
                    'profiles.lastname',
                    'routine_shareholding_details.pay_date',
                    'members.shareholding')
                ->orderBy('membercode')
                ->select([
                    DB::raw("members.id as membercode"),
                    DB::raw("CONCAT(profiles.name, ' ', profiles.lastname) as fullname"),
                    DB::raw("routine_shareholding_details.pay_date as paydate"),
                    DB::raw("(routine_shareholding_details.amount / 10.0) as shareholding"),
                    DB::raw("routine_shareholding_details.amount as amount"),
                    DB::raw("SUM(IF(shareholdings.amount IS NOT NULL, shareholdings.amount, 0)) as total")
                ])
                ->get();
        }
        
        return $details;
    }

    private function routine_payment($date) {
        $routine = RoutinePayment::whereDate('calculated_date', '=', $date)->first();
        $details = [];

        if ($routine != null) {
            $details = RoutinePayment::join('routine_payment_details', 'routine_payment_details.routine_payment_id', '=', 'routine_payments.id')
                ->join('loans', 'routine_payment_details.loan_id', '=', 'loans.id')
                ->join('loan_types', 'loans.loan_type_id', '=', 'loan_types.id')
                ->join('members', 'loans.member_id', '=', 'members.id')
                ->join('profiles', 'profiles.id', '=', 'members.profile_id')
                ->where('routine_payments.id', $routine->id)
                ->orderBy('membercode')
                ->select([
                    'loans.code as loancode',
                    'loan_types.name as loantypename',
                    DB::raw("members.id as membercode"),
                    DB::raw("CONCAT(profiles.name, ' ', profiles.lastname) as fullname"),
                    DB::raw("routine_payment_details.period as period"),
                    DB::raw("routine_payment_details.pay_date as paydate"),
                    DB::raw("routine_payment_details.principle as principle"),
                    DB::raw("routine_payment_details.interest as interest")
                ])
                ->get();
        }

        return $details;
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
