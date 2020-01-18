<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Response;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use Bing;
use DB;
use Diamond;
use Image;
use File;
use LoanCalculator;
use MemberProperty;
use UploadDocument;
use App\Classes\Icon;
use History;
use Statistic;
use stdClass;
use Storage;
use Routine;
use App\Beneficiary;
use App\VisitorStatistic;
use App\UserStatistic;
use App\AdministratorStatistic;
use App\Bailsman;
use App\Employee;
use App\Member;
use App\Profile;
use App\District;
use App\Subdistrict;
use App\Province;
use App\Postcode;
use App\Dividend;
use App\Dividendmember;
use App\Shareholding;
use App\Document;
use App\DocumentType;
use App\Carousel;
use App\NewsAttachment;
use App\KnowledgeAttachment;
use App\Loan;
use App\RoutineSetting;
use App\RoutinePayment;
use App\RoutinePaymentDetail;
use App\RoutineShareholding;
use App\RoutineShareholdingDetail;
use App\LoanType;
use App\User;
use Datatables;

class AjaxController extends Controller
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
        $this->middleware('auth:admins', ['except' => 'getBackground']);
    }

    public function postMembers(Request $request) {
        $type = $request->input('type');

        if ($type == 'active') {
            $members = DB::table('members')
                ->join('profiles', 'members.profile_id', '=', 'profiles.id')
                ->join('employees', 'profiles.id', '=', 'employees.profile_id')
                ->join('employee_types', 'employees.employee_type_id', '=', 'employee_types.id')
                ->leftJoin('users', 'members.id', '=', 'users.member_id')
                ->whereNull('members.leave_date')
                ->select([
                    DB::raw("LPAD(members.id, 5, '0') as code"),
                    DB::raw("CONCAT('<span class=\"text-primary\"><i class=\"fa fa-user fa-fw\"></i> ', IF(profiles.name = '<ข้อมูลถูกลบ>', profiles.name, CONCAT(profiles.name, ' ', profiles.lastname)), '</span>') as fullname"),
                    DB::raw("CONCAT('<span class=\"label label-primary\">', employee_types.name, '</span>') as typename"),
                    'members.start_date as startdate',
                    DB::raw("IF(users.id IS NOT NULL, '<span class=\"label label-primary\">ลงทะเบียนสมาชิกแล้ว</span>', '<span class=\"label label-danger\">ยังไม่ได้ลงทะเบียนใช้งาน</span>') as status")
                ]);

            return Datatables::queryBuilder($members)
                ->editColumn('startdate', function($member) {
                        return Diamond::parse($member->startdate)->thai_format('Y-m-d');
                    })
                ->editColumn('leavedate', function($member) {
                        return '-';
                    }) 
                ->make(true);
        }
        else {
            $members = DB::table('members')
                ->join('profiles', 'members.profile_id', '=', 'profiles.id')
                ->whereNotNull('members.leave_date')
                ->select([
                    DB::raw("LPAD(members.id, 5, '0') as code"),
                    DB::raw("CONCAT('<span class=\"text-primary\">', IF(profiles.name = '<ข้อมูลถูกลบ>', profiles.name, CONCAT('<i class=\"fa fa-user fa-fw\"></i> ', profiles.name, ' ', profiles.lastname)), '</span>') as fullname"),
                    DB::raw("'<span class=\"label label-danger\">ลาออก</span>' as typename"),
                    'members.start_date as startdate',
                    'members.leave_date as leavedate'
                ]);

            return Datatables::queryBuilder($members)
                ->editColumn('startdate', function($member) {
                        return Diamond::parse($member->startdate)->thai_format('Y-m-d');
                    })
                ->editColumn('leavedate', function($member) {
                        return Diamond::parse($member->leavedate)->thai_format('Y-m-d');
                    })
                ->make(true);
        }
    }

    public function postShareholding() {
        $members = DB::table('members')
            ->join('profiles', 'members.profile_id', '=', 'profiles.id')
            ->join('employees', 'profiles.id', '=', 'employees.profile_id')
            ->join('employee_types', 'employees.employee_type_id', '=', 'employee_types.id')
            ->leftJoin('shareholdings', 'members.id', '=', 'shareholdings.member_id')
            ->whereNull('members.leave_date')
            ->groupBy(['members.id', 'profiles.name', 'profiles.lastname', 'employee_types.name', 'members.shareholding', 'members.start_date'])
            ->select([
                DB::raw("LPAD(members.id, 5, '0') as code"),
                DB::raw("CONCAT('<span class=\"text-primary\"><i class=\"fa fa-user fa-fw\"></i> ', IF(profiles.name = '<ข้อมูลถูกลบ>', profiles.name, CONCAT(profiles.name, ' ', profiles.lastname)), '</span>') as fullname"),
                DB::raw("CONCAT('<span class=\"label label-primary\">', employee_types.name, '</span>') as typename"),
                DB::raw("IF(members.shareholding > 0, CONCAT(FORMAT(members.shareholding, 0), ' หุ้น'), '-') as shareholding"),
                DB::raw("CONCAT(FORMAT(COALESCE(SUM(shareholdings.amount), 0), 2), ' บาท') as amount")
            ]);

        return Datatables::queryBuilder($members)->make(true);
    }

    public function postDistricts(Request $request) {
        $id = $request->input('id');

        return District::where('province_id', $id)->orderBy('name')->get();
    }

    public function postSubdistricts(Request $request) {
        $id = $request->input('id');

        return Subdistrict::where('district_id', $id)->orderBy('name')->get();
    }

    public function postPostcode(Request $request) {
        $id = $request->input('id');

        $subdistrict = Subdistrict::find($id);

        return $subdistrict->postcode->code;
    }

    public function postStatus(Request $request) {
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

    /**
     * Get Bing photo of the day.
     *
     * @param  Request
     * @return Response
     */
    public function getBackground(Request $request) {
        $date = $request->input('date');

        return Bing::photo($date);
    }

    public function postChart(Request $request) {
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

    public function postDetail(Request $request) {
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
                        Icon::user($user->user->member->profile->fullname . ' (' . $user->user->email . ')'), 
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
                        Icon::user($officer->administrator->fullname . ' (' . $officer->administrator->email . ')'), 
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

    public function postDocuments() {
        $rules = UploadDocument::documentLists(1);
        $forms = UploadDocument::documentLists(2);

        return compact(['rules', 'forms']);
    }

    public function postDocumentsbytype(Request $request) {
        $documents = UploadDocument::documentLists($request->input('id'));

        return compact(['documents']);;
    }

    public function postReorder(Request $request) {
        $id = $request->input('id');
        $index = $request->input('index');

        $affect = UploadDocument::reorderDocument($id, $index);

        if ($affect > 0) {
            History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'จัดเรียงลำดับเอกสารบนหน้าเว็บไซต์');
        }

        return Response::json('Success: ' . $affect);
    }

    public function postUploadfile(Request $request) {
        $file = $request->file('File');
        $documentType = intval($request->input('DocType'));

        $display = mb_ereg_replace('\s+', ' ', basename($file->getClientOriginalName(), '.' . $file->getClientOriginalExtension()));
        $filename = time() . uniqid() . '.' . $file->getClientOriginalExtension();
        $order = UploadDocument::lastOrder(0, $documentType);
        $path = ($file->getRealPath() != false) ? $file->getRealPath() : $file->getPathname();
        Storage::disk('documents')->put($filename, file_get_contents($path));

        $id = UploadDocument::insertDocument($display, $filename, $documentType, $order);
        History::addAdminHistory(Auth::guard($this->guard)->id(), 'เพิ่มข้อมูล', 'เพิ่มเอกสารบนหน้าเว็บไซต์');

        $data = new stdClass();
        $data->id = $id;
        $data->Display = $display;

        return Response::json($data);
    }

    public function postUpdatefile(Request $request) {
        $id = $request->input('ID');
        $file = $request->file('File');

        $display = mb_ereg_replace('\s+', ' ', basename($file->getClientOriginalName(), '.' . $file->getClientOriginalExtension()));
        $filename = time() . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = ($file->getRealPath() != false) ? $file->getRealPath() : $file->getPathname();
        Storage::disk('documents')->put($filename, file_get_contents($path));

        $oldFile = UploadDocument::updateDocument($id, $display, $filename);
        Storage::disk('documents')->delete($oldFile);
        History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'แก้ไขเอกสารบนหน้าเว็บไซต์');

        $data = new stdClass();
        $data->id = $id;
        $data->Display = $display;

        return Response::json($data);
    }

    public function postRestorefile(Request $request) {
        $id = $request->input('id');
        $display = Document::find($id)->display;

        return compact('display');
    }

    public function postUpdateother(Request $request) {
        $id = $request->input('id');
        $file = $request->file('file');

        $filename = mb_ereg_replace('\s+', ' ', $file->getClientOriginalName());
        $path = ($file->getRealPath() != false) ? $file->getRealPath() : $file->getPathname();
        Storage::disk('documents')->put($filename, file_get_contents($path));  

        $oldFile = UploadDocument::updateOther($id, $filename);
        Storage::disk('documents')->delete($oldFile);

        History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'แก้ไขเอกสารบนหน้าเว็บไซต์');

        return Response::json($filename);      
    }

    public function postDeletefile(Request $request) {
        $id = $request->input('id');

        UploadDocument::reindexDocument($id);

        $document = Document::find($id);
        Storage::disk('documents')->delete($document->file);
        $document->delete();

        History::addAdminHistory(Auth::guard($this->guard)->id(), 'ลบข้อมูล', 'ลบเอกสารบนหน้าเว็บไซต์');

        return Response::json($id);
    }

    public function postUploadcarousel(Request $request) {
        $image = $request->file('image');
        $document_id = $request->input('document_id');
        $imagename = time() . uniqid() . '.' . $image->getClientOriginalExtension();

        $path = ($image->getRealPath() != false) ? $image->getRealPath() : $image->getPathname();
        Storage::disk('carousels')->put($imagename, file_get_contents($path));
        Storage::disk('carousels')->put('thumbnail_' . $imagename, Image::make($path)->resize(256, 144)->stream()->__toString());

        $order = UploadDocument::lastOrder(1);
        $id = UploadDocument::insertCarousel($document_id, $imagename, $order);
        $document = Document::find($document_id);

        History::addAdminHistory(Auth::guard($this->guard)->id(), 'เพิ่มข้อมูล', 'เพิ่มข่าวประชาสัมพันธ์บนหน้าเว็บไซต์');

        $data = new stdClass();
        $data->id = $id;
        $data->thumbnail = "data:image/jpeg;base64," . base64_encode(Storage::disk('carousels')->get('thumbnail_' . $imagename));
        $data->document_id = $document->id;
        $data->document_type_id = $document->document_type_id;
        $data->document_types = DocumentType::where('id', '<>', 3)->get();
        $data->documents = DocumentType::find($document->document_type_id)->documents->sortBy('position');

        return Response::json($data);
    }

    public function postUpdatecarouselimage(Request $request) {
        $id = $request->input('id');
        $image = $request->file('image');
        $imagename = time() . uniqid() . '.' . $image->getClientOriginalExtension();

        $path = ($image->getRealPath() != false) ? $image->getRealPath() : $image->getPathname();
        Storage::disk('carousels')->put($imagename, file_get_contents($path));
        Storage::disk('carousels')->put('thumbnail_' . $imagename, Image::make($path)->resize(256, 144)->stream()->__toString());

        $oldImage = UploadDocument::updateCarouselImage($id, $imagename);
        Storage::disk('carousels')->delete($oldImage);
        Storage::disk('carousels')->delete('thumbnail_' . $oldImage);

        History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'แก้ไขรูปประกอบข่าวประชาสัมพันธ์บนหน้าเว็บไซต์');

        $data = new stdClass();
        $data->id = $id;
        $data->thumbnail = Response::make(File::get(storage_path('thumbnail_' . $imagename) . '/' . $image))->header('Content-Type', 'image/jpg');

        return Response::json($data);
    }

    public function postUpdatecarouseldocument(Request $request) {
        $id = $request->input('id');
        $document_id = $request->input('document_id');

        UploadDocument::updateCarouselDocument($id, $document_id);
        History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'แก้ไขเอกสารประกอบข่าวประชาสัมพันธ์บนหน้าเว็บไซต์');

        return compact('id');
    }

    public function postDeletecarousel(Request $request) {
        $id = $request->input('id');

        UploadDocument::reindexCarousel($id);

        History::addAdminHistory(Auth::guard($this->guard)->id(), 'ลบข้อมูล', 'ลบข่าวประชาสัมพันธ์บนหน้าเว็บไซต์');

        $carousel = Carousel::find($id);
        Storage::disk('carousels')->delete($carousel->image);
        Storage::disk('carousels')->delete('thumbnail_' . $carousel->image);
        $carousel->delete();

        return Response::json($id);
    }

    public function postReordercarousel(Request $request) {
        $id = $request->input('id');
        $index = $request->input('index');

        $affect = UploadDocument::reorderCarousel($id, $index);
        History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'จัดเรียงลำดับข่าวประชาสัมพันธ์บนหน้าเว็บไซต์');

        return Response::json('Success: ' . $affect);
    }

    public function postDocumentlists() {
        $document_types = DocumentType::where('id', '<>', 3)->get();
        $documents = Document::where('document_type_id', 1)->get();

        return compact('document_types', 'documents');
    }

    public function postUploadphoto(Request $request) {
        $photo = $request->file('photo');
        $type = $request->input('type');
        $id = $request->input('id');
        $photoname = time() . uniqid() . '.' . $photo->getClientOriginalExtension();
        $display = mb_ereg_replace('\s+', ' ', basename($photo->getClientOriginalName(), '.' . $photo->getClientOriginalExtension()));

        $path = ($photo->getRealPath() != false) ? $photo->getRealPath() : $photo->getPathname();
        $image = Image::make($path);
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
        History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'เพิ่มรูปประกอบข่าวสารสำหรับสมาชิก/สาระน่ารู้เกี่ยวกับสหกรณ์');

        $data = new stdClass();
        $data->id = $new_id;
        $data->file = "data:image/jpeg;base64," . base64_encode(Storage::disk('attachments')->get($photoname));

        return Response::json($data);
    }

    public function postDeletephoto(Request $request) {
        $id = $request->input('id');
        $type = $request->input('type');

        $photo = ($type == 'news') ? NewsAttachment::find($id) : KnowledgeAttachment::find($id);
        Storage::disk('attachments')->delete($photo->file);
        $photo->delete();

        History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'ลบรูปประกอบข่าวสารสำหรับสมาชิก/สาระน่ารู้เกี่ยวกับสหกรณ์');

        return Response::json($id);
    }

    public function postUploaddocument(Request $request) {
        $document = $request->file('document');
        $type = $request->input('type');
        $id = $request->input('id');
        $documentname = time() . uniqid() . '.' . $document->getClientOriginalExtension();
        $display = mb_ereg_replace('\s+', ' ', basename($document->getClientOriginalName(), '.' . $document->getClientOriginalExtension()));

        $new_id = UploadDocument::attachFile($type, $id, 'document', $documentname, $display);
        $path = ($document->getRealPath() != false) ? $document->getRealPath() : $document->getPathname();
        Storage::disk('attachments')->put($documentname, file_get_contents($path));
        History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'เพิ่มเอกสารแนบข่าวสารสำหรับสมาชิก/สาระน่ารู้เกี่ยวกับสหกรณ์');

        $data = new stdClass();
        $data->id = $new_id;
        $data->display = $display;

        return Response::json($data);
    }

    public function postDeletedocument(Request $request) {
        $id = $request->input('id');
        $type = $request->input('type');

        $document = ($type == 'news') ? NewsAttachment::find($id) : KnowledgeAttachment::find($id);
        Storage::disk('attachments')->delete($document->file);
        $document->delete();

        History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'ลบเอกสารแนบข่าวสารสำหรับสมาชิก/สาระน่ารู้เกี่ยวกับสหกรณ์');

        return Response::json($id);
    }

    public function postLoadmore(Request $request) {
        $index = intval($request->input('index'));
        $count = History::countAdminHistory(Auth::guard($this->guard)->id());
        $histories = History::administrator(Auth::guard($this->guard)->id(), $index);

        return compact('index', 'count', 'histories');
    }

    public function getAccounts(Request $request) {
        $users = DB::table('users')
            ->join('members', 'users.member_id', '=', 'members.id')
            ->join('profiles', 'members.profile_id', '=', 'profiles.id')
            ->whereNull('members.leave_date')
            ->select([
                DB::raw("LPAD(members.id, 5, '0') as code"),
                DB::raw("CONCAT('<span class=\"text-primary\"><i class=\"fa fa-user fa-fw\"></i> ', IF(profiles.name = '<ข้อมูลถูกลบ>', profiles.name, CONCAT(profiles.name, ' ', profiles.lastname)), '</span>') as fullname"),
                'users.email as email',
                'users.created_at as register_at',
                DB::raw("IF(members.leave_date IS NULL, IF(users.confirmed = 1, '<span class=\"label label-success\">ปกติ</span>', '<span class=\"label label-warning\">ยังไม่ได้ยืนยันตัวตน</span>'), '<span class=\"label label-danger\">ลาออกจากสมาชิก</span>') as status")
            ]);

        return Datatables::queryBuilder($users)
            ->editColumn('register_at', function($user) {
                    return Diamond::parse($user->register_at)->thai_format('Y-m-d');
                })
            ->make(true);
    }

    public function postClearloan(Request $request) {
        // clear temp
        Loan::where('member_id', $request->input('id'))
            ->whereNull('code')->delete();
    }

    public function postChecksurety(Request $request) {
        $loan = Loan::find($request->input('loan_id'));
        $member = Member::find($request->input('member_id'));
        $surety = Member::find($request->input('surety_id'));
        $result = null;

        // ตรวจสอบสมาชิก
        if ($surety != null) {
            // ตรวจสอบว่าค้ำตัวเองหรือไม่
            if ($surety->id == $member->id) {
                // ตรวจสอบว่าได้ใช้ค้ำในสัญญานี้แล้วหรือไม่
                if ($loan->sureties->where('member_id', $surety->id)->count() == 0) {
                    $result = new stdClass();
                    $result->loan_id = $loan->id;
                    $result->id = $surety->id;
                    $result->memberCode = $surety->memberCode;
                    $result->fullname = $surety->profile->fullname;
                    $result->yourself = true;
                    $result->employee = $surety->profile->employee->employee_type->id == 1;
                }
                else {
                    $result = new stdClass();
                    $result->id = 0;
                    $result->message = "ผู้ค้ำประกันไม่สามารถเพิ่มซ้ำได้";  
                }
            }
            else {
                // ต้องเป็นพนักงานเท่านั้น
                if ($surety->profile->employee->employee_type_id == 1) {
                    // ตรวจว่าค้ำประกันไม่เกิน 2 สัญญา ไม่นับค้ำด้วยหุ้นตนเอง
                    if ($surety->sureties->filter(function ($value, $key) use ($surety) { return !is_null($value->code) && is_null($value->completed_at) && $value->member_id != $surety->id; })->count() < 2) {
                        // ตรวจสอบว่าได้ใช้ค้ำในสัญญานี้แล้วหรือไม่
                        if ($loan->sureties->where('member_id', $surety->id)->count() == 0) {
                            $result = new stdClass();
                            $result->loan_id = $loan->id;
                            $result->id = $surety->id;
                            $result->memberCode = $surety->memberCode;
                            $result->fullname = $surety->profile->fullname;
                            $result->yourself = false;
                            $result->employee = $surety->profile->employee->employee_type->id == 1;
                        }
                        else {
                            $result = new stdClass();
                            $result->id = 0;
                            $result->message = "ผู้ค้ำประกันไม่สามารถเพิ่มซ้ำได้";  
                        }
                    }
                    else {
                        $result = new stdClass();
                        $result->id = 0;
                        $result->message = "ผู้ค้ำประกันได้ใช้สิทธิ์การค้ำ 2 สัญญาเท่านั้น";
                    }
                }
                else {
                    $result = new stdClass();
                    $result->id = 0;
                    $result->message = "ผู้ค้ำประกันต้องเป็นพนักงานเท่านั้น";  
                }
            }
        }
        else {
            $result = new stdClass();
            $result->id = 0;
            $result->message = "ไม่พบข้อมูลสมาชิก";
        }

        return Response::json($result);
    }

    public function postAddsurety(Request $request) {
        $loan = Loan::find($request->input('loan_id'));
        $member = Member::find($request->input('member_id'));
        $amount = $request->input('amount');
        $result = null;

        if ($loan->outstanding >= $amount) {
            // ค้ำตัวเอง (ใช้หุ้น)
            if ($loan->member_id == $member->id) {
                if ($member->profile->employee->employee_type->id == 1) { // พนักงาน/ลูกจ้าง ททท.
                    $rule = Bailsman::find(1);
                    $available = ($member->shareholdings->sum('amount') * $rule->self_rate < $rule->self_maxguaruntee) ? $member->shareholdings->sum('amount') * $rule->self_rate : $rule->self_maxguaruntee;

                    if ($available >= $amount) {
                        $loan->sureties()->attach($member->id, ['salary' => 0, 'amount' => $amount, 'yourself' => true]);
    
                        $result = new stdClass();
                        $result->id = $member->id;
                        $result->loan_id = $loan->id;
                        $result->name = $member->profile->fullname;
                        $result->amount = number_format($amount, 2, '.', ',');
                        $result->available = number_format($available, 2, '.', ',');
                        $result->yourself = true;
                    }
                    else {
                        $result = new stdClass();
                        $result->id = 0;
                        $result->message = "ไม่สามารถค้ำประกันได้ เนื่องจากจำนวนหุ้นไม่พอใช้ค้ำประกัน (" . number_format($rule->self_rate * 100, 0, '.', ',') . "% ของหุ้น แต่ไม่เกิน " . number_format($rule->self_maxguaruntee, 2, '.', ',') . " บาท)";
                    }
                }
                else { // บุคคลภายนอก
                    $rule = Bailsman::find(2);
                    $available = ($member->shareholdings->sum('amount') * $rule->self_rate < $rule->self_maxguaruntee) ? $member->shareholdings->sum('amount') * $rule->self_rate : $rule->self_maxguaruntee;

                    if ($available >= $amount) {
                        $loan->sureties()->attach($member->id, ['salary' => 0, 'amount' => $amount, 'yourself' => true]);

                        $result = new stdClass();
                        $result->id = $member->id;
                        $result->loan_id = $loan->id;
                        $result->name = $member->profile->fullname;
                        $result->amount = number_format($amount, 2, '.', ',');
                        $result->available = number_format($available, 2, '.', ',');
                        $result->yourself = false;
                    }
                    else {
                        $result = new stdClass();
                        $result->id = 0;
                        $result->message = "ไม่สามารถค้ำประกันได้ เนื่องจากจำนวนหุ้นไม่พอใช้ค้ำประกัน (" . number_format($rule->self_rate * 100, 0, '.', ',') . "% ของหุ้น แต่ไม่เกิน " . number_format($rule->self_maxguaruntee, 2, '.', ',') . " บาท)";
                    }
                }
            }
            // ค้ำผู้อื่น (ต้องเป็นพนักงาน/ลูกจ้าง ททท. เท่านั้น ใช้เงินเดือนค้ำ)
            else {
                if ($member->profile->employee->employee_type->id == 1) { // พนักงาน/ลูกจ้าง ททท.   
                    $rule = Bailsman::find(1); 
                    $salary = $request->input('salary');
                    $netSalary = $request->input('netSalary');

                    $limit = ($salary * $rule->other_rate < $rule->other_maxguaruntee) ? $salary * $rule->other_rate : $rule->other_maxguaruntee;
                    $gaurantee = $member->sureties->filter(function ($value, $key) { return !is_null($value->code) && is_null($value->completed_at); })
                        ->sum(function ($value) { return $value->sureties->filter(function ($value, $key) { return $value->yourself; })
                        ->sum('amount'); });
                    $outstanding = $member->sureties->filter(function ($value, $key) { return !is_null($value->code) && is_null($value->completed_at); })
                        ->sum('outstanding');
                    $principle = $member->sureties->filter(function ($value, $key) { return !is_null($value->code) && is_null($value->completed_at); })
                        ->sum(function ($value) { return $value->payments->sum('principle'); });         
                    $available = ($outstanding > 0) ? $limit - ($gaurantee * (($outstanding - $principle) / $outstanding)) : $limit;
            
                    if ($netSalary < $rule->other_netsalary) {
                        $result = new stdClass();
                        $result->id = 0;
                        $result->message = "ไม่สามารถค้ำประกันได้ เนื่องจากเงินเดือนสุทธิผู้ค้ำ น้อยกว่า " . number_format($rule->other_netsalary, 0, '.', ',') . " บาท";

                    }
                    else if ($available < $amount) {
                        $result = new stdClass();
                        $result->id = 0;
                        $result->message = "ไม่สามารถค้ำประกันได้ เงินเดือนไม่พอที่ใช้ค้ำประกัน (สามารถค้ำได้ " . number_format($available, 2, '.', ',') . " บาท)";
                    }
                    else {
                        $loan->sureties()->attach($member->id, ['salary' => $salary, 'amount' => $amount, 'yourself' => false]);

                        $result = new stdClass();
                        $result->id = $member->id;
                        $result->loan_id = $loan->id;
                        $result->name = $member->profile->fullname;
                        $result->amount = number_format($amount, 2, '.', ',');
                        $result->available = number_format($available, 2, '.', ',');
                        $result->yourself = false;
                    }
                }
                else { // บุคคลภายนอก
                    $rule = Bailsman::find(2);
                    $available = ($member->shareholdings->sum('amount') * $rule->other_rate < $rule->other_maxguaruntee) ? $member->shareholdings->sum('amount') * $rule->other_rate : $rule->other_maxguaruntee;

                    if ($available >= $amount) {
                        $loan->sureties()->attach($member->id, ['salary' => 0, 'amount' => $amount, 'yourself' => true]);

                        $result = new stdClass();
                        $result->id = $member->id;
                        $result->loan_id = $loan->id;
                        $result->name = $member->profile->fullname;
                        $result->amount = number_format($amount, 2, '.', ',');
                        $result->available = number_format($available, 2, '.', ',');
                        $result->yourself = false;
                    }
                    else {
                        $result = new stdClass();
                        $result->id = 0;
                        $result->message = "ไม่สามารถค้ำประกันได้ เนื่องจากจำนวนหุ้นไม่พอใช้ค้ำประกัน (" . number_format($rule->other_rate * 100, 0, '.', ',') . "% ของหุ้น แต่ไม่เกิน " . number_format($rule->other_maxguaruntee, 2, '.', ',') . " บาท)";
                    }
                }
            }
        }
        else {
            $result = new stdClass();
            $result->id = 0;
            $result->message = "ไม่สามารถค้ำประกันได้ เนื่องจากยอดเงินที่ค้ำประกันต้องไม่เกินยอดที่ต้องการกู้";
        }

        return Response::json($result);
    }

    public function postRemovesurety(Request $request) {
        $loan = Loan::find($request->input('loan_id'));
        $member_id = $request->input('member_id');

        $loan->sureties()->detach($member_id);

        return Response::json($member_id);
    }

    public function postLoan(Request $request) {
        $loan = Loan::find($request->input('loan_id'));

        $payment = collect(LoanCalculator::payment($loan->loanType->rate, $loan->pmt, $loan->paymentType->id, $loan->outstanding, $loan->period, Diamond::today()));

        $info = (object)[
            'rate' => $loan->loanType->rate,
            'payment_type' => $loan->paymentType->id,
            'total' => ($loan->paymentType->id == 1) 
                ? (object) [ 'total_pay' => $payment->sum('pay'), 'total_interest' => $payment->sum('interest') ] 
                : (object) [ 'total_pay' => $payment->sum('pay') + $payment->sum('addon'), 'total_interest' => $payment->sum('interest') ]
        ];

        return compact('info', 'payment');
    }

    public function postLoanlist(Request $request) {
        $members = DB::table('members')
            ->join('profiles', 'members.profile_id', '=', 'profiles.id')
            ->join('employees', 'profiles.id', '=', 'employees.profile_id')
            ->join('employee_types', 'employees.employee_type_id', '=', 'employee_types.id')
            ->leftJoin(DB::raw('(SELECT * FROM loans WHERE loans.code IS NOT NULL AND loans.completed_at IS NULL) loans'), 'members.id', '=', 'loans.member_id')
            ->leftJoin('payments', 'loans.id', '=', 'payments.loan_id')
            ->whereNull('members.leave_date')
            ->groupBy(['members.id', 'profiles.name', 'profiles.lastname', 'employee_types.name'])
            ->select([
                DB::raw("LPAD(members.id, 5, '0') as code"),
                DB::raw("CONCAT('<span class=\"text-primary\"><i class=\"fa fa-user fa-fw\"></i> ', IF(profiles.name = '<ข้อมูลถูกลบ>', profiles.name, CONCAT(profiles.name, ' ', profiles.lastname)), '</span>') as fullname"),
                DB::raw("CONCAT('<span class=\"label label-primary\">', employee_types.name, '</span>') as typename"),
                DB::raw("IF(COUNT(DISTINCT loans.id) > 0, CONCAT(FORMAT(COUNT(DISTINCT loans.id), 0), ' สัญญา'), '-') as loans"),
                DB::raw("IF(COUNT(DISTINCT loans.id) > 0, CONCAT(FORMAT(COALESCE((SELECT SUM(loans.outstanding) FROM loans WHERE loans.member_id = members.id AND loans.code IS NOT NULL AND loans.completed_at IS NULL) - IF(COUNT(DISTINCT payments.id) > 0, SUM(payments.principle), 0), 0), 2), ' บาท'), '-') as amount")
            ]);

        return Datatables::queryBuilder($members)->make(true);
    }

    public function postGuaruntee(Request $request) {
        $members = DB::table('members')
            ->join('profiles', 'members.profile_id', '=', 'profiles.id')
            ->join('employees', 'profiles.id', '=', 'employees.profile_id')
            ->join('employee_types', 'employees.employee_type_id', '=', 'employee_types.id')
            ->leftJoin('loan_member', 'members.id', '=', 'loan_member.member_id')
            ->leftJoin('loans', 'loan_member.loan_id', '=', 'loans.id')
            ->whereNull('members.leave_date')
            //->whereNull('loans.completed_at')
            //->whereNotNull('loans.code')
            ->groupBy(['members.id', 'profiles.name', 'profiles.lastname', 'employee_types.id', 'employee_types.name'])
            ->select([
                DB::raw("LPAD(members.id, 5, '0') as code"),
                DB::raw("CONCAT('<span class=\"text-primary\"><i class=\"fa fa-user fa-fw\"></i> ', IF(profiles.name = '<ข้อมูลถูกลบ>', profiles.name, CONCAT(profiles.name, ' ', profiles.lastname)), '</span>') as fullname"),
                DB::raw("CONCAT('<span class=\"label label-primary\">', employee_types.name, '</span>') as typename"),
                DB::raw("IF(COALESCE(SUM(IF(loan_member.yourself = 1 AND loans.code IS NOT NULL AND loans.completed_at IS NULL, loan_member.amount, 0))) > 0, CONCAT(FORMAT(COALESCE(SUM(IF(loan_member.yourself = 1 AND loans.code IS NOT NULL AND loans.completed_at IS NULL, loan_member.amount, 0))), 2), ' บาท'), '-') as yourself"),
                DB::raw("IF(COALESCE(SUM(IF(loan_member.yourself = 0 AND loans.code IS NOT NULL AND loans.completed_at IS NULL, loan_member.amount, 0))) > 0, CONCAT(FORMAT(COALESCE(SUM(IF(loan_member.yourself = 0 AND loans.code IS NOT NULL AND loans.completed_at IS NULL, loan_member.amount, 0))), 2), ' บาท'), '-') as other"),
				DB::raw("IF(SUM(IF(loan_member.yourself = 0 AND loans.code IS NOT NULL AND loans.completed_at IS NULL, 1, 0)) < 2, CONCAT('<span class=\"label label-success\">', 2 - SUM(IF(loan_member.yourself = 0 AND loans.code IS NOT NULL AND loans.completed_at IS NULL, 1, 0)), ' สัญญา</span>'), '<span class=\"label label-danger\">เต็มแล้ว</span>') as status")
            ]);

        return Datatables::queryBuilder($members)->make(true);
    }

    public function postDividendlist(Request $request) {
        $year = intval($request->input('year'));
        $dividend_id = Dividend::where('rate_year', $year)->first()->id;
        $members = DB::select(DB::raw("select dm.member_id as member_id, concat(p.name, ' ', p.lastname) as fullname, et.name as typename, " .
            'sum(dm.shareholding_dividend) as shareholding, sum(dm.interest_dividend) as interest, sum(dm.shareholding_dividend) + sum(dm.interest_dividend) as total ' .
            'from dividends d ' .
            'inner join dividend_member dm on d.id = dm.dividend_id ' .
            'inner join members m on dm.member_id = m.id ' .
            'inner join profiles p on m.profile_id = p.id ' .
            'inner join employees e on p.id = e.profile_id ' .
            'inner join employee_types et on e.employee_type_id = et.id ' .
            'where d.id = ' . $dividend_id . ' ' .
            'group by dm.member_id, p.name, p.lastname, et.name;'
        ));
        $dividends = collect([]);

        foreach ($members as $member) {
            $item = new stdClass();
            $item->code = str_pad($member->member_id, 5, "0", STR_PAD_LEFT);
            $item->fullname = '<span class="text-primary"><i class="fa fa-user fa-fw"></i> ' . $member->fullname . '</span>';
            $item->typename = '<span class="label label-primary">' . $member->typename . '</span>';
            $item->shareholding = number_format($member->shareholding, 2, '.', ',') . ' บาท';
            $item->interest = number_format($member->interest, 2, '.', ',') . ' บาท';
            $item->total = number_format($member->total, 2, '.', ',') . ' บาท';
            $dividends->push($item);
        }

        return Datatables::collection($dividends)->make(true);
    }

    public function postDividendsummary(Request $request) {
        $year = intval($request->input('year'));
        $dividend_id = Dividend::where('rate_year', $year)->first()->id;
        $dividend = DB::select(DB::raw('select d.rate_year + 543 as rate_year, d.shareholding_rate, d.loan_rate as interest_rate, date(d.release_date) as release_date, ' .
            'sum(dm.shareholding_dividend) as shareholding_dividend, sum(dm.interest_dividend) as interest_dividend, sum(dm.shareholding_dividend) + sum(dm.interest_dividend) as total ' .
            'from dividends d ' .
            'inner join dividend_member dm on d.id = dm.dividend_id ' .
            'where d.id = ' . $dividend_id . ' ' .
            'group by d.rate_year, d.shareholding_rate, d.loan_rate;'
        ))[0];

        return compact('dividend');
    }

    public function postRefreshdividend(Request $request) {
        // $year = intval($request->input('year'));
        // $dividend = Dividend::where('rate_year', $year)->first();

        // DB::statement('update dividend_member ' .
        //     'set interest = 0, ' .
        //     'interest_dividend = 0 ' .
        //     'where dividend_id = ' . $dividend->id . ';');

        // DB::statement('update dividend_member as d, ' .
        //     '(' .
        //     'select l.member_id, sum(p.interest) as interest ' .
        //     'from loans l ' .
        //     'inner join payments p on l.id = p.loan_id ' .
        //     'where year(p.pay_date) = ' . strval($dividend->rate_year - 1) . ' and month(p.pay_date) = 12 ' .
        //     'group by l.member_id' .
        //     ') as p ' .
        //     'set d.interest = p.interest, ' .
        //     'd.interest_dividend = (p.interest * ' . ($dividend->loan_rate / 100) . ') ' .
        //     'where d.member_id = p.member_id ' .
        //     'and d.dividend_id = ' . $dividend->id . ' ' .
        //     'and year(d.dividend_date) = ' . $dividend->rate_year . ' ' .
        //     'and month(d.dividend_date) = 1 and ' .
        //     'day(d.dividend_date) = 1;');

        // for ($month = 1; $month < 12; $month++) {
        //     DB::statement('update dividend_member as d, ' .
        //         '(' .
        //         'select l.member_id, sum(p.interest) as interest ' .
        //         'from loans l ' .
        //         'inner join payments p on l.id = p.loan_id ' .
        //         'where year(p.pay_date) = ' . $dividend->rate_year . ' and month(p.pay_date) = ' . $month . ' ' .
        //         'group by l.member_id' .
        //         ') as p ' .
        //         'set d.interest = p.interest, ' .
        //         'd.interest_dividend = (p.interest * ' . ($dividend->loan_rate / 100) . ') ' .
        //         'where d.member_id = p.member_id ' .
        //         'and d.dividend_id = ' . $dividend->id . ' ' .
        //         'and year(d.dividend_date) = ' . $dividend->rate_year . ' ' .
        //         'and month(d.dividend_date) = ' . $month . ' ' .
        //         'and day(d.dividend_date) <> 1;');
        // }

        return $this->postDividendlist($request);
    }

    public function postDividend(Request $request) {
        $member = Member::find($request->input('id'));
        $year = intval($request->input('year'));
        $rate = Dividend::where('rate_year', $year)->first();
        $dividends = Dividendmember::where('member_id', $member->id)
            ->where('dividend_id', $rate->id)
            ->orderBy('dividend_date')
            ->get();
        
        return compact('member', 'dividends', 'rate');
    }

    public function postCalculate(Request $request) {
        $lastpay_date = Diamond::parse($request->input('lastpay_date'));
        $pay_date = Diamond::parse($request->input('pay_date'));

        $loan = Loan::find($request->input('loan_id'));
        $amount = $request->input('amount');
        $summary = LoanCalculator::normal_payment_2($loan, $lastpay_date->format('Y-m-j'), $pay_date->format('Y-m-j'), $amount);

        $result = new stdClass();
        $result->cal = $summary->cal;
        $result->period = $summary->period;
        $result->principle = $summary->principle;
        $result->interest = $summary->interest;
        $result->total = ($summary->principle + $summary->interest);
        $result->routine_cal = $summary->routine_cal;
        $result->routine_period = $summary->routine_period;
        $result->routine_principle = $summary->routine_principle;
        $result->routine_interest = $summary->routine_interest;
        $result->routine_total = ($summary->routine_principle + $summary->routine_interest);

        return Response::json($result);
    }

    public function postClosecalculate(Request $request) {
        $lastpay_date = Diamond::parse($request->input('lastpay_date'));
        $pay_date = Diamond::parse($request->input('pay_date'));

        $loan = Loan::find($request->input('loan_id'));
        $summary = LoanCalculator::close_payment($loan, $lastpay_date->format('Y-m-j'), $pay_date->format('Y-m-j'));

        $result = new stdClass();
        $result->cal = $summary->cal;
        $result->principle = $summary->principle;
        $result->interest = $summary->interest;
        $result->total = ($summary->principle + $summary->interest);
        $result->routine_cal = $summary->routine_cal;
        $result->routine_principle = $summary->routine_principle;
        $result->routine_interest = $summary->routine_interest;
        $result->routine_total = ($summary->routine_principle + $summary->routine_interest);

        return Response::json($result);
    }

    public function postRefinancecalculate(Request $request) {
        $lastpay_date = Diamond::parse($request->input('lastpay_date'));
        $pay_date = Diamond::parse($request->input('pay_date'));

        $loan = Loan::find($request->input('loan_id'));
        $summary = LoanCalculator::refinance_payment($loan, $lastpay_date->format('Y-m-j'), $pay_date->format('Y-m-j'));

        $result = new stdClass();
        $result->cal = $summary->cal;
        $result->principle = $summary->principle;
        $result->interest = $summary->interest;
        $result->total = ($summary->principle + $summary->interest);
        $result->refund = $summary->refund;

        return Response::json($result);
    }

    public function postCalculateavailable(Request $request) {
        $member = Member::find($request->input('member_id'));
        $salary = $request->input('salary');

        if ($member != null) {
            if ($member->profile->employee->employee_type_id == 1) {
                if ($member->sureties->filter(function ($value, $key) { return !is_null($value->code) && is_null($value->completed_at); })->count() < 2) {
                    $available = LoanCalculator::salary_available($member, $salary);

                    return Response::json($member->profile->fullname . ' มีความสามารถในการค้ำประกันจำนวน ' . (($available > 1200000) ? number_format(1200000, 2, '.', ',') : number_format($available, 2, '.', ',')) . ' บาท');
                }
                else {
                    return Response::json('ผู้ค้ำใช้สิทธิ์ค้ำประกันครบ 2 สัญญาแล้ว');
                }
            }
            else {
                return Response::json('สมาชิกต้องเป็นพนักงาน หรือลูกจ้างของ ททท. จึงจะสามารถใช้เงินเดือนค้ำประกันได้');
            }
        }
        else {
            return Response::json('ไม่พบสมาชิกรหัสนี้');
        }
    }

    public function postDisplayloan(Request $request) {
        $type = $request->input('type');

        if ($type == 1) {
            $loans = DB::table('loans')
                ->join('members', 'loans.member_id', '=', 'members.id')
                ->join('profiles', 'members.profile_id', '=', 'profiles.id')
                ->join('loan_types', 'loans.loan_type_id', '=', 'loan_types.id')
                ->join('payments', 'loans.id', '=', 'payments.loan_id')
                ->whereNull('loans.completed_at')
                ->whereNotNull('loans.code')
                ->groupBy(['members.id', 'profiles.name', 'profiles.lastname', 'loans.loan_type_id', 'loan_types.name', 'loans.id', 'loans.loaned_at', 'loans.outstanding'])
                ->orderBy('loans.loan_type_id')
                ->orderBy('members.id')
                ->select([
                    DB::raw("CONCAT('<span class=\"text-primary\"><i class=\"fa fa-money fa-fw\"></i> ', loan_types.name, '</span>') as loantype"),
                    DB::raw("loans.id as loanid"),
                    DB::raw("CONCAT('<span class=\"label label-primary\">', loans.code, '<span>') as loancode"),
                    DB::raw("members.id as memberid"),
                    DB::raw("LPAD(members.id, 5, '0') as membercode"),
                    DB::raw("CONCAT(profiles.name, ' ', profiles.lastname) as membername"),
                    DB::raw("DATE_FORMAT(DATE_ADD(loans.loaned_at, INTERVAL 543 YEAR), \"%Y-%m-%d\") as loandate"),
                    DB::raw("FORMAT(loans.outstanding, 2) as outstanding"),
                    DB::raw("CONCAT(FORMAT(MAX(payments.period), 0), '/', FORMAT(loans.period, 0)) as period"),
                    DB::raw("FORMAT(CAST(loans.outstanding - SUM(payments.principle) AS DECIMAL(11, 2)), 2) as priciple")
                ])->get();
        }
        else {
            $loans = DB::table('loans')
                ->join('members', 'loans.member_id', '=', 'members.id')
                ->join('profiles', 'members.profile_id', '=', 'profiles.id')
                ->join('loan_types', 'loans.loan_type_id', '=', 'loan_types.id')
                ->join('payments', 'loans.id', '=', 'payments.loan_id')
                ->whereNotNull('loans.completed_at')
                ->whereNotNull('loans.code')
                ->groupBy(['members.id', 'profiles.name', 'profiles.lastname', 'loans.loan_type_id', 'loan_types.name', 'loans.id', 'loans.loaned_at', 'loans.outstanding'])
                ->orderBy('loans.loan_type_id')
                ->orderBy('members.id')
                ->select([
                    DB::raw("CONCAT('<span class=\"text-primary\"><i class=\"fa fa-money fa-fw\"></i> ', loan_types.name, '</span>') as loantype"),
                    DB::raw("loans.id as loanid"),
                    DB::raw("loans.code as loancode"),
                    DB::raw("members.id as memberid"),
                    DB::raw("LPAD(members.id, 5, '0') as membercode"),
                    DB::raw("CONCAT(profiles.name, ' ', profiles.lastname) as membername"),
                    DB::raw("DATE_FORMAT(DATE_ADD(loans.loaned_at, INTERVAL 543 YEAR), \"%Y-%m-%d\") as loandate"),
                    DB::raw("FORMAT(loans.outstanding, 2) as outstanding"),
                    DB::raw("CONCAT(FORMAT(MAX(payments.period), 0), '/', FORMAT(loans.period, 0)) as period"),
                    DB::raw("FORMAT(CAST(loans.outstanding - SUM(payments.principle) AS DECIMAL(11, 2)), 2) as priciple")
                ])->get();        
        }

        $count = 0;
        $collection = collect([]);
        foreach ($loans as $loan) {
            $item = new stdClass();
            $item->index = strval(++$count) . '.';
            $item->loantype = $loan->loantype;
            $item->loanid = $loan->loanid;
            $item->loancode = $loan->loancode;
            $item->memberid = $loan->memberid;
            $item->membercode = $loan->membercode;
            $item->membername = $loan->membername;
            $item->loandate = $loan->loandate;
            $item->outstanding = $loan->outstanding;
            $item->period = $loan->period;
            $item->priciple = $loan->priciple;
            $collection->push($item);
        }

        return Datatables::collection($collection)
            ->make(true);
    }

    public function postCheckmember(Request $request) {
        $member = Member::find($request->input('id'));
        $exist = (!is_null($member)) ? true : false;
        $is_employee = ($exist) ? ($member->profile->employee->employee_type_id == 1) ? true : false : false;
        $id = ($exist) ? $member->id : 0;

        return compact('exist', 'is_employee', 'id');
    }

    public function postEmployeebailsman(Request $request) {
        $member = Member::find($request->input('id'));
        $salary = $request->input('salary');
        $netsalary = $request->input('netsalary');

        $limit = Bailsman::find($member->profile->employee->employee_type_id);
        $surety = $member->sureties->filter(function ($value, $key) { 
                return !is_null($value->code) && is_null($value->completed_at) && !$value->pivot->yourself; })
            ->count();
        $yourself = $member->sureties->filter(function ($value, $key) { 
                return !is_null($value->code) && is_null($value->completed_at) && $value->pivot->yourself; })
            ->sum(function ($value) { 
                return $value->pivot->amount; });
        $sureties = $member->sureties->filter(function ($value, $key) use ($member) { 
                return !is_null($value->code) && is_null($value->completed_at) && $value->member_id != $member->id; });
        // $surety_amount = $member->sureties->filter(function ($value, $key) { 
        //         return !is_null($value->code) && is_null($value->completed_at) && !$value->pivot->yourself; })
        //     ->sum(function ($value) { 
        //         return $value->pivot->amount; });
        $surety_amount = ($sureties->count() > 0) ? LoanCalculator::sureties_balance($sureties) : 0;
        $salary_available = ($salary * $limit->other_rate < $limit->other_maxguaruntee) ? $salary * $limit->other_rate : $limit->other_maxguaruntee;

        $bailsman = new stdClass();
        $bailsman->member_code = $member->memberCode;
        $bailsman->member_name = $member->profile->fullname;
        $bailsman->member_type = $member->profile->employee->employee_type->name;
        $bailsman->bailsman = $surety;
        $bailsman->yourself = $yourself;
        $bailsman->amount = ($salary_available - $surety_amount > 0) ? $salary_available - $surety_amount : 0;
        $bailsman->message = ($netsalary >= $limit->other_netsalary) ? 
            ($surety < 2) ? 
            (($salary_available - $surety_amount) > 0) ? 
            "สามารถค้ำประกันผู้อื่นได้อีก " . number_format(2 - $surety, 0, '.', ',') . " สัญญา ในวงเงิน " . number_format($salary_available - $surety_amount, 2, '.', ',') . " บาท" : 
            "ไม่สามารถค้ำประกันผู้อื่นได้ เนื่องจากไม่มีวงเงินที่ใช้ค้ำประกันเหลือแล้ว" :
            "ไม่สามารถค้ำประกันผู้อื่นได้ เนื่องจากค้ำประกันครบ 2 สัญญาแล้ว" :
            "ไม่สามารถค้ำประกันผู้อื่นได้ เนื่องจากเงินเดือนสุทธิน้อยกว่า " . number_format($limit->other_netsalary, 2, '.', ',') . " บาท";

        return Response::json($bailsman);
    }

    public function postOutsiderbailsman(Request $request) {
        $member = Member::find($request->input('id'));

        $limit = Bailsman::find($member->profile->employee->employee_type_id);
        $surety = $member->sureties->filter(function ($value, $key) { 
                return !is_null($value->code) && is_null($value->completed_at) && !$value->pivot->yourself; })
            ->count();
        $yourself = $member->sureties->filter(function ($value, $key) { 
                return !is_null($value->code) && is_null($value->completed_at) && $value->pivot->yourself; })
            ->sum(function ($value) { 
                return $value->pivot->amount; });
        $sureties = $member->sureties->filter(function ($value, $key) { 
                return !is_null($value->code) && is_null($value->completed_at); });
        // $surety_amount = $member->sureties->filter(function ($value, $key) { 
        //         return !is_null($value->code) && is_null($value->completed_at); })
        //     ->sum(function ($value) { 
        //         return $value->pivot->amount; });
        $surety_amount = ($sureties->count() > 0) ? LoanCalculator::sureties_balance($sureties) : 0;
        $shareholding = $member->shareholdings->sum('amount');
        $shareholding_available = ($shareholding * $limit->other_rate < $limit->other_maxguaruntee) ? $shareholding * $limit->other_rate : $limit->other_maxguaruntee;

        $bailsman = new stdClass();
        $bailsman->member_code = $member->memberCode;
        $bailsman->member_name = $member->profile->fullname;
        $bailsman->member_type = $member->profile->employee->employee_type->name;
        $bailsman->bailsman = $surety;
        $bailsman->yourself = $yourself;
        $bailsman->amount = ($shareholding_available - $surety_amount > 0) ? $shareholding_available - $surety_amount : 0;
        $bailsman->message = ($surety < 2) ?
            (($shareholding_available - $surety_amount) > 0) ? 
            "สามารถค้ำประกันผู้อื่นได้อีก " . number_format(2 - $surety, 0, '.', ',') . " สัญญา ในวงเงิน " . number_format($shareholding_available - $surety_amount, 2, '.', ',') . " บาท" : 
            "ไม่สามารถค้ำประกันผู้อื่นได้ เนื่องจากไม่มีวงเงินที่ใช้ค้ำประกันเหลือแล้ว" :
            "ไม่สามารถค้ำประกันผู้อื่นได้ เนื่องจากค้ำประกันครบ 2 สัญญาแล้ว";

        return Response::json($bailsman);
    }

    public function postEmployeeloan(Request $request) {
        $member = Member::find($request->input('id'));
        $salary = $request->input('salary');
        $netsalary = $request->input('netsalary');

        $loantype = LoanType::find(1);
        $has_normal_loan = $member->loans->filter(function ($value, $key) { return !is_null($value->code) && is_null($value->completed_at) && $value->loan_type_id == 1; })->count() > 0 ? true : false;
        $loans = $member->loans->filter(function ($value, $key) { return !is_null($value->code) && is_null($value->completed_at); })->count();
        $loans_outstanding = $member->loans->filter(function ($value, $key) { return !is_null($value->code) && is_null($value->completed_at) && $value->loan_type_id != 2; })->sum('outstanding');
        $loans_principle = $member->loans->filter(function ($value, $key) { return !is_null($value->code) && is_null($value->completed_at) && $value->loan_type_id != 2; })->sum(function ($value) { return $value->payments->sum('principle'); });
        $salary_available = ($salary * $loantype->employee_ratesalary < $loantype->max_loansummary) ? $salary * $loantype->employee_ratesalary : $loantype->max_loansummary;

        $salary_outstanding = $salary_available - ($loans_outstanding - $loans_principle);
        $shareholding = $member->shareholdings->sum('amount');
        $limit = $loantype->limits->filter(function ($value, $key) use ($shareholding) { return $shareholding >= $value->cash_begin * $value->shareholding / 100 && $shareholding <= $value->cash_end * $value->shareholding / 100; })->first();
        $shareholding_outstanding = $shareholding * 100 / (is_null($limit) ? $loantype->limits->max('shareholding') : $limit->shareholding);
        $max_outstanding = ($salary_outstanding < $shareholding_outstanding) ? $salary_outstanding : $shareholding_outstanding;

        if ($has_normal_loan) {
            $normal_outstanding = $member->loans->filter(function ($value, $key) { return !is_null($value->code) && is_null($value->completed_at) && $value->loan_type_id == 1; })->sum('outstanding');
            $normal_principle = $member->loans->filter(function ($value, $key) { return !is_null($value->code) && is_null($value->completed_at) && $value->loan_type_id == 1; })->sum(function ($value) { return $value->payments->sum('principle'); });
        }

        $loan = new stdClass();
        $loan->member_code = $member->memberCode;
        $loan->member_name = $member->profile->fullname;
        $loan->member_type = $member->profile->employee->employee_type->name;
        $loan->loans = $loans;
        $loan->shareholding = $shareholding;
        $loan->amount = ($max_outstanding > 0 && ($loans_outstanding - $loans_principle) <= $loantype->max_loansummary && $netsalary >= $loantype->employee_netsalary) ? $max_outstanding : 0;
        $loan->message = ($netsalary >= $loantype->employee_netsalary) ? 
            ($max_outstanding > 0) ?
            (($loans_outstanding - $loans_principle) <= $loantype->max_loansummary) ?
            ($has_normal_loan) ?
            "สามารถกู้สามัญได้ จำนวน " . number_format(($normal_outstanding - $normal_principle) + $max_outstanding, 2, '.', ',') . " บาท และมีส่วนต่างจากการกู้เป็นเงิน " . number_format($max_outstanding, 2, '.', ',') . " บาท" :
            "สามารถกู้สามัญได้ จำนวน " . number_format($max_outstanding, 2, '.', ',') . " บาท" :
            "ไม่สามารถกู้ได้ เนื่องจากยอดรวมของการกู้สามัญและการกู้เฉพาะกิจอื่นๆ เกิน " . number_format($loantype->max_loansummary, 2, '.', ',') .  " บาท" :
            "ไม่สามารถกู้ได้ เนื่องจากหุ้นเรือนหุ้นหรือเงินดือนไม่พอกู้" :
            "ไม่สามารถค้ำประกันผู้อื่นได้ เนื่องจากเงินเดือนสุทธิน้อยกว่า " . number_format($loantype->employee_netsalary, 2, '.', ',') . " บาท";

        return Response::json($loan);
    }

    public function postOutsiderloan(Request $request) {
        $member = Member::find($request->input('id'));

        $loantype = LoanType::find(1);
        $has_normal_loan = $member->loans->filter(function ($value, $key) { return !is_null($value->code) && is_null($value->completed_at) && $value->loan_type_id == 1; })->count() > 0 ? true : false;
        $loans = $member->loans->filter(function ($value, $key) { return !is_null($value->code) && is_null($value->completed_at); })->count();
        $loans_outstanding = $member->loans->filter(function ($value, $key) { return !is_null($value->code) && is_null($value->completed_at) && $value->loan_type_id != 2; })->sum('outstanding');
        $loans_principle = $member->loans->filter(function ($value, $key) { return !is_null($value->code) && is_null($value->completed_at) && $value->loan_type_id != 2; })->sum(function ($value) { return $value->payments->sum('principle'); });
        $shareholding = $member->shareholdings->sum('amount');
        $shareholding_available = $shareholding * $loantype->outsider_rateshareholding;

        if ($has_normal_loan) {
            $normal_outstanding = $member->loans->filter(function ($value, $key) { return !is_null($value->code) && is_null($value->completed_at) && $value->loan_type_id == 1; })->sum('outstanding');
            $normal_principle = $member->loans->filter(function ($value, $key) { return !is_null($value->code) && is_null($value->completed_at) && $value->loan_type_id == 1; })->sum(function ($value) { return $value->payments->sum('principle'); });
        }

        $loan = new stdClass();
        $loan->member_code = $member->memberCode;
        $loan->member_name = $member->profile->fullname;
        $loan->member_type = $member->profile->employee->employee_type->name;
        $loan->loans = $loans;
        $loan->shareholding = $shareholding;
        $loan->amount = ($shareholding_available - ($loans_outstanding - $loans_principle) > 0 && ($loans_outstanding - $loans_principle) <= $loantype->max_loansummary) ? $shareholding_available - ($loans_outstanding - $loans_principle) : 0;
        $loan->message = ($shareholding_available - ($loans_outstanding - $loans_principle) > 0) ?
            (($loans_outstanding - $loans_principle) <= $loantype->max_loansummary) ?
            ($has_normal_loan) ?
            "สามารถกู้สามัญได้ จำนวน " . number_format(($normal_outstanding - $normal_principle) + $shareholding_available - ($loans_outstanding - $loans_principle), 2, '.', ',') . " บาท และมีส่วนต่างจากการกู้เป็นเงิน " . number_format($shareholding_available - ($loans_outstanding - $loans_principle), 2, '.', ',') . " บาท" :
            "สามารถกู้สามัญได้ จำนวน " . number_format($shareholding_available - ($loans_outstanding - $loans_principle), 2, '.', ',') . " บาท" :
            "ไม่สามารถกู้ได้ เนื่องจากยอดรวมของการกู้สามัญและการกู้เฉพาะกิจอื่นๆ เกิน " . number_format($loantype->max_loansummary, 2, '.', ',') .  " บาท" :
            "ไม่สามารถกู้ได้ เนื่องจากหุ้นเรือนหุ้นหรือเงินดือนไม่พอกู้";

        return Response::json($loan);
    }

    public function postUnlockresetpassword(Request $request) {
        $user = User::find($request->input('id'));
        $user->newaccount = false;
        $user->save();
    }

    public function postAutoshareholdingsetting() {
        $setting = RoutineSetting::find(1);

        $result = new stdClass();
        $result->calculate_status = boolval($setting->calculate_status);
        $result->save_status = boolval($setting->save_status);

        return Response::json($result);
    }

    public function postAutoshareholdingcal(Request $request) {
        $status = $request->input('status') === 'true';
    
        DB::transaction(function() use ($status) {
            $setting = RoutineSetting::find(1);
            $setting->calculate_status = !$status;

            if ($status) {
                $setting->save_status = false;
            }

            $setting->save();

            History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', (!$status) ? 'เปิดการคำนวณค่าหุ้นปกติรายเดือนอัตโนมัติ' : 'ปิดการคำนวณค่าหุ้นปกติรายเดือนอัตโนมัติ');
        });

        $setting = RoutineSetting::find(1);
        $result = new stdClass();
        $result->calculate_status = boolval($setting->calculate_status);
        $result->save_status = boolval($setting->save_status);

        return Response::json($result);
    }

    public function postAutoshareholdingsav(Request $request) {
        $status = $request->input('status') === 'true';
    
        DB::transaction(function() use ($status) {
            $setting = RoutineSetting::find(1);
            $setting->save_status = !$status;
            $setting->save();

            History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', (!$status) ? 'เปิดการบันทึกค่าหุ้นปกติรายเดือนอัตโนมัติ' : 'ปิดการบันทึกค่าหุ้นปกติรายเดือนอัตโนมัติ');
        });

        $setting = RoutineSetting::find(1);
        $result = new stdClass();
        $result->calculate_status = boolval($setting->calculate_status);
        $result->save_status = boolval($setting->save_status);

        return Response::json($result);
    }

    public function postAutoshareholdingapprove(Request $request) {
        $id = $request->input('id');
        $routine = RoutineShareholding::find($id);

        return Response::json(!is_null($routine->approved_date));
    }

    public function postAutoshareholdingsetapprove(Request $request) {
        $id = $request->input('id');
        $status = $request->input('status') === 'true';

        DB::transaction(function() use ($id, $status) {
            $routine = RoutineShareholding::find($id);
            $routine->approved_date = (!$status) ? Diamond::today() : null;
            $routine->save();

            History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', (!$status) ? 
                'ตรวจสอบค่าหุ้นอัตโนมัติเดือน' . Diamond::parse($routine->calculated_date)->thai_format('M Y') : 
                'ยกเลิกการตรวจสอบค่าหุ้นอัตโนมัติเดือน' . Diamond::parse($routine->calculated_date)->thai_format('M Y'));
        });

        $routine = RoutineShareholding::find($id);

        return Response::json(!is_null($routine->approved_date));
    }

    public function postAutopaymentsetting() {
        $setting = RoutineSetting::find(2);

        $result = new stdClass();
        $result->calculate_status = boolval($setting->calculate_status);
        $result->save_status = boolval($setting->save_status);

        return Response::json($result);
    }

    public function postAutopaymentcal(Request $request) {
        $status = $request->input('status') === 'true';
    
        DB::transaction(function() use ($status) {
            $setting = RoutineSetting::find(2);
            $setting->calculate_status = !$status;

            if ($status) {
                $setting->save_status = false;
            }

            $setting->save();

            History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', (!$status) ? 'เปิดการคำนวณชำระเงินกู้ปกติรายเดือนอัตโนมัติ' : 'ปิดการคำนวณชำระเงินกู้ปกติรายเดือนอัตโนมัติ');
        });

        $setting = RoutineSetting::find(2);
        $result = new stdClass();
        $result->calculate_status = boolval($setting->calculate_status);
        $result->save_status = boolval($setting->save_status);

        return Response::json($result);
    }

    public function postAutopaymentsav(Request $request) {
        $status = $request->input('status') === 'true';
    
        DB::transaction(function() use ($status) {
            $setting = RoutineSetting::find(2);
            $setting->save_status = !$status;
            $setting->save();

            History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', (!$status) ? 'เปิดการบันทึกชำระเงินกู้ปกติรายเดือนอัตโนมัติ' : 'ปิดการบันทึกชำระเงินกู้ปกติรายเดือนอัตโนมัติ');
        });

        $setting = RoutineSetting::find(2);
        $result = new stdClass();
        $result->calculate_status = boolval($setting->calculate_status);
        $result->save_status = boolval($setting->save_status);

        return Response::json($result);
    }

    public function postAutopaymentapprove(Request $request) {
        $id = $request->input('id');
        $routine = RoutinePayment::find($id);

        return Response::json(!is_null($routine->approved_date));
    }

    public function postAutopaymentsetapprove(Request $request) {
        $id = $request->input('id');
        $status = $request->input('status') === 'true';

        DB::transaction(function() use ($id, $status) {
            $routine = RoutinePayment::find($id);
            $routine->approved_date = (!$status) ? Diamond::today() : null;
            $routine->save();

            History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', (!$status) ? 
                'ตรวจสอบค่าชำระเงินกู้อัตโนมัติเดือน' . Diamond::parse($routine->calculated_date)->thai_format('M Y') : 
                'ยกเลิกการตรวจสอบค่าชำระเงินกู้อัตโนมัติเดือน' . Diamond::parse($routine->calculated_date)->thai_format('M Y'));
        });

        $routine = RoutinePayment::find($id);

        return Response::json(!is_null($routine->approved_date));
    }

    public function postUploadbeneficiary(Request $request) {
        $id = $request->input('ID');
        $file = $request->file('File');

        $filename = time() . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = ($file->getRealPath() != false) ? $file->getRealPath() : $file->getPathname();
        Storage::disk('beneficiaries')->put($filename, file_get_contents($path));

        History::addAdminHistory(Auth::guard($this->guard)->id(), 'เพิ่มข้อมูล', 'เพิ่มเอกสารผู้รับผลประโยชน์ของ ' . Member::find($id)->profile->fullname);
        $beneficiary = new Beneficiary();
        $beneficiary->member_id = $id;
        $beneficiary->file = $filename;
        $beneficiary->save();

        $data = new stdClass();
        $data->id = $beneficiary->id;
        $data->display = Diamond::parse($beneficiary->created_at)->thai_format('j M Y');
        $data->link = url(env('APP_URL') . '/storage/file/beneficiaries/' . $beneficiary->file);

        return Response::json($data);
    }

    public function postDeletebeneficiary(Request $request) {
        $id = $request->input('id');

        $document = Beneficiary::find($id);
        Storage::disk('beneficiaries')->delete($document->file);
        History::addAdminHistory(Auth::guard($this->guard)->id(), 'ลบข้อมูล', 'ลบเอกสารผู้รับผลประโยชน์ของ ' . Member::find($document->member_id)->profile->fullname);
        $document->delete();

        return Response::json($id);
    }
}
