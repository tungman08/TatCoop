<?php

use Illuminate\Database\Seeder;

class DocumentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['document_type_id' => 3, 'display' => 'สถานะทางการเงิน', 'file' => 'status'],
            ['document_type_id' => 3, 'display' => 'สรุปยอดเงินฝาก/การซื้อสลาก', 'file' => 'deposit'],
            ['document_type_id' => 3, 'display' => 'ตารางอัตราการหักคืนเงินกู้พร้อมดอกเบี้ยรายงวด', 'file' => 'loan_rate'],
            ['document_type_id' => 3, 'display' => 'ประกาศอัตราดอกเบี้ยใหม่', 'file' => 'rate'],
            ['document_type_id' => 3, 'display' => 'คณะกรรมการดำเนินการสหกรณ์ฯ', 'file' => 'boards'],
            ['document_type_id' => 3, 'display' => 'เจ้าหน้าที่ประจำสหกรณ์ฯ', 'file' => 'officers'],
            ['document_type_id' => 1, 'display' => 'ระเบียบฯ ว่าด้วยการให้เงินกู้แก่สมาชิกและดอกเบี้ยเงินกู้ (ฉบับที่ 3) พ.ศ. 2554 (เงินกู้สามัญ)', 'file' => 'loan_reg2009-2'],
            ['document_type_id' => 1, 'display' => 'ระเบียบฯ ว่าด้วยการให้เงินกู้แก่สมาชิกและดอกเบี้ยเงินกู้ (ฉบับที่ 5) พ.ศ. 2558 (เงินกู้เพื่อเหตุฉุกเฉิน)', 'file' => 'loan_reg200706'],
            ['document_type_id' => 1, 'display' => 'ระเบียบฯ ว่าด้วยการใช้ทุนสาธารณประโยชน์ พ.ศ. 2549', 'file' => 'docs03'],
            ['document_type_id' => 1, 'display' => 'ระเบียบฯ ว่าด้วยการใช้ทุนให้สวัสดิการแก่สามชิกและครอบครัว พ.ศ. 2549', 'file' => 'docs04'],
            ['document_type_id' => 1, 'display' => 'ระเบียบฯ ว่าด้วยพนักงานและลูกจ้าง พ.ศ. 2547', 'file' => 'docs05'],
            ['document_type_id' => 1, 'display' => 'ระเบียบฯ ว่าด้วยอัตราเงินเดือนพนักงานสหกรณ์ พ.ศ. 2547', 'file' => 'docs06'],
            ['document_type_id' => 1, 'display' => 'ระเบียบฯ ว่าด้วยการถือหุ้น พ.ศ. 2558', 'file' => 'docs07'],
            ['document_type_id' => 1, 'display' => 'ข้อบังคับ', 'file' => 'rule'],
            ['document_type_id' => 2, 'display' => 'แบบคำขอและหนังสือกู้เงินสามัญเฉพาะกิจเพื่อการท่องเที่ยว', 'file' => 'loan_tour1'],
            ['document_type_id' => 2, 'display' => 'แบบคำขอและหนังสือกู้เงินสามัญเฉพาะกิจเพื่อดำรงชีพ', 'file' => 'living_loan_form'],
            ['document_type_id' => 2, 'display' => 'แบบคำขอและหนังสือกู้เงินสามัญเฉพาะกิจเพื่อการศึกษา', 'file' => 'edu_loan_form'],
            ['document_type_id' => 2, 'display' => 'แบบคำขอและหนังสือกู้เงินสามัญเฉพาะกิจเพื่อการท่องเที่ยวเชิงศาสนา', 'file' => 'region_loan_form'],
            ['document_type_id' => 2, 'display' => 'แบบแจ้งผู้ประสบอุทกภัย', 'file' => 'floodhelp'],
            ['document_type_id' => 2, 'display' => 'แบบคำขอกู้สามัญเฉพาะกิจเพื่อคุณภาพชีวิต', 'file' => 'quality'],
            ['document_type_id' => 2, 'display' => 'หนังสือตั้งผู้รับโอนประโยชน์', 'file' => 'benef_form_new'],
            ['document_type_id' => 2, 'display' => 'หนังสือแสดงความยินยอมให้หักเงินเดือนคู่สมรส (กรณีติดตาม)', 'file' => 'agree2_form'],
            ['document_type_id' => 2, 'display' => 'หนังสือให้คำยินยอมหักเงินเดือนหรือค่าจ้างหรือบำเหน็จ', 'file' => 'agree_form'],
            ['document_type_id' => 2, 'display' => 'ใบรับรองข้อมูลสมาชิก', 'file' => 'member_info'],
            ['document_type_id' => 2, 'display' => 'ใบสมัครสมาชิกสหกรณ์ฯ', 'file' => 'AppForm58'],
            ['document_type_id' => 2, 'display' => 'ใบขอเพิ่ม-ลดหุ้น', 'file' => 'inc_oct'],
            ['document_type_id' => 2, 'display' => 'ใบลาออก', 'file' => 'resign'],
            ['document_type_id' => 2, 'display' => 'หนังสือแจ้งการเปลี่ยนแปลงผู้ค้ำประกันเงินกู้สามัญ', 'file' => 'change1_form'],
            ['document_type_id' => 2, 'display' => 'ตัวอย่างการกรอกคำขอกู้เงินสามัญและเอกสารประกอบต่างๆ', 'file' => 'sample_common_form_new'],
            ['document_type_id' => 2, 'display' => 'ตัวอย่างการกรอกคำขอกู้เงินและหนังสือกู้เงินเพื่อเหตุฉุกเฉิน', 'file' => 'ex_emer_reqst_form'],
            ['document_type_id' => 2, 'display' => 'ตัวอย่างการกรอกคำขอกู้เงินและหนังสือกู้เงินสามัญเฉพาะกิจเพื่อการท่องเที่ยวเชิงศาสนา', 'file' => 'religion'],
        ];

        Storage::deleteDirectory('documents');

        // Loop through each type above and create the record for them in the database
        foreach ($array as $document) {
            $old = $document['file'] . '.pdf';
            $new = time() . uniqid() . '.pdf';
            Storage::copy('initial/' . $old, ($document['document_type_id'] < 3) ? 'documents/' . $new : 'documents/' . $old);

            $obj = new App\Document([
                'document_type_id' => $document['document_type_id'], 
                'display' => $document['display'], 
                'file' => ($document['document_type_id'] < 3) ? $new : $old,
                'position' => App\Document::where('document_type_id', $document['document_type_id'])->count() + 1,
            ]);
            $obj->save();
        }
    }
}
