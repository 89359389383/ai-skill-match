@extends('layouts.public')

@section('title', '販売実績')

@push('styles')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
            background-color: #F9FAFB;
            color: #111827;
            line-height: 1.5;
        }

        .sp-container {
            min-height: 100vh;
            padding: 2rem 0;
        }

        .sp-wrapper {
            max-width: 72rem;
            margin: 0 auto;
            padding: 0 1rem;
        }

        @media (min-width: 640px) {
            .sp-wrapper {
                padding: 0 1.5rem;
            }
        }

        @media (min-width: 1024px) {
            .sp-wrapper {
                padding: 0 2rem;
            }
        }

        .sp-header {
            margin-bottom: 2rem;
        }

        .sp-header-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.5rem;
        }

        .sp-icon-box {
            padding: 0.75rem;
            background: linear-gradient(to bottom right, #F97316, #EA580C);
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .sp-icon {
            width: 1.5rem;
            height: 1.5rem;
            color: white;
        }

        .sp-title {
            font-size: 1.875rem;
            font-weight: bold;
            color: #111827;
        }

        .sp-subtitle {
            color: #4B5563;
            margin-left: 60px;
        }

        /* 統計カード */
        .sp-stats-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        @media (min-width: 768px) {
            .sp-stats-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .sp-stat-card {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            border: 1px solid #E5E7EB;
            padding: 1.5rem;
        }

        .sp-stat-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.5rem;
        }

        .sp-stat-icon-box {
            padding: 0.5rem;
            border-radius: 0.5rem;
        }

        .sp-stat-icon-box-green {
            background: #D1FAE5;
        }

        .sp-stat-icon-box-blue {
            background: #DBEAFE;
        }

        .sp-stat-icon-box-orange {
            background: #FFEDD5;
        }

        .sp-stat-icon {
            width: 1.25rem;
            height: 1.25rem;
        }

        .sp-stat-icon-green {
            color: #059669;
        }

        .sp-stat-icon-blue {
            color: #2563EB;
        }

        .sp-stat-icon-orange {
            color: #EA580C;
        }

        .sp-stat-label {
            font-size: 0.875rem;
            color: #4B5563;
            font-weight: 500;
        }

        .sp-stat-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #111827;
        }

        .sp-tabs-container {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            border: 1px solid #E5E7EB;
            margin-bottom: 1.5rem;
        }

        .sp-tabs {
            display: flex;
            border-bottom: 1px solid #E5E7EB;
        }

        .sp-tab {
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

        .sp-tab-active {
            color: #EA580C;
            background: #FFF7ED;
            border-bottom: 2px solid #EA580C;
        }

        .sp-tab-inactive {
            color: #4B5563;
        }

        .sp-tab-inactive:hover {
            color: #111827;
            background: #F9FAFB;
        }

        .sp-badge {
            display: inline-block;
            margin-left: 0.5rem;
            padding: 0.125rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: bold;
        }

        .sp-badge-orange {
            background: #FFEDD5;
            color: #C2410C;
        }

        .sp-badge-gray {
            background: #F3F4F6;
            color: #374151;
        }

        .sp-transactions {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .sp-card {
            display: block;
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            border: 1px solid #E5E7EB;
            transition: all 0.2s;
            text-decoration: none;
            color: inherit;
        }

        .sp-card:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border-color: #FDBA74;
        }

        .sp-card-content {
            padding: 1.5rem;
        }

        .sp-card-flex {
            display: flex;
            gap: 1rem;
        }

        .sp-skill-image {
            width: 8rem;
            height: 6rem;
            object-fit: cover;
            border-radius: 0.5rem;
            flex-shrink: 0;
        }

        .sp-card-info {
            flex: 1;
            min-width: 0;
        }

        .sp-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 0.75rem;
        }

        .sp-card-header-left {
            flex: 1;
            min-width: 0;
        }

        .sp-skill-title {
            font-size: 1.125rem;
            font-weight: bold;
            color: #111827;
            margin-bottom: 0.5rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sp-buyer-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 0.875rem;
            color: #4B5563;
        }

        .sp-buyer-avatar-box {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .sp-buyer-avatar {
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 9999px;
            object-fit: cover;
        }

        .sp-date-box {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .sp-date-icon {
            width: 1rem;
            height: 1rem;
        }

        .sp-status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .sp-status-progress {
            background: #DBEAFE;
            color: #1E40AF;
        }

        .sp-status-delivered {
            background: #FFEDD5;
            color: #C2410C;
        }

        .sp-status-completed {
            background: #D1FAE5;
            color: #047857;
        }

        .sp-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sp-price-box {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            color: #EA580C;
            font-weight: bold;
            font-size: 1.125rem;
        }

        .sp-price-icon {
            width: 1.25rem;
            height: 1.25rem;
        }

        .sp-chat-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #EA580C;
            font-weight: 600;
        }

        .sp-chat-icon {
            width: 1.25rem;
            height: 1.25rem;
        }

        .sp-notification {
            margin-top: 0.75rem;
            padding: 0.75rem;
            border-radius: 0.5rem;
        }

        .sp-notification-progress {
            background: #EFF6FF;
            border: 1px solid #BFDBFE;
        }

        .sp-notification-delivered {
            background: #FFF7ED;
            border: 1px solid #FED7AA;
        }

        .sp-notification-text {
            font-size: 0.875rem;
            font-weight: 600;
        }

        .sp-notification-text-progress {
            color: #1E3A8A;
        }

        .sp-notification-text-delivered {
            color: #9A3412;
        }

        .sp-empty {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            border: 1px solid #E5E7EB;
            padding: 3rem;
            text-align: center;
        }

        .sp-empty-icon {
            width: 4rem;
            height: 4rem;
            color: #D1D5DB;
            margin: 0 auto 1rem;
        }

        .sp-empty-text {
            color: #6B7280;
            font-size: 1.125rem;
        }
    </style>
@endpush

@section('content')
@php
    $tab = request('tab', 'active');
    $currentCount = isset($currentTransactions) ? $currentTransactions->count() : 0;
    $pastCount = isset($pastTransactions) ? $pastTransactions->count() : 0;

    $statusLabel = function (?string $status): string {
        return match ($status) {
            'in_progress' => '作業中',
            'delivered' => '納品待ち',
            'completed' => '完了',
            default => '不明',
        };
    };
    $statusClass = function (?string $status): string {
        return match ($status) {
            'in_progress' => 'sp-status-progress',
            'delivered' => 'sp-status-delivered',
            'completed' => 'sp-status-completed',
            default => 'sp-status-progress',
        };
    };
@endphp

<div class="sp-container">
    <div class="sp-wrapper">
        <!-- ヘッダー -->
        <div class="sp-header">
            <div class="sp-header-title">
                <div class="sp-icon-box">
                    <svg class="sp-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <h1 class="sp-title">販売実績</h1>
            </div>
            <p class="sp-subtitle">あなたが販売したスキルの取引履歴です</p>
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

        <!-- 売上統計 -->
        <div class="sp-stats-grid">
            <div class="sp-stat-card">
                <div class="sp-stat-header">
                    <div class="sp-stat-icon-box sp-stat-icon-box-green">
                        <svg class="sp-stat-icon sp-stat-icon-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="sp-stat-label">総売上</span>
                </div>
                <p class="sp-stat-value">¥{{ number_format((int) ($totalSales ?? 0)) }}</p>
            </div>

            <div class="sp-stat-card">
                <div class="sp-stat-header">
                    <div class="sp-stat-icon-box sp-stat-icon-box-blue">
                        <svg class="sp-stat-icon sp-stat-icon-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <span class="sp-stat-label">完了取引数</span>
                </div>
                <p class="sp-stat-value">{{ (int) ($completedCount ?? 0) }}件</p>
            </div>

            <div class="sp-stat-card">
                <div class="sp-stat-header">
                    <div class="sp-stat-icon-box sp-stat-icon-box-orange">
                        <svg class="sp-stat-icon sp-stat-icon-orange" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                    <span class="sp-stat-label">平均評価</span>
                </div>
                <p class="sp-stat-value">{{ number_format((float) ($avgRating ?? 0), 1) }}</p>
            </div>
        </div>

        <!-- タブ -->
        <div class="sp-tabs-container">
            <div class="sp-tabs">
                <a
                    class="sp-tab {{ $tab === 'active' ? 'sp-tab-active' : 'sp-tab-inactive' }}"
                    href="{{ route('sales-performance.index', array_filter(['tab' => 'active'])) }}"
                >
                    現在取引中
                    <span class="sp-badge sp-badge-orange">{{ $currentCount }}</span>
                </a>
                <a
                    class="sp-tab {{ $tab === 'past' ? 'sp-tab-active' : 'sp-tab-inactive' }}"
                    href="{{ route('sales-performance.index', array_filter(['tab' => 'past'])) }}"
                >
                    過去の取引
                    <span class="sp-badge sp-badge-gray">{{ $pastCount }}</span>
                </a>
            </div>
        </div>

        <!-- 取引一覧 -->
        <div class="sp-transactions">
            @php
                $list = $tab === 'past' ? ($pastTransactions ?? collect()) : ($currentTransactions ?? collect());
            @endphp

            @forelse ($list as $tx)
                @php
                    $listing = $tx->skillListing;
                    $thumb = $listing?->thumbnail_url;
                    $price = (int) ($tx->amount ?? 0);
                    $date = $tx->purchased_at?->format('Y/n/j') ?? '-';

                    $buyer = $tx->buyer;
                    $buyerName = $buyer?->company?->contact_name
                        ?? $buyer?->company?->name
                        ?? $buyer?->freelancer?->display_name
                        ?? $buyer?->email
                        ?? '購入者';
                    $buyerIcon = $buyer?->company?->icon_path ?? $buyer?->freelancer?->icon_path;
                    $buyerAvatarSrc = null;
                    if (!empty($buyerIcon)) {
                        if (str_starts_with($buyerIcon, 'http://') || str_starts_with($buyerIcon, 'https://')) {
                            $buyerAvatarSrc = $buyerIcon;
                        } else {
                            $iconRel = ltrim($buyerIcon, '/');
                            if (str_starts_with($iconRel, 'storage/')) {
                                $iconRel = substr($iconRel, strlen('storage/'));
                            }
                            $buyerAvatarSrc = asset('storage/' . $iconRel);
                        }
                    }
                    $buyerInitial = mb_substr($buyerName, 0, 1);

                    $isInProgress = ($tx->transaction_status === 'in_progress');
                    $isDelivered = ($tx->transaction_status === 'delivered');
                @endphp

                <a href="{{ route('transactions.show', ['skill_order' => $tx->id]) }}" class="sp-card">
                    <div class="sp-card-content">
                        <div class="sp-card-flex">
                            @if (!empty($thumb))
                                <img src="{{ $thumb }}" alt="{{ $listing?->title ?? 'スキル' }}" class="sp-skill-image">
                            @else
                                <div class="sp-skill-image" style="background:#E5E7EB; display:flex; align-items:center; justify-content:center; color:#6B7280; font-size:12px;">
                                    No Image
                                </div>
                            @endif

                            <div class="sp-card-info">
                                <div class="sp-card-header">
                                    <div class="sp-card-header-left">
                                        <h3 class="sp-skill-title">{{ $listing?->title ?? '（削除されたスキル）' }}</h3>
                                        <div class="sp-buyer-info">
                                            <div class="sp-buyer-avatar-box">
                                                @if (!empty($buyerAvatarSrc))
                                                    <img src="{{ $buyerAvatarSrc }}" alt="{{ $buyerName }}" class="sp-buyer-avatar">
                                                @else
                                                    <div class="sp-buyer-avatar" style="background:#E5E7EB; display:flex; align-items:center; justify-content:center; color:#374151; font-weight:700;">
                                                        {{ $buyerInitial }}
                                                    </div>
                                                @endif
                                                <span>{{ $buyerName }}</span>
                                            </div>
                                            <div class="sp-date-box">
                                                <svg class="sp-date-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span>{{ $date }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="sp-status-badge {{ $statusClass($tx->transaction_status) }}">
                                        {{ $statusLabel($tx->transaction_status) }}
                                    </span>
                                </div>

                                <div class="sp-card-footer">
                                    <div class="sp-price-box">
                                        <svg class="sp-price-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>¥{{ number_format($price) }}</span>
                                    </div>
                                    <div class="sp-chat-link">
                                        <svg class="sp-chat-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                        <span>チャットを開く</span>
                                    </div>
                                </div>

                                @if ($isInProgress)
                                    <div class="sp-notification sp-notification-progress">
                                        <p class="sp-notification-text sp-notification-text-progress">
                                            ⚙️ 作業中です。完了したら納品ボタンを押してください。
                                        </p>
                                    </div>
                                @elseif ($isDelivered)
                                    <div class="sp-notification sp-notification-delivered">
                                        <p class="sp-notification-text sp-notification-text-delivered">
                                            ⏳ 購入者の承認待ちです。
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="sp-empty">
                    <svg class="sp-empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    <p class="sp-empty-text">
                        {{ $tab === 'past' ? '過去の取引はありません' : '現在進行中の取引はありません' }}
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
