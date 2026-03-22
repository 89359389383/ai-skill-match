@extends('layouts.public')

@section('title', 'メッセージ')

@push('styles')
<style>
    .dm-chat {
        max-width: 72rem;
        margin: 0 auto;
        padding: 1.5rem 1rem 2rem;
    }

    .dm-panel {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .dm-chat-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    }

    .dm-chat-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        min-width: 0;
    }

    .dm-back {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #374151;
        text-decoration: none;
        flex-shrink: 0;
    }

    .dm-counterpart {
        min-width: 0;
    }

    .dm-counterpart-name {
        font-size: 1.125rem;
        font-weight: 900;
        color: #111827;
        margin-bottom: 0.125rem;
    }

    .dm-counterpart-meta {
        color: #6b7280;
        font-size: 0.875rem;
    }

    .dm-counterpart-link {
        color: #2563eb;
        text-decoration: none;
        font-weight: 700;
    }

    .dm-counterpart-link:hover {
        text-decoration: underline;
    }

    .dm-messages {
        padding: 1.25rem 1rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        max-height: min(68vh, 760px);
        overflow-y: auto;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    }

    .dm-message {
        display: flex;
        gap: 0.75rem;
        align-items: flex-end;
    }

    .dm-message.me {
        justify-content: flex-end;
    }

    .dm-avatar {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 9999px;
        object-fit: cover;
        flex-shrink: 0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
    }

    .dm-bubble {
        max-width: 74%;
        border-radius: 1rem;
        padding: 0.9rem 1rem;
        border: 1px solid #dbe2ee;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
        overflow-wrap: anywhere;
    }

    .dm-bubble.me {
        background: linear-gradient(180deg, #eff6ff 0%, #dbeafe 100%);
        border-color: #bfdbfe;
    }

    .dm-sender {
        font-size: 0.9375rem;
        font-weight: 800;
        color: #111827;
        margin-bottom: 0.35rem;
    }

    .dm-body {
        color: #1f2937;
        line-height: 1.7;
        white-space: pre-wrap;
        word-break: break-word;
    }

    .dm-time {
        margin-top: 0.4rem;
        color: #6b7280;
        font-size: 0.8125rem;
        font-weight: 700;
        text-align: right;
    }

    .dm-compose {
        border-top: 1px solid #e5e7eb;
        padding: 1rem;
        background: #ffffff;
    }

    .dm-form {
        display: grid;
        gap: 0.75rem;
    }

    .dm-textarea {
        width: 100%;
        min-height: 7rem;
        border-radius: 0.9rem;
        border: 1px solid #cbd5e1;
        padding: 0.9rem 1rem;
        font-size: 1rem;
        line-height: 1.7;
        resize: vertical;
        outline: none;
    }

    .dm-textarea:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.14);
    }

    .dm-textarea.is-invalid {
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.10);
    }

    .dm-error {
        color: #dc2626;
        font-size: 0.875rem;
        font-weight: 800;
    }

    .dm-send {
        justify-self: end;
        padding: 0.85rem 1.75rem;
        border: none;
        border-radius: 0.9rem;
        background: linear-gradient(180deg, #f97316 0%, #ea580c 100%);
        color: #ffffff;
        font-weight: 900;
        cursor: pointer;
    }

    .dm-send:disabled {
        background: #cbd5e1;
        cursor: not-allowed;
    }

    .dm-empty {
        padding: 2rem;
        text-align: center;
        color: #6b7280;
    }
</style>
@endpush

@section('content')
@php
    $counterpart = $viewerRole === 'company' ? $conversation->freelancer : $conversation->company;
    $counterpartName = $viewerRole === 'company'
        ? ($conversation->freelancer?->display_name ?? 'フリーランス')
        : ($conversation->company?->contact_name
            ?? $conversation->company?->name
            ?? '企業');
    $counterpartRole = $viewerRole === 'company' ? 'フリーランス' : '企業';
    $counterpartIcon = $viewerRole === 'company'
        ? ($conversation->freelancer?->icon_path ?? null)
        : ($conversation->company?->icon_path ?? null);
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
    $messages = ($messages ?? collect())->whereNull('deleted_at')->sortBy('sent_at')->values();
    $latestMessage = $messages->last();
    $viewerName = $viewerRole === 'company'
        ? ($viewerProfile?->contact_name ?? $viewerProfile?->name ?? '企業')
        : ($viewerProfile?->display_name ?? 'フリーランス');
    $viewerInitial = mb_substr($viewerName, 0, 1);
@endphp

<div class="dm-chat">
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

    <div class="dm-panel">
        <div class="dm-chat-header">
            <div class="dm-chat-title">
                <div class="flex items-center gap-2 min-w-0">
                    <a class="dm-back" href="{{ route('direct-messages.index') }}" aria-label="メッセージ一覧へ戻る">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <a href="{{ route('direct-messages.index') }}"
                       class="text-sm font-bold text-slate-600 hover:text-slate-900 truncate">
                        メッセージ一覧へ戻る
                    </a>
                </div>

                @if($avatarSrc)
                    <img src="{{ $avatarSrc }}" alt="{{ $counterpartName }}" class="dm-avatar">
                @else
                    <div class="dm-avatar">{{ mb_substr($counterpartName, 0, 1) }}</div>
                @endif

                <div class="dm-counterpart">
                    <div class="dm-counterpart-name">{{ $counterpartName }}</div>
                    <div class="dm-counterpart-meta">
                        {{ $counterpartRole }}とのチャット
                        @if($viewerRole === 'company' && $conversation->freelancer)
                            ・<a class="dm-counterpart-link" href="{{ route('profiles.show', $conversation->freelancer->user) }}">プロフィールを見る</a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="text-sm font-bold text-slate-500">
                {{ $conversation->latest_message_at?->format('Y/m/d H:i') ?? '-' }}
            </div>
        </div>

        <div class="dm-messages" id="dmMessages" aria-label="メッセージ一覧">
            @forelse($messages as $message)
                @php
                    $isMe = $message->sender_type === $viewerRole;
                    $sentAt = $message->sent_at?->format('Y/m/d H:i') ?? '';
                    $senderName = $isMe
                        ? $viewerName
                        : ($viewerRole === 'company'
                            ? ($conversation->freelancer?->display_name ?? 'フリーランス')
                            : ($conversation->company?->contact_name
                                ?? $conversation->company?->name
                                ?? '企業'));
                @endphp
                <div class="dm-message {{ $isMe ? 'me' : '' }}">
                    @if(!$isMe)
                        @if($avatarSrc)
                            <img src="{{ $avatarSrc }}" alt="{{ $senderName }}" class="dm-avatar">
                        @else
                            <div class="dm-avatar">{{ mb_substr($senderName, 0, 1) }}</div>
                        @endif
                    @endif

                    <div class="dm-bubble {{ $isMe ? 'me' : '' }}">
                        <div class="dm-sender">{{ $senderName }}</div>
                        <div class="dm-body">{{ $message->body }}</div>
                        <div class="dm-time">{{ $sentAt }}</div>
                    </div>
                </div>
            @empty
                <div class="dm-empty">まだメッセージはありません。</div>
            @endforelse
        </div>

        <div class="dm-compose">
            <form class="dm-form" method="POST" action="{{ route('direct-messages.reply', ['direct_conversation' => $conversation->id]) }}">
                @csrf
                <textarea
                    id="dmContent"
                    name="content"
                    class="dm-textarea @error('content') is-invalid @enderror"
                    placeholder="メッセージを入力..."
                >{{ old('content') }}</textarea>
                @error('content')
                    <div class="dm-error">{{ $message }}</div>
                @enderror
                <button id="dmSendButton" type="submit" class="dm-send" disabled>送信</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const messages = document.getElementById('dmMessages');
        if (messages) {
            messages.scrollTop = messages.scrollHeight;
        }

        const textarea = document.getElementById('dmContent');
        const button = document.getElementById('dmSendButton');
        if (!textarea || !button) return;

        const toggle = () => {
            button.disabled = !textarea.value || !textarea.value.trim();
        };

        textarea.addEventListener('input', toggle);
        toggle();
    })();
</script>
@endpush
