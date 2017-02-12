<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Knowledge;
use DB;
use Validator;

class KnowledgeController extends Controller
{
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
            });

            return redirect()->route('website.knowledge.show', ['id' => $id])
                ->with('flash_message', 'แก้ไขสาระน่ารู้เรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function destroy($id) {
        $knowledge = Knowledge::find($id);
        $knowledge->delete();

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
        $knowledge = Knowledge::withTrashed()->where('id', $id)->first();
        $knowledge->restore();

        return redirect()->route('website.knowledge.index')
            ->with('flash_message', 'คืนสภาพสาระน่ารู้เรียบร้อยแล้ว')
            ->with('callout_class', 'callout-success');
    }

    public function postDelete($id) {
        $knowledge = Knowledge::withTrashed()->where('id', $id)->first();
        $knowledge->forceDelete();

        return redirect()->route('website.knowledge.index')
            ->with('flash_message', 'ลบสาระน่ารู้เรียบร้อยแล้ว')
            ->with('callout_class', 'callout-success');
    }
}
