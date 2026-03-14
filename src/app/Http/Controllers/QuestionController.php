<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuestionRequest;
use App\Models\Question;
use App\Services\QuestionService;
use Illuminate\Http\Request;

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
        $question->load(['user', 'tags', 'answers.user', 'acceptedAnswer.user']);

        if (view()->exists('questions.show')) {
            return view('questions.show', compact('question'));
        }

        return view('welcome');
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

