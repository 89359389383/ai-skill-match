<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuestionRequest;
use App\Models\Answer;
use App\Models\Question;
use App\Services\QuestionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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
        $tab = $request->query('tab', 'open');
        if (! in_array($tab, ['open', 'resolved'], true)) {
            $tab = 'open';
        }

        $query = Question::query()
            ->with(['user.freelancer', 'user.company', 'tags', 'bestAnswer']);

        if ($tab === 'resolved') {
            $query->where('status', Question::STATUS_RESOLVED)
                ->orderByDesc('updated_at');
        } else {
            $query->where('status', Question::STATUS_OPEN)
                ->orderByDesc('id');
        }

        $questions = $query->paginate(12)->appends(['tab' => $tab]);

        if (view()->exists('questions.index')) {
            return view('questions.index', compact('questions', 'tab'));
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
            'bestAnswer.user',
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

        $bestAnswer = null;
        $restAnswers = $question->answers;
        if ($question->best_answer_id) {
            $bestAnswer = $question->answers->firstWhere('id', (int) $question->best_answer_id);
            if ($bestAnswer) {
                $restAnswers = $question->answers->where('id', '!=', (int) $question->best_answer_id)->values();
            }
        }

        if (view()->exists('questions.show')) {
            $response = response()->view('questions.show', compact('question', 'bestAnswer', 'restAnswers'));
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
        } elseif (Auth::guard('buyer')->check()) {
            $user = Auth::guard('buyer')->user();
        }

        if (!$user) {
            return redirect()->route('auth.login.form');
        }

        $tab = $request->query('tab', 'open');
        if (! in_array($tab, ['open', 'resolved'], true)) {
            $tab = 'open';
        }

        $query = Question::query()
            ->with(['user.freelancer', 'user.company', 'tags', 'bestAnswer'])
            ->where('user_id', $user->id);

        if ($tab === 'resolved') {
            $query->where('status', Question::STATUS_RESOLVED)
                ->orderByDesc('updated_at');
        } else {
            $query->where('status', Question::STATUS_OPEN)
                ->orderByDesc('id');
        }

        $questions = $query->paginate(12)->appends(['tab' => $tab]);

        if (view()->exists('my-questions.index')) {
            return view('my-questions.index', compact('questions', 'tab'));
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
        } elseif (Auth::guard('buyer')->check()) {
            $user = Auth::guard('buyer')->user();
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

    /**
     * ベストアンサー決定（質問投稿者のみ・1回限り）。
     */
    public function setBestAnswer(Request $request, Question $question, Answer $answer)
    {
        $user = $request->user();
        if (! $user) {
            abort(403);
        }

        if ((int) $question->user_id !== (int) $user->id) {
            abort(403);
        }

        if ($question->status === Question::STATUS_RESOLVED || $question->best_answer_id !== null) {
            return redirect()
                ->route('questions.show', ['question' => $question->id])
                ->with('error', 'ベストアンサーは既に決まっています。');
        }

        if ((int) $answer->question_id !== (int) $question->id) {
            abort(404);
        }

        DB::transaction(function () use ($question, $answer): void {
            $question->answers()->update(['is_accepted' => false]);
            $answer->update(['is_accepted' => true]);
            $question->update([
                'status' => Question::STATUS_RESOLVED,
                'best_answer_id' => $answer->id,
            ]);
        });

        return redirect()
            ->route('questions.show', ['question' => $question->id])
            ->with('success', 'ベストアンサーを選びました。');
    }
}

