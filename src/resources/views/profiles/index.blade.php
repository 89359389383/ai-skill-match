@extends('layouts.public')

@section('title', 'AIプロフェッショナル - AIスキルマッチ')

@push('styles')
<style>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush

@section('content')
<div class="min-h-screen py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">AIプロフェッショナル</h1>
                <p class="text-gray-600">経験豊富なAIスペシャリストと繋がろう</p>
            </div>
        </div>

        @if($freelancers->isEmpty())
            <div class="text-center py-16 bg-white rounded-2xl shadow">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-16 h-16 text-gray-300 mx-auto mb-4"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <h3 class="text-xl font-bold text-gray-900 mb-2">プロフィールが見つかりませんでした</h3>
                <p class="text-gray-600">まだフリーランスが登録されていません</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($freelancers as $f)
                    <a href="{{ route('profiles.show', ['user' => $f->user_id]) }}" class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 overflow-hidden">
                        <div class="h-24 bg-gradient-to-r from-orange-500 via-red-500 to-pink-500"></div>
                        <div class="relative px-6">
                            <div class="absolute -top-12 left-1/2 transform -translate-x-1/2">
                                @php
                                    $iconPath = $f->icon_path ?? null;
                                    $iconSrc = null;

                                    if (!empty($iconPath)) {
                                        if (str_starts_with($iconPath, 'http://') || str_starts_with($iconPath, 'https://')) {
                                            $iconSrc = $iconPath;
                                        } else {
                                            $iconRel = ltrim($iconPath, '/');
                                            if (str_starts_with($iconRel, 'storage/')) {
                                                $iconRel = substr($iconRel, strlen('storage/'));
                                            }
                                            $iconSrc = \Illuminate\Support\Facades\Storage::disk('public')->url($iconRel);
                                        }
                                    }

                                    $minRate = (int) ($f->min_rate ?? 0);
                                    $minRateMan = $minRate > 0 ? $minRate / 10000 : 0;
                                    $minRateManStr = ($minRate > 0 && $minRate % 10000 === 0)
                                        ? number_format($minRateMan, 0)
                                        : number_format($minRateMan, 1);
                                @endphp

                                <img src="{{ $iconSrc ?? 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop' }}"
                                     alt="{{ $f->display_name }}"
                                     class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg">
                            </div>
                        </div>
                        <div class="pt-16 px-6 pb-6 text-center">
                            <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $f->display_name ?? '名前未設定' }}</h3>
                            <p class="text-sm text-gray-600 mb-2">職種: {{ $f->job_title ?? '未設定' }}</p>
                            <p class="text-sm mb-3">
                                <span class="font-bold text-gray-700">希望時給単価: </span>
                                <span class="font-bold text-orange-600">{{ $minRateManStr }}万</span>
                            </p>
                            <p class="text-sm text-gray-600 mb-4 line-clamp-3">{{ Str::limit($f->bio ?? '', 100) }}</p>
                            <div class="flex flex-wrap gap-2 justify-center">
                                @foreach($f->skills->take(3) as $skill)
                                    <span class="px-3 py-1 bg-orange-50 text-orange-600 text-xs font-medium rounded-full">{{ $skill->name }}</span>
                                @endforeach
                                @if($f->skills->count() > 3)
                                    <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">+{{ $f->skills->count() - 3 }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $freelancers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
