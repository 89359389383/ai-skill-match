@extends('layouts.public')

@section('title', 'スキル販売 - AIスキルマッチ')

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush

@section('content')
<div class="min-h-screen py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">スキル販売</h1>
                    <p class="text-gray-600">AIスキルを持つプロフェッショナルに直接依頼できます</p>
                </div>
                <a href="{{ route('skills.create') }}" class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                    スキルを出品
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800 font-bold">
                {{ session('success') }}
            </div>
        @endif

        <div class="text-sm text-gray-600 mb-4">
            {{ $listings->total() }} 件中 {{ $listings->firstItem() ?? 0 }} - {{ $listings->lastItem() ?? 0 }} 件表示
        </div>

        @if($listings->isEmpty())
            <div class="text-center py-16 bg-white rounded-2xl shadow">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-16 h-16 text-gray-300 mx-auto mb-4"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <h3 class="text-xl font-bold text-gray-900 mb-2">スキルが見つかりませんでした</h3>
                <p class="text-gray-600">まだスキルは出品されていません</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($listings as $l)
                    <a href="{{ route('skills.show', ['skill_listing' => $l->id]) }}" class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 overflow-hidden">
                        <div class="relative h-48 overflow-hidden">
                            <img src="{{ $l->thumbnail_url ?? 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=800&h=600&fit=crop' }}" alt="{{ $l->title }}" class="w-full h-full object-cover transform hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="p-6">
                            <div class="flex items-center gap-2 mb-3">
                                @php $seller = $l->freelancer; @endphp
                                <img src="{{ $seller->icon_path ?? 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=400&h=400&fit=crop' }}" alt="{{ $seller->display_name }}" class="w-8 h-8 rounded-full object-cover">
                                <div class="flex-1">
                                    <div class="font-bold text-sm text-gray-900">{{ $seller->display_name ?? '出品者' }}</div>
                                    <div class="text-xs text-gray-500">職種: {{ $seller->job_title ?? '-' }}</div>
                                </div>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">{{ $l->title }}</h3>
                            <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ Str::limit($l->description, 100) }}</p>
                            <div class="flex flex-wrap gap-1 mb-4">
                                @foreach($l->skills->take(3) as $skill)
                                    <span class="px-2 py-1 bg-purple-50 text-purple-600 text-xs rounded-full">{{ $skill->name }}</span>
                                @endforeach
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-yellow-400"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                    <span class="font-bold text-sm">{{ $l->rating_average ?? '0' }}</span>
                                    <span class="text-xs text-gray-500">({{ $l->reviews_count ?? 0 }})</span>
                                </div>
                                <div class="text-right">
                                    <div class="text-xl font-bold text-purple-600">¥{{ number_format($l->price) }}</div>
                                    @if($l->delivery_days)
                                        <div class="text-xs text-gray-500">納期: {{ $l->delivery_days }}日</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $listings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
