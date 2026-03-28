@extends('layouts.public')

@section('title', 'フリーランス チャット')

@push('styles')
    @include('partials.pscc-chat-core-styles')
    <style>
        /* 全体：メッセージカードの横幅を抑える */
        .pscc-message .pscc-message-card {
            max-width: 80%;
        }

        /* 自分：右寄せ（アイコン＋メッセージ欄セット） */
        .pscc-message.is-me {
            justify-content: flex-end;
        }
        .pscc-message.is-me .pscc-message-card {
            max-width: 80%;
        }
    </style>
@endpush

@section('content')
    @php
        $viewerProfile = $freelancer ?? (auth('freelancer')->user()->freelancer ?? null);
        $viewerId = (int) ($viewerProfile->id ?? 0);

        $counterpartName = $thread->company->contact_name ?? $thread->company->name ?? '企業';
        $counterpartRole = '企業';
        $counterpartIcon = $thread->company?->icon_path ?? null;

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

        $headerStickyTop = 'var(--public-header-height)';

        $jobDetailUrl = $thread->job ? route('freelancer.jobs.show', ['job' => $thread->job->id]) : null;
        $backUrl = $thread->job
            ? route('freelancer.jobs.show', ['job' => $thread->job->id])
            : route('freelancer.applications.index');

        $latestAt = $thread->latest_message_at ?? null;

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
    @endphp

    <div class="pscc-container">
        @include('partials.error-panel')

        <div class="pscc-header" style="top: {{ $headerStickyTop }};">
            <div class="pscc-header-content">
                <a class="pscc-back-button" href="{{ $backUrl }}" aria-label="戻る">
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
                        <div class="pscc-meta-item">
                            <svg class="pscc-meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>{{ $latestAt?->format('Y/m/d H:i') ?? '-' }}</span>
                        </div>
                        @if ($jobDetailUrl)
                            <div class="pscc-meta-item">
                                <a href="{{ $jobDetailUrl }}" class="text-blue-600 hover:underline font-medium">案件詳細</a>
                            </div>
                        @endif
                    </div>
                </div>

                @if ($jobDetailUrl)
                    <a class="pscc-status-badge pscc-status-progress" href="{{ $jobDetailUrl }}" aria-label="案件詳細へ">
                        案件詳細
                    </a>
                @else
                    <span class="pscc-status-badge pscc-status-progress">ダイレクト</span>
                @endif
            </div>
        </div>

        <div class="pscc-chat-area" id="dmChatArea">
            <div class="pscc-chat-content">
                <div class="pscc-messages">
                    @forelse ($messages as $message)
                        @php
                            $isMe = $message->sender_type === 'freelancer' && (int) $message->sender_id === $viewerId;

                            $msgAvatarSrc = null;
                            if ($message->sender_type === 'company') {
                                $senderCompany = \App\Models\Company::find($message->sender_id);
                                $senderName = $senderCompany?->contact_name ?? $senderCompany?->name ?? '企業';
                                $iconPath = $senderCompany?->icon_path;
                            } else {
                                $senderFr = (int) $message->sender_id === $viewerId
                                    ? $viewerProfile
                                    : \App\Models\Freelancer::find($message->sender_id);
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

                        <div class="pscc-message {{ $isMe ? 'is-me' : '' }}">
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
                                                    {{-- <span class="pscc-read-status">既読</span> --}}
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

                                @if(filled($message->body))
                                    <p class="pscc-message-body">{{ $message->body }}</p>
                                @endif

                                @if(!empty($message->attachments) && $message->attachments->count() > 0)
                                    <div style="margin-top: 0.5rem; display:flex; flex-direction:column; gap:0.25rem;">
                                        @foreach($message->attachments as $att)
                                            @php
                                                $attUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($att->attachment_path);
                                                $attName = $att->attachment_name ?? basename($att->attachment_path);
                                                $sizeMb = $att->attachment_size ? round(((int)$att->attachment_size) / (1024 * 1024), 2) : null;
                                            @endphp
                                            <a
                                                href="{{ $attUrl }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                style="display:inline-flex; align-items:center; gap:0.35rem; color:#2563eb; text-decoration:underline; font-weight:700; word-break:break-all;"
                                            >
                                                <span>添付:</span>
                                                <span>{{ $attName }}</span>
                                                @if($sizeMb !== null)
                                                    <span style="color:#6b7280; font-weight:800; font-size:0.8125rem;">({{ $sizeMb }}MB)</span>
                                                @endif
                                            </a>
                                        @endforeach
                                    </div>
                                @elseif(!empty($message->attachment_path))
                                    <div style="margin-top: 0.5rem;">
                                        <a
                                            href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($message->attachment_path) }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            style="display:inline-flex; align-items:center; gap:0.35rem; color:#2563eb; text-decoration:underline; font-weight:700; word-break:break-all;"
                                        >
                                            <span>添付:</span>
                                            <span>{{ $message->attachment_name ?? basename($message->attachment_path) }}</span>
                                        </a>
                                    </div>
                                @endif
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
            <form class="pscc-input-content"
                  method="POST"
                  enctype="multipart/form-data"
                  action="{{ route('freelancer.threads.messages.store', ['thread' => $thread->id]) }}">
                @csrf
                <input
                    type="file"
                    id="dmAttachment"
                    name="attachments[]"
                    multiple
                    accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.zip,.jpg,.jpeg,.png,.gif,.webp"
                    style="display:none;"
                >

                <div style="display:flex; flex-direction:column; flex:1; min-width:0;">
                    <textarea
                        id="messageInput"
                        name="content"
                        class="pscc-input-field @error('content') pscc-input-error @enderror resize-none"
                        placeholder="メッセージを入力..."
                        style="min-height:150px;"
                        autocomplete="off"
                        rows="4"
                    >{{ old('content') }}</textarea>

                    <div style="margin-top:0.5rem; display:flex; flex-direction:column; gap:0.2rem;">
                        <button
                            class="pscc-attach-button"
                            id="attachButton"
                            title="ファイルを添付"
                            type="button"
                            aria-label="ファイルを添付"
                            style="display:inline-flex; align-items:center; gap:0.35rem; padding:0; border:none; background:none; color:#64748b; font-weight:800; cursor:pointer;"
                        >
                            <svg class="pscc-attach-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:1.1rem; height:1.1rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                            <span>ファイルを添付</span>
                        </button>
                        <div style="font-size:0.875rem; color:#64748b; font-weight:800; line-height:1.15;">
                            3ファイル 合計10MB まで
                        </div>
                    </div>

                    <div
                        id="dmAttachmentList"
                        style="font-size:0.875rem; color:#64748b; margin-top:0.5rem; word-break:break-word; line-height:1.5; display:none;"
                    ></div>
                </div>

                <button class="pscc-send-button" type="submit" id="sendButton" disabled>
                    <svg class="pscc-button-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    送信
                </button>

                @error('content')
                    <div class="pscc-field-error"><span>{{ $message }}</span></div>
                @enderror
                @error('attachments')
                    <div class="pscc-field-error"><span>{{ $message }}</span></div>
                @enderror
                @error('attachments.*')
                    <div class="pscc-field-error"><span>{{ $message }}</span></div>
                @enderror
            </form>
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
            const attachBtn = document.getElementById('attachButton');
            const fileInput = document.getElementById('dmAttachment');
            const attachmentList = document.getElementById('dmAttachmentList');
            let selectedFiles = [];

            if (!input || !sendBtn) return;

            const toggle = () => {
                const hasText = !!(input.value && input.value.trim());
                const hasFile = !!(fileInput && fileInput.files && fileInput.files.length > 0);
                sendBtn.disabled = !(hasText || hasFile);
            };

            input.addEventListener('input', toggle);

            const safeName = (s) => String(s).replace(/[&<>"']/g, (c) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;',
            }[c] || c));

            const renderAttachmentList = () => {
                if (!attachmentList) return;
                if (selectedFiles.length <= 0) {
                    attachmentList.style.display = 'none';
                    attachmentList.innerHTML = '';
                    return;
                }

                attachmentList.style.display = 'block';
                attachmentList.innerHTML = selectedFiles
                    .map((f, idx) => {
                        const displayName = safeName(f.name);
                        return `
                            <div style="display:flex; align-items:center; justify-content:flex-start; gap:0.25rem;">
                                <div style="word-break:break-all; color:#334155; font-weight:800;">・${displayName}</div>
                                <button
                                    type="button"
                                    class="dm-attachment-remove"
                                    data-idx="${idx}"
                                    aria-label="この添付を削除"
                                    title="削除"
                                    style="border:none; background:none; color:#f43f5e; font-weight:900; cursor:pointer; font-size:1.25rem; line-height:1; padding:0; margin:0;"
                                >×</button>
                            </div>
                        `;
                    })
                    .join('');
            };

            attachmentList?.addEventListener('click', (evt) => {
                const btn = evt.target && evt.target.closest ? evt.target.closest('.dm-attachment-remove') : null;
                if (!btn) return;

                const idx = Number(btn.getAttribute('data-idx'));
                if (Number.isNaN(idx)) return;
                if (idx < 0 || idx >= selectedFiles.length) return;

                selectedFiles.splice(idx, 1);

                const dt = new DataTransfer();
                selectedFiles.forEach((f) => dt.items.add(f));
                fileInput.files = dt.files;

                renderAttachmentList();
                toggle();
            });

            if (attachBtn && fileInput) {
                attachBtn.addEventListener('click', () => fileInput.click());
                fileInput.addEventListener('change', () => {
                    const pickedFiles = fileInput && fileInput.files ? Array.from(fileInput.files) : [];
                    if (pickedFiles.length <= 0) {
                        selectedFiles = [];
                        renderAttachmentList();
                        toggle();
                        return;
                    }

                    const nextFiles = [...selectedFiles];
                    pickedFiles.forEach((picked) => {
                        if (nextFiles.length >= 3) return;
                        const dup = nextFiles.some((current) =>
                            current.name === picked.name &&
                            current.size === picked.size &&
                            current.lastModified === picked.lastModified
                        );
                        if (!dup) {
                            nextFiles.push(picked);
                        }
                    });

                    selectedFiles = nextFiles.slice(0, 3);

                    const dt = new DataTransfer();
                    selectedFiles.forEach((f) => dt.items.add(f));
                    fileInput.files = dt.files;

                    renderAttachmentList();
                    toggle();
                });
            }

            toggle();
            renderAttachmentList();
        })();
    </script>
@endpush

@extends('layouts.public')

@section('title', 'フリーランス チャット')

@push('styles')
    @include('partials.pscc-chat-core-styles')
    <style>
        /* 全体：メッセージカードの横幅を抑える */
        .pscc-message .pscc-message-card {
            max-width: 80%;
        }

        /* 自分：右寄せ（アイコン＋メッセージ欄セット） */
        .pscc-message.is-me {
            justify-content: flex-end;
        }
        .pscc-message.is-me .pscc-message-card {
            max-width: 80%;
        }
    </style>
@endpush

@section('content')
    @php
        $viewerProfile = $freelancer ?? (auth('freelancer')->user()->freelancer ?? null);
        $viewerId = (int) ($viewerProfile->id ?? 0);

        $counterpartName = $thread->company->contact_name ?? $thread->company->name ?? '企業';
        $counterpartRole = '企業';
        $counterpartIcon = $thread->company?->icon_path ?? null;

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

        $headerStickyTop = 'var(--public-header-height)';

        $jobDetailUrl = $thread->job ? route('freelancer.jobs.show', ['job' => $thread->job->id]) : null;

        $backUrl = $thread->job
            ? route('freelancer.jobs.show', ['job' => $thread->job->id])
            : route('freelancer.applications.index');

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

        $latestAt = $thread->latest_message_at ?? null;
    @endphp

    <div class="pscc-container">
        @include('partials.error-panel')

        <div class="pscc-header" style="top: {{ $headerStickyTop }};">
            <div class="pscc-header-content">
                <a class="pscc-back-button" href="{{ $backUrl }}" aria-label="戻る">
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
                        <div class="pscc-meta-item">
                            <svg class="pscc-meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>{{ $latestAt?->format('Y/m/d H:i') ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                @if ($jobDetailUrl)
                    <a class="pscc-status-badge pscc-status-progress" href="{{ $jobDetailUrl }}" aria-label="案件詳細へ">案件詳細</a>
                @else
                    <span class="pscc-status-badge pscc-status-progress">ダイレクト</span>
                @endif
            </div>
        </div>

        <div class="pscc-chat-area" id="dmChatArea">
            <div class="pscc-chat-content">
                <div class="pscc-messages">
                    @forelse ($messages as $message)
                        @php
                            $isMe = $message->sender_type === 'freelancer' && $message->sender_id === $viewerId;
                            $msgAvatarSrc = null;

                            if ($message->sender_type === 'company') {
                                $senderCompany = \App\Models\Company::find($message->sender_id);
                                $senderName = $senderCompany?->contact_name ?? $senderCompany?->name ?? '企業';
                                $iconPath = $senderCompany?->icon_path;
                            } else {
                                $senderFr = $message->sender_id === $viewerId
                                    ? $viewerProfile
                                    : \App\Models\Freelancer::find($message->sender_id);
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

                        <div class="pscc-message {{ $isMe ? 'is-me' : '' }}">
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
                                                    {{-- <span class="pscc-read-status">既読</span> --}}
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

                                @if(filled($message->body))
                                    <p class="pscc-message-body">{{ $message->body }}</p>
                                @endif

                                @if(!empty($message->attachments) && $message->attachments->count() > 0)
                                    <div style="margin-top: 0.5rem; display:flex; flex-direction:column; gap:0.25rem;">
                                        @foreach($message->attachments as $att)
                                            @php
                                                $attUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($att->attachment_path);
                                                $attName = $att->attachment_name ?? basename($att->attachment_path);
                                                $sizeMb = $att->attachment_size ? round(((int)$att->attachment_size) / (1024 * 1024), 2) : null;
                                            @endphp
                                            <a
                                                href="{{ $attUrl }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                style="display:inline-flex; align-items:center; gap:0.35rem; color:#2563eb; text-decoration:underline; font-weight:700; word-break:break-all;"
                                            >
                                                <span>添付:</span>
                                                <span>{{ $attName }}</span>
                                                @if($sizeMb !== null)
                                                    <span style="color:#6b7280; font-weight:800; font-size:0.8125rem;">({{ $sizeMb }}MB)</span>
                                                @endif
                                            </a>
                                        @endforeach
                                    </div>
                                @elseif(!empty($message->attachment_path))
                                    <div style="margin-top: 0.5rem;">
                                        <a
                                            href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($message->attachment_path) }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            style="display:inline-flex; align-items:center; gap:0.35rem; color:#2563eb; text-decoration:underline; font-weight:700; word-break:break-all;"
                                        >
                                            <span>添付:</span>
                                            <span>{{ $message->attachment_name ?? basename($message->attachment_path) }}</span>
                                        </a>
                                    </div>
                                @endif
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
            <form class="pscc-input-content"
                  method="POST"
                  enctype="multipart/form-data"
                  action="{{ route('freelancer.threads.messages.store', ['thread' => $thread->id]) }}">
                @csrf
                <input type="file"
                       id="dmAttachment"
                       name="attachments[]"
                       multiple
                       accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.zip,.jpg,.jpeg,.png,.gif,.webp"
                       style="display:none;">
                <div style="display:flex; flex-direction:column; flex:1; min-width:0;">
                    <textarea
                        id="messageInput"
                        name="content"
                        class="pscc-input-field @error('content') pscc-input-error @enderror resize-none"
                        placeholder="メッセージを入力..."
                        style="min-height:150px;"
                        autocomplete="off"
                        rows="4"
                    >{{ old('content') }}</textarea>

                    <div style="margin-top:0.5rem; display:flex; flex-direction:column; gap:0.2rem;">
                        <button
                            class="pscc-attach-button"
                            id="attachButton"
                            title="ファイルを添付"
                            type="button"
                            aria-label="ファイルを添付"
                            style="display:inline-flex; align-items:center; gap:0.35rem; padding:0; border:none; background:none; color:#64748b; font-weight:800; cursor:pointer;"
                        >
                            <svg class="pscc-attach-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:1.1rem; height:1.1rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                            <span>ファイルを添付</span>
                        </button>
                        <div style="font-size:0.875rem; color:#64748b; font-weight:800; line-height:1.15;">
                            3ファイル 合計10MB まで
                        </div>
                    </div>

                    <div
                        id="dmAttachmentList"
                        style="font-size:0.875rem; color:#64748b; margin-top:0.5rem; word-break:break-word; line-height:1.5; display:none;"
                    ></div>
                </div>
                <button class="pscc-send-button" type="submit" id="sendButton" disabled>
                    <svg class="pscc-button-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    送信
                </button>

                @error('content')
                    <div class="pscc-field-error"><span>{{ $message }}</span></div>
                @enderror
                @error('attachments')
                    <div class="pscc-field-error"><span>{{ $message }}</span></div>
                @enderror
                @error('attachments.*')
                    <div class="pscc-field-error"><span>{{ $message }}</span></div>
                @enderror
            </form>
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
            const attachBtn = document.getElementById('attachButton');
            const fileInput = document.getElementById('dmAttachment');
            const attachmentList = document.getElementById('dmAttachmentList');
            let selectedFiles = [];

            if (!input || !sendBtn) return;

            const toggle = () => {
                const hasText = !!(input.value && input.value.trim());
                const hasFile = !!(fileInput && fileInput.files && fileInput.files.length > 0);
                sendBtn.disabled = !(hasText || hasFile);
            };

            input.addEventListener('input', toggle);

            const safeName = (s) => String(s).replace(/[&<>"']/g, (c) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;',
            }[c] || c));

            const renderAttachmentList = () => {
                if (!attachmentList) return;
                if (selectedFiles.length <= 0) {
                    attachmentList.style.display = 'none';
                    attachmentList.innerHTML = '';
                    return;
                }

                attachmentList.style.display = 'block';
                attachmentList.innerHTML = selectedFiles
                    .map((f, idx) => {
                        const displayName = safeName(f.name);
                        return `
                            <div style="display:flex; align-items:center; justify-content:flex-start; gap:0.25rem;">
                                <div style="word-break:break-all; color:#334155; font-weight:800;">・${displayName}</div>
                                <button
                                    type="button"
                                    class="dm-attachment-remove"
                                    data-idx="${idx}"
                                    aria-label="この添付を削除"
                                    title="削除"
                                    style="border:none; background:none; color:#f43f5e; font-weight:900; cursor:pointer; font-size:1.25rem; line-height:1; padding:0; margin:0;"
                                >×</button>
                            </div>
                        `;
                    })
                    .join('');
            };

            attachmentList?.addEventListener('click', (evt) => {
                const btn = evt.target && evt.target.closest ? evt.target.closest('.dm-attachment-remove') : null;
                if (!btn) return;

                const idx = Number(btn.getAttribute('data-idx'));
                if (Number.isNaN(idx)) return;
                if (idx < 0 || idx >= selectedFiles.length) return;

                selectedFiles.splice(idx, 1);

                const dt = new DataTransfer();
                selectedFiles.forEach((f) => dt.items.add(f));
                fileInput.files = dt.files;

                renderAttachmentList();
                toggle();
            });

            if (attachBtn && fileInput) {
                attachBtn.addEventListener('click', () => fileInput.click());
                fileInput.addEventListener('change', () => {
                    const pickedFiles = fileInput && fileInput.files ? Array.from(fileInput.files) : [];
                    if (pickedFiles.length <= 0) {
                        selectedFiles = [];
                        renderAttachmentList();
                        toggle();
                        return;
                    }

                    const nextFiles = [...selectedFiles];
                    pickedFiles.forEach((picked) => {
                        if (nextFiles.length >= 3) return;
                        const dup = nextFiles.some((current) =>
                            current.name === picked.name &&
                            current.size === picked.size &&
                            current.lastModified === picked.lastModified
                        );
                        if (!dup) {
                            nextFiles.push(picked);
                        }
                    });

                    selectedFiles = nextFiles.slice(0, 3);

                    const dt = new DataTransfer();
                    selectedFiles.forEach((f) => dt.items.add(f));
                    fileInput.files = dt.files;

                    renderAttachmentList();
                    toggle();
                });
            }

            toggle();
            renderAttachmentList();
        })();
    </script>
@endpush

@extends('layouts.public')

@section('title', 'フリーランス チャット')

@push('styles')
    @include('partials.pscc-chat-core-styles')
    <style>
        /* 全体：メッセージカードの横幅を抑える */
        .pscc-message .pscc-message-card {
            max-width: 80%;
        }

        /* 自分：右寄せ（アイコン＋メッセージ欄セット） */
        .pscc-message.is-me {
            justify-content: flex-end;
        }
        .pscc-message.is-me .pscc-message-card {
            max-width: 80%;
        }
    </style>
@endpush

@section('content')
    @php
        $viewerProfile = $freelancer ?? (auth('freelancer')->user()->freelancer ?? null);
        $viewerId = (int) ($viewerProfile->id ?? 0);

        $counterpartName = $thread->company->contact_name ?? $thread->company->name ?? '企業';
        $counterpartRole = '企業';
        $counterpartIcon = $thread->company?->icon_path ?? null;

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

        // direct-messages/show と同様に使うための簡易互換
        $conversation = (object) [
            'latest_message_at' => $thread->latest_message_at ?? null,
        ];

        $viewerName = $viewerProfile?->display_name ?? 'フリーランス';
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

        $backUrl = $thread?->job
            ? route('freelancer.jobs.show', ['job' => $thread->job->id])
            : route('freelancer.applications.index');

        $jobDetailUrl = $thread?->job
            ? route('freelancer.jobs.show', ['job' => $thread->job->id])
            : null;

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
                <a class="pscc-back-button" href="{{ $backUrl }}" aria-label="戻る">
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
                        <div class="pscc-meta-item">
                            <svg class="pscc-meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>{{ $conversation->latest_message_at?->format('Y/m/d H:i') ?? '-' }}</span>
                        </div>
                        @if ($jobDetailUrl)
                            <div class="pscc-meta-item">
                                <a href="{{ $jobDetailUrl }}" class="text-blue-600 hover:underline font-medium">案件詳細</a>
                            </div>
                        @endif
                    </div>
                </div>

                @if ($jobDetailUrl)
                    <a class="pscc-status-badge pscc-status-progress" href="{{ $jobDetailUrl }}" aria-label="案件詳細へ">
                        案件詳細
                    </a>
                @else
                    <span class="pscc-status-badge pscc-status-progress">ダイレクト</span>
                @endif
            </div>
        </div>

        <div class="pscc-chat-area" id="dmChatArea">
            <div class="pscc-chat-content">
                <div class="pscc-messages">
                    @forelse ($messages as $message)
                        @php
                            $isMe = $message->sender_type === 'freelancer' && $message->sender_id === $viewerProfile->id;
                            $msgAvatarSrc = null;

                            if ($message->sender_type === 'company') {
                                $senderCompany = \App\Models\Company::find($message->sender_id);
                                $senderName = $senderCompany?->contact_name ?? $senderCompany?->name ?? '企業';
                                $iconPath = $senderCompany?->icon_path;
                            } else {
                                $senderFr = $message->sender_id === $viewerProfile->id
                                    ? $viewerProfile
                                    : \App\Models\Freelancer::find($message->sender_id);
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

                        <div class="pscc-message {{ $isMe ? 'is-me' : '' }}">
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
                                                    {{-- <span class="pscc-read-status">既読</span> --}}
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

                                @if(filled($message->body))
                                    <p class="pscc-message-body">{{ $message->body }}</p>
                                @endif

                                @if(!empty($message->attachments) && $message->attachments->count() > 0)
                                    <div style="margin-top: 0.5rem; display:flex; flex-direction:column; gap:0.25rem;">
                                        @foreach($message->attachments as $att)
                                            @php
                                                $attUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($att->attachment_path);
                                                $attName = $att->attachment_name ?? basename($att->attachment_path);
                                                $sizeMb = $att->attachment_size ? round(((int)$att->attachment_size) / (1024 * 1024), 2) : null;
                                            @endphp
                                            <a
                                                href="{{ $attUrl }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                style="display:inline-flex; align-items:center; gap:0.35rem; color:#2563eb; text-decoration:underline; font-weight:700; word-break:break-all;"
                                            >
                                                <span>添付:</span>
                                                <span>{{ $attName }}</span>
                                                @if($sizeMb !== null)
                                                    <span style="color:#6b7280; font-weight:800; font-size:0.8125rem;">({{ $sizeMb }}MB)</span>
                                                @endif
                                            </a>
                                        @endforeach
                                    </div>
                                @elseif(!empty($message->attachment_path))
                                    <div style="margin-top: 0.5rem;">
                                        <a
                                            href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($message->attachment_path) }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            style="display:inline-flex; align-items:center; gap:0.35rem; color:#2563eb; text-decoration:underline; font-weight:700; word-break:break-all;"
                                        >
                                            <span>添付:</span>
                                            <span>{{ $message->attachment_name ?? basename($message->attachment_path) }}</span>
                                        </a>
                                    </div>
                                @endif
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
            <form class="pscc-input-content"
                  method="POST"
                  enctype="multipart/form-data"
                  action="{{ route('freelancer.threads.messages.store', ['thread' => $thread->id]) }}">
                @csrf
                <input type="file"
                       id="dmAttachment"
                       name="attachments[]"
                       multiple
                       accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.zip,.jpg,.jpeg,.png,.gif,.webp"
                       style="display:none;">

                <div style="display:flex; flex-direction:column; flex:1; min-width:0;">
                    <textarea
                        id="messageInput"
                        name="content"
                        class="pscc-input-field @error('content') pscc-input-error @enderror resize-none"
                        placeholder="メッセージを入力..."
                        style="min-height:150px;"
                        autocomplete="off"
                        rows="4"
                    >{{ old('content') }}</textarea>

                    {{-- textarea の下に、添付ボタン案内（文字幅を崩さず配置） --}}
                    <div style="margin-top:0.5rem; display:flex; flex-direction:column; gap:0.2rem;">
                        <button
                            class="pscc-attach-button"
                            id="attachButton"
                            title="ファイルを添付"
                            type="button"
                            aria-label="ファイルを添付"
                            style="display:inline-flex; align-items:center; gap:0.35rem; padding:0; border:none; background:none; color:#64748b; font-weight:800; cursor:pointer;"
                        >
                            <svg class="pscc-attach-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:1.1rem; height:1.1rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                            <span>ファイルを添付</span>
                        </button>
                        <div style="font-size:0.875rem; color:#64748b; font-weight:800; line-height:1.15;">
                            3ファイル 合計10MB まで
                        </div>
                    </div>

                    <div
                        id="dmAttachmentList"
                        style="font-size:0.875rem; color:#64748b; margin-top:0.5rem; word-break:break-word; line-height:1.5; display:none;"
                    ></div>
                </div>
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
            @error('attachments')
                <div class="pscc-field-error"><span>{{ $message }}</span></div>
            @enderror
            @error('attachments.*')
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
            const attachBtn = document.getElementById('attachButton');
            const fileInput = document.getElementById('dmAttachment');
            const attachmentList = document.getElementById('dmAttachmentList');
            let selectedFiles = [];

            if (!input || !sendBtn) return;

            const toggle = () => {
                const hasText = !!(input.value && input.value.trim());
                const hasFile = !!(fileInput && fileInput.files && fileInput.files.length > 0);
                sendBtn.disabled = !(hasText || hasFile);
            };

            input.addEventListener('input', toggle);

            const safeName = (s) => String(s).replace(/[&<>"']/g, (c) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;',
            }[c] || c));

            const renderAttachmentList = () => {
                if (!attachmentList) return;
                if (selectedFiles.length <= 0) {
                    attachmentList.style.display = 'none';
                    attachmentList.innerHTML = '';
                    return;
                }

                attachmentList.style.display = 'block';
                attachmentList.innerHTML = selectedFiles
                    .map((f, idx) => {
                        const displayName = safeName(f.name);
                        return `
                            <div style="display:flex; align-items:center; justify-content:flex-start; gap:0.25rem;">
                                <div style="word-break:break-all; color:#334155; font-weight:800;">・${displayName}</div>
                                <button
                                    type="button"
                                    class="dm-attachment-remove"
                                    data-idx="${idx}"
                                    aria-label="この添付を削除"
                                    title="削除"
                                    style="border:none; background:none; color:#f43f5e; font-weight:900; cursor:pointer; font-size:1.25rem; line-height:1; padding:0; margin:0;"
                                >×</button>
                            </div>
                        `;
                    })
                    .join('');
            };

            // 添付ごとの削除（×）
            attachmentList?.addEventListener('click', (evt) => {
                const btn = evt.target && evt.target.closest ? evt.target.closest('.dm-attachment-remove') : null;
                if (!btn) return;

                const idx = Number(btn.getAttribute('data-idx'));
                if (Number.isNaN(idx)) return;
                if (idx < 0 || idx >= selectedFiles.length) return;

                selectedFiles.splice(idx, 1);

                const dt = new DataTransfer();
                selectedFiles.forEach((f) => dt.items.add(f));
                fileInput.files = dt.files;

                renderAttachmentList();
                toggle();
            });

            if (attachBtn && fileInput) {
                attachBtn.addEventListener('click', () => fileInput.click());
                fileInput.addEventListener('change', () => {
                    const pickedFiles = fileInput && fileInput.files ? Array.from(fileInput.files) : [];
                    if (pickedFiles.length <= 0) {
                        selectedFiles = [];
                        renderAttachmentList();
                        toggle();
                        return;
                    }

                    const nextFiles = [...selectedFiles];
                    pickedFiles.forEach((picked) => {
                        if (nextFiles.length >= 3) return;
                        const dup = nextFiles.some((current) =>
                            current.name === picked.name &&
                            current.size === picked.size &&
                            current.lastModified === picked.lastModified
                        );
                        if (!dup) {
                            nextFiles.push(picked);
                        }
                    });

                    selectedFiles = nextFiles.slice(0, 3);

                    const dt = new DataTransfer();
                    selectedFiles.forEach((f) => dt.items.add(f));
                    fileInput.files = dt.files;

                    renderAttachmentList();
                    toggle();
                });
            }

            toggle();
        })();
    </script>
@endpush

@extends('layouts.public')

@section('title', 'フリーランス チャット')

@push('styles')
    @include('partials.pscc-chat-core-styles')
    <style>
        /* 全体：メッセージカードの横幅を抑える */
        .pscc-message .pscc-message-card {
            max-width: 80%;
        }

        /* 自分：右寄せ（アイコン＋メッセージ欄セット） */
        .pscc-message.is-me {
            justify-content: flex-end;
        }
        .pscc-message.is-me .pscc-message-card {
            max-width: 80%;
        }
    </style>
@endpush

@section('content')
    @php
        $viewerProfile = $freelancer ?? (auth('freelancer')->user()->freelancer ?? null);
        $viewerId = (int) ($viewerProfile->id ?? 0);

        // この画面は「応募した案件のスレッドメッセージ」前提
        $counterpartName = $thread->company->contact_name ?? $thread->company->name ?? '企業';
        $counterpartRole = '企業';
        $counterpartIcon = $thread->company?->icon_path ?? null;

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

        $headerStickyTop = 'var(--public-header-height)';

        $viewerName = $viewerProfile?->display_name ?? 'フリーランス';
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

        // 戻り先と案件詳細リンク（direct とは差分）
        $backUrl = $thread?->job
            ? route('freelancer.jobs.show', ['job' => $thread->job->id])
            : route('freelancer.applications.index');

        $jobDetailUrl = $thread?->job
            ? route('freelancer.jobs.show', ['job' => $thread->job->id])
            : null;

        // direct 側テンプレが参照する latest message 表示用
        $conversation = (object) [
            'latest_message_at' => $thread->latest_message_at ?? null,
        ];
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
                <a class="pscc-back-button" href="{{ $backUrl }}" aria-label="戻る">
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

                        <div class="pscc-meta-item">
                            <svg class="pscc-meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>{{ $conversation->latest_message_at?->format('Y/m/d H:i') ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                @if ($jobDetailUrl)
                    <a
                        class="pscc-status-badge pscc-status-progress"
                        href="{{ $jobDetailUrl }}"
                        aria-label="案件詳細へ"
                    >
                        案件詳細
                    </a>
                @else
                    <span class="pscc-status-badge pscc-status-progress">ダイレクト</span>
                @endif
            </div>
        </div>

        <div class="pscc-chat-area" id="dmChatArea">
            <div class="pscc-chat-content">
                <div class="pscc-messages">
                    @forelse ($messages as $message)
                        @php
                            $isMe = $message->sender_type === 'freelancer' && $message->sender_id === $viewerProfile->id;
                            $msgAvatarSrc = null;

                            if ($message->sender_type === 'company') {
                                $senderCompany = $thread->company;
                                $senderName = $senderCompany?->contact_name ?? $senderCompany?->name ?? '企業';
                                $iconPath = $senderCompany?->icon_path;
                            } else {
                                $senderFr = $message->sender_id === $viewerProfile->id
                                    ? $viewerProfile
                                    : \App\Models\Freelancer::find($message->sender_id);
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

                        <div class="pscc-message {{ $isMe ? 'is-me' : '' }}">
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
                                                    {{-- <span class="pscc-read-status">既読</span> --}}
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

                                @if(filled($message->body))
                                    <p class="pscc-message-body">{{ $message->body }}</p>
                                @endif

                                @if(!empty($message->attachments) && $message->attachments->count() > 0)
                                    <div style="margin-top: 0.5rem; display:flex; flex-direction:column; gap:0.25rem;">
                                        @foreach($message->attachments as $att)
                                            @php
                                                $attUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($att->attachment_path);
                                                $attName = $att->attachment_name ?? basename($att->attachment_path);
                                                $sizeMb = $att->attachment_size ? round(((int)$att->attachment_size) / (1024 * 1024), 2) : null;
                                            @endphp
                                            <a
                                                href="{{ $attUrl }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                style="display:inline-flex; align-items:center; gap:0.35rem; color:#2563eb; text-decoration:underline; font-weight:700; word-break:break-all;"
                                            >
                                                <span>添付:</span>
                                                <span>{{ $attName }}</span>
                                                @if($sizeMb !== null)
                                                    <span style="color:#6b7280; font-weight:800; font-size:0.8125rem;">({{ $sizeMb }}MB)</span>
                                                @endif
                                            </a>
                                        @endforeach
                                    </div>
                                @elseif(!empty($message->attachment_path))
                                    <div style="margin-top: 0.5rem;">
                                        <a
                                            href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($message->attachment_path) }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            style="display:inline-flex; align-items:center; gap:0.35rem; color:#2563eb; text-decoration:underline; font-weight:700; word-break:break-all;"
                                        >
                                            <span>添付:</span>
                                            <span>{{ $message->attachment_name ?? basename($message->attachment_path) }}</span>
                                        </a>
                                    </div>
                                @endif
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
            <form class="pscc-input-content"
                  method="POST"
                  enctype="multipart/form-data"
                  action="{{ route('freelancer.threads.messages.store', ['thread' => $thread->id]) }}">
                @csrf
                <input type="file"
                       id="dmAttachment"
                       name="attachments[]"
                       multiple
                       accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.zip,.jpg,.jpeg,.png,.gif,.webp"
                       style="display:none;">

                <div style="display:flex; flex-direction:column; flex:1; min-width:0;">
                    <textarea
                        id="messageInput"
                        name="content"
                        class="pscc-input-field @error('content') pscc-input-error @enderror resize-none"
                        placeholder="メッセージを入力..."
                        style="min-height:150px;"
                        autocomplete="off"
                        rows="4"
                    >{{ old('content') }}</textarea>
                    {{-- textarea の下に、添付ボタン案内（文字幅を崩さず配置） --}}
                    <div style="margin-top:0.5rem; display:flex; flex-direction:column; gap:0.2rem;">
                        <button
                            class="pscc-attach-button"
                            id="attachButton"
                            title="ファイルを添付"
                            type="button"
                            aria-label="ファイルを添付"
                            style="display:inline-flex; align-items:center; gap:0.35rem; padding:0; border:none; background:none; color:#64748b; font-weight:800; cursor:pointer;"
                        >
                            <svg class="pscc-attach-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:1.1rem; height:1.1rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                            <span>ファイルを添付</span>
                        </button>
                        <div style="font-size:0.875rem; color:#64748b; font-weight:800; line-height:1.15;">
                            3ファイル 合計10MB まで
                        </div>
                    </div>

                    <div
                        id="dmAttachmentList"
                        style="font-size:0.875rem; color:#64748b; margin-top:0.5rem; word-break:break-word; line-height:1.5; display:none;"
                    ></div>
                </div>

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
            @error('attachments')
                <div class="pscc-field-error"><span>{{ $message }}</span></div>
            @enderror
            @error('attachments.*')
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
            const attachBtn = document.getElementById('attachButton');
            const fileInput = document.getElementById('dmAttachment');
            const attachmentList = document.getElementById('dmAttachmentList');
            let selectedFiles = [];

            if (!input || !sendBtn) return;

            const toggle = () => {
                const hasText = !!(input.value && input.value.trim());
                const hasFile = !!(fileInput && fileInput.files && fileInput.files.length > 0);
                sendBtn.disabled = !(hasText || hasFile);
            };

            input.addEventListener('input', toggle);

            const safeName = (s) => String(s).replace(/[&<>"']/g, (c) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;',
            }[c] || c));

            const renderAttachmentList = () => {
                if (!attachmentList) return;
                if (selectedFiles.length <= 0) {
                    attachmentList.style.display = 'none';
                    attachmentList.innerHTML = '';
                    return;
                }

                attachmentList.style.display = 'block';
                attachmentList.innerHTML = selectedFiles
                    .map((f, idx) => {
                        const displayName = safeName(f.name);
                        return `
                            <div style="display:flex; align-items:center; justify-content:flex-start; gap:0.25rem;">
                                <div style="word-break:break-all; color:#334155; font-weight:800;">・${displayName}</div>
                                <button
                                    type="button"
                                    class="dm-attachment-remove"
                                    data-idx="${idx}"
                                    aria-label="この添付を削除"
                                    title="削除"
                                    style="border:none; background:none; color:#f43f5e; font-weight:900; cursor:pointer; font-size:1.25rem; line-height:1; padding:0; margin:0;"
                                >×</button>
                            </div>
                        `;
                    })
                    .join('');
            };

            // 添付ごとの削除（×）
            attachmentList?.addEventListener('click', (evt) => {
                const btn = evt.target && evt.target.closest ? evt.target.closest('.dm-attachment-remove') : null;
                if (!btn) return;

                const idx = Number(btn.getAttribute('data-idx'));
                if (Number.isNaN(idx)) return;
                if (idx < 0 || idx >= selectedFiles.length) return;

                selectedFiles.splice(idx, 1);

                const dt = new DataTransfer();
                selectedFiles.forEach((f) => dt.items.add(f));
                fileInput.files = dt.files;

                renderAttachmentList();
                toggle();
            });

            if (attachBtn && fileInput) {
                attachBtn.addEventListener('click', () => fileInput.click());
                fileInput.addEventListener('change', () => {
                    const pickedFiles = fileInput && fileInput.files ? Array.from(fileInput.files) : [];
                    if (pickedFiles.length <= 0) {
                        selectedFiles = [];
                        renderAttachmentList();
                        toggle();
                        return;
                    }

                    const nextFiles = [...selectedFiles];
                    pickedFiles.forEach((picked) => {
                        if (nextFiles.length >= 3) return;
                        const dup = nextFiles.some((current) =>
                            current.name === picked.name &&
                            current.size === picked.size &&
                            current.lastModified === picked.lastModified
                        );
                        if (!dup) {
                            nextFiles.push(picked);
                        }
                    });

                    selectedFiles = nextFiles.slice(0, 3);

                    const dt = new DataTransfer();
                    selectedFiles.forEach((f) => dt.items.add(f));
                    fileInput.files = dt.files;

                    renderAttachmentList();
                    toggle();
                });
            }

            toggle();
            renderAttachmentList();
        })();
    </script>
@endpush

{{-- @extends('layouts.public')

@section('title', 'フリーランス チャット')

@push('styles')
    @include('partials.pscc-chat-core-styles')
    <style>
        /* 全体：メッセージカードの横幅を抑える */
        .pscc-message .pscc-message-card {
            max-width: 80%;
        }

        /* 自分：右寄せ（アイコン＋メッセージ欄セット） */
        .pscc-message.is-me {
            justify-content: flex-end;
        }
        .pscc-message.is-me .pscc-message-card {
            max-width: 80%;
        }
    </style>
@endpush

@section('content')
    @php
        $viewerProfile = $freelancer ?? (auth('freelancer')->user()->freelancer ?? null);
        $viewerId = (int) ($viewerProfile->id ?? 0);

        // この画面は「応募した案件のスレッドメッセージ」前提
        $counterpartName = $thread->company->contact_name ?? $thread->company->name ?? '企業';
        $counterpartRole = '企業';
        $counterpartIcon = $thread->company?->icon_path ?? null;
        $counterpartUserId = null;

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

        $headerStickyTop = 'var(--public-header-height)';

        // `direct-messages/show` と同様に、時間表示・右寄せに必要な情報を組み立て
        $viewerName = $viewerProfile?->display_name ?? 'フリーランス';
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

        // ルート差分：この画面は direct ではないため、戻り先と送信先をスレッド用に
        $backUrl = $thread?->job
            ? route('freelancer.jobs.show', ['job' => $thread->job->id])
            : route('freelancer.applications.index');
        $jobDetailUrl = $thread?->job
            ? route('freelancer.jobs.show', ['job' => $thread->job->id])
            : null;

        // direct 側テンプレが参照するが、今回の「応募スレッド」では direct 用 conversation が無い可能性があるため補完
        $conversation = (object) [
            'latest_message_at' => $thread->latest_message_at ?? null,
        ];
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
                <a class="pscc-back-button" href="{{ $backUrl }}" aria-label="戻る">
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

                        <div class="pscc-meta-item">
                            <svg class="pscc-meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>{{ $conversation->latest_message_at?->format('Y/m/d H:i') ?? '-' }}</span>
                        </div>

                        @if ($jobDetailUrl)
                            <div class="pscc-meta-item">
                                <a href="{{ $jobDetailUrl }}" class="text-blue-600 hover:underline font-medium">案件詳細</a>
                            </div>
                        @endif
                    </div>
                </div>

                @if ($jobDetailUrl)
                    <a
                        class="pscc-status-badge pscc-status-progress"
                        href="{{ $jobDetailUrl }}"
                        aria-label="案件詳細へ"
                    >
                        案件詳細
                    </a>
                @else
                    <span class="pscc-status-badge pscc-status-progress">ダイレクト</span>
                @endif
            </div>
        </div>

        <div class="pscc-chat-area" id="dmChatArea">
            <div class="pscc-chat-content">
                <div class="pscc-messages">
                    @forelse ($messages as $message)
                        @php
                            $isMe = $message->sender_type === 'freelancer' && $message->sender_id === $viewerProfile->id;
                            $msgAvatarSrc = null;

                            if ($message->sender_type === 'company') {
                                $senderCompany = $thread->company;
                                $senderName = $senderCompany?->contact_name ?? $senderCompany?->name ?? '企業';
                                $iconPath = $senderCompany?->icon_path;
                            } else {
                                $senderFr = $message->sender_id === $viewerProfile->id
                                    ? $viewerProfile
                                    : \App\Models\Freelancer::find($message->sender_id);
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

                        <div class="pscc-message {{ $isMe ? 'is-me' : '' }}">
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
                                                    {{-- <span class="pscc-read-status">既読</span> --}}
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

                                @if(filled($message->body))
                                    <p class="pscc-message-body">{{ $message->body }}</p>
                                @endif

                                @if(!empty($message->attachments) && $message->attachments->count() > 0)
                                    <div style="margin-top: 0.5rem; display:flex; flex-direction:column; gap:0.25rem;">
                                        @foreach($message->attachments as $att)
                                            @php
                                                $attUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($att->attachment_path);
                                                $attName = $att->attachment_name ?? basename($att->attachment_path);
                                                $sizeMb = $att->attachment_size ? round(((int)$att->attachment_size) / (1024 * 1024), 2) : null;
                                            @endphp
                                            <a
                                                href="{{ $attUrl }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                style="display:inline-flex; align-items:center; gap:0.35rem; color:#2563eb; text-decoration:underline; font-weight:700; word-break:break-all;"
                                            >
                                                <span>添付:</span>
                                                <span>{{ $attName }}</span>
                                                @if($sizeMb !== null)
                                                    <span style="color:#6b7280; font-weight:800; font-size:0.8125rem;">({{ $sizeMb }}MB)</span>
                                                @endif
                                            </a>
                                        @endforeach
                                    </div>
                                @elseif(!empty($message->attachment_path))
                                    <div style="margin-top: 0.5rem;">
                                        <a
                                            href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($message->attachment_path) }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            style="display:inline-flex; align-items:center; gap:0.35rem; color:#2563eb; text-decoration:underline; font-weight:700; word-break:break-all;"
                                        >
                                            <span>添付:</span>
                                            <span>{{ $message->attachment_name ?? basename($message->attachment_path) }}</span>
                                        </a>
                                    </div>
                                @endif
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
            <form class="pscc-input-content"
                  method="POST"
                  enctype="multipart/form-data"
                  action="{{ route('freelancer.threads.messages.store', ['thread' => $thread->id]) }}">
                @csrf
                <input type="file"
                       id="dmAttachment"
                       name="attachments[]"
                       multiple
                       accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.zip,.jpg,.jpeg,.png,.gif,.webp"
                       style="display:none;">

                <div style="display:flex; flex-direction:column; flex:1; min-width:0;">
                    <textarea
                        id="messageInput"
                        name="content"
                        class="pscc-input-field @error('content') pscc-input-error @enderror resize-none"
                        placeholder="メッセージを入力..."
                        style="min-height:150px;"
                        autocomplete="off"
                        rows="4"
                    >{{ old('content') }}</textarea>

                    <div style="margin-top:0.5rem; display:flex; flex-direction:column; gap:0.2rem;">
                        <button
                            class="pscc-attach-button"
                            id="attachButton"
                            title="ファイルを添付"
                            type="button"
                            aria-label="ファイルを添付"
                            style="display:inline-flex; align-items:center; gap:0.35rem; padding:0; border:none; background:none; color:#64748b; font-weight:800; cursor:pointer;"
                        >
                            <svg class="pscc-attach-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:1.1rem; height:1.1rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                            <span>ファイルを添付</span>
                        </button>
                        <div style="font-size:0.875rem; color:#64748b; font-weight:800; line-height:1.15;">
                            3ファイル 合計10MB まで
                        </div>
                    </div>

                    <div
                        id="dmAttachmentList"
                        style="font-size:0.875rem; color:#64748b; margin-top:0.5rem; word-break:break-word; line-height:1.5; display:none;"
                    ></div>
                </div>

                <button class="pscc-send-button" type="submit" id="sendButton" disabled>
                    <svg class="pscc-button-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    送信
                </button>

                @error('content')
                    <div class="pscc-field-error"><span>{{ $message }}</span></div>
                @enderror
                @error('attachments')
                    <div class="pscc-field-error"><span>{{ $message }}</span></div>
                @enderror
                @error('attachments.*')
                    <div class="pscc-field-error"><span>{{ $message }}</span></div>
                @enderror
            </form>
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
            const attachBtn = document.getElementById('attachButton');
            const fileInput = document.getElementById('dmAttachment');
            const attachmentList = document.getElementById('dmAttachmentList');
            let selectedFiles = [];

            if (!input || !sendBtn) return;

            const toggle = () => {
                const hasText = !!(input.value && input.value.trim());
                const hasFile = !!(fileInput && fileInput.files && fileInput.files.length > 0);
                sendBtn.disabled = !(hasText || hasFile);
            };

            input.addEventListener('input', toggle);

            const safeName = (s) => String(s).replace(/[&<>"']/g, (c) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;',
            }[c] || c));

            const renderAttachmentList = () => {
                if (!attachmentList) return;
                if (selectedFiles.length <= 0) {
                    attachmentList.style.display = 'none';
                    attachmentList.innerHTML = '';
                    return;
                }

                attachmentList.style.display = 'block';
                attachmentList.innerHTML = selectedFiles
                    .map((f, idx) => {
                        const displayName = safeName(f.name);
                        return `
                            <div style="display:flex; align-items:center; justify-content:flex-start; gap:0.25rem;">
                                <div style="word-break:break-all; color:#334155; font-weight:800;">・${displayName}</div>
                                <button
                                    type="button"
                                    class="dm-attachment-remove"
                                    data-idx="${idx}"
                                    aria-label="この添付を削除"
                                    title="削除"
                                    style="border:none; background:none; color:#f43f5e; font-weight:900; cursor:pointer; font-size:1.25rem; line-height:1; padding:0; margin:0;"
                                >×</button>
                            </div>
                        `;
                    })
                    .join('');
            };

            attachmentList?.addEventListener('click', (evt) => {
                const btn = evt.target && evt.target.closest ? evt.target.closest('.dm-attachment-remove') : null;
                if (!btn) return;

                const idx = Number(btn.getAttribute('data-idx'));
                if (Number.isNaN(idx)) return;
                if (idx < 0 || idx >= selectedFiles.length) return;

                selectedFiles.splice(idx, 1);

                const dt = new DataTransfer();
                selectedFiles.forEach((f) => dt.items.add(f));
                fileInput.files = dt.files;

                renderAttachmentList();
                toggle();
            });

            if (attachBtn && fileInput) {
                attachBtn.addEventListener('click', () => fileInput.click());
                fileInput.addEventListener('change', () => {
                    const pickedFiles = fileInput && fileInput.files ? Array.from(fileInput.files) : [];
                    if (pickedFiles.length <= 0) {
                        selectedFiles = [];
                        renderAttachmentList();
                        toggle();
                        return;
                    }

                    const nextFiles = [...selectedFiles];
                    pickedFiles.forEach((picked) => {
                        if (nextFiles.length >= 3) return;
                        const dup = nextFiles.some((current) =>
                            current.name === picked.name &&
                            current.size === picked.size &&
                            current.lastModified === picked.lastModified
                        );
                        if (!dup) {
                            nextFiles.push(picked);
                        }
                    });

                    selectedFiles = nextFiles.slice(0, 3);

                    const dt = new DataTransfer();
                    selectedFiles.forEach((f) => dt.items.add(f));
                    fileInput.files = dt.files;

                    renderAttachmentList();
                    toggle();
                });
            }

            toggle();
            renderAttachmentList();
        })();
    </script>
@endpush

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>メッセージ - AITECH</title>
    <link rel="icon" href="{{ asset('aifavicon.png') }}">
    @auth('freelancer')
        @include('partials.freelancer-header-style')
    @endauth
    {{-- ヘッダーに必要なスタイルのみをここに記載 --}}
    <style>
        /* Header (企業側と同じレスポンシブ構造: 640 / 768 / 1024 / 1280) */
        .header {
            background-color: #ffffff;
            border-bottom: 1px solid #e1e4e8;
            padding: 0 var(--header-padding-x, 1rem);
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            min-height: var(--header-height-current, 91px);
        }

        .header-content {
            max-width: 1600px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr auto; /* mobile: ロゴ / 右側 */
            align-items: center;
            gap: 0.5rem;
            height: var(--header-height-current, 91px);
            position: relative;
            min-width: 0;
            padding: 0.25rem 0; /* 縦余白 */
        }

        /* md以上: ロゴ / 中央ナビ / 右側 */
        @media (min-width: 768px) {
            .header-content { grid-template-columns: auto 1fr auto; gap: 1rem; }
        }

        /* lg: 間隔を広げる */
        @media (min-width: 1024px) {
            .header-content { gap: 1.5rem; padding: 0.5rem 0; }
        }

        .header-left { display: flex; align-items: center; gap: 0.75rem; min-width: 0; }
        .header-right { display: flex; align-items: center; justify-content: flex-end; min-width: 0; gap: 0.75rem; }

        /* ロゴ */
        .logo { display: flex; align-items: center; gap: 8px; min-width: 0; }
        .logo-text {
            font-weight: 900;
            font-size: 18px;
            margin-left: 0;
            color: #111827;
            letter-spacing: 1px;
            white-space: nowrap;
        }
        @media (min-width: 640px) { .logo-text { font-size: 20px; } }
        @media (min-width: 768px) { .logo-text { font-size: 22px; } }
        @media (min-width: 1024px) { .logo-text { font-size: 24px; } }
        @media (min-width: 1280px) { .logo-text { font-size: 26px; } }

        /* Mobile nav toggle (<=768pxで表示) */
        .nav-toggle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            border: 1px solid #e1e4e8;
            background: #fff;
            cursor: pointer;
            transition: all 0.15s ease;
            flex: 0 0 auto;
        }
        .nav-toggle:hover { background: #f6f8fa; }
        .nav-toggle:focus-visible { outline: none; box-shadow: 0 0 0 3px rgba(3, 102, 214, 0.15); }
        .nav-toggle svg { width: 22px; height: 22px; color: #24292e; }
        @media (min-width: 768px) { .nav-toggle { display: none; } }

        /* Desktop nav (>=768pxで表示) */
        .nav-links {
            display: none; /* mobile: hidden (use hamburger) */
            align-items: center;
            justify-content: center;
            flex-wrap: nowrap;
            min-width: 0;
            overflow: hidden;
            gap: 1.25rem;
        }
        @media (min-width: 640px) { .nav-links { display: none; } }
        @media (min-width: 768px) { .nav-links { display: flex; gap: 1.25rem; } }
        @media (min-width: 1024px) { .nav-links { gap: 2rem; } }
        @media (min-width: 1280px) { .nav-links { gap: 3rem; } }

        .nav-link {
            text-decoration: none;
            color: #586069;
            font-weight: 500;
            font-size: 1.05rem;
            padding: 0.6rem 1rem;
            border-radius: 8px;
            transition: all 0.15s ease;
            position: relative;
            letter-spacing: -0.01em;
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
        }
        @media (min-width: 768px) { .nav-link { font-size: 1.1rem; padding: 0.75rem 1.25rem; } }
        @media (min-width: 1280px) { .nav-link { font-size: 1.15rem; } }
        .nav-link.has-badge { padding-right: 3rem; }
        .nav-link:hover { background-color: #f6f8fa; color: #24292e; }
        .nav-link.active {
            background-color: #0366d6;
            color: white;
            box-shadow: 0 2px 8px rgba(3, 102, 214, 0.3);
        }

        .badge {
            background-color: #d73a49;
            color: white;
            border-radius: 50%;
            padding: 0.15rem 0.45rem;
            font-size: 0.7rem;
            font-weight: 600;
            min-width: 18px;
            height: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 3px rgba(209, 58, 73, 0.3);
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
        }

        /* Mobile nav menu */
        .mobile-nav {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #fff;
            border-bottom: 1px solid #e1e4e8;
            box-shadow: 0 16px 40px rgba(0,0,0,0.10);
            padding: 0.75rem var(--header-padding-x, 1rem);
            display: none;
            z-index: 110;
        }
        .header.is-mobile-nav-open .mobile-nav { display: block; }
        @media (min-width: 768px) { .mobile-nav { display: none !important; } }
        .mobile-nav-inner {
            max-width: 1600px;
            margin: 0 auto;
            display: grid;
            gap: 0.5rem;
        }
        .mobile-nav .nav-link {
            width: 100%;
            justify-content: flex-start;
            background: #fafbfc;
            border: 1px solid #e1e4e8;
            padding: 0.875rem 1rem;
        }
        .mobile-nav .nav-link:hover { background: #f6f8fa; }
        .mobile-nav .nav-link.active {
            background-color: #0366d6;
            color: #fff;
            border-color: #0366d6;
        }
        .mobile-nav .nav-link.has-badge { padding-right: 1rem; }
        .mobile-nav .badge {
            position: static;
            transform: none;
            margin-left: auto;
            margin-right: 0;
        }

        /* User menu / Dropdown */
        .user-menu { display: flex; align-items: center; position: static; transform: none; }
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border: none;
            padding: 0;
            appearance: none;
        }
        .dropdown { position: relative; }
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            background-color: white;
            min-width: 240px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12), 0 2px 8px rgba(0,0,0,0.08);
            border-radius: 12px;
            z-index: 1000;
            border: 1px solid #e1e4e8;
            margin-top: 0.5rem;
        }
        .dropdown.is-open .dropdown-content { display: block; }
        .dropdown-divider { height: 1px; background-color: #e1e4e8; margin: 0.5rem 0; }
    </style>
    <style>
        /* 元のページ内スタイル（そのまま保持） */
        :root {
            --header-height: 72px;
            --header-height-mobile: 72px;
            --header-height-sm: 72px;         /* sm */
            --header-height-md: 72px;        /* md */
            --header-height-lg: 72px;        /* lg */
            --header-height-xl: 72px;        /* xl */
            --header-height-current: var(--header-height-mobile);
            --header-padding-x: 1rem;
        }

        /* Breakpoint: sm (>=640px) */
        @media (min-width: 640px) {
            :root {
                --header-padding-x: 1.5rem;
                --header-height-current: var(--header-height-sm);
            }
        }

        /* Breakpoint: md (>=768px) */
        @media (min-width: 768px) {
            :root {
                --header-padding-x: 2rem;
                --header-height-current: var(--header-height-md);
            }
        }

        /* Breakpoint: lg (>=1024px) */
        @media (min-width: 1024px) {
            :root {
                --header-padding-x: 2.5rem;
                --header-height-current: var(--header-height-lg);
            }
        }

        /* Breakpoint: xl (>=1280px) */
        @media (min-width: 1280px) {
            :root {
                --header-padding-x: 3rem;
                --header-height-current: var(--header-height-xl);
            }
        }

        :root {
            --header-height: 72px;
            --header-height-mobile: 72px;
            --bg: #f6f8fb;
            --surface: #ffffff;
            --surface-2: #fbfcfe;
            --text: #0f172a;
            --muted: #64748b;
            --border: #e6eaf2;
            --border-2: #dbe2ee;
            --primary: #0366d6;
            --primary-2: #0256cc;
            --shadow-sm: 0 1px 2px rgba(15, 23, 42, 0.06);
            --shadow-md: 0 10px 30px rgba(15, 23, 42, 0.10);
            --radius-lg: 18px;
            --radius-md: 14px;
            --radius-sm: 12px;
            --focus: 0 0 0 4px rgba(3, 102, 214, 0.14);
        }
        /* 以下、既存の詳細スタイルをそのまま保持しています（省略不可） */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { font-size: 100%; }
        body {
            font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background:
                radial-gradient(1200px 600px at 20% 0%, rgba(3, 102, 214, 0.08), transparent 60%),
                radial-gradient(900px 500px at 90% 10%, rgba(102, 126, 234, 0.10), transparent 55%),
                var(--bg);
            color: var(--text);
            line-height: 1.6;
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Page */
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2.5rem 1.75rem 3rem;
        }
        .page-title {
            font-size: 1.9rem;
            font-weight: 900;
            margin-bottom: 0.6rem;
            color: var(--text);
            letter-spacing: -0.03em;
        }
        .page-subtitle {
            color: var(--muted);
            font-size: 1.02rem;
            margin-bottom: 1.75rem;
            font-weight: 600;
        }
        .panel {
            background-color: var(--surface);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border);
            overflow: hidden;
        }
        .chat-pane {
            display: flex;
            flex-direction: column;
            min-height: min(800px, calc(100vh - var(--header-height) - 8rem));
        }
        .chat-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            background: linear-gradient(180deg, var(--surface) 0%, var(--surface-2) 100%);
        }
        .chat-title {
            display: grid;
            gap: 0.2rem;
            min-width: 0;
        }
        .chat-title strong {
            font-size: 26px;
            font-weight: 900;
            color: var(--text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            letter-spacing: -0.02em;
        }
        .chat-title span {
            color: var(--muted);
            font-size: 0.9rem;
            font-weight: 700;
        }
        .btn {
            padding: 0.7rem 1rem;
            border-radius: 10px;
            font-weight: 800;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
            cursor: pointer;
            border: 1px solid #e1e4e8;
            background: #fafbfc;
            color: #24292e;
            font-size: 0.9rem;
            white-space: nowrap;
        }
        .btn:hover { background: #f6f8fa; transform: translateY(-1px); }
        .messages {
            padding: 0px 10px 15px 25px;
            overflow-y: auto;
            display: grid;
            gap: 0.85rem;
            background:
                radial-gradient(900px 420px at 10% 0%, rgba(3, 102, 214, 0.06), transparent 60%),
                linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            position: relative;
            scrollbar-gutter: stable;
            flex: 1 1 auto;
        }
        .bubble-row { display: flex; align-items: flex-end; gap: 0.75rem; }
        .bubble-row.me { justify-content: flex-end; }
        .bubble-row.first-message {
            /* 固定表示を解除：他のメッセージと同様のフローにする */
            justify-content: flex-end;
            width: 100%;
            margin-left: auto;
        }
        .avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: grid;
            place-items: center;
            color: white;
            font-weight: 900;
            font-size: 0.95rem;
            flex-shrink: 0;
        }
        .bubble {
            max-width: 74%;
            width: 80%;
            padding: 0.9rem 1rem;
            border-radius: 16px;
            border: 1px solid var(--border);
            background: rgba(255,255,255,0.92);
            box-shadow: var(--shadow-sm);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            overflow-wrap: anywhere;
        }
        .bubble.me {
            background: linear-gradient(180deg, rgba(241,248,255,0.98) 0%, rgba(236,246,255,0.98) 100%);
            border-color: #cfe4ff;
            width: 80%;
            padding: 20px;
        }
        .bubble.first-message {
            max-width: 74%;
            width: 80%;
            padding: 20px;
            margin-top: 12px;
            border-radius: 16px;
            border-color: #cfe4ff;
            background: linear-gradient(180deg, rgba(241,248,255,0.98) 0%, rgba(236,246,255,0.98) 100%);
            box-shadow: var(--shadow-sm);
            position: relative;
        }
        .bubble.first-message::before {
            display: none;
            content: "";
        }
        .bubble.first-message p {
            color: var(--text);
            font-size: 0.95rem;
            line-height: 1.7;
            white-space: pre-wrap;
        }
        .bubble.first-message small {
            color: var(--muted);
            font-weight: 800;
        }
        .bubble p { color: var(--text); font-size: 0.95rem; line-height: 1.7; white-space: pre-wrap; }
        .bubble small {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.75rem;
            margin-top: 0.45rem;
            color: var(--muted);
            font-weight: 800;
            font-size: 0.82rem;
        }
        .composer {
            padding: 1rem 1.25rem 1.25rem;
            border-top: 1px solid var(--border);
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.75rem;
            background: linear-gradient(180deg, var(--surface-2) 0%, var(--surface) 100%);
        }
        .input {
            width: 100%;
            padding: 0.95rem 1rem;
            border: 1px solid var(--border-2);
            border-radius: 14px;
            font-size: 0.98rem;
            transition: all 0.15s ease;
            background-color: #ffffff;
            min-height: 7.25rem;
            max-height: 18rem;
            resize: vertical;
            line-height: 1.7;
            box-shadow: inset 0 1px 0 rgba(15, 23, 42, 0.03);
        }
        .input:focus {
            outline: none;
            border-color: rgba(3, 102, 214, 0.55);
            box-shadow: var(--focus);
        }
        .input.is-invalid {
            border-color: rgba(239, 68, 68, 0.8);
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.10);
        }
        .error-message {
            display: block;
            margin-top: 6px;
            font-size: 13px;
            font-weight: 800;
            color: #dc2626;
        }
        .send {
            padding: 14px 40px;
            border-radius: 14px;
            font-weight: 900;
            border: none;
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-2) 100%);
            color: white;
            cursor: pointer;
            transition: all 0.15s ease;
            font-size: 20px;
            min-width: 260px;
            max-width: 100%;
            margin-left: auto;
            box-shadow: 0 10px 20px rgba(3, 102, 214, 0.22);
        }
        .send:hover { transform: translateY(-1px); box-shadow: 0 14px 26px rgba(3, 102, 214, 0.26); }
        .send:active { transform: translateY(0px); }
        .send:focus-visible { outline: none; box-shadow: var(--focus), 0 14px 26px rgba(3, 102, 214, 0.26); }
        .send:disabled { opacity: 0.55; cursor: not-allowed; box-shadow: none; transform: none; }

        @media (prefers-reduced-motion: reduce) {
            * { transition: none !important; scroll-behavior: auto !important; }
        }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    @include('partials.public-header')
    {{-- <header class="header header-role" role="banner">
        <div class="header-content">
            <div class="header-left">
                <div class="logo" aria-hidden="true">
                    <div class="logo-text">複業AI</div>
                </div>
            </div>

            <nav class="nav-links" role="navigation" aria-label="フリーランスナビゲーション">
                <a href="{{ route('freelancer.jobs.index') }}" class="nav-link">案件一覧</a>
                @php
                    $appUnread = ($unreadApplicationCount ?? 0);
                    $scoutUnread = ($unreadScoutCount ?? 0);
                @endphp
                <a href="{{ route('freelancer.applications.index') }}" class="nav-link {{ (Request::routeIs('freelancer.applications.*') || (isset($thread) && isset($thread->job_id) && $thread->job_id !== null)) ? 'active' : '' }} {{ $appUnread > 0 ? 'has-badge' : '' }}">
                    応募した案件
                    @if($appUnread > 0)
                        <span class="badge" aria-live="polite">{{ $appUnread }}</span>
                    @endif
                </a>
                <a href="{{ route('freelancer.scouts.index') }}" class="nav-link {{ (Request::routeIs('freelancer.scouts.*') || (isset($thread) && isset($thread->job_id) && $thread->job_id === null)) ? 'active' : '' }} {{ $scoutUnread > 0 ? 'has-badge' : '' }}">
                    スカウト
                    @if($scoutUnread > 0)
                        <span class="badge" aria-hidden="false">{{ $scoutUnread }}</span>
                    @endif
                </a>
            </nav>

            <div class="header-right" role="region" aria-label="ユーザー">
                <button
                    class="nav-toggle"
                    id="mobileNavToggle"
                    type="button"
                    aria-label="メニューを開く"
                    aria-haspopup="menu"
                    aria-expanded="false"
                    aria-controls="mobileNav"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M3 6h18"></path>
                        <path d="M3 12h18"></path>
                        <path d="M3 18h18"></path>
                    </svg>
                </button>

                <div class="user-menu">
                    <div class="dropdown" id="userDropdown">
                        <button class="user-avatar" id="userDropdownToggle" type="button" aria-haspopup="menu" aria-expanded="false" aria-controls="userDropdownMenu">
                            @if(isset($freelancer) && $freelancer && $freelancer->icon_path)
                                <img src="{{ asset('storage/' . $freelancer->icon_path) }}" alt="プロフィール画像" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            @else
                                {{ $userInitial ?? 'U' }}
                            @endif
                        </button>
                        <div class="dropdown-content" id="userDropdownMenu" role="menu" aria-label="ユーザーメニュー">
                            <a href="{{ route('profiles.show', auth('freelancer')->user()) }}" class="dropdown-item" role="menuitem">プロフィール詳細</a>
                            <a href="{{ route('freelancer.profile.settings') }}" class="dropdown-item" role="menuitem">プロフィール設定</a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('auth.logout') }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="dropdown-item" role="menuitem" style="width: 100%; text-align: left; background: none; border: none; padding: 0.875rem 1.25rem; color: #586069; cursor: pointer; font-size: inherit; font-family: inherit;">ログアウト</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mobile-nav" id="mobileNav" role="menu" aria-label="モバイルナビゲーション">
            <div class="mobile-nav-inner">
                <a href="{{ route('freelancer.jobs.index') }}" class="nav-link">案件一覧</a>
                <a href="{{ route('freelancer.applications.index') }}" class="nav-link {{ (Request::routeIs('freelancer.applications.*') || (isset($thread) && isset($thread->job_id) && $thread->job_id !== null)) ? 'active' : '' }} {{ $appUnread > 0 ? 'has-badge' : '' }}">
                    応募した案件
                    @if($appUnread > 0)
                        <span class="badge" aria-live="polite">{{ $appUnread }}</span>
                    @endif
                </a>
                <a href="{{ route('freelancer.scouts.index') }}" class="nav-link {{ (Request::routeIs('freelancer.scouts.*') || (isset($thread) && isset($thread->job_id) && $thread->job_id === null)) ? 'active' : '' }} {{ $scoutUnread > 0 ? 'has-badge' : '' }}">
                    スカウト
                    @if($scoutUnread > 0)
                        <span class="badge" aria-hidden="false">{{ $scoutUnread }}</span>
                    @endif
                </a>
            </div>
        </div>
    </header> --}}

    <main class="main-content max-w-6xl mx-auto px-4 md:px-6 lg:px-8 py-6 md:py-10">
        @include('partials.error-panel')
        <section class="panel chat-pane" aria-label="チャット">
            <div class="chat-header">
                <div class="chat-title">
                    <strong>{{ $thread->company->name ?? '企業名不明' }}@if($thread->job) / {{ $thread->job->title }}@endif</strong>
                </div>
                @if($thread->job)
                    <a class="btn" href="{{ route('freelancer.jobs.show', ['job' => $thread->job->id]) }}">案件詳細</a>
                @endif
            </div>

            <div class="messages max-h-[70vh] md:max-h-[64vh] lg:max-h-[66vh]" id="messages" aria-label="メッセージ一覧">
                @forelse($messages as $message)
                    @php
                        $isMe = $message->sender_type === 'freelancer';
                        $isFirst = $loop->first;
                        $senderName = '';
                        if ($message->sender_type === 'company') {
                            $senderName = mb_substr($thread->company->name ?? '企業', 0, 1);
                        } elseif ($message->sender_type === 'freelancer') {
                            $senderName = mb_substr(auth()->user()->freelancer->display_name ?? auth()->user()->email ?? 'U', 0, 1);
                        }
                        $sentAt = $message->sent_at ? $message->sent_at->format('m/d H:i') : '';
                        $isLatest = $loop->last;
                        $canDelete = $isMe && $message->sender_type === 'freelancer';
                    @endphp
                    @if($isFirst)
                        <div class="bubble-row first-message">
                            <div class="bubble first-message">
                                @if(filled($message->body))
                                    <p>{{ $message->body }}</p>
                                @endif
                                @if(!empty($message->attachments) && $message->attachments->count() > 0)
                                    <div style="margin-top: 0.5rem; display:flex; flex-direction:column; gap:0.25rem;">
                                        @foreach($message->attachments as $att)
                                            @php
                                                $attPath = (string) ($att->attachment_path ?? '');
                                                $attUrl = $attPath !== '' ? \Illuminate\Support\Facades\Storage::disk('public')->url($attPath) : null;
                                                $attName = $att->attachment_name ?? ($attPath !== '' ? basename($attPath) : '');
                                                $sizeMb = $att->attachment_size !== null ? round(((int)$att->attachment_size) / (1024 * 1024), 2) : null;
                                            @endphp
                                            @if(!empty($attUrl))
                                                <a
                                                    href="{{ $attUrl }}"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    style="display:inline-flex; align-items:center; gap:0.35rem; color:#2563eb; text-decoration:underline; font-weight:700; word-break:break-all;"
                                                >
                                                    <span>添付:</span>
                                                    <span>{{ $attName }}</span>
                                                    @if($sizeMb !== null)
                                                        <span style="color:#6b7280; font-weight:800; font-size:0.8125rem;">({{ $sizeMb }}MB)</span>
                                                    @endif
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                                <small>{{ $sentAt }}</small>
                            </div>
                        </div>
                    @endif
                    @if(!$isFirst)
                        <div class="bubble-row {{ $isMe ? 'me' : '' }}">
                            <div class="bubble {{ $isMe ? 'me' : '' }}">
                                @if(filled($message->body))
                                    <p>{{ $message->body }}</p>
                                @endif
                                @if(!empty($message->attachments) && $message->attachments->count() > 0)
                                    <div style="margin-top: 0.5rem; display:flex; flex-direction:column; gap:0.25rem;">
                                        @foreach($message->attachments as $att)
                                            @php
                                                $attPath = (string) ($att->attachment_path ?? '');
                                                $attUrl = $attPath !== '' ? \Illuminate\Support\Facades\Storage::disk('public')->url($attPath) : null;
                                                $attName = $att->attachment_name ?? ($attPath !== '' ? basename($attPath) : '');
                                                $sizeMb = $att->attachment_size !== null ? round(((int)$att->attachment_size) / (1024 * 1024), 2) : null;
                                            @endphp
                                            @if(!empty($attUrl))
                                                <a
                                                    href="{{ $attUrl }}"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    style="display:inline-flex; align-items:center; gap:0.35rem; color:#2563eb; text-decoration:underline; font-weight:700; word-break:break-all;"
                                                >
                                                    <span>添付:</span>
                                                    <span>{{ $attName }}</span>
                                                    @if($sizeMb !== null)
                                                        <span style="color:#6b7280; font-weight:800; font-size:0.8125rem;">({{ $sizeMb }}MB)</span>
                                                    @endif
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                                <small>
                                    {{ $sentAt }}
                                    @if($canDelete && $isLatest)
                                        <span style="margin-left:0.75rem;">
                                            <form action="{{ route('freelancer.messages.destroy', ['message' => $message->id]) }}" method="POST" style="display:inline;" class="delete-form" data-message-id="{{ $message->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="delete-trigger" style="background:none;border:none;color:#d73a49;font-weight:900;cursor:pointer;">削除</button>
                                            </form>
                                        </span>
                                    @endif
                                </small>
                            </div>
                        </div>
                    @endif
                @empty
                    <div style="text-align: center; padding: 2rem; color: #6a737d;">
                        <p>メッセージがありません。</p>
                    </div>
                @endforelse
            </div>

            <form
                class="composer"
                action="{{ route('freelancer.threads.messages.store', ['thread' => $thread->id]) }}"
                method="post"
                enctype="multipart/form-data"
            >
                @csrf
                <input
                    type="file"
                    id="dmAttachment"
                    name="attachments[]"
                    multiple
                    accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.zip,.jpg,.jpeg,.png,.gif,.webp"
                    style="display:none;"
                >
                <textarea
                    id="messageInput"
                    class="input @error('content') is-invalid @enderror"
                    name="content"
                    placeholder="メッセージを入力…"
                    aria-label="メッセージを入力"
                ></textarea>
                @error('content')
                    <span class="error-message">{{ $message }}</span>
                @enderror

                {{-- 添付ボタン & 添付一覧 --}}
                <div style="margin-top:0.5rem; display:flex; flex-direction:column; gap:0.2rem;">
                    <button
                        class="pscc-attach-button"
                        id="attachButton"
                        title="ファイルを添付"
                        type="button"
                        aria-label="ファイルを添付"
                        style="display:inline-flex; align-items:center; gap:0.35rem; padding:0; border:none; background:none; color:#64748b; font-weight:800; cursor:pointer;"
                    >
                        <svg class="pscc-attach-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:1.1rem; height:1.1rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                        <span>ファイルを添付</span>
                    </button>
                    <div style="font-size:0.875rem; color:#64748b; font-weight:800; line-height:1.15;">
                        3ファイル 合計10MB まで
                    </div>
                </div>

                <div
                    id="dmAttachmentList"
                    style="font-size:0.875rem; color:#64748b; margin-top:0.5rem; word-break:break-word; line-height:1.5; display:none;"
                ></div>

                @error('attachments')
                    <div class="pscc-field-error" style="padding:0.5rem 0; font-weight:800; color:#DC2626;">
                        <span>{{ $message }}</span>
                    </div>
                @enderror
                @error('attachments.*')
                    <div class="pscc-field-error" style="padding:0.5rem 0; font-weight:800; color:#DC2626;">
                        <span>{{ $message }}</span>
                    </div>
                @enderror

                <button class="send w-full md:w-auto" type="submit" id="sendButton" disabled>送信</button>
            </form>
        </section>
    </main>

    <script>
        (function () {
            const header = document.querySelector('header.header');
            const toggle = document.getElementById('mobileNavToggle');
            const mobileNav = document.getElementById('mobileNav');
            if (!header || !toggle || !mobileNav) return;

            const OPEN_CLASS = 'is-mobile-nav-open';
            const isOpen = () => header.classList.contains(OPEN_CLASS);

            const open = () => {
                header.classList.add(OPEN_CLASS);
                toggle.setAttribute('aria-expanded', 'true');
            };

            const close = () => {
                header.classList.remove(OPEN_CLASS);
                toggle.setAttribute('aria-expanded', 'false');
            };

            toggle.addEventListener('click', (e) => {
                e.stopPropagation();
                if (isOpen()) close();
                else open();
            });

            document.addEventListener('click', (e) => {
                if (!header.contains(e.target)) close();
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') close();
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth >= 768) close();
            });
        })();
    </script>
    <script>
        (function () {
            const dropdown = document.getElementById('userDropdown');
            const toggle = document.getElementById('userDropdownToggle');
            const menu = document.getElementById('userDropdownMenu');
            if (!dropdown || !toggle || !menu) return;

            const open = () => {
                dropdown.classList.add('is-open');
                toggle.setAttribute('aria-expanded', 'true');
            };

            const close = () => {
                dropdown.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
            };

            const isOpen = () => dropdown.classList.contains('is-open');

            toggle.addEventListener('click', (e) => {
                e.stopPropagation();
                if (isOpen()) close();
                else open();
            });

            document.addEventListener('click', (e) => {
                if (!dropdown.contains(e.target)) close();
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') close();
            });
        })();
    </script>

    <script>
        (function () {
            const el = document.getElementById('messages');
            if (el) el.scrollTop = el.scrollHeight;
        })();
    </script>

    {{-- 添付（アップロード）UI + 送信ボタン活性化 --}}
    <script>
        (function () {
            const input = document.getElementById('messageInput');
            const sendBtn = document.getElementById('sendButton');
            const attachBtn = document.getElementById('attachButton');
            const fileInput = document.getElementById('dmAttachment');
            const attachmentList = document.getElementById('dmAttachmentList');
            let selectedFiles = [];

            if (!input || !sendBtn || !fileInput) return;

            const toggle = () => {
                const hasText = !!(input.value && input.value.trim());
                const hasFile = selectedFiles && selectedFiles.length > 0;
                sendBtn.disabled = !(hasText || hasFile);
            };

            input.addEventListener('input', toggle);

            const safeName = (s) => String(s).replace(/[&<>"']/g, (c) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;',
            }[c] || c));

            const renderAttachmentList = () => {
                if (!attachmentList) return;
                if (!selectedFiles || selectedFiles.length <= 0) {
                    attachmentList.style.display = 'none';
                    attachmentList.innerHTML = '';
                    return;
                }

                attachmentList.style.display = 'block';
                attachmentList.innerHTML = selectedFiles
                    .map((f, idx) => {
                        const displayName = safeName(f.name);
                        return `
                            <div style="display:flex; align-items:center; justify-content:flex-start; gap:0.25rem;">
                                <div style="word-break:break-all; color:#334155; font-weight:800;">・${displayName}</div>
                                <button
                                    type="button"
                                    class="dm-attachment-remove"
                                    data-idx="${idx}"
                                    aria-label="この添付を削除"
                                    title="削除"
                                    style="border:none; background:none; color:#f43f5e; font-weight:900; cursor:pointer; font-size:1.25rem; line-height:1; padding:0; margin:0;"
                                >×</button>
                            </div>
                        `;
                    })
                    .join('');
            };

            if (attachBtn) {
                attachBtn.addEventListener('click', () => fileInput.click());
            }

            fileInput.addEventListener('change', () => {
                const pickedFiles = fileInput && fileInput.files ? Array.from(fileInput.files) : [];
                if (pickedFiles.length <= 0) {
                    selectedFiles = [];
                    renderAttachmentList();
                    toggle();
                    return;
                }

                const nextFiles = [...selectedFiles];
                pickedFiles.forEach((picked) => {
                    if (nextFiles.length >= 3) return;
                    const dup = nextFiles.some((current) =>
                        current.name === picked.name &&
                        current.size === picked.size &&
                        current.lastModified === picked.lastModified
                    );
                    if (!dup) nextFiles.push(picked);
                });

                selectedFiles = nextFiles.slice(0, 3);

                const dt = new DataTransfer();
                selectedFiles.forEach((f) => dt.items.add(f));
                fileInput.files = dt.files;

                renderAttachmentList();
                toggle();
            });

            attachmentList?.addEventListener('click', (evt) => {
                const btn = evt.target && evt.target.closest ? evt.target.closest('.dm-attachment-remove') : null;
                if (!btn) return;
                const idx = Number(btn.getAttribute('data-idx'));
                if (Number.isNaN(idx)) return;

                selectedFiles.splice(idx, 1);
                const dt = new DataTransfer();
                selectedFiles.forEach((f) => dt.items.add(f));
                fileInput.files = dt.files;

                renderAttachmentList();
                toggle();
            }, { passive: true });

            renderAttachmentList();
            toggle();
        })();
    </script>

    <!-- 削除確認モーダル -->
    <div id="confirmDeleteModal" role="dialog" aria-hidden="true" aria-labelledby="confirmDeleteTitle" style="display:block;">
        <div style="position:fixed;inset:0;display:flex;align-items:center;justify-content:center;pointer-events:none;z-index:1000;">
            <div id="confirmDeleteDialog" style="pointer-events:auto;width:min(540px,92%);background:#fff;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,0.15);padding:1.25rem;display:none;" aria-modal="true">
                <h2 id="confirmDeleteTitle" style="margin:0 0 0.5rem;font-size:1.05rem;font-weight:800;color:#0f172a;">本当に削除しますか？</h2>
                <p style="margin:0 0 1rem;color:#64748b;">この操作は取り消せません。よろしければ「削除する」をクリックしてください。</p>
                <div style="display:flex;gap:0.75rem;">
                    <button id="cancelDeleteBtn" style="flex:1;padding:0.6rem 0.9rem;border-radius:8px;border:1px solid #e6eaf2;background:#fafbfc;cursor:pointer;">キャンセル</button>
                    <button id="confirmDeleteBtn" style="flex:1;padding:0.6rem 0.9rem;border-radius:8px;border:none;background:#d73a49;color:#fff;cursor:pointer;">削除する</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            let pendingForm = null;
            const modal = document.getElementById('confirmDeleteModal');
            const dialog = document.getElementById('confirmDeleteDialog');
            const confirmBtn = document.getElementById('confirmDeleteBtn');
            const cancelBtn = document.getElementById('cancelDeleteBtn');
            function openModal(form) {
                pendingForm = form;
                if (modal && dialog) {
                    modal.setAttribute('aria-hidden','false');
                    dialog.style.display = 'block';
                    modal.classList.add('is-open');
                    confirmBtn && confirmBtn.focus();
                }
            }
            function closeModal() {
                pendingForm = null;
                if (modal && dialog) {
                    dialog.style.display = 'none';
                    modal.setAttribute('aria-hidden','true');
                    modal.classList.remove('is-open');
                }
            }
            document.addEventListener('click', (e) => {
                const trigger = e.target.closest && e.target.closest('.delete-trigger');
                if (trigger) { e.preventDefault(); const form = trigger.closest('form'); if (form) openModal(form); }
            });
            modal && modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
            cancelBtn && cancelBtn.addEventListener('click', (e) => { e.preventDefault(); closeModal(); });
            confirmBtn && confirmBtn.addEventListener('click', (e) => { e.preventDefault(); if (!pendingForm) return closeModal(); pendingForm.submit(); });
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && modal && modal.classList.contains('is-open')) closeModal(); });
        })();
    </script>
</body>
</html>
--}}