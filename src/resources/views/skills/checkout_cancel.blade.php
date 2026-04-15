@extends('layouts.public')

@section('title', '支払い未完了')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-12">
    <div class="bg-white rounded-xl shadow p-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">支払いは完了していません</h1>
        <p class="text-gray-700 mb-4">
            決済はキャンセルされました。注文は保留中のままです。
        </p>
        <p class="text-sm text-gray-600 mb-6">注文ID: #{{ $order->id }}</p>

        <div class="flex flex-wrap gap-3">
            <form method="POST" action="{{ route('skills.purchase', ['skill_listing' => $order->skill_listing_id]) }}">
                @csrf
                <button type="submit" class="px-4 py-2 rounded bg-orange-500 text-white font-semibold hover:bg-orange-600">
                    再度購入する
                </button>
            </form>
            <a href="{{ route('skills.show', ['skill_listing' => $order->skill_listing_id]) }}"
               class="px-4 py-2 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">
                スキル詳細へ戻る
            </a>
        </div>
    </div>
</div>
@endsection
