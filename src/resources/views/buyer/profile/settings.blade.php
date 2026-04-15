@extends('layouts.public')

@section('title', '購入者プロフィール設定')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-extrabold text-gray-900 mb-2">購入者プロフィール設定</h1>
    <p class="text-gray-600 mb-6">表示名を変更できます（必須）</p>

    @include('partials.error-panel')

    <div class="bg-white rounded-xl shadow p-6">
        <form method="POST" action="{{ route('buyer.profile.settings.update') }}" enctype="multipart/form-data" class="flex flex-col gap-5">
            @csrf
            @include('partials.session-slot-field')

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1" for="display_name">表示名（必須）</label>
                <input
                    type="text"
                    name="display_name"
                    id="display_name"
                    value="{{ old('display_name', $buyer->display_name ?? '') }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 @error('display_name') border-red-400 @enderror"
                >
                @error('display_name')
                    <div class="mt-1 text-sm font-bold text-red-600">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1" for="icon">アイコン（任意）</label>
                <input type="file" name="icon" id="icon" accept="image/*" class="w-full">
                @error('icon')
                    <div class="mt-1 text-sm font-bold text-red-600">{{ $message }}</div>
                @enderror
                @if (!empty($buyer->icon_path))
                    <div class="mt-3 text-sm text-gray-600">
                        現在のアイコン：{{ $buyer->icon_path }}
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1" for="age_group">年代（任意）</label>
                    <input type="text" name="age_group" id="age_group" value="{{ old('age_group', $buyer->age_group ?? '') }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2">
                    @error('age_group')
                        <div class="mt-1 text-sm font-bold text-red-600">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1" for="prefecture">都道府県（任意）</label>
                    <input type="text" name="prefecture" id="prefecture" value="{{ old('prefecture', $buyer->prefecture ?? '') }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2">
                    @error('prefecture')
                        <div class="mt-1 text-sm font-bold text-red-600">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1" for="address">住所（任意）</label>
                <textarea name="address" id="address" rows="3"
                          class="w-full border border-gray-200 rounded-lg px-3 py-2 @error('address') border-red-400 @enderror">{{ old('address', $buyer->address ?? '') }}</textarea>
                @error('address')
                    <div class="mt-1 text-sm font-bold text-red-600">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="w-full bg-orange-500 text-white font-extrabold py-3 rounded-lg hover:bg-orange-600">
                保存する
            </button>
        </form>
    </div>
</div>
@endsection

