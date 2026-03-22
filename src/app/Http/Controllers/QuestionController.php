<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuestionRequest;
use App\Models\Question;
use App\Services\QuestionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class QuestionController extends Controller
{
    /**
     * 質問一覧（ログイン不要）。
     *
     * 将来:
     * - ステータス（解決済み/募集中）タブ
     * - 検索（タイトル/本文）
     * - ページネーション
     */
    public function index(Request $request)
    {
        $questions = Question::query()
            ->with(['user', 'tags'])
            ->orderByDesc('id')
            ->paginate(12);

        if (view()->exists('questions.index')) {
            return view('questions.index', compact('questions'));
        }

        return view('welcome');
    }

    /**
     * 質問詳細（ログイン不要）。
     *
     * 注意:
     * - 回答投稿フォームは “未ログインならログイン促進” の想定
     * - 実際の表示制御は View 側で行う（ユーザーがいるかどうかで分岐）
     */
    public function show(Request $request, Question $question)
    {
        // 詳細画面で必要な関連をまとめてロード
        // 回答とそのコメント、ユーザー情報をEager Load
        $question->load([
            'user',
            'tags',
            'answers' => function ($query) {
                $query->with(['user', 'comments.user'])->orderBy('created_at', 'asc');
            },
            'acceptedAnswer.user'
        ]);

        // 閲覧数カウント（詳細ページ表示時のみ +1）
        // - ログイン中: user_id 判定
        // - 未ログイン: guest_token（cookie）判定
        // - 同一人物は24時間以内の再閲覧では +1 しない
        // - 作成者の閲覧（本人）はカウントしない
        $viewerId = null;
        if (Auth::guard('freelancer')->check()) {
            $viewerId = (int) Auth::guard('freelancer')->user()->id;
        } elseif (Auth::guard('company')->check()) {
            $viewerId = (int) Auth::guard('company')->user()->id;
        }

        $guestToken = null;
        $needsGuestCookie = false;
        if ($viewerId === null) {
            $guestToken = $request->cookie('guest_token');
            if (empty($guestToken)) {
                $guestToken = (string) Str::uuid();
                $needsGuestCookie = true;
            }
        }

        $shouldIncrement = true;
        if ($viewerId !== null && $viewerId === (int) $question->user_id) {
            $shouldIncrement = false;
        }

        $cacheKey = $viewerId !== null
            ? "question_viewed:{$question->id}:u:{$viewerId}"
            : "question_viewed:{$question->id}:g:{$guestToken}";

        if ($shouldIncrement) {
            if (Cache::add($cacheKey, 1, 60 * 60 * 24)) {
                $question->increment('views_count');
                $question->views_count = (int) ($question->views_count ?? 0) + 1;
            }
        }

        if (view()->exists('questions.show')) {
            $response = response()->view('questions.show', compact('question'));
            if ($needsGuestCookie) {
                $response->withCookie(cookie('guest_token', $guestToken, 60 * 24 * 365));
            }
            return $response;
        }

        $response = response()->view('welcome');
        if ($needsGuestCookie) {
            $response->withCookie(cookie('guest_token', $guestToken, 60 * 24 * 365));
        }
        return $response;
    }

    /**
     * 自分の質問一覧（ログインユーザー本人のみ）。
     */
    public function myIndex(Request $request)
    {
        $user = null;
        if (Auth::guard('freelancer')->check()) {
            $user = Auth::guard('freelancer')->user();
        } elseif (Auth::guard('company')->check()) {
            $user = Auth::guard('company')->user();
        }

        if (!$user) {
            return redirect()->route('auth.login.form');
        }

        $questions = Question::query()
            ->with(['user', 'tags'])
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->paginate(12);

        if (view()->exists('questions.my-questions')) {
            return view('questions.my-questions', compact('questions'));
        }

        return view('welcome');
    }

    /**
     * 質問削除（作成者本人のみ）。
     */
    public function destroy(Request $request, Question $question)
    {
        $user = null;
        if (Auth::guard('freelancer')->check()) {
            $user = Auth::guard('freelancer')->user();
        } elseif (Auth::guard('company')->check()) {
            $user = Auth::guard('company')->user();
        }

        if (!$user) {
            abort(403);
        }

        if ((int) $question->user_id !== (int) $user->id) {
            abort(403);
        }

        $question->delete();

        return redirect()
            ->route('questions.my.index')
            ->with('success', '質問を削除しました');
    }

    /**
     * 質問投稿画面（ログイン必須）。
     */
    public function create()
    {
        if (view()->exists('questions.create')) {
            return view('questions.create');
        }

        return view('welcome');
    }

    /**
     * 質問投稿（ログイン必須）。
     */
    public function store(StoreQuestionRequest $request, QuestionService $service)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('auth.login.form');
        }

        // 入力チェックは FormRequest 側へ移動（StoreQuestionRequest）
        $validated = $request->validated();

        $question = $service->store($user, $validated);

        return redirect()->route('questions.show', ['question' => $question->id]);
    }
}

