<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Document;
use App\DocumentType;
use Storage;
use File;
use stdClass;

class DocumentController extends Controller
{
    /**
     * Responds to requests to GET /rules
     */
    public function getRules($key = null) {
        $header = 'ระเบียบ/คำสั่ง/ข้อบังคับ';

        if (!is_null($key)) {
            $documents = Document::where('display', $key)->first();

            if (!is_null($documents)) {
                return view('website.documents.show', [
                    'header' => $header,
                    'link' => '/documents/rules',
                    'files' => $documents,
                ]);
            }
            else {
                return view('website.documents.show', [
                    'header' => $header,
                    'link' => '',
                    'files' => null,
                ]);
            }
        }
        else {
            return view('website.documents.show', [
                'header' => $header,
                'link' => '',
                'files' => Document::where('document_type_id', 1)
                    ->orderBy('position', 'asc')
                    ->get(),
            ]);
        }
    }

    /**
     * Responds to requests to GET /forms
     */
    public function getForms($key = null) {
        $header = 'ใบสมัคร/แบบฟอร์มต่างๆ';

        if (!is_null($key)) {
            $documents = Document::where('display', $key)->first();

            if (!is_null($documents)) {
                return view('website.documents.show', [
                    'header' => $header,
                    'link' => '/documents/forms',
                    'files' => $documents,
                ]);
            }
            else {
                return view('website.documents.show', [
                    'header' => $header,
                    'link' => '',
                    'files' => null,
                ]);
            }
        }
        else {
            return view('website.documents.show', [
                'header' => $header,
                'link' => '',
                'files' => Document::where('document_type_id', 2)
                    ->orderBy('position', 'asc')
                    ->get(),
            ]);
        }
    }

    /**
     * Responds to requests to GET /documents/1
     */
    public function getOthers($key) {
        switch ($key) {
            default:
                $header = 'ไม่พบเอกสาร';
                break;
            case 'status':
                $header = 'สถานะทางการเงิน';
                break;            
            case 'deposit':
                $header = 'สรุปยอดเงินฝาก/การซื้อสลาก';
                break;
            case 'loan_rate':
                $header = 'ตารางอัตราการหักคืนเงินกู้พร้อมดอกเบี้ยรายงวด';
                break;
            case 'rate':
                $header = 'ประกาศอัตราดอกเบี้ยใหม่';
                break;
            case 'boards':
                $header = 'คณะกรรมการดำเนินการสหกรณ์ฯ';
                break;
            case 'officers':
                $header = 'เจ้าหน้าที่ประจำสหกรณ์ฯ';
                break;
        }

        $document = DocumentType::find(3)->documents->where('display', $header)->first();

        return view('website.documents.show', [
            'header' => $header,
            'link' => '',
            'files' => $document,
        ]);
    }
}
