<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Response;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Bing;
use DB;
use Diamond;
use Image;
use File;
use MemberProperty;
use UploadDocument;
use App\Classes\Icon;
use Statistic;
use stdClass;
use Storage;
use App\VisitorStatistic;
use App\UserStatistic;
use App\AdministratorStatistic;
use App\Employee;
use App\Member;
use App\Profile;
use App\District;
use App\Subdistrict;
use App\Province;
use App\Postcode;
use App\Dividend;
use App\Shareholding;
use App\Document;
use App\DocumentType;
use App\Carousel;
use App\NewsAttachment;
use App\KnowledgeAttachment;
use Datatables;

class AjaxController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:admins', ['except' => 'getBackground']);
    }

    public function getMembers(Request $request) {
        $data = [];
        $type = $request->input('type');

        if ($type == 'active') {
            $members = Member::active()->get();

            foreach ($members as $member) {
                $row = [
                    $member->memberCode, 
                    '<span class="text-primary"><i class="fa fa-user fa-fw"></i> ' . (($member->profile->name == '<ข้อมูลถูกลบ>') ? '<ข้อมูลถูกลบ>' : $member->profile->fullName) . '</span>',
                    $member->profile->employee->employee_type->name,
                    number_format($member->shareholding, 0,'.', ',') . ' หุ้น',
                    number_format($member->shareholdings()->sum('amount'), 2,'.', ',') . ' บาท',
                    Diamond::parse($member->start_date)->thai_format('j M Y')
                ];

                $data[] = $row;
            }
        }
        else {
            $members = Member::inactive()->get();

            foreach ($members as $member) {
                $row = [
                    $member->memberCode, 
                    '<span class="text-primary">' . (($member->profile->name == '<ข้อมูลถูกลบ>') ? '<ข้อมูลถูกลบ>' : '<i class="fa fa-user fa-fw"></i> ' . $member->profile->fullName) . '</span>',
                    'ลาออก',
                    number_format($member->shareholding, 0,'.', ',') . ' หุ้น',
                    number_format($member->shareholdings()->sum('amount'), 2,'.', ',') . ' บาท',
                    Diamond::parse($member->start_date)->thai_format('j M Y'),
                    Diamond::parse($member->leave_date)->thai_format('j M Y')
                ];

                $data[] = $row;
            }
        }

        return compact('data');
    }

    public function getDistricts(Request $request) {
        $id = $request->input('id');

        return District::where('province_id', $id)->get();
    }

    public function getSubdistricts(Request $request) {
        $id = $request->input('id');

        return Subdistrict::where('district_id', $id)->get();
    }

    public function getPostcode(Request $request) {
        $id = $request->input('id');

        $subdistrict = Subdistrict::find($id);

        return $subdistrict->postcode->code;
    }

    public function getStatus(Request $request) {
        $employee = Employee::where('code', $request->input('code'))->first();
        $message = (!is_null($employee)) ? (Member::where('profile_id', $employee->profile_id)->whereNull('leave_date')->count() == 1) ? 'ยังคงเป็นสมาชิกอยู่' : '200' : '100';
        $member = null;
        
        if ($message == '200') {
            $memberx = Member::where('profile_id', $employee->profile_id)->first();
            $profile = Profile::find($employee->profile_id);

            $member = [
                'profile'=>$profile, 
                'employee'=>$employee, 
                'districts'=>District::where('province_id', $memberx->profile->province_id)->get(), 
                'subdistricts'=>Subdistrict::where('district_id', $memberx->profile->district_id)->get(), 
                'postcode'=>Postcode::find($memberx->profile->postcode_id)->code
            ];
        }

        return compact('message', 'member');
    }

    public function getDividend(Request $request) {
        $member = Member::find($request->input('id'));
        $year = $request->input('year');
        $dividends = MemberProperty::getDividend($member->id);
        
        return compact('dividends', 'dividend_rate');
    }

    /**
     * Get Bing photo of the day.
     *
     * @param  Request
     * @return Response
     */
    public function getBackground(Request $request) {

        return response()->json(Bing::setArgs(['date'=>$request->input('date')])->getImage());
    }

    public function getChart(Request $request) {
        $date = Diamond::parse($request->input('date'));
        $web = $request->input('web');

        switch ($web) {
            default:
                $_visitors = VisitorStatistic::whereYear('created_at', '=', $date->year)
                    ->whereMonth('created_at', '=', $date->month)
                    ->groupBy(DB::raw('year(created_at)'), DB::raw('month(created_at)'), DB::raw('day(created_at)'))
                    ->select(DB::raw('day(created_at) as visit_date'), DB::raw('count(id) as amount'))
                    ->get();
                $_platforms = VisitorStatistic::join('platforms', 'visitor_statistics.platform_id', '=', 'platforms.id')
                    ->whereYear('visitor_statistics.created_at', '=', $date->year)
                    ->whereMonth('visitor_statistics.created_at', '=', $date->month)
                    ->groupBy('platforms.id')
                    ->select('platforms.name as tick', DB::raw('count(visitor_statistics.id) as amount'))
                    ->get();
                $_browsers = VisitorStatistic::join('browsers', 'visitor_statistics.browser_id', '=', 'browsers.id')
                    ->whereYear('visitor_statistics.created_at', '=', $date->year)
                    ->whereMonth('visitor_statistics.created_at', '=', $date->month)
                    ->groupBy('browsers.id')
                    ->select('browsers.name as tick', DB::raw('count(visitor_statistics.id) as amount'))
                    ->get();
                break;
            case 'webuser':
                $_visitors = UserStatistic::whereYear('created_at', '=', $date->year)
                    ->whereMonth('created_at', '=', $date->month)
                    ->groupBy(DB::raw('year(created_at)'), DB::raw('month(created_at)'), DB::raw('day(created_at)'))
                    ->select(DB::raw('day(created_at) as visit_date'), DB::raw('count(id) as amount'))
                    ->get();
                $_platforms = UserStatistic::join('platforms', 'user_statistics.platform_id', '=', 'platforms.id')
                    ->whereYear('user_statistics.created_at', '=', $date->year)
                    ->whereMonth('user_statistics.created_at', '=', $date->month)
                    ->groupBy('platforms.id')
                    ->select('platforms.name as tick', DB::raw('count(user_statistics.id) as amount'))
                    ->get();
                $_browsers = UserStatistic::join('browsers', 'user_statistics.browser_id', '=', 'browsers.id')
                    ->whereYear('user_statistics.created_at', '=', $date->year)
                    ->whereMonth('user_statistics.created_at', '=', $date->month)
                    ->groupBy('browsers.id')
                    ->select('browsers.name as tick', DB::raw('count(user_statistics.id) as amount'))
                    ->get();            
                break;
            case 'webapp':
                $_visitors = AdministratorStatistic::whereYear('created_at', '=', $date->year)
                    ->whereMonth('created_at', '=', $date->month)
                    ->groupBy(DB::raw('year(created_at)'), DB::raw('month(created_at)'), DB::raw('day(created_at)'))
                    ->select(DB::raw('day(created_at) as visit_date'), DB::raw('count(id) as amount'))
                    ->get();
                $_platforms = AdministratorStatistic::join('platforms', 'administrator_statistics.platform_id', '=', 'platforms.id')
                    ->whereYear('administrator_statistics.created_at', '=', $date->year)
                    ->whereMonth('administrator_statistics.created_at', '=', $date->month)
                    ->groupBy('platforms.id')
                    ->select('platforms.name as tick', DB::raw('count(administrator_statistics.id) as amount'))
                    ->get();
                $_browsers = AdministratorStatistic::join('browsers', 'administrator_statistics.browser_id', '=', 'browsers.id')
                    ->whereYear('administrator_statistics.created_at', '=', $date->year)
                    ->whereMonth('administrator_statistics.created_at', '=', $date->month)
                    ->groupBy('browsers.id')
                    ->select('browsers.name as tick', DB::raw('count(administrator_statistics.id) as amount'))
                    ->get();
                break;
        }

        $visitors = Statistic::visitor_chart($_visitors, $date);
        $platforms = Statistic::bar_chart($_platforms);
        $browsers = Statistic::bar_chart($_browsers);

        return compact('visitors', 'platforms', 'browsers');
    }

    public function getDetail(Request $request) {
        $date = Diamond::parse($request->input('date'));
        $web = $request->input('web');

        $data = [];

        switch ($web) {
            case 'website':
                $visitors = VisitorStatistic::whereYear('created_at', '=', $date->year)
                    ->whereMonth('created_at', '=', $date->month)
                    ->orderBy('created_at', 'desc')
                    ->get();

                $index = 0;
                foreach ($visitors as $visitor) {
                    $row = [
                        ++$index, 
                        Icon::user($visitor->session), 
                        '<span class="display-number">' . Diamond::parse($visitor->created_at)->thai_format('j M Y H:i น.') . '</span>', 
                        $visitor->ip_address, 
                        Icon::platform($visitor->platform->name), 
                        Icon::browser($visitor->browser->name)
                    ];

                    $data[] = $row;
                }
                break;
            case 'webuser':
                $users = UserStatistic::whereYear('created_at', '=', $date->year)
                    ->whereMonth('created_at', '=', $date->month)
                    ->orderBy('created_at', 'desc')
                    ->get();

                $index = 0;
                foreach ($users as $user) {
                    $row = [
                        ++$index, 
                        Icon::user($user->user->member->profile->fullName . ' (' . $user->user->email . ')'), 
                        '<span class="display-number">' . Diamond::parse($user->created_at)->thai_format('j M Y H:i น.') . '</span>', 
                        $user->ip_address, 
                        Icon::platform($user->platform->name), 
                        Icon::browser($user->browser->name)
                    ];

                    $data[] = $row;
                }
                break;
            case 'webapp':
                $officers = AdministratorStatistic::whereYear('created_at', '=', $date->year)
                    ->whereMonth('created_at', '=', $date->month)
                    ->orderBy('created_at', 'desc')
                    ->get();

                $index = 0;
                foreach ($officers as $officer) {
                    $row = [
                        ++$index, 
                        Icon::user($officer->administrator->name . ' (' . $officer->administrator->email . ')'), 
                        '<span class="display-number">' . Diamond::parse($officer->created_at)->thai_format('j M Y H:i น.') . '</span>', 
                        $officer->ip_address, 
                        Icon::platform($officer->platform->name), 
                        Icon::browser($officer->browser->name)
                    ];

                    $data[] = $row;
                }
                break;
        }

        return compact('data');
    }

    public function postPassword(Request $request) {
        //$alphabet = 'abcdefghijklmnpoqrstuvwxyzABCDEFGHIJKLMNPOQRSTUVWXYZ0123456789';

        $alphabet = '0123456789';
        $pass = []; //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache

        for ($i = 0; $i < 6; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }

        return response()->json(implode($pass)); //turn the array into a string
    }

    public function getDocuments() {
        $rules = UploadDocument::documentLists(1);
        $forms = UploadDocument::documentLists(2);

        return compact(['rules', 'forms']);
    }

    public function getDocumentsbytype(Request $request) {
        $documents = UploadDocument::documentLists($request->input('id'));

        return compact(['documents']);;
    }

    public function getReorder(Request $request) {
        $id = $request->input('id');
        $index = $request->input('index');

        $affect = UploadDocument::reorderDocument($id, $index);

        return Response::json('Success: ' . $affect);
    }

    public function postUploadFile(Request $request) {
        $file = $request->file('File');
        $documentType = intval($request->input('DocType'));

        $display = mb_ereg_replace('\s+', ' ', basename($file->getClientOriginalName(), '.' . $file->getClientOriginalExtension()));
        $filename = time() . uniqid() . '.' . $file->getClientOriginalExtension();
        $order = UploadDocument::lastOrder(0, $documentType);
        Storage::disk('documents')->put($filename, file_get_contents($file->getRealPath()));

        $id = UploadDocument::insertDocument($display, $filename, $documentType, $order);

        $data = new stdClass();
        $data->id = $id;
        $data->Display = $display;

        return Response::json($data);
    }

    public function postUpdateFile(Request $request) {
        $id = $request->input('ID');
        $file = $request->file('File');

        $display = mb_ereg_replace('\s+', ' ', basename($file->getClientOriginalName(), '.' . $file->getClientOriginalExtension()));
        $filename = time() . uniqid() . '.' . $file->getClientOriginalExtension();
        Storage::disk('documents')->put($filename, file_get_contents($file->getRealPath()));

        $oldFile = UploadDocument::updateDocument($id, $display, $filename);
        Storage::disk('documents')->delete($oldFile);

        $data = new stdClass();
        $data->id = $id;
        $data->Display = $display;

        return Response::json($data);
    }

    public function getRestorefile(Request $request) {
        $id = $request->input('id');
        $display = Document::find($id)->display;

        return compact('display');
    }

    public function postUpdateOther(Request $request) {
        $id = $request->input('id');
        $file = $request->file('file');

        $filename = mb_ereg_replace('\s+', ' ', $file->getClientOriginalName());
        Storage::disk('documents')->put($filename, file_get_contents($file->getRealPath()));  

        $oldFile = UploadDocument::updateOther($id, $filename);
        Storage::disk('documents')->delete($oldFile);

        return Response::json($filename);      
    }

    public function postDeleteFile(Request $request) {
        $id = $request->input('id');

        UploadDocument::reindexDocument($id);

        $document = Document::find($id);
        Storage::disk('documents')->delete($document->file);
        $document->delete();

        return Response::json($id);
    }

    public function postUploadCarousel(Request $request) {
        $image = $request->file('image');
        $document_id = $request->input('document_id');
        $imagename = time() . uniqid() . '.' . $image->getClientOriginalExtension();

        Storage::disk('carousels')->put($imagename, file_get_contents($image->getRealPath()));
        Storage::disk('carousels')->put('thumbnail_' . $imagename, Image::make($image->getRealPath())->resize(256, 144)->stream()->__toString());

        $order = UploadDocument::lastOrder(1);
        $id = UploadDocument::insertCarousel($document_id, $imagename, $order);
        $document = Document::find($document_id);

        $data = new stdClass();
        $data->id = $id;
        $data->thumbnail = "data:image/jpeg;base64," . base64_encode(Storage::disk('carousels')->get('thumbnail_' . $imagename));
        $data->document_id = $document->id;
        $data->document_type_id = $document->document_type_id;
        $data->document_types = DocumentType::where('id', '<>', 3)->get();
        $data->documents = DocumentType::find($document->document_type_id)->documents->sortBy('position');

        return Response::json($data);
    }

    public function postUpdateCarouselImage(Request $request) {
        $id = $request->input('id');
        $image = $request->file('image');
        $imagename = time() . uniqid() . '.' . $image->getClientOriginalExtension();

        Storage::disk('carousels')->put($imagename, file_get_contents($image->getRealPath()));
        Storage::disk('carousels')->put('thumbnail_' . $imagename, Image::make($image->getRealPath())->resize(256, 144)->stream()->__toString());

        $oldImage = UploadDocument::updateCarouselImage($id, $imagename);
        Storage::disk('carousels')->delete($oldImage);
        Storage::disk('carousels')->delete('thumbnail_' . $oldImage);

        $data = new stdClass();
        $data->id = $id;
        $data->thumbnail = Response::make(File::get(storage_path('thumbnail_' . $imagename) . '/' . $image))->header('Content-Type', 'image/jpg');

        return Response::json($data);
    }

    public function postUpdateCarouselDocument(Request $request) {
        $id = $request->input('id');
        $document_id = $request->input('document_id');

        UploadDocument::updateCarouselDocument($id, $document_id);

        return compact('id');
    }

    public function postDeleteCarousel(Request $request) {
        $id = $request->input('id');

        UploadDocument::reindexCarousel($id);

        $carousel = Carousel::find($id);
        Storage::disk('carousels')->delete($carousel->image);
        Storage::disk('carousels')->delete('thumbnail_' . $carousel->image);
        $carousel->delete();

        return Response::json($id);
    }

    public function getReordercarousel(Request $request) {
        $id = $request->input('id');
        $index = $request->input('index');

        $affect = UploadDocument::reorderCarousel($id, $index);

        return Response::json('Success: ' . $affect);
    }

    public function getDocumentlists() {
        $document_types = DocumentType::where('id', '<>', 3)->get();
        $documents = Document::where('document_type_id', 1)->get();

        return compact('document_types', 'documents');
    }

    public function postUploadPhoto(Request $request) {
        $photo = $request->file('photo');
        $type = $request->input('type');
        $id = $request->input('id');
        $photoname = time() . uniqid() . '.' . $photo->getClientOriginalExtension();
        $display = mb_ereg_replace('\s+', ' ', basename($photo->getClientOriginalName(), '.' . $photo->getClientOriginalExtension()));

        $image = Image::make($photo->getRealPath());
        $max_width = 800;
        $max_height = 800;
        $width = $image->width();
        $height = $image->height();

        if ($width > $height) {
            if ($width > $max_width) {
                $height *= $max_width / $width;
                $width = $max_width;
            }
        } else {
            if ($height > $max_height) {
                $width *= $max_height / $height;
                $height = $max_height;
            }
        }

        $new_id = UploadDocument::attachFile($type, $id, 'photo', $photoname, $display);
        Storage::disk('attachments')->put($photoname, $image->resize($width, $height)->stream()->__toString());

        $data = new stdClass();
        $data->id = $new_id;
        $data->file = "data:image/jpeg;base64," . base64_encode(Storage::disk('attachments')->get($photoname));

        return Response::json($data);
    }

    public function postDeletePhoto(Request $request) {
        $id = $request->input('id');
        $type = $request->input('type');

        $photo = ($type == 'news') ? NewsAttachment::find($id) : KnowledgeAttachment::find($id);
        Storage::disk('attachments')->delete($photo->file);
        $photo->delete();

        return Response::json($id);
    }

    public function postUploadDocument(Request $request) {
        $document = $request->file('document');
        $type = $request->input('type');
        $id = $request->input('id');
        $documentname = time() . uniqid() . '.' . $document->getClientOriginalExtension();
        $display = mb_ereg_replace('\s+', ' ', basename($document->getClientOriginalName(), '.' . $document->getClientOriginalExtension()));

        $new_id = UploadDocument::attachFile($type, $id, 'document', $documentname, $display);
        Storage::disk('attachments')->put($documentname, file_get_contents($document->getRealPath()));

        $data = new stdClass();
        $data->id = $new_id;
        $data->display = $display;

        return Response::json($data);
    }

    public function postDeleteDocument(Request $request) {
        $id = $request->input('id');
        $type = $request->input('type');

        $document = ($type == 'news') ? NewsAttachment::find($id) : KnowledgeAttachment::find($id);
        Storage::disk('attachments')->delete($document->file);
        $document->delete();

        return Response::json($id);
    }
}
