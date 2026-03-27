@extends('layouts.public')

@section('title', 'フリーランス チャット')

@push('styles')
    @include('partials.pscc-chat-core-styles')
@endpush

@section('content')
@php
    $viewerId = (int) ($viewerProfile->id ?? 0);

    if ($conversation->company_id !== null) {
        $counterpartName = $conversation->company?->contact_name
            ?? $conversation->company?->name
            ?? '企業';
        $counterpartRole = '企業';
        $counterpartIcon = $conversation->company?->icon_path ?? null;
        $counterpartUserId = null;
    } elseif (
        $conversation->initiator_type === 'freelancer'
        && $conversation->initiator_id !== null
        && $conversation->freelancer_id !== null
    ) {
        $counterpartFreelancerId = ($viewerId === (int) $conversation->freelancer_id)
            ? (int) $conversation->initiator_id
            : (int) $conversation->freelancer_id;
        $counterpartFreelancer = \App\Models\Freelancer::find($counterpartFreelancerId);
        $counterpartName = $counterpartFreelancer?->display_name ?? 'フリーランス';
        $counterpartRole = 'フリーランス';
        $counterpartIcon = $counterpartFreelancer?->icon_path ?? null;
        $counterpartUserId = $counterpartFreelancer?->user_id;
    } else {
        $counterpartFreelancer = $conversation->freelancer;
        if ($counterpartFreelancer && (int) $counterpartFreelancer->id === $viewerId) {
            $counterpartName = 'フリーランス';
            $counterpartRole = 'フリーランス';
            $counterpartIcon = null;
            $counterpartUserId = null;
        } else {
            $counterpartName = $counterpartFreelancer?->display_name ?? 'フリーランス';
            $counterpartRole = 'フリーランス';
            $counterpartIcon = $counterpartFreelancer?->icon_path ?? null;
            $counterpartUserId = $counterpartFreelancer?->user_id;
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
        <form class="pscc-input-content" method="POST" enctype="multipart/form-data" action="{{ route('direct-messages.reply', ['direct_conversation' => $conversation->id]) }}">
            @csrf
            <input type="file" id="dmAttachment" name="attachments[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.zip,.jpg,.jpeg,.png,.gif,.webp" style="display:none;">
            <button class="pscc-attach-button" id="attachButton" title="ファイルを添付" type="button" aria-label="ファイルを添付">
                <svg class="pscc-attach-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                </svg>
            </button>
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
