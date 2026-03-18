@extends('layouts.public')

@section('title', ($listing->title ?? 'スキル詳細') . ' - AIスキルマッチ')

@section('content')
<div class="min-h-screen py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="mb-6 text-sm text-gray-500">
            <a href="{{ route('top') }}" class="hover:text-gray-900">ホーム</a>
            <span class="mx-2">></span>
            <a href="{{ route('skills.index') }}" class="hover:text-gray-900">スキル販売</a>
            <span class="mx-2">></span>
            <span class="font-bold text-gray-900">{{ Str::limit($listing->title, 40) }}</span>
        </nav>

        @include('partials.error-panel')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl overflow-hidden shadow-xl mb-6">
                    <img src="{{ $listing->thumbnail_url ?? 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=800&h=600&fit=crop' }}" alt="{{ $listing->title }}" class="w-full h-96 object-cover">
                </div>

                <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $listing->title }}</h1>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-yellow-400"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            <span class="font-bold">{{ $listing->rating_average ?? '0' }}</span>
                            <span class="text-sm text-gray-500">({{ $listing->reviews_count ?? 0 }}件のレビュー)</span>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 mb-6">
                        @foreach($listing->skills as $skill)
                            <span class="px-3 py-1 bg-purple-50 text-purple-600 text-sm rounded-full">{{ $skill->name }}</span>
                        @endforeach
                    </div>

                    <h2 class="text-2xl font-bold text-gray-900 mb-4">サービス内容</h2>
                    <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $listing->description }}</p>
                </div>

                @php $seller = $listing->freelancer; @endphp
                <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">販売者情報</h2>
                    <div class="bg-gradient-to-r from-orange-500 to-red-500 rounded-2xl p-6">
                        <div class="bg-white rounded-xl p-6 relative">
                            <div class="absolute -top-12 left-1/2 transform -translate-x-1/2">
                                <img src="{{ $seller->icon_path ?? 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=400&h=400&fit=crop' }}" alt="{{ $seller->display_name }}" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg">
                            </div>
                            <div class="mt-16 text-center">
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $seller->display_name ?? '出品者' }}</h3>
                                <p class="text-gray-600 mb-6">{{ $seller->job_title ?? '-' }}</p>
                                <a href="{{ route('profiles.show', ['user' => $seller->user_id]) }}" class="inline-block w-full px-6 py-3 border-2 border-purple-600 text-purple-600 rounded-xl font-semibold hover:bg-purple-50 transition-all">
                                    プロフィールを見る
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                @if($listing->reviews->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">レビュー</h2>
                    <div class="space-y-6">
                        @foreach($listing->reviews->take(5) as $review)
                            <div class="border-b border-gray-200 pb-6 last:border-0">
                                <div class="flex items-start gap-4">
                                    @php $reviewer = $review->user?->freelancer ?? $review->user; @endphp
                                    <img src="{{ $reviewer?->icon_path ?? 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop' }}" alt="" class="w-12 h-12 rounded-full object-cover">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="font-bold text-gray-900">{{ $reviewer?->display_name ?? $review->user?->email ?? '匿名' }}</div>
                                            <div class="text-sm text-gray-500">{{ $review->created_at?->format('Y/m/d') }}</div>
                                        </div>
                                        <div class="flex items-center gap-2 mb-2">
                                            <div class="flex items-center gap-0.5" aria-label="評価 {{ $review->rating ?? 0 }}点">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="{{ ($review->rating ?? 0) >= $i ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" class="w-4 h-4 {{ ($review->rating ?? 0) >= $i ? 'text-yellow-400' : 'text-gray-300' }}"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                                @endfor
                                            </div>
                                            <span class="font-bold text-gray-900">{{ $review->rating ?? 0 }}</span>
                                        </div>
                                        <p class="text-sm text-gray-700">{{ $review->body ?? '' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <div class="lg:col-span-1">
                <div class="sticky top-24">
                    <div class="bg-white rounded-2xl shadow-xl p-6">
                        <div class="bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl p-6 mb-6">
                            <div class="text-3xl font-bold mb-2">¥{{ number_format($listing->price) }}</div>
                            @if($listing->delivery_days)
                                <div class="text-sm">納期: {{ $listing->delivery_days }}日</div>
                            @endif
                        </div>

                        @php
                            $isOwnListing = auth('freelancer')->check()
                                && auth('freelancer')->user()->freelancer
                                && (int) auth('freelancer')->user()->freelancer->id === (int) $listing->freelancer_id;
                        @endphp

                        @if (!$isOwnListing)
                            <form action="{{ route('skills.purchase', ['skill_listing' => $listing->id]) }}" method="POST" class="mb-4">
                                @csrf
                                <button type="submit" class="w-full px-6 py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                                    購入する
                                </button>
                            </form>

                            <form action="{{ route('skills.inquiry', ['skill_listing' => $listing->id]) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-all flex items-center justify-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                    問い合わせる
                                </button>
                            </form>
                        @else
                            <p class="text-center text-gray-500 font-medium py-4">あなたの出品です</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
