@extends('layouts.public')

@section('title', '企業 チャット')

@auth('company')
    @push('styles')
        @include('partials.company-header-style')
    @endpush
@endauth

@push('styles')
    @include('partials.pscc-chat-core-styles')
@endpush

@section('content')
@php
    // 企業視点：相手はフリーランスまたは別の企業
    $counterpart = $conversation->freelancer;
    $counterpartName = $conversation->freelancer?->display_name ?? 'フリーランス';
    $counterpartRole = 'フリーランス';
    $counterpartIcon = $conversation->freelancer?->icon_path ?? null;
    $counterpartUserId = $conversation->freelancer?->user_id;

    if ($conversation->freelancer_id === null && $conversation->initiator_type === 'company') {
        if ($conversation->initiator_id !== $viewerProfile->id) {
            $counterpartCompany = \App\Models\Company::find($conversation->initiator_id);
            $counterpartName = $counterpartCompany?->name ?? '企業';
            $counterpartRole = '企業';
            $counterpartIcon = $counterpartCompany?->icon_path;
            $counterpartUserId = $counterpartCompany?->user_id;
        } else {
            $counterpartCompany = \App\Models\Company::find($conversation->company_id);
            $counterpartName = $counterpartCompany?->name ?? '企業';
            $counterpartRole = '企業';
            $counterpartIcon = $counterpartCompany?->icon_path;
            $counterpartUserId = $counterpartCompany?->user_id;
        }
    }

    $headerThumbSrc = null;
    if (!empty($counterpartIcon)) {
        if (str_starts_with($counterpartIcon, 'http://') || str_starts_with($counterpartIcon, 'https://')) {
            $headerThumbSrc = $counterpartIcon;
        } else {
            $iconRel = ltrim($counterpartIcon, '/');
            if (str_starts_with($iconRel, 'storage/')) {
                $iconRel = substr($iconRel, strlen('storage/'));
            }
            $headerThumbSrc = asset('storage/' . $iconRel);
        }
    }

    $messages = ($messages ?? collect())->whereNull('deleted_at')->sortBy('sent_at')->values();
    $viewerName = $viewerProfile?->name ?? '企業';
    $viewerInitial = mb_substr($viewerName, 0, 1);

    $meIcon = $viewerProfile->icon_path ?? null;
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

    $headerStickyTop = 'var(--public-header-height)';
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

    <div class="pscc-header" style="top: {{ $headerStickyTop }};">
        <div class="pscc-header-content">
            <a class="pscc-back-button" href="{{ route('direct-messages.index') }}" aria-label="戻る">
                <svg class="pscc-back-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>

            @if (!empty($headerThumbSrc))
                <img src="{{ $headerThumbSrc }}" alt="{{ $counterpartName }}" class="pscc-skill-image">
            @else
                <div class="pscc-skill-image" style="background:#E5E7EB; display:flex; align-items:center; justify-content:center; color:#6B7280; font-size:12px; font-weight:600;">
                    {{ mb_substr($counterpartName, 0, 1) }}
                </div>
            @endif

            <div class="pscc-header-info">
                <h1 class="pscc-skill-title">{{ $counterpartName }}</h1>
                <div class="pscc-header-meta">
                    <div class="pscc-meta-item">
                        <svg class="pscc-meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>{{ $counterpartRole }}</span>
                    </div>
                    @if ($counterpartUserId)
                        <div class="pscc-meta-item">
                            <a href="{{ route('profiles.show', $counterpartUserId) }}" class="text-blue-600 hover:underline font-medium">プロフィールを見る</a>
                        </div>
                    @endif
                    <div class="pscc-meta-item">
                        <svg class="pscc-meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ $conversation->latest_message_at?->format('Y/m/d H:i') ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <span class="pscc-status-badge pscc-status-progress">ダイレクト</span>
        </div>
    </div>

    <div class="pscc-chat-area" id="dmChatArea">
        <div class="pscc-chat-content">
            <div class="pscc-messages">
                @forelse ($messages as $message)
                    @php
                        $isMe = $message->sender_type === 'company' && $message->sender_id === $viewerProfile->id;
                        $msgAvatarSrc = null;

                        if ($message->sender_type === 'company') {
                            $senderCompany = $message->sender_id === $viewerProfile->id
                                ? $viewerProfile
                                : \App\Models\Company::find($message->sender_id);
                            $senderName = $senderCompany?->name ?? '企業';
                            $iconPath = $senderCompany?->icon_path;
                        } else {
                            $senderFr = \App\Models\Freelancer::find($message->sender_id);
                            $senderName = $senderFr?->display_name ?? 'フリーランス';
                            $iconPath = $senderFr?->icon_path;
                        }

                        if (!empty($iconPath)) {
                            if (str_starts_with($iconPath, 'http://') || str_starts_with($iconPath, 'https://')) {
                                $msgAvatarSrc = $iconPath;
                            } else {
                                $iconRel = ltrim($iconPath, '/');
                                if (str_starts_with($iconRel, 'storage/')) {
                                    $iconRel = substr($iconRel, strlen('storage/'));
                                }
                                $msgAvatarSrc = asset('storage/' . $iconRel);
                            }
                        }

                        $senderInitial = mb_substr($senderName, 0, 1);
                    @endphp

                    <div class="pscc-message">
                        @if ($msgAvatarSrc)
                            <img src="{{ $msgAvatarSrc }}" alt="{{ $senderName }}" class="pscc-avatar">
                        @else
                            <div class="pscc-avatar-initial" style="background:#E5E7EB; color:#374151;">{{ $senderInitial }}</div>
                        @endif
                        <div class="pscc-message-card">
                            <div class="pscc-message-card-header">
                                <div class="pscc-message-card-header-left">
                                    <div class="pscc-message-meta">
                                        <span class="pscc-sender-name">{{ $senderName }}</span>
                                        <div class="pscc-message-time-row">
                                            <span class="pscc-message-time">{{ $message->sent_at?->format('Y-m-d H:i:s') }}</span>
                                            @if ($isMe)
                                                <span class="pscc-read-status">既読</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if ($isMe)
                                    <button type="button" class="pscc-message-options" aria-label="メッセージオプション" title="オプション">
                                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                            <circle cx="12" cy="6" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="18" r="1.5"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                            <p class="pscc-message-body">{{ $message->body }}</p>
                        </div>
                    </div>
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

    <div class="pscc-input-area">
        <form class="pscc-input-content" method="POST" action="{{ route('direct-messages.reply', ['direct_conversation' => $conversation->id]) }}">
            @csrf
            @if ($meAvatarSrc)
                <img src="{{ $meAvatarSrc }}" alt="{{ $viewerName }}" class="pscc-input-avatar">
            @else
                <div class="pscc-input-avatar-initial" aria-hidden="true">{{ $viewerInitial }}</div>
            @endif
            <button class="pscc-attach-button" title="ファイル添付（未実装）" type="button" disabled aria-disabled="true">
                <svg class="pscc-attach-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                </svg>
            </button>
            <input
                type="text"
                class="pscc-input-field @error('content') pscc-input-error @enderror"
                placeholder="メッセージを入力..."
                id="messageInput"
                name="content"
                value="{{ old('content') }}"
                autocomplete="off"
            >
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
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const chatArea = document.getElementById('dmChatArea');
        if (chatArea) {
            chatArea.scrollTop = chatArea.scrollHeight;
        }

        const input = document.getElementById('messageInput');
        const sendBtn = document.getElementById('sendButton');
        if (!input || !sendBtn) return;
        const toggle = () => {
            sendBtn.disabled = !input.value || !input.value.trim();
        };
        input.addEventListener('input', toggle);
        toggle();
    })();
</script>
@endpush
