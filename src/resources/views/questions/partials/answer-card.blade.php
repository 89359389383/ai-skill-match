@php
    $featuredBest = $featuredBest ?? false;
    $isAnswerAuthor = $currentUserId && (int) $currentUserId === (int) $answer->user_id;
    $lastComment = $answer->comments?->last();
    $isLastCommentByQuestioner = (bool) ($lastComment && (int) $lastComment->user_id === (int) $question->user_id);
    $isLastCommentByAnswerer = (bool) ($lastComment && (int) $lastComment->user_id === (int) $answer->user_id);

    // 質問が解決済み（ベストアンサー確定）後のコメント制御
    // - ベストアンサーへ：質問者だけが1回だけコメント可能
    // - それ以外（質問者/回答者を問わず）：コメント不可
    $isResolved = $question->status === \App\Models\Question::STATUS_RESOLVED || $question->best_answer_id !== null;
    $questionerHasCommentOnThisAnswer = $answer->comments?->where('user_id', (int) $question->user_id)->isNotEmpty();

    $canComment = $currentUser && (
        $isResolved
            ? (
                $featuredBest
                && $isQuestioner
                && ! $questionerHasCommentOnThisAnswer
            )
            : (
                // 未解決時：
                // - 質問者は「直前のコメントが質問者本人ではないとき」だけコメント可能
                // - 回答者は「直前のコメントが質問者のとき」だけコメント可能
                //   => 相手からの返信がない限り連投できない
                ($isQuestioner && ! $isLastCommentByQuestioner)
                || ($isAnswerAuthor && $isLastCommentByQuestioner)
            )
    );
    $canChooseBest = $isQuestioner
        && $question->status === \App\Models\Question::STATUS_OPEN
        && $question->best_answer_id === null
        && ! $featuredBest;
@endphp
<div id="answer-{{ $answer->id }}" class="bg-white rounded-xl shadow-md overflow-hidden {{ $featuredBest ? 'border border-amber-200/80 ring-1 ring-amber-100' : '' }}">
    @if($featuredBest)
        <div class="relative px-6 pt-6 pb-2">
            <div class="flex items-start justify-between gap-4">
                <h3 class="text-2xl font-bold text-gray-900 tracking-tight">ベストアンサー</h3>
                <div class="flex-shrink-0 text-amber-500" aria-hidden="true">
                    <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="24" cy="22" r="18" fill="#F5C542" stroke="#D4A017" stroke-width="1.5"/>
                        <path d="M24 12l2 4h4l-3 3 1 4-4-2-4 2 1-4-3-3h4l2-4z" fill="#fff" stroke="#B8860B" stroke-width="0.8"/>
                        <path d="M16 38c2-3 5-4 8-4s6 1 8 4" stroke="#E8A0A0" stroke-width="2" stroke-linecap="round" fill="none"/>
                    </svg>
                </div>
            </div>
        </div>
    @endif

    <div class="p-6 {{ $featuredBest ? 'pt-2' : '' }}">
        <div class="flex items-center gap-3 mb-4">
            @php
                $answerInitial = mb_substr($answer->author_name ?? 'U', 0, 1);
            @endphp
            @if(!empty($answer->author_icon_url))
                <img src="{{ $answer->author_icon_url }}" alt="" class="w-10 h-10 rounded-full object-cover">
            @else
                <div class="w-10 h-10 rounded-full bg-[#E5E7EB] flex items-center justify-center text-[#374151] font-bold">
                    {{ $answerInitial }}
                </div>
            @endif
            <div>
                <div class="font-medium text-sm text-blue-600">{{ $answer->author_name }}さん</div>
                <div class="text-xs text-gray-500">{{ $answer->created_at?->format('Y/m/d H:i') }}</div>
            </div>
        </div>

        <div class="prose max-w-none mb-4">
            <p class="text-gray-800 leading-relaxed whitespace-pre-wrap">{{ $answer->content }}</p>
        </div>

        @if(! $featuredBest && $canChooseBest)
            <div class="flex flex-wrap justify-end gap-3">
                <form action="{{ route('questions.answers.best', ['question' => $question->id, 'answer' => $answer->id]) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-sm font-semibold rounded-lg bg-white border border-gray-300 text-gray-800 hover:bg-gray-50 transition-colors">
                        ベストアンサーにする
                    </button>
                </form>
            </div>
        @endif
    </div>

    @if($answer->comments && $answer->comments->count() > 0)
        <div class="bg-white px-6 py-4 border-t border-gray-100">
            <h4 class="text-sm font-semibold text-gray-700 mb-3">この回答へのコメント</h4>
            <div class="space-y-3">
                @foreach($answer->comments as $comment)
                    @php
                        $isCommentByQuestioner = (int) $comment->user_id === (int) $question->user_id;
                        $commentLabel = $isCommentByQuestioner ? '質問者' : '回答者';
                        // 返信（コメント）の背景が薄青/薄緑になるのを避け、常に白に統一
                        $commentBgClass = $isCommentByQuestioner ? 'bg-white border-gray-200' : 'bg-white border-gray-200';
                    @endphp
                    <div class="flex gap-3 {{ $commentBgClass }} rounded-lg p-3 border">
                        @php
                            $commentInitial = mb_substr($comment->author_name ?? 'U', 0, 1);
                        @endphp
                        @if(!empty($comment->author_icon_url))
                            <img src="{{ $comment->author_icon_url }}" alt="" class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                        @else
                            <div class="w-8 h-8 rounded-full bg-[#E5E7EB] flex items-center justify-center text-[#374151] font-bold flex-shrink-0">
                                {{ $commentInitial }}
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-medium text-sm text-gray-900">{{ $comment->author_name }}</span>
                                <span class="text-xs px-2 py-0.5 rounded-full bg-white text-gray-700 border border-gray-200">{{ $commentLabel }}</span>
                                <span class="text-xs text-gray-500">{{ $comment->created_at?->format('Y/m/d H:i') }}</span>
                            </div>
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $comment->content }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($canComment)
        <div class="bg-white px-6 py-4 border-t border-gray-100">
            <form action="{{ route('questions.answers.comments.store', ['question' => $question->id, 'answer' => $answer->id]) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        @if($isQuestioner)
                            回答者へのコメント（質問者）
                        @else
                            質問者への返信（回答者）
                        @endif
                    </label>
                    <textarea name="content" rows="3" maxlength="2000"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm"
                        placeholder="コメントを入力してください">{{ old('content') }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg font-semibold text-sm shadow hover:shadow-md transition-all">
                        コメントを投稿
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
