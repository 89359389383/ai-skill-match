@extends('layouts.public')

@section('title', '購入したスキル')

@push('styles')
<style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .ps-container {
            padding: 2rem 0;
        }

        .ps-wrapper {
            max-width: 72rem;
            margin: 0 auto;
            padding: 0 1rem;
        }

        @media (min-width: 640px) {
            .ps-wrapper {
                padding: 0 1.5rem;
            }
        }

        @media (min-width: 1024px) {
            .ps-wrapper {
                padding: 0 2rem;
            }
        }

        .ps-header {
            margin-bottom: 2rem;
        }

        .ps-header-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.5rem;
        }

        .ps-icon-box {
            padding: 0.75rem;
            background: linear-gradient(to bottom right, #F97316, #EA580C);
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .ps-icon {
            width: 1.5rem;
            height: 1.5rem;
            color: white;
        }

        .ps-title {
            font-size: 1.875rem;
            font-weight: bold;
            color: #111827;
        }

        .ps-subtitle {
            color: #4B5563;
            margin-left: 60px;
        }

        .ps-tabs-container {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            border: 1px solid #E5E7EB;
            margin-bottom: 1.5rem;
        }

        .ps-tabs {
            display: flex;
            border-bottom: 1px solid #E5E7EB;
        }

        .ps-tab {
            flex: 1;
            padding: 1rem 1.5rem;
            font-weight: 600;
            text-align: center;
            background: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }

        .ps-tab-active {
            color: #EA580C;
            background: #FFF7ED;
            border-bottom: 2px solid #EA580C;
        }

        .ps-tab-inactive {
            color: #4B5563;
        }

        .ps-tab-inactive:hover {
            color: #111827;
            background: #F9FAFB;
        }

        .ps-badge {
            display: inline-block;
            margin-left: 0.5rem;
            padding: 0.125rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: bold;
        }

        .ps-badge-orange {
            background: #FFEDD5;
            color: #C2410C;
        }

        .ps-badge-gray {
            background: #F3F4F6;
            color: #374151;
        }

        .ps-transactions {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .ps-card {
            display: block;
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            border: 1px solid #E5E7EB;
            transition: all 0.2s;
            text-decoration: none;
            color: inherit;
        }

        .ps-card:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border-color: #FDBA74;
        }

        .ps-card-content {
            padding: 1.5rem;
        }

        .ps-card-flex {
            display: flex;
            gap: 1rem;
        }

        .ps-skill-image {
            width: 8rem;
            height: 6rem;
            object-fit: cover;
            border-radius: 0.5rem;
            flex-shrink: 0;
        }

        .ps-card-info {
            flex: 1;
            min-width: 0;
        }

        .ps-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 0.75rem;
        }

        .ps-card-header-left {
            flex: 1;
            min-width: 0;
        }

        .ps-skill-title {
            font-size: 1.125rem;
            font-weight: bold;
            color: #111827;
            margin-bottom: 0.5rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .ps-seller-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 0.875rem;
            color: #4B5563;
        }

        .ps-seller-avatar-box {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .ps-seller-avatar {
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 9999px;
            object-fit: cover;
        }

        .ps-date-box {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .ps-date-icon {
            width: 1rem;
            height: 1rem;
        }

        .ps-status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .ps-status-progress {
            background: #DBEAFE;
            color: #1E40AF;
        }

        .ps-status-delivered {
            background: #FFEDD5;
            color: #C2410C;
        }

        .ps-status-completed {
            background: #D1FAE5;
            color: #047857;
        }

        .ps-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .ps-price-box {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            color: #EA580C;
            font-weight: bold;
            font-size: 1.125rem;
        }

        .ps-price-icon {
            width: 1.25rem;
            height: 1.25rem;
        }

        .ps-rating-box {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .ps-rating-label {
            font-size: 0.875rem;
            color: #4B5563;
        }

        .ps-stars {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .ps-star {
            width: 1rem;
            height: 1rem;
        }

        .ps-star-filled {
            color: #FB923C;
            fill: #FB923C;
        }

        .ps-star-empty {
            color: #D1D5DB;
            fill: #D1D5DB;
        }

        .ps-chat-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #EA580C;
            font-weight: 600;
        }

        .ps-chat-icon {
            width: 1.25rem;
            height: 1.25rem;
        }

        .ps-notification {
            margin-top: 0.75rem;
            padding: 0.75rem;
            border-radius: 0.5rem;
        }

        .ps-notification-delivered {
            background: #FFF7ED;
            border: 1px solid #FED7AA;
        }

        .ps-notification-text {
            font-size: 0.875rem;
            font-weight: 600;
        }

        .ps-notification-text-delivered {
            color: #9A3412;
        }

        .ps-empty {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            border: 1px solid #E5E7EB;
            padding: 3rem;
            text-align: center;
        }

        .ps-empty-icon {
            width: 4rem;
            height: 4rem;
            color: #D1D5DB;
            margin: 0 auto 1rem;
        }

        .ps-empty-text {
            color: #6B7280;
            font-size: 1.125rem;
        }

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
@endpush

@section('content')
@php
    $tab = request('tab', 'active');
    $currentCount = isset($currentTransactions) ? $currentTransactions->count() : 0;
    $pastCount = isset($pastTransactions) ? $pastTransactions->count() : 0;
    $viewerRole = auth()->user()?->role;
    $isBuyer = $viewerRole === 'buyer';

    $statusLabel = function (?string $status): string {
        return match ($status) {
            'waiting_payment' => '支払い待ち',
            'waiting_payment' => '支払い待ち',
            'in_progress' => '取引中',
            'delivered' => '納品済み',
            'completed' => '完了',
            default => '不明',
        };
    };
    $statusClass = function (?string $status): string {
        return match ($status) {
            'waiting_payment' => 'ps-status-progress',
            'in_progress' => 'ps-status-progress',
            'delivered' => 'ps-status-delivered',
            'completed' => 'ps-status-completed',
            default => 'ps-status-progress',
        };
    };
@endphp

<div class="ps-container">
    <div class="ps-wrapper">
        <!-- ヘッダー -->
        <div class="ps-header">
            <div class="ps-header-title">
                <div class="ps-icon-box">
                    <svg class="ps-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h1 class="ps-title">購入したスキル</h1>
            </div>
            <p class="ps-subtitle">あなたが購入したスキルの取引履歴です</p>
        </div>

        @if (session('success') || session('error'))
            <div class="mb-4 space-y-2">
                @if (session('success'))
                    <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                        {{ session('error') }}
                    </div>
                @endif
            </div>
        @endif

        <!-- タブ -->
        <div class="ps-tabs-container">
            <div class="ps-tabs">
                <a
                    class="ps-tab {{ $tab === 'active' ? 'ps-tab-active' : 'ps-tab-inactive' }}"
                    href="{{ $isBuyer
                        ? route('buyer.purchased-skills.index', array_filter(['tab' => 'active']))
                        : route('purchased-skills.index', array_filter(['tab' => 'active'])) }}"
                >
                    現在取引中
                    <span class="ps-badge ps-badge-orange">{{ $currentCount }}</span>
                </a>
                <a
                    class="ps-tab {{ $tab === 'past' ? 'ps-tab-active' : 'ps-tab-inactive' }}"
                    href="{{ $isBuyer
                        ? route('buyer.purchased-skills.index', array_filter(['tab' => 'past']))
                        : route('purchased-skills.index', array_filter(['tab' => 'past'])) }}"
                >
                    過去の取引
                    <span class="ps-badge ps-badge-gray">{{ $pastCount }}</span>
                </a>
            </div>
        </div>

        <!-- 取引一覧 -->
        <div class="ps-transactions">
            @php
                $paginator = $tab === 'past'
                    ? ($pastTransactions ?? null)
                    : ($currentTransactions ?? null);
                $isPaginator = $paginator instanceof \Illuminate\Pagination\AbstractPaginator;
                $pTotal = $isPaginator ? (int) $paginator->total() : 0;
                $pFirstItem = $isPaginator ? (int) $paginator->firstItem() : 0;
                $pLastItem = $isPaginator ? (int) $paginator->lastItem() : 0;
                $pLastPage = $isPaginator ? (int) $paginator->lastPage() : 1;
                $pCurPage = $isPaginator ? (int) $paginator->currentPage() : 1;
                $baseUrl = $isBuyer
                    ? route('buyer.purchased-skills.index', array_filter(['tab' => $tab]))
                    : route('purchased-skills.index', array_filter(['tab' => $tab]));
            @endphp

            <p class="text-sm text-gray-600 mb-4">
                {{ number_format($pTotal) }} 件中
                {{ number_format($pFirstItem) }} - {{ number_format($pLastItem) }}
                件表示
            </p>

            @php
                $list = $tab === 'past' ? ($pastTransactions ?? collect()) : ($currentTransactions ?? collect());
            @endphp

            @forelse ($list as $tx)
                @php
                    $listing = $tx->skillListing;
                    $seller = $listing?->freelancer;
                    $thumb = $listing?->thumbnail_url;
                    $price = (int) ($tx->amount ?? 0);
                    $date = $tx->purchased_at?->format('Y/n/j') ?? '-';
                    $isDelivered = ($tx->transaction_status === 'delivered');
                @endphp

                <a href="{{ $isBuyer
                    ? route('buyer.transactions.show', ['skill_order' => $tx->id])
                    : route('transactions.show', ['skill_order' => $tx->id]) }}" class="ps-card">
                    <div class="ps-card-content">
                        <div class="ps-card-flex">
                            @if (!empty($thumb))
                                <img src="{{ $thumb }}" alt="{{ $listing?->title ?? 'スキル' }}" class="ps-skill-image">
                            @else
                                <div class="ps-skill-image" style="background:#E5E7EB; display:flex; align-items:center; justify-content:center; color:#6B7280; font-size:12px;">
                                    No Image
                                </div>
                            @endif

                            <div class="ps-card-info">
                                <div class="ps-card-header">
                                    <div class="ps-card-header-left">
                                        <h3 class="ps-skill-title">{{ $listing?->title ?? '（削除されたスキル）' }}</h3>
                                        <div class="ps-seller-info">
                                            <div class="ps-seller-avatar-box">
                                                @php
                                                    $sellerName = $seller?->display_name ?? '出品者';
                                                    $sellerIcon = $seller?->icon_path;
                                                    $sellerAvatarSrc = null;
                                                    if (!empty($sellerIcon)) {
                                                        if (str_starts_with($sellerIcon, 'http://') || str_starts_with($sellerIcon, 'https://')) {
                                                            $sellerAvatarSrc = $sellerIcon;
                                                        } else {
                                                            $iconRel = ltrim($sellerIcon, '/');
                                                            if (str_starts_with($iconRel, 'storage/')) {
                                                                $iconRel = substr($iconRel, strlen('storage/'));
                                                            }
                                                            $sellerAvatarSrc = asset('storage/' . $iconRel);
                                                        }
                                                    }
                                                    $sellerInitial = mb_substr($sellerName, 0, 1);
                                                @endphp
                                                @if (!empty($sellerAvatarSrc))
                                                    <img src="{{ $sellerAvatarSrc }}" alt="{{ $sellerName }}" class="ps-seller-avatar">
                                                @else
                                                    <div class="ps-seller-avatar" style="background:#E5E7EB; display:flex; align-items:center; justify-content:center; color:#374151; font-weight:700;">
                                                        {{ $sellerInitial }}
                                                    </div>
                                                @endif
                                                <span>{{ $sellerName }}</span>
                                            </div>
                                            <div class="ps-date-box">
                                                <svg class="ps-date-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span>{{ $date }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="ps-status-badge {{ $statusClass($tx->transaction_status) }}">
                                        {{ $statusLabel($tx->transaction_status) }}
                                    </span>
                                </div>

                                <div class="ps-card-footer">
                                    <div class="ps-price-box">
                                        <svg class="ps-price-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>¥{{ number_format($price) }}</span>
                                    </div>

                                    @if ($tx->transaction_status === 'completed')
                                        @php
                                            $avg = (float) ($listing?->rating_average ?? 0);
                                            $stars = (int) round($avg);
                                        @endphp
                                        <div class="ps-rating-box" title="このスキルの平均評価">
                                            <span class="ps-rating-label">評価</span>
                                            <div class="ps-stars" aria-label="平均評価 {{ $avg }}">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <svg class="ps-star {{ $i <= $stars ? 'ps-star-filled' : 'ps-star-empty' }}" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                            </div>
                                        </div>
                                    @else
                                        <div class="ps-chat-link">
                                            <svg class="ps-chat-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                            <span>チャットを開く</span>
                                        </div>
                                    @endif
                                </div>

                                @if ($isDelivered)
                                    <div class="ps-notification ps-notification-delivered">
                                        <p class="ps-notification-text ps-notification-text-delivered">
                                            📦 納品されました。内容を確認して承認してください。
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="ps-empty">
                    <svg class="ps-empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p class="ps-empty-text">
                        {{ $tab === 'past' ? '過去の取引はありません' : '現在進行中の取引はありません' }}
                    </p>
                </div>
            @endforelse
        </div>

        @php
            $paginator = $tab === 'past'
                ? ($pastTransactions ?? null)
                : ($currentTransactions ?? null);
        @endphp

        @if($pLastPage >= 1)
            @php
                $pLast = $pLastPage;
                $pCur = $pCurPage;
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
                    @if($isPaginator && $paginator->onFirstPage())
                        <span class="profiles-page-nav profiles-page-nav-disabled" aria-disabled="true">&lt;</span>
                    @else
                        @if($isPaginator)
                            <a href="{{ $paginator->previousPageUrl() }}" class="profiles-page-nav" rel="prev" aria-label="前のページ">&lt;</a>
                        @else
                            <span class="profiles-page-nav profiles-page-nav-disabled" aria-disabled="true">&lt;</span>
                        @endif
                    @endif

                    @foreach($profilePaginationElements as $el)
                        @if($el['type'] === 'ellipsis')
                            <span class="profiles-page-ellipsis" aria-hidden="true">...</span>
                        @else
                            @if($el['n'] === $pCur)
                                <span class="profiles-page-link profiles-page-active">{{ $el['n'] }}</span>
                            @else
                                <a href="{{ $isPaginator ? $paginator->url($el['n']) : ($baseUrl . '?page=' . $el['n']) }}" class="profiles-page-link">{{ $el['n'] }}</a>
                            @endif
                        @endif
                    @endforeach

                    @if($isPaginator ? $paginator->hasMorePages() : false)
                        <a href="{{ $paginator->nextPageUrl() }}" class="profiles-page-nav" rel="next" aria-label="次のページ">&gt;</a>
                    @else
                        <span class="profiles-page-nav profiles-page-nav-disabled" aria-disabled="true">&gt;</span>
                    @endif
                </nav>
            @endif
        @endif
    </div>
</div>
@endsection
