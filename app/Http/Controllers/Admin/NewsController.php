<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\News;
use Auth;
use History;
use DB;
use Validator;

class NewsController extends Controller
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

    public function index() {
        $newses = News::orderBy('id', 'desc')->get();

        return view('admin.news.index', [
            'newses' => $newses
        ]);
    }

    public function create() {
        return view('admin.news.create');
    }

    public function store(Request $request) {
        $rules = [
            'title' => 'required',
            'content' => 'required'
        ];

        $attributeNames = [
            'title' => 'หัวข้อข่าวสารสำหรับสมาชิก',
            'content' => 'เนื้อหาข่าวสารสำหรับสมาชิก'
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        $validator->after(function($validator) use ($request) {
            if ($this->isHotLink($request->input('content'))) {
                $validator->errors()->add('hotlink', 'ไม่สามารถใช้รูปภาพจากเว็บไซต์อื่นได้');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        else {
            DB::transaction(function() use ($request) {
                $title = $request->input('title');
                $content = $request->input('content');

                $news = new News();
                $news->title = $title;
                $news->content = $content;
                $news->save();

                History::addAdminHistory(Auth::guard($this->guard)->id(), 'เพิ่มข้อมูล', 'เพิ่มข้อมูลข่าวสารสำหรับสมาชิกบนหน้าเว็บไซต์');
            });

            return redirect()->action('Admin\NewsController@index')
                ->with('flash_message', 'เพิ่มข่าวสารสำหรับสมาชิกเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function show($id) {
        $news = News::find($id);

        return view('admin.news.show', [
            'news' => $news
        ]);
    }

    public function edit($id) {
        $news = News::find($id);

        return view('admin.news.edit', [
            'news' => $news
        ]);
    }

    public function update(Request $request, $id) {
        $rules = [
            'title' => 'required',
            'content' => 'required'
        ];

        $attributeNames = [
            'title' => 'หัวข้อข่าวสารสำหรับสมาชิก',
            'content' => 'เนื้อหาข่าวสารสำหรับสมาชิก'
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        $validator->after(function($validator) use ($request) {
            if ($this->isHotLink($request->input('content'))) {
                $validator->errors()->add('hotlink', 'ไม่สามารถใช้รูปภาพจากเว็บไซต์อื่นได้');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        else {
            DB::transaction(function() use ($request, $id) {
                $title = $request->input('title');
                $content = $request->input('content');

                $news = News::find($id);
                $news->title = $title;
                $news->content = $content;
                $news->save();

                History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'แก้ไขข้อมูลข่าวสารสำหรับสมาชิกบนหน้าเว็บไซต์');
            });

            return redirect()->action('Admin\NewsController@show', ['id' => $id])
                ->with('flash_message', 'แก้ไขข่าวสารสำหรับสมาชิกเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function destroy($id) {
        DB::transaction(function() use ($id) {
            $news = News::find($id);
            $news->delete();

            History::addAdminHistory(Auth::guard($this->guard)->id(), 'ลบข้อมูล', 'ลบข้อมูลข่าวสารสำหรับสมาชิกบนหน้าเว็บไซต์');
        });

        return redirect()->action('Admin\NewsController@index')
            ->with('flash_message', 'ลบข่าวสารสำหรับสมาชิกเรียบร้อยแล้ว')
            ->with('callout_class', 'callout-success');
    }

    public function getInactive() {
        $inactives = News::inactive()->orderBy('deleted_at', 'desc')->get();

        return view('admin.news.inactive', [
            'inactives' => $inactives
        ]);
    }

	public function getShowInactive($id) {
		$news = News::inactive()->find($id);

		return view('admin.news.showinactive', [
            'news' => $news
        ]);
	}

    public function postRestore($id) {
        DB::transaction(function() use ($id) {
            $news = News::withTrashed()->where('id', $id)->first();
            $news->restore();

            History::addAdminHistory(Auth::guard($this->guard)->id(), 'คืนสภาพข้อมูล', 'คืนสภาพข้อมูลข่าวสารสำหรับสมาชิกบนหน้าเว็บไซต์');
        });

        return redirect()->action('Admin\NewsController@index')
            ->with('flash_message', 'คืนสภาพข่าวสารสำหรับสมาชิกเรียบร้อยแล้ว')
            ->with('callout_class', 'callout-success');
    }

    public function postDelete($id) {
        DB::transaction(function() use ($id) {
            $news = News::withTrashed()->where('id', $id)->first();

            foreach ($news->attachments as $attachment) {
                Storage::disk('attachments')->delete($attachment->file);
            }

            $news->forceDelete();

            History::addAdminHistory(Auth::guard($this->guard)->id(), 'ลบข้อมูลอย่างถาวร', 'ลบข้อมูลข่าวสารสำหรับสมาชิกบนหน้าเว็บไซต์อย่างถาวร');
        });

        return redirect()->action('Admin\NewsController@index')
            ->with('flash_message', 'ลบข่าวสารสำหรับสมาชิกเรียบร้อยแล้ว')
            ->with('callout_class', 'callout-success');
    }
    
    private function isHotLink($content) {
        $images = [];
        preg_match_all( '/src="([^"]*)"/i', $content, $images);

        if (count($images[1]) > 0) {
            foreach($images[1] as $image) {
                if (!preg_match('/https?\:\/\/(www|admin)\.tatcoop\.com/', $image)) {
                    return true;
                }
            }
        }

        return false;
    }
}
