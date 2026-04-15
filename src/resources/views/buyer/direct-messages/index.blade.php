<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>購入者 メッセージ一覧 - AIスキルマッチ</title>
    <link rel="icon" href="{{ asset('aifavicon.png') }}">
    @include('partials.freelancer-header-style')
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .profiles-pagination-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
        }

        .profiles-pagination-bar a,
        .profiles-pagination-bar span.profiles-page-ellipsis {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2.5rem;
            height: 2.5rem;
            padding: 0 0.35rem;
            border-radius: 0.35rem;
            font-size: 0.875rem;
            font-weight: 600;
            line-height: 1;
        }

        .profiles-pagination-bar a.profiles-page-link {
            background-color: #f3f4f6;
            color: #111827;
            transition: background-color 0.15s ease, color 0.15s ease;
        }

        .profiles-pagination-bar a.profiles-page-link:hover:not(.profiles-page-active) {
            background-color: #e5e7eb;
        }

        .profiles-pagination-bar a.profiles-page-active {
            background-color: #FC4C0C;
            color: #fff;
            pointer-events: none;
            cursor: default;
        }

        .profiles-pagination-bar span.profiles-page-ellipsis {
            min-width: 1.75rem;
            color: #6b7280;
            font-weight: 600;
            user-select: none;
        }

        .profiles-pagination-bar a.profiles-page-nav {
            background-color: #f3f4f6;
            color: #92400e;
        }

        .profiles-pagination-bar a.profiles-page-nav:hover:not(.profiles-page-nav-disabled) {
            background-color: #e5e7eb;
        }

        .profiles-pagination-bar span.profiles-page-nav-disabled {
            background-color: #f3f4f6;
            color: #d1d5db;
            cursor: not-allowed;
            user-select: none;
        }
    </style>
</head>
<body>
    @include('partials.public-header')

    <main class="pt-24">
        <div class="max-w-[900px] mx-auto px-4 py-8">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">メッセージ</h1>
                <p class="text-gray-600">購入者としてフリーランスとのやり取りを一覧で確認できます。</p>
            </div>

            @if (session('success') || session('error'))
                <div class="mb-4 space-y-2">
                    @if (session('success'))
                        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800">{{ session('error') }}</div>
                    @endif
                </div>
            @endif

            @php $isUnreadFilter = ($filter ?? '') === 'unread'; @endphp

            <div class="flex gap-3 mb-6 flex-wrap">
                <a class="inline-flex items-center gap-2 px-4 py-2 rounded-full font-semibold border {{ !$isUnreadFilter ? 'bg-orange-500 text-white border-orange-500' : 'bg-white text-gray-700 border-gray-300' }}"
                   href="{{ route('buyer.direct-messages.index', ['filter' => 'all']) }}">
                    すべて
                    <span class="inline-flex items-center justify-center min-w-6 h-6 px-1.5 rounded-full text-xs {{ !$isUnreadFilter ? 'bg-white/20' : 'bg-gray-100' }}">{{ $allCount ?? 0 }}</span>
                </a>
                <a class="inline-flex items-center gap-2 px-4 py-2 rounded-full font-semibold border {{ $isUnreadFilter ? 'bg-orange-500 text-white border-orange-500' : 'bg-white text-gray-700 border-gray-300' }}"
                   href="{{ route('buyer.direct-messages.index', ['filter' => 'unread']) }}">
                    未読
                    <span class="inline-flex items-center justify-center min-w-6 h-6 px-1.5 rounded-full text-xs {{ $isUnreadFilter ? 'bg-white/20' : 'bg-gray-100' }}">{{ $unreadCount ?? 0 }}</span>
                </a>
            </div>

            @if($conversations instanceof \Illuminate\Pagination\AbstractPaginator)
                <p class="text-sm text-gray-600 mb-4">
                    {{ number_format($conversations->total()) }} 件中
                    {{ number_format($conversations->firstItem()) }} - {{ number_format($conversations->lastItem()) }}
                    件表示
                </p>
            @endif

            @if(($conversations ?? collect())->isNotEmpty())
                <div class="grid gap-4">
                    @foreach($conversations as $conversation)
                        @php
                            $viewerId = (int) ($viewerProfile->id ?? 0);

                            $counterpart = $conversation->freelancer;
                            $counterpartName = $counterpart?->display_name ?? 'フリーランス';
                            $counterpartRole = 'フリーランス';
                            $counterpartIcon = $counterpart?->icon_path ?? null;

                            $avatarSrc = null;
                            if (!empty($counterpartIcon)) {
                                if (str_starts_with($counterpartIcon, 'http://') || str_starts_with($counterpartIcon, 'https://')) {
                                    $avatarSrc = $counterpartIcon;
                                } else {
                                    $iconRel = ltrim($counterpartIcon, '/');
                                    if (str_starts_with($iconRel, 'storage/')) {
                                        $iconRel = substr($iconRel, strlen('storage/'));
                                    }
                                    $avatarSrc = asset('storage/' . $iconRel);
                                }
                            }

                            $latestMessage = $conversation->messages->last();
                            $preview = $latestMessage?->body ?? 'メッセージはまだありません。';
                            $sentAt = $conversation->latest_message_at?->format('Y/m/d H:i') ?? '-';

                            $isLatestMessageFromSelf = $latestMessage
                                && $latestMessage->sender_type === 'buyer'
                                && (int)$latestMessage->sender_id === (int)($viewerId ?? 0);
                            $latestMessageSenderLabel = $latestMessage ? ($isLatestMessageFromSelf ? '自分' : '相手') : null;

                            $isUnread = (bool) ($conversation->is_unread_for_buyer ?? false);
                        @endphp

                        <a href="{{ route('buyer.direct-messages.show', ['direct_conversation' => $conversation->id]) }}"
                           class="block bg-white border border-gray-200 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-orange-300 transition-all">
                            <div class="flex justify-between gap-4 items-start mb-3">
                                <div class="flex gap-3 items-center min-w-0">
                                    @if($avatarSrc)
                                        <img src="{{ $avatarSrc }}" alt="{{ $counterpartName }}" class="w-12 h-12 rounded-full object-cover flex-shrink-0">
                                    @else
                                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center font-bold flex-shrink-0">
                                            {{ mb_substr($counterpartName, 0, 1) }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <div class="font-bold text-gray-900 truncate">{{ $counterpartName }}</div>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold border {{ $isUnread ? 'bg-orange-50 text-orange-700 border-orange-200' : 'bg-gray-50 text-gray-600 border-gray-200' }}">
                                    {{ $isUnread ? '未読' : '既読' }}
                                </span>
                            </div>

                            <div class="flex gap-2 mb-3 items-start">
                                @if($latestMessageSenderLabel)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border {{ $isLatestMessageFromSelf ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-green-50 text-green-700 border-green-200' }}">
                                        {{ $latestMessageSenderLabel }}
                                    </span>
                                @endif
                                <p class="text-gray-600 line-clamp-2">{{ $preview }}</p>
                            </div>

                            <div class="flex justify-between items-center text-sm text-gray-500 pt-3 border-t border-gray-100">
                                <span>最終更新: {{ $sentAt }}</span>
                                <span class="text-orange-600 font-medium">チャットを開く →</span>
                            </div>
                        </a>
                    @endforeach
                </div>

                @if($conversations instanceof \Illuminate\Pagination\AbstractPaginator)
                    @php
                        $pLast = $conversations->lastPage();
                        $pCur = $conversations->currentPage();
                        $profilePaginationElements = [];

                        if ($pLast <= 1) {
                            if ($pLast === 1) {
                                $profilePaginationElements[] = ['type' => 'page', 'n' => 1];
                            }
                        } elseif ($pLast <= 15) {
                            for ($n = 1; $n <= $pLast; $n++) {
                                $profilePaginationElements[] = ['type' => 'page', 'n' => $n];
                            }
                        } elseif ($pCur <= 7) {
                            $upto = min(13, $pLast);
                            for ($n = 1; $n <= $upto; $n++) {
                                $profilePaginationElements[] = ['type' => 'page', 'n' => $n];
                            }
                            if ($upto < $pLast) {
                                $profilePaginationElements[] = ['type' => 'ellipsis'];
                                $profilePaginationElements[] = ['type' => 'page', 'n' => $pLast];
                            }
                        } elseif ($pCur >= $pLast - 6) {
                            $profilePaginationElements[] = ['type' => 'page', 'n' => 1];
                            $profilePaginationElements[] = ['type' => 'ellipsis'];
                            $from = max(2, $pLast - 12);
                            for ($n = $from; $n <= $pLast; $n++) {
                                $profilePaginationElements[] = ['type' => 'page', 'n' => $n];
                            }
                        } else {
                            $profilePaginationElements[] = ['type' => 'page', 'n' => 1];
                            $profilePaginationElements[] = ['type' => 'ellipsis'];
                            $from = max(2, $pCur - 6);
                            $to = min($pLast - 1, $pCur + 6);
                            for ($n = $from; $n <= $to; $n++) {
                                $profilePaginationElements[] = ['type' => 'page', 'n' => $n];
                            }
                            if ($to < $pLast) {
                                if ($to + 1 < $pLast) {
                                    $profilePaginationElements[] = ['type' => 'ellipsis'];
                                }
                                $profilePaginationElements[] = ['type' => 'page', 'n' => $pLast];
                            }
                        }
                    @endphp

                    @if($pLast >= 1 && count($profilePaginationElements) > 0)
                        <nav class="profiles-pagination-bar mt-8" aria-label="ページ送り">
                            @if($conversations->onFirstPage())
                                <span class="profiles-page-nav profiles-page-nav-disabled" aria-disabled="true">&lt;</span>
                            @else
                                <a href="{{ $conversations->previousPageUrl() }}" class="profiles-page-nav" rel="prev" aria-label="前のページ">&lt;</a>
                            @endif

                            @foreach($profilePaginationElements as $el)
                                @if($el['type'] === 'ellipsis')
                                    <span class="profiles-page-ellipsis" aria-hidden="true">...</span>
                                @else
                                    @if($el['n'] === $pCur)
                                        <span class="profiles-page-link profiles-page-active">{{ $el['n'] }}</span>
                                    @else
                                        <a href="{{ $conversations->url($el['n']) }}" class="profiles-page-link">{{ $el['n'] }}</a>
                                    @endif
                                @endif
                            @endforeach

                            @if($conversations->hasMorePages())
                                <a href="{{ $conversations->nextPageUrl() }}" class="profiles-page-nav" rel="next" aria-label="次のページ">&gt;</a>
                            @else
                                <span class="profiles-page-nav profiles-page-nav-disabled" aria-disabled="true">&gt;</span>
                            @endif
                        </nav>
                    @endif
                @endif
            @else
                <div class="bg-white border border-gray-200 rounded-xl p-8 text-center text-gray-500">
                    <p class="font-semibold text-lg">
                        {{ $isUnreadFilter ? '未読メッセージはありません' : 'メッセージはまだありません' }}
                    </p>
                    <p class="text-sm mt-2">フリーランスのプロフィールからメッセージを送信できます。</p>
                </div>
            @endif
        </div>
    </main>
</body>
</html>

