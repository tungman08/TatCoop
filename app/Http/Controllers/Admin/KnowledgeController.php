<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Knowledge;
use Auth;
use History;
use DB;
use Validator;

class KnowledgeController extends Controller
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
        $knowledges = Knowledge::orderBy('id', 'desc')->get();

        return view('admin.knowledge.index', [
            'knowledges' => $knowledges
        ]);
    }

    public function create() {
        return view('admin.knowledge.create');
    }

    public function show($id) {
        $knowledge = Knowledge::find($id);

        return view('admin.knowledge.show', [
            'knowledge' => $knowledge
        ]);
    }

    public function edit($id) {
        $knowledge = Knowledge::find($id);

        return view('admin.knowledge.edit', [
            'knowledge' => $knowledge
        ]);
    }

    public function store(Request $request) {
        $rules = [
            'title' => 'required',
            'content' => 'required'
        ];

        $attributeNames = [
            'title' => 'หัวข้อสาระน่ารู้',
            'content' => 'เนื้อหาสาระน่ารู้'
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
                $title = $request->input('title');
                $content = $request->input('content');

                $knowledge = new Knowledge();
                $knowledge->title = $title;
                $knowledge->content = $content;
                $knowledge->save();

                History::addAdminHistory(Auth::guard($this->guard)->id(), 'เพิ่มข้อมูล', 'เพิ่มสาระน่ารู้บนเว็บไซต์');
            });

            return redirect()->route('website.knowledge.index')
                ->with('flash_message', 'เพิ่มสาระน่ารู้เรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function update(Request $request, $id) {
        $rules = [
            'title' => 'required',
            'content' => 'required'
        ];

        $attributeNames = [
            'title' => 'หัวข้อสาระน่ารู้',
            'content' => 'เนื้อหาสาระน่ารู้'
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
                $title = $request->input('title');
                $content = $request->input('content');

                $knowledge = Knowledge::find($id);
                $knowledge->title = $title;
                $knowledge->content = $content;
                $knowledge->save();

                History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'แก้ไขสาระน่ารู้บนเว็บไซต์');
            });

            return redirect()->route('website.knowledge.show', ['id' => $id])
                ->with('flash_message', 'แก้ไขสาระน่ารู้เรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function destroy($id) {
        DB::transaction(function() use ($id) {
            $knowledge = Knowledge::find($id);
            $knowledge->delete();

            History::addAdminHistory(Auth::guard($this->guard)->id(), 'ลบข้อมูล', 'ลบสาระน่ารู้บนเว็บไซต์');
        });

        return redirect()->route('website.knowledge.index')
            ->with('flash_message', 'ลบสาระน่ารู้เรียบร้อยแล้ว')
            ->with('callout_class', 'callout-success');
    }

    public function getInactive() {
        $inactives = Knowledge::inactive()->orderBy('deleted_at', 'desc')->get();

        return view('admin.knowledge.inactive', [
            'inactives' => $inactives
        ]);
    }

    public function postRestore($id) {
        DB::transaction(function() use ($id) {
            $knowledge = Knowledge::withTrashed()->where('id', $id)->first();
            $knowledge->restore();
        
            History::addAdminHistory(Auth::guard($this->guard)->id(), 'คืนสภาพข้อมูล', 'คืนสภาพสาระน่ารู้บนเว็บไซต์');
        });

        return redirect()->route('website.knowledge.index')
            ->with('flash_message', 'คืนสภาพสาระน่ารู้เรียบร้อยแล้ว')
            ->with('callout_class', 'callout-success');
    }

    public function postDelete($id) {
        DB::transaction(function() use ($id) {
            $knowledge = Knowledge::withTrashed()->where('id', $id)->first();

            foreach ($knowledge->attachments as $attachment) {
                Storage::disk('attachments')->delete($attachment->file);
            }

            $knowledge->forceDelete();
        
            History::addAdminHistory(Auth::guard($this->guard)->id(), 'ลบข้อมูลอย่างถาวร', 'ลบสาระน่ารู้บนเว็บไซต์อย่างถาวร');
        });

        return redirect()->route('website.knowledge.index')
            ->with('flash_message', 'ลบสาระน่ารู้เรียบร้อยแล้ว')
            ->with('callout_class', 'callout-success');
    }
}
