@extends('layouts.public')

@section('title', '支払い確認中')

@section('content')

<div class="max-w-3xl mx-auto px-4 py-12">
    <div class="bg-white rounded-xl shadow p-8">

        <h1 class="text-2xl font-bold text-gray-900 mb-5 text-center">
            支払いを確認中です
        </h1>

        <!-- 説明文 -->
        <div class="text-gray-700 text-center space-y-3 mb-6">
            <p>
                現在、お支払いの処理を確認しています。
            </p>

            <p class="text-red-500 font-semibold">
                ※この画面は閉じずに、そのままお待ちください。
            </p>
        </div>

        @if (!empty($showTransactionButton))
            <div class="flex flex-wrap gap-3 justify-center">
                <a href="{{ route('buyer.transactions.show', ['skill_order' => $order->id]) }}"
                   class="px-4 py-2 rounded bg-orange-500 text-white font-semibold hover:bg-orange-600">
                    取引画面へ
                </a>
            </div>
        @endif

    </div>
</div>

@endsection