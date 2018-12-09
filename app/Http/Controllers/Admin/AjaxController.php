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
use App\Dividendmember;
use App\Shareholding;
use App\Document;
use App\DocumentType;
use App\Carousel;
use App\NewsAttachment;
use App\KnowledgeAttachment;
use App\Loan;
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

    public function postDividend(Request $request) {
        $member = Member::find($request->input('id'));
        $year = intval($request->input('year'));
        $dividends = MemberProperty::getDividend($member, $year);
        $rate = Dividend::where('rate_year', $year)->first();
        
        return compact('member', 'dividends', 'rate');
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

    public function postMembershareholding(Request $request) {
        $date = Diamond::parse($request->input('date'))->endOfMonth();

        $members = DB::table('members')
            ->join('profiles', 'members.profile_id', '=', 'profiles.id')
            ->join('employees', 'profiles.id', '=', 'employees.profile_id')
            ->join('employee_types', 'employees.employee_type_id', '=', 'employee_types.id')
            ->leftJoin('shareholdings', 'members.id', '=', 'shareholdings.member_id')
            ->whereNull('members.leave_date')
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
            ->groupBy(['members.id', 'profiles.name', 'profiles.lastname', 'employee_types.name', 'members.shareholding'])
            ->select([
                DB::raw("LPAD(members.id, 5, '0') as code"),
                DB::raw("CONCAT('<span class=\"text-primary\"><i class=\"fa fa-user fa-fw\"></i> ', IF(profiles.name = '<ข้อมูลถูกลบ>', profiles.name, CONCAT(profiles.name, ' ', profiles.lastname)), '</span>') as fullname"),
                DB::raw("CONCAT('<span class=\"label label-primary\">', employee_types.name, '</span>') as typename"),
                DB::raw("CONCAT(FORMAT(members.shareholding, 0), ' หุ้น') as shareholding"),
                DB::raw("CONCAT(FORMAT(SUM(shareholdings.amount), 2), ' บาท') as amount")
            ]);

        return Datatables::queryBuilder($members)
             ->make(true);
    }

    public function postMemberpayment(Request $request) {
        $date = Diamond::parse($request->input('date'))->endOfMonth();

        $members = Db::table('members')
            ->select('members.id')
            ->join('profiles', 'members.profile_id', '=', 'profiles.id')
            ->join('employees', 'profiles.id', '=', 'employees.profile_id')
            ->join('employee_types', 'employees.employee_type_id', '=', 'employee_types.id')
            ->join('loans', 'members.id', '=', 'loans.member_id')
            ->where('employees.employee_type_id', 1)
            ->whereDate('members.start_date', '<', $date)
            ->whereNull('loans.completed_at')
            ->get();

        $payments = collect([]);
        foreach ($members as $q) {
            $member = Member::find($q->id);
            $payment = MemberProperty::getMonthlyPayment($member, $date);

            $item = new stdClass();
            $item->code = $member->memberCode;
            $item->fullname = '<span class="text-primary"><i class="fa fa-user fa-fw"></i> ' . $member->profile->fullName . '</span>';
            $item->typename = '<span class="label label-primary">' . $member->profile->employee->employee_type->name . '</span>';
            $item->loanCount = number_format($member->loans->count(), 0, '.', ',');
            $item->principle = number_format($payment->principle, 2, '.', ',');
            $item->interest = number_format($payment->interest, 2, '.', ',');
            $item->total = number_format($payment->principle + $payment->interest, 2, '.', ',');

            $payments->push($item);
        }

        return Datatables::collection($payments)
             ->make(true);
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
                    $result->fullName = $surety->profile->fullName;
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
                // ต้องเป็นพนักงานเท่ากัน
                if ($surety->profile->employee->employee_type_id == 1) {
                    // ตรวจว่าค้ำประกันไม่เกิน 2 สัญญา ไม่นับค้ำด้วยหุ้นตนเอง
                    if ($surety->sureties->filter(function ($value, $key) use ($surety) { return !is_null($value->code) && is_null($value->completed_at) && $value->member_id != $surety->id; })->count() < 2) {
                        // ตรวจสอบว่าได้ใช้ค้ำในสัญญานี้แล้วหรือไม่
                        if ($loan->sureties->where('member_id', $surety->id)->count() == 0) {
                            $result = new stdClass();
                            $result->loan_id = $loan->id;
                            $result->id = $surety->id;
                            $result->memberCode = $surety->memberCode;
                            $result->fullName = $surety->profile->fullName;
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
                    $available = ($member->shareholdings->sum('amount') * 0.9 < 1200000) ? $member->shareholdings->sum('amount') * 0.9 : 1200000;

                    if ($available >= $amount) {
                        $loan->sureties()->attach($member->id, ['salary' => 0, 'amount' => $amount, 'yourself' => true]);
    
                        $result = new stdClass();
                        $result->id = $member->id;
                        $result->loan_id = $loan->id;
                        $result->name = $member->profile->fullName;
                        $result->amount = number_format($amount, 2, '.', ',');
                        $result->available = number_format($available, 2, '.', ',');
                        $result->yourself = true;
                    }
                    else {
                        $result = new stdClass();
                        $result->id = 0;
                        $result->message = "ไม่สามารถค้ำประกันได้ เนื่องจากจำนวนหุ้นไม่พอใช้ค้ำประกัน (90% ของหุ้น แต่ไม่เกิน 1,200,000 บาท)";
                    }
                }
                else { // บุคคลภายนอก
                    $available = LoanCalculator::shareholding_available($member);

                    if ($available >= $amount) {
                        $loan->sureties()->attach($member->id, ['salary' => 0, 'amount' => $amount, 'yourself' => true]);

                        $result = new stdClass();
                        $result->id = $member->id;
                        $result->loan_id = $loan->id;
                        $result->name = $member->profile->fullName;
                        $result->amount = number_format($amount, 2, '.', ',');
                        $result->available = number_format($available, 2, '.', ',');
                        $result->yourself = false;
                    }
                    else {
                        $result = new stdClass();
                        $result->id = 0;
                        $result->message = "ไม่สามารถค้ำประกันได้ เนื่องจากจำนวนหุ้นไม่พอใช้ค้ำประกัน (80% ของหุ้น แต่ไม่เกิน 1,200,000 บาท)";
                    }
                }
            }
            // ค้ำผู้อื่น (ต้องเป็นพนักงาน/ลูกจ้าง ททท. เท่านั้น ใช้เงินเดือนค้ำ)
            else {
                $salary = $request->input('salary');
                $netSalary = $request->input('netSalary');
                $available = LoanCalculator::salary_available($member, $salary);

                if ($netSalary < 3000) {
                    $result = new stdClass();
                    $result->id = 0;
                    $result->message = "ไม่สามารถค้ำประกันได้ เนื่องจากเงินเดือนสุทธิผู้ค้ำ น้อยกว่า 3,000 บาท";

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
                    $result->name = $member->profile->fullName;
                    $result->amount = number_format($amount, 2, '.', ',');
                    $result->available = number_format($available, 2, '.', ',');
                    $result->yourself = false;
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

        $payment = collect(LoanCalculator::payment($loan->loanType->rate, $loan->paymentType->id, $loan->outstanding, $loan->period, Diamond::today()));

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
            ->groupBy(['members.id', 'profiles.name', 'profiles.lastname', 'employee_types.name'])
            ->select([
                DB::raw("LPAD(members.id, 5, '0') as code"),
                DB::raw("CONCAT('<span class=\"text-primary\"><i class=\"fa fa-user fa-fw\"></i> ', IF(profiles.name = '<ข้อมูลถูกลบ>', profiles.name, CONCAT(profiles.name, ' ', profiles.lastname)), '</span>') as fullname"),
                DB::raw("CONCAT('<span class=\"label label-primary\">', employee_types.name, '</span>') as typename"),
                DB::raw("IF(COALESCE(SUM(IF(loan_member.yourself = 1 AND loans.code IS NOT NULL AND loans.completed_at IS NULL, loan_member.amount, 0))) > 0, CONCAT(FORMAT(COALESCE(SUM(IF(loan_member.yourself = 1 AND loans.code IS NOT NULL AND loans.completed_at IS NULL, loan_member.amount, 0))), 2), ' บาท'), '-') as yourself"),
                DB::raw("IF(COALESCE(SUM(IF(loan_member.yourself = 0 AND loans.code IS NOT NULL AND loans.completed_at IS NULL, loan_member.amount, 0))) > 0, CONCAT(FORMAT(COALESCE(SUM(IF(loan_member.yourself = 0 AND loans.code IS NOT NULL AND loans.completed_at IS NULL, loan_member.amount, 0))), 2), ' บาท'), '-') as other")
            ]);

        return Datatables::queryBuilder($members)->make(true);
    }

    public function postDividendlist(Request $request) {
        $year = intval($request->input('year'));
        $dividend_id = Dividend::where('rate_year', $year)->first()->id;
        $members = Member::active()->get();
        $dividends = collect([]);

        foreach ($members as $member) {
            $dividend = Dividendmember::where('dividend_id', $dividend_id)
                ->where('member_id', $member->id)
                ->get();

            $shareholding_dividend = $dividend->sum('shareholding_dividend');
            $interest_dividend = $dividend->sum('interest_dividend');

            $item = new stdClass();
            $item->code = $member->memberCode;
            $item->fullname = '<span class="text-primary"><i class="fa fa-user fa-fw"></i> ' . $member->profile->fullName . '</span>';
            $item->typename = '<span class=\"label label-primary\">' . $member->profile->employee->employee_type->name . '</span>';
            $item->shareholding = number_format($shareholding_dividend, 2, '.', ',') . ' บาท';
            $item->interest = number_format($interest_dividend, 2, '.', ',') . ' บาท';
            $item->total = number_format($shareholding_dividend + $interest_dividend, 2, '.', ',') . ' บาท';
            $dividends->push($item);
        }

        return Datatables::collection($dividends)->make(true);
    }

    public function postCalculate(Request $request) {
        $loan = Loan::find($request->input('loan_id'));
        $member = Member::find($loan->member_id);
        $pay_date = Diamond::parse($request->input('pay_date'))->format('Y-m-j');
        $pay_amount = $request->input('pay_amount');
        $summary = LoanCalculator::normal_payment($loan, $pay_amount, $pay_date);

        $result = new stdClass();
        $result->principle = $summary->principle;
        $result->interest = $summary->interest;
        $result->total = $summary->principle + $summary->interest;

        return Response::json($result);
    }

    public function postClosecalculate(Request $request) {
        $loan = Loan::find($request->input('loan_id'));
        $member = Member::find($loan->member_id);
        $pay_date = Diamond::parse($request->input('pay_date'))->format('Y-m-j');
        $summary = LoanCalculator::close_payment($loan, $pay_date);
        
        $result = new stdClass();
        $result->principle = $summary->principle;
        $result->interest = $summary->interest;
        $result->total = $summary->principle + $summary->interest;
        $result->remark = $summary->refund > 0 ? 'คืนเงินจำนวน ' . number_format($summary->refund, 2, '.', ',') . ' บาท' : '-';

        return Response::json($result);
    }

    public function postCalculateavailable(Request $request) {
        $member = Member::find($request->input('member_id'));
        $salary = $request->input('salary');

        if ($member != null) {
            if ($member->profile->employee->employee_type_id == 1) {
                if ($member->sureties->filter(function ($value, $key) { return !is_null($value->code) && is_null($value->completed_at); })->count() < 2) {
                    $available = LoanCalculator::salary_available($member, $salary);

                    return Response::json($member->profile->fullName . ' มีความสามารถในการค้ำประกันจำนวน ' . (($available > 1200000) ? number_format(1200000, 2, '.', ',') : number_format($available, 2, '.', ',')) . ' บาท');
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

    public function postActiveloan() {
        $loans = Loan::whereNull('completed_at')
            ->whereNotNull('code')
            ->orderBy('loan_type_id')
            ->get();

        $count = 0;
        $collection = collect([]);
        foreach ($loans as $loan) {
            $item = new stdClass();
            $item->index = ++$count;
            $item->loantype = '<span class="text-primary"><i class="fa fa-meney fa-fw"></i> ' . $loan->loanType->name . '</span>';
            $item->loanid = $loan->id;
            $item->loancode = $loan->code;
            $item->memberid = $loan->member->id;
            $item->membercode = $loan->member->memberCode;
            $item->membername = $loan->member->profile->fullName;
            $item->loandate = Diamond::parse($loan->loaned_at)->thai_format('Y-m-d');
            $item->outstanding = number_format($loan->outstanding, 2, '.', ',');
            $item->period = $loan->payments->count() . '/' . $loan->period;
            $item->priciple = number_format($loan->outstanding - $loan->payments->sum('principle'), 2, '.', ',');
            $collection->push($item);
        }

        return Datatables::collection($collection)
            ->make(true);
    }

    public function postInactiveloan() {
        $loans = Loan::whereNotNull('completed_at')
            ->whereNotNull('code')
            ->orderBy('loan_type_id')
            ->get();

        $count = 0;
        $collection = collect([]);
        foreach ($loans as $loan) {
            $item = new stdClass();
            $item->index = ++$count;
            $item->loantype = '<span class="text-primary"><i class="fa fa-meney fa-fw"></i> ' . $loan->loanType->name . '</span>';
            $item->loanid = $loan->id;
            $item->loancode = $loan->code;
            $item->memberid = $loan->member->id;
            $item->membercode = $loan->member->memberCode;
            $item->membername = $loan->member->profile->fullName;
            $item->loandate = Diamond::parse($loan->loaned_at)->thai_format('Y-m-d');
            $item->outstanding = number_format($loan->outstanding, 2, '.', ',');
            $item->period = $loan->payments->count() . '/' . $loan->period;
            $item->priciple = number_format($loan->outstanding - $loan->payments->sum('principle'), 2, '.', ',');
            $collection->push($item);
        }

        return Datatables::collection($collection)
            ->make(true);
    }

    public function postCountsurety(Request $request) {
        $member = Member::find($request->input('id'));
        $result = $member->sureties->filter(function ($value, $key) {
            return !is_null($value->code) && is_null($value->completed_at); 
        });

        return Response::json($result);
    }

    public function postChecksuretyavailable(Request $request) {
        $member = Member::find($request->input('id'));
        $salary = $request->input('salary');
        $netSalary = $request->input('netSalary');
        $available = LoanCalculator::salary_available($member, $salary);

        if ($netSalary < 3000) {
            $message = "ไม่สามารถค้ำประกันได้ เนื่องจากเงินเดือนสุทธิผู้ค้ำ น้อยกว่า 3,000 บาท";

        }
        else if ($available < $amount) {
            $message = "ไม่สามารถค้ำประกันได้ เงินเดือนไม่พอที่ใช้ค้ำประกัน (สามารถค้ำได้ " . number_format($available, 2, '.', ',') . " บาท)";
        }
        else {
            $message = "สามารถค้ำได้ " . number_format($available, 2, '.', ',') . " บาท";
        }

        return Response::json($result);
    }

    public function postCheckloanavailable(Request $request) {
        $member = Member::find($request->input('id'));

        $loans = Loan::where('member_id', $member->id)
            ->where('loan_type_id', '<>', 2)
            ->whereNotNull('code')
            ->whereNull('completed_at');
        $total_outstanding = $loans->sum('outstanding');
        $total_payments = 0;

        foreach($loans as $loan) {
            $total_payments += $loan->payments()->sum('principle');
        }

        $balance = 1200000 - ($total_outstanding - $total_payments);
        $result = "สามารถกู้สามัญได้อีก " . number_format($balance, 2, '.', ',') . " บาท (ยอดเงินกู้สามัญ + กู้เฉพาะกิจอื่นๆ รวมกันแล้วเกิน 1,200,000 บาท)";

        return Response::json($result);
    }

    public function postUnlockresetpassword(Request $request) {
        $user = User::find($request->input('id'));
        $user->newaccount = false;
        $user->save();
    }
}
