@extends('layouts.public')

@section('title', 'メッセージ一覧')

@push('styles')
<style>
    .dm-wrap {
        max-width: 72rem;
        margin: 0 auto;
        padding: 2rem 1rem 3rem;
    }

    .dm-header {
        margin-bottom: 1.5rem;
    }

    .dm-title {
        font-size: 1.875rem;
        font-weight: 900;
        color: #111827;
        margin-bottom: 0.25rem;
    }

    .dm-subtitle {
        color: #4b5563;
    }

    .dm-tabs {
        display: flex;
        gap: 0.75rem;
        margin: 1.5rem 0;
        flex-wrap: wrap;
    }

    .dm-tab {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        border-radius: 9999px;
        text-decoration: none;
        font-weight: 800;
        border: 1px solid #dbe2ee;
        color: #374151;
        background: #ffffff;
    }

    .dm-tab.active {
        background: #f97316;
        color: #ffffff;
        border-color: #f97316;
    }

    .dm-tab-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 1.5rem;
        height: 1.5rem;
        padding: 0 0.4rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        background: rgba(255,255,255,0.2);
    }

    .dm-list {
        display: grid;
        gap: 1rem;
    }

    .dm-card {
        display: block;
        text-decoration: none;
        color: inherit;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        padding: 1.25rem;
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
    }

    .dm-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
        border-color: #fdba74;
    }

    .dm-card-top {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: flex-start;
        margin-bottom: 0.75rem;
    }

    .dm-participant {
        display: flex;
        gap: 0.75rem;
        align-items: center;
        min-width: 0;
    }

    .dm-avatar {
        width: 3rem;
        height: 3rem;
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

    .dm-name {
        font-size: 1.125rem;
        font-weight: 900;
        color: #111827;
        margin-bottom: 0.125rem;
    }

    .dm-role {
        font-size: 0.875rem;
        color: #6b7280;
    }

    .dm-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.7rem;
        border-radius: 9999px;
        font-size: 0.85rem;
        font-weight: 900;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        color: #374151;
        white-space: nowrap;
    }

    .dm-pill.unread {
        background: #fff7ed;
        color: #c2410c;
        border-color: #fdba74;
    }

    .dm-preview {
        color: #4b5563;
        line-height: 1.7;
        margin-bottom: 1rem;
        white-space: pre-wrap;
        word-break: break-word;
    }

    .dm-meta {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        padding-top: 1rem;
        border-top: 1px solid #e5e7eb;
        color: #6b7280;
        font-size: 0.875rem;
    }

    .dm-empty {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        padding: 3rem 1.5rem;
        text-align: center;
        color: #6b7280;
    }
</style>
@endpush

@section('content')
@php
    $isUnreadFilter = $filter === 'unread';
@endphp

<div class="dm-wrap">
    <div class="dm-header">
        <h1 class="dm-title">メッセージ</h1>
        <p class="dm-subtitle">純粋なチャットのやり取りを一覧で確認できます。</p>
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

    <div class="dm-tabs">
        <a class="dm-tab {{ !$isUnreadFilter ? 'active' : '' }}" href="{{ route('direct-messages.index', ['filter' => 'all']) }}">
            すべて
            <span class="dm-tab-badge">{{ $allCount ?? 0 }}</span>
        </a>
        <a class="dm-tab {{ $isUnreadFilter ? 'active' : '' }}" href="{{ route('direct-messages.index', ['filter' => 'unread']) }}">
            未読
            <span class="dm-tab-badge">{{ $unreadCount ?? 0 }}</span>
        </a>
    </div>

    @if(($conversations ?? collect())->isNotEmpty())
        <div class="dm-list">
            @foreach($conversations as $conversation)
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
                    $latestMessage = $conversation->messages->last();
                    $preview = $latestMessage?->body ?? 'まだメッセージはありません。';
                    $sentAt = $conversation->latest_message_at?->format('Y/m/d H:i') ?? '-';
                    $isUnread = $viewerRole === 'company'
                        ? (bool) $conversation->is_unread_for_company
                        : (bool) $conversation->is_unread_for_freelancer;
                @endphp
                <a href="{{ route('direct-messages.show', ['direct_conversation' => $conversation->id]) }}" class="dm-card">
                    <div class="dm-card-top">
                        <div class="dm-participant">
                            @if($avatarSrc)
                                <img src="{{ $avatarSrc }}" alt="{{ $counterpartName }}" class="dm-avatar">
                            @else
                                <div class="dm-avatar">{{ mb_substr($counterpartName, 0, 1) }}</div>
                            @endif
                            <div class="min-w-0">
                                <div class="dm-name">{{ $counterpartName }}</div>
                                <div class="dm-role">{{ $counterpartRole }}とのメッセージ</div>
                            </div>
                        </div>
                        <div class="dm-pill {{ $isUnread ? 'unread' : '' }}">
                            {{ $isUnread ? '未読' : '既読' }}
                        </div>
                    </div>

                    <div class="dm-preview">{{ $preview }}</div>

                    <div class="dm-meta">
                        <span>最終更新: {{ $sentAt }}</span>
                        <span>チャットを開く</span>
                    </div>
                </a>
            @endforeach
        </div>

        @if($conversations->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $conversations->links() }}
            </div>
        @endif
    @else
        <div class="dm-empty">
            <p class="text-base font-semibold">
                {{ $isUnreadFilter ? '未読メッセージはありません' : 'メッセージはまだありません' }}
            </p>
        </div>
    @endif
</div>
@endsection
