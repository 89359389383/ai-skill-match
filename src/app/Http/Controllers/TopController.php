<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TopController extends Controller
{
    /**
     * トップページ表示。
     *
     * ここでやること（現段階）:
     * - 本来は「新着スキル」「人気記事」「新着質問」などを取得して view に渡す
     * - ただしビューがまだ無いので、まずは安全に welcome を返す
     */
    public function index(Request $request)
    {
        return view('top.index');
    }
}

