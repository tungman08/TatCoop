<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Knowledge;

class KnowledgeController extends Controller
{
    public function index() {
        $knowledges = Knowledge::orderBy('id', 'desc')->paginate(16);

        return view('website.knowledges.index', [
            'knowledges' => $knowledges
        ]);
    }

    public function show($id) {
        $knowledge = Knowledge::find($id);
        $knowledge->viewer = $knowledge->viewer + 1;
        $knowledge->save();

        return view('website.knowledges.show', [
            'knowledge' => $knowledge
        ]); 
    }
}
