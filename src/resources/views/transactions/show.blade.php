@extends('layouts.public')

@section('title', '取引チャット')

@push('styles')
    @include('partials.pscc-chat-core-styles')
    <style>
        /* 評価モーダル */
        .pscc-modal-overlay {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 50;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.5);
        }

        .pscc-modal-overlay.active {
            display: flex;
        }

        .pscc-modal {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            max-width: 28rem;
            width: 100%;
            padding: 2rem;
            position: relative;
        }

        .pscc-modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            padding: 0.5rem;
            color: #9CA3AF;
            background: none;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .pscc-modal-close:hover {
            color: #4B5563;
            background: #F3F4F6;
        }

        .pscc-modal-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .pscc-modal-icon-box {
            display: inline-block;
            padding: 0.75rem;
            background: #FFEDD5;
            border-radius: 9999px;
            margin-bottom: 1rem;
        }

        .pscc-modal-icon {
            width: 2rem;
            height: 2rem;
            color: #F97316;
        }

        .pscc-modal-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #111827;
            margin-bottom: 0.5rem;
        }

        .pscc-modal-subtitle {
            color: #4B5563;
        }

        .pscc-rating-stars {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .pscc-star-button {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            transition: transform 0.2s;
        }

        .pscc-star-button:hover {
            transform: scale(1.1);
        }

        .pscc-star {
            width: 2.5rem;
            height: 2.5rem;
        }

        .pscc-star-filled {
            color: #FB923C;
            fill: #FB923C;
        }

        .pscc-star-empty {
            color: #D1D5DB;
        }

        .pscc-review-section {
            margin-bottom: 1.5rem;
        }

        .pscc-review-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .pscc-review-textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #D1D5DB;
            border-radius: 0.5rem;
            font-size: 1rem;
            resize: none;
            outline: none;
            transition: all 0.2s;
            font-family: inherit;
        }

        .pscc-review-textarea:focus {
            border-color: #F97316;
            box-shadow: 0 0 0 2px rgba(249, 115, 22, 0.2);
        }

        .pscc-submit-button {
            width: 100%;
            padding: 0.75rem 1.5rem;
            background: #F97316;
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .pscc-submit-button:hover {
            background: #EA580C;
        }

        .pscc-submit-button:disabled {
            background: #D1D5DB;
            cursor: not-allowed;
        }
    </style>
@endpush

@section('content')
@php
    $tx = $transaction;
    $listing = $tx->skillListing;
    $seller = $listing?->freelancer;
    $buyer = $tx->buyer;
    $me = auth()->user();
    $meId = $me?->id;

    $sellerName = $seller?->display_name ?? '出品者';
    $buyerName = $buyer?->company?->contact_name
        ?? $buyer?->company?->name
        ?? $buyer?->freelancer?->display_name
        ?? $buyer?->email
        ?? '購入者';

    $counterpartyLabel = $isSeller ? "購入者: {$buyerName}" : "出品者: {$sellerName}";
    $backUrl = $isSeller ? route('sales-performance.index') : route('purchased-skills.index');

    $status = $tx->transaction_status;
    $statusLabel = match ($status) {
        'in_progress' => '取引中',
        'delivered' => '納品済み',
        'completed' => '完了',
        default => '不明',
    };
    $statusClass = match ($status) {
        'in_progress' => 'pscc-status-progress',
        'delivered' => 'pscc-status-delivered',
        'completed' => 'pscc-status-completed',
        default => 'pscc-status-progress',
    };

    $headerStickyTop = auth('freelancer')->check()
        ? 'calc(var(--public-header-height) + var(--freelancer-header-height))'
        : 'var(--public-header-height)';
@endphp

<div class="pscc-container">
    @include('partials.error-panel')
    @if (session('success') || session('error'))
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
            @if (session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800 mt-2">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800 mt-2">
                    {{ session('error') }}
                </div>
            @endif
        </div>
    @endif

    <!-- 取引ヘッダー（画面内ヘッダー） -->
    <div class="pscc-header" style="top: {{ $headerStickyTop }};">
        <div class="pscc-header-content">
            <a class="pscc-back-button" href="{{ $backUrl }}" aria-label="戻る">
                <svg class="pscc-back-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>

            @php $thumb = $listing?->thumbnail_url; @endphp
            @if (!empty($thumb))
                <img src="{{ $thumb }}" alt="{{ $listing?->title ?? 'スキル' }}" class="pscc-skill-image">
            @else
                <div class="pscc-skill-image" style="background:#E5E7EB; display:flex; align-items:center; justify-content:center; color:#6B7280; font-size:12px;">
                    No Image
                </div>
            @endif

            <div class="pscc-header-info">
                <h1 class="pscc-skill-title">{{ $listing?->title ?? '（削除されたスキル）' }}</h1>
                <div class="pscc-header-meta">
                    <div class="pscc-meta-item">
                        <svg class="pscc-meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>{{ $counterpartyLabel }}</span>
                    </div>
                    <div class="pscc-meta-item">
                        <svg class="pscc-meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>¥{{ number_format((int) ($tx->amount ?? 0)) }}</span>
                    </div>
                </div>
            </div>

            <span class="pscc-status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
        </div>
    </div>

    <!-- チャットエリア -->
    <div class="pscc-chat-area">
        <div class="pscc-chat-content">
            <div class="pscc-messages">
                @forelse (($messages ?? collect()) as $msg)
                    @if ($msg->message_type === 'system')
                        <div class="pscc-message-system">
                            <div class="pscc-system-bubble">
                                <p class="pscc-system-text">{{ $msg->body }}</p>
                                <p class="pscc-system-time">{{ $msg->sent_at?->format('Y/n/j H:i:s') }}</p>
                            </div>
                        </div>
                    @else
                        @php
                            $isOwn = (int) ($msg->sender_user_id ?? 0) === (int) ($meId ?? 0);
                            $sender = $msg->sender;
                            $senderName = $sender?->company?->contact_name
                                ?? $sender?->company?->name
                                ?? $sender?->freelancer?->display_name
                                ?? $sender?->email
                                ?? 'ユーザー';
                            $senderInitial = mb_substr($senderName, 0, 1);
                            $senderIcon = $sender?->company?->icon_path ?? $sender?->freelancer?->icon_path;
                            $avatarSrc = null;
                            if (!empty($senderIcon)) {
                                if (str_starts_with($senderIcon, 'http://') || str_starts_with($senderIcon, 'https://')) {
                                    $avatarSrc = $senderIcon;
                                } else {
                                    $iconRel = ltrim($senderIcon, '/');
                                    if (str_starts_with($iconRel, 'storage/')) {
                                        $iconRel = substr($iconRel, strlen('storage/'));
                                    }
                                    $avatarSrc = asset('storage/' . $iconRel);
                                }
                            }
                        @endphp

                        <div class="pscc-message">
                            @if ($avatarSrc)
                                <img src="{{ $avatarSrc }}" alt="{{ $senderName }}" class="pscc-avatar">
                            @else
                                <div class="pscc-avatar-initial" style="background:#E5E7EB; color:#374151;">{{ $senderInitial }}</div>
                            @endif
                            <div class="pscc-message-card">
                                <div class="pscc-message-card-header">
                                    <div class="pscc-message-card-header-left">
                                        <div class="pscc-message-meta">
                                            <span class="pscc-sender-name">{{ $senderName }}</span>
                                            <div class="pscc-message-time-row">
                                                <span class="pscc-message-time">{{ $msg->sent_at?->format('Y-m-d H:i:s') }}</span>
                                                @if ($isOwn)
                                                    <span class="pscc-read-status">既読</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @if ($isOwn)
                                        <button type="button" class="pscc-message-options" aria-label="メッセージオプション" title="オプション">
                                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                                <circle cx="12" cy="6" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="18" r="1.5"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                                <p class="pscc-message-body">{{ $msg->body }}</p>
                                <!-- reactions removed -->
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="pscc-message-system">
                        <div class="pscc-system-bubble">
                            <p class="pscc-system-text">まだメッセージはありません。</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- アクションボタンエリア -->
    @if ($isSeller && $status === 'in_progress')
        <div class="pscc-action-area">
            <div class="pscc-action-content">
                <form method="POST" action="{{ route('transactions.deliver', ['skill_order' => $tx->id]) }}" onsubmit="return confirm('納品します。よろしいですか？');">
                    @csrf
                    <button class="pscc-deliver-button" type="submit">
                        <svg class="pscc-button-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6m16 0H4"/>
                        </svg>
                        納品する
                    </button>
                </form>
            </div>
        </div>
    @elseif (!$isSeller && $status === 'delivered')
        <div class="pscc-action-area">
            <div class="pscc-action-content">
                <button class="pscc-approve-button" onclick="showModal()" type="button">
                    <svg class="pscc-button-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    承認する
                </button>
            </div>
        </div>
    @endif

    <!-- メッセージ入力エリア（取引完了まで） -->
    @if ($status !== 'completed')
        @php
            $meName = $me?->company?->contact_name
                ?? $me?->company?->name
                ?? $me?->freelancer?->display_name
                ?? $me?->email
                ?? 'ユーザー';
            $meInitial = mb_substr($meName, 0, 1);
            $meIcon = $me?->company?->icon_path ?? $me?->freelancer?->icon_path;
            $meAvatarSrc = null;
            if (!empty($meIcon)) {
                if (str_starts_with($meIcon, 'http://') || str_starts_with($meIcon, 'https://')) {
                    $meAvatarSrc = $meIcon;
                } else {
                    $iconRel = ltrim($meIcon, '/');
                    if (str_starts_with($iconRel, 'storage/')) {
                        $iconRel = substr($iconRel, strlen('storage/'));
                    }
                    $meAvatarSrc = asset('storage/' . $iconRel);
                }
            }
        @endphp
        <div class="pscc-input-area">
            <form class="pscc-input-content" method="POST" action="{{ route('transactions.messages.store', ['skill_order' => $tx->id]) }}">
                @csrf
                @if ($meAvatarSrc)
                    <img src="{{ $meAvatarSrc }}" alt="{{ $meName }}" class="pscc-input-avatar">
                @else
                    <div class="pscc-input-avatar-initial" aria-hidden="true">{{ $meInitial }}</div>
                @endif
                <button class="pscc-attach-button" title="ファイル添付（未実装）" type="button" disabled aria-disabled="true">
                    <svg class="pscc-attach-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                    </svg>
                </button>
                <input type="text" class="pscc-input-field @error('content') pscc-input-error @enderror" placeholder="メッセージを入力..." id="messageInput" name="content" value="{{ old('content') }}">
                <button class="pscc-send-button" type="submit" id="sendButton" disabled>
                    <svg class="pscc-button-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    送信
                </button>
            </form>
            @error('content')
                <div class="pscc-field-error"><span>{{ $message }}</span></div>
            @enderror
        </div>
    @endif

    <!-- 評価モーダル（購入者×納品済み） -->
    @if (!$isSeller && $status === 'delivered')
        <div class="pscc-modal-overlay" id="ratingModal">
            <div class="pscc-modal">
                <button class="pscc-modal-close" onclick="hideModal()" type="button" aria-label="閉じる">
                    <svg class="pscc-button-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <form method="POST" action="{{ route('transactions.complete', ['skill_order' => $tx->id]) }}" onsubmit="return validateRating();">
                    @csrf
                    <input type="hidden" name="rating" id="ratingInput" value="{{ old('rating') }}">

                    <div class="pscc-modal-header">
                        <div class="pscc-modal-icon-box">
                            <svg class="pscc-modal-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        </div>
                        <h2 class="pscc-modal-title">取引を評価</h2>
                        <p class="pscc-modal-subtitle">出品者の対応について評価してください</p>
                    </div>

                    @error('rating')
                        <p class="pscc-field-error" style="margin-bottom:1rem;">{{ $message }}</p>
                    @enderror
                    <div class="pscc-rating-stars">
                        @for ($i = 1; $i <= 5; $i++)
                            <button class="pscc-star-button" onclick="setRating({{ $i }})" type="button" aria-label="{{ $i }}つ星">
                                <svg class="pscc-star pscc-star-empty" id="star{{ $i }}" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </button>
                        @endfor
                    </div>

                    <div class="pscc-review-section">
                        <label class="pscc-review-label" for="reviewText">コメント（任意）</label>
                        <textarea class="pscc-review-textarea" rows="4" placeholder="取引の感想を入力してください..." id="reviewText" name="review">{{ old('review') }}</textarea>
                        @error('review')
                            <p class="pscc-field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <button class="pscc-submit-button" id="submitButton" disabled type="submit">
                        評価を送信
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    (function () {
        const input = document.getElementById('messageInput');
        const sendBtn = document.getElementById('sendButton');
        if (!input || !sendBtn) return;
        const toggle = () => {
            sendBtn.disabled = !input.value || !input.value.trim();
        };
        input.addEventListener('input', toggle);
        toggle();
    })();

    function showModal() {
        const el = document.getElementById('ratingModal');
        if (el) el.classList.add('active');
    }

    function hideModal() {
        const el = document.getElementById('ratingModal');
        if (el) el.classList.remove('active');
    }

    function setRating(rating) {
        const ratingInput = document.getElementById('ratingInput');
        if (ratingInput) ratingInput.value = String(rating);

        for (let i = 1; i <= 5; i++) {
            const star = document.getElementById('star' + i);
            if (!star) continue;
            if (i <= rating) {
                star.classList.remove('pscc-star-empty');
                star.classList.add('pscc-star-filled');
            } else {
                star.classList.remove('pscc-star-filled');
                star.classList.add('pscc-star-empty');
            }
        }
        const btn = document.getElementById('submitButton');
        if (btn) btn.disabled = false;
    }

    function validateRating() {
        const ratingInput = document.getElementById('ratingInput');
        const v = ratingInput ? parseInt(ratingInput.value || '0', 10) : 0;
        if (!v) {
            alert('評価（星）を選択してください');
            return false;
        }
        return true;
    }
</script>
@endpush
@endsection
