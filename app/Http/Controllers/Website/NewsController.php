<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\News;

class NewsController extends Controller
{
    public function index() {
        $newses = News::orderBy('id', 'desc')->paginate(8);

        return view('website.news.index', [
            'newses' => $newses
        ]);
    }

    public function show($id) {
        $news = News::find($id);
        $news->viewer = $news->viewer + 1;
        $news->save();

        return view('website.news.show', [
            'news' => $news
        ]);
    }
}
