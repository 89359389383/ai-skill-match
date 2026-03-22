@extends('layouts.public')

@section('title', 'スキル出品 - AIスキルマーケットプレイス')

@push('styles')
<style>
    /* 固定ヘッダー分：セクション・スクロールがヘッダーにかぶらないように（profiles/show と同様） */
    html { scroll-padding-top: var(--main-pt-freelancer, 13rem); }
    #basicInfo, #details, #pricing, #image { scroll-margin-top: 13rem; }
    /* サイドバー sticky 位置をヘッダー直下に */
    .skill-create-sidebar { top: var(--main-pt-freelancer, 20rem); }

    /* Override vertical spacing for stacked sections on this page */
    .space-y-6 > :not([hidden]) ~ :not([hidden]) {
        --tw-space-y-reverse: 0;
        margin-top: calc(5rem * calc(1 - var(--tw-space-y-reverse)));
        margin-bottom: calc(0px * var(--tw-space-y-reverse));
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <a href="{{ route('skills.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            スキル一覧に戻る
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Left Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden lg:sticky skill-create-sidebar">
                    <div class="bg-orange-500 text-white px-6 py-4">
                        <h2 class="font-bold text-center">スキル出品</h2>
                    </div>
                    <nav class="border-b">
                        <button onclick="scrollToSection('basicInfo')" class="w-full text-left px-6 py-3 text-sm border-b transition-colors text-gray-700 hover:bg-gray-50">基本情報</button>
                        <button onclick="scrollToSection('details')" class="w-full text-left px-6 py-3 text-sm border-b transition-colors text-gray-700 hover:bg-gray-50">詳細説明</button>
                        <button onclick="scrollToSection('pricing')" class="w-full text-left px-6 py-3 text-sm border-b transition-colors text-gray-700 hover:bg-gray-50">料金・納期</button>
                        <button onclick="scrollToSection('image')" class="w-full text-left px-6 py-3 text-sm transition-colors text-gray-700 hover:bg-gray-50">画像</button>
                    </nav>
                    <div class="p-6 space-y-3">
                        <button onclick="handlePreview()" type="button" class="w-full py-3 border-2 border-orange-500 text-orange-600 rounded-lg font-semibold hover:bg-orange-50 transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            プレビュー
                        </button>
                        <button onclick="handleSubmit()" type="button" class="w-full py-3 bg-orange-500 text-white rounded-lg font-bold shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            出品する
                        </button>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3">
                @php
                    $slotQuery = request()->filled('slot')
                        ? request('slot')
                        : request()->attributes->get('resolved_slot');
                    $skillsStoreParams = (is_string($slotQuery) && $slotQuery !== '') ? ['slot' => $slotQuery] : [];
                @endphp
                <form id="skillForm" action="{{ route('skills.store', $skillsStoreParams) }}" method="POST" class="space-y-6" enctype="multipart/form-data">
                    @csrf
                    @include('partials.session-slot-field')
                    @include('partials.error-panel')
                    <!-- Basic Info Section -->
                    <div class="bg-white rounded-lg shadow-sm p-6" id="basicInfo">
                        <h2 class="text-xl font-bold text-orange-600 mb-4 pb-2 border-b-2 border-orange-600">基本情報</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">サービスタイトル <span class="text-red-500">*</span></label>
                                <input type="text" id="title" name="title" value="{{ old('title') }}" placeholder="例：ChatGPTを活用した業務効率化コンサルティング" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('title') border-red-500 @enderror">
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                                @enderror
                                <p class="text-sm text-gray-500 mt-1">50文字以内で、サービスの内容が分かりやすいタイトルをつけましょう</p>
                            </div>
                            @php
                                $skillNameSlots = old('skill_names', []);
                                if (!is_array($skillNameSlots)) {
                                    $skillNameSlots = $skillNameSlots ? [$skillNameSlots] : [];
                                }
                                $skillNameSlots = array_values($skillNameSlots);
                                $skillNameSlots = array_slice($skillNameSlots, 0, 16);

                                $minSlots = 4;
                                $maxSlots = 16;
                                $styleRows = (int) max(1, ceil(max(count($skillNameSlots), $minSlots) / 4));
                                $styleRows = min(4, $styleRows);
                                $skillNameSlots = array_pad($skillNameSlots, $styleRows * 4, '');
                            @endphp
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">タグ（スキル名）</label>

                                <div id="skill-tag-items-container" class="space-y-3">
                                    @for($row = 0; $row < $styleRows; $row++)
                                        <div class="skill-tag-input-row grid grid-cols-2 sm:grid-cols-4 gap-3">
                                            @for($col = 0; $col < 4; $col++)
                                                @php $idx = $row * 4 + $col; @endphp
                                                <input
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                                    name="skill_names[]"
                                                    type="text"
                                                    value="{{ $skillNameSlots[$idx] ?? '' }}"
                                                    placeholder="例: Laravel"
                                                >
                                            @endfor
                                        </div>
                                    @endfor
                                </div>

                                <div style="display:flex; gap:0.75rem; flex-wrap:wrap; margin-top:0.75rem;">
                                    <button type="button" id="add-skill-tags-row" class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:shadow-md transition-all">
                                        追加する
                                    </button>
                                    <button type="button" id="remove-skill-tags-row" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all" aria-label="タグ入力行を減らす">
                                        ×
                                    </button>
                                </div>

                                <p class="text-sm text-gray-500 mt-2">1行4件で入力できます（4〜16件）</p>
                            </div>
                        </div>
                    </div>

                    <!-- Details Section -->
                    <div class="bg-white rounded-lg shadow-sm p-6" id="details">
                        <h2 class="text-xl font-bold text-orange-600 mb-4 pb-2 border-b-2 border-orange-600">詳細説明</h2>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">サービス内容 <span class="text-red-500">*</span></label>
                            <textarea id="description" name="description" placeholder="サービスの内容を詳しく説明してください。&#10;&#10;・提供する内容&#10;・どんな課題を解決できるか&#10;・対象となる方&#10;・納品物の詳細&#10;など" rows="12" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                            @enderror
                            <p class="text-sm text-gray-500 mt-1">購入者が理解しやすいよう、具体的に記載しましょう</p>
                        </div>
                    </div>

                    <!-- Pricing Section -->
                    <div class="bg-white rounded-lg shadow-sm p-6" id="pricing">
                        <h2 class="text-xl font-bold text-orange-600 mb-4 pb-2 border-b-2 border-orange-600">料金・納期</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">料金 <span class="text-red-500">*</span></label>
                                <input type="text" id="price" name="price" value="{{ old('price') }}" placeholder="例：30000" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('price') border-red-500 @enderror">
                                @error('price')
                                    <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                                @enderror
                                <p class="text-sm text-gray-500 mt-1">数値で入力してください（例：30000）</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">納期 <span class="text-red-500">*</span></label>
                                <input type="text" id="duration" name="delivery_days" value="{{ old('delivery_days') }}" placeholder="例：3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('delivery_days') border-red-500 @enderror">
                                @error('delivery_days')
                                    <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                                @enderror
                                <p class="text-sm text-gray-500 mt-1">納品までの目安の日数を数値で入力してください</p>
                            </div>
                        </div>
                    </div>

                    <!-- Image Section -->
                    <div class="bg-white rounded-lg shadow-sm p-6" id="image">
                        <h2 class="text-xl font-bold text-orange-600 mb-4 pb-2 border-b-2 border-orange-600">サービス画像</h2>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">メイン画像</label>
                            <div id="imagePreview" style="display: none;" class="relative mb-4">
                                <img id="previewImg" src="" alt="Preview" class="w-full aspect-video object-cover rounded-lg">
                                <button type="button" onclick="removeImage()" class="absolute top-2 right-2 p-2 bg-red-500 text-white rounded-full hover:bg-red-600 transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            <label id="uploadLabel" class="flex flex-col items-center justify-center w-full aspect-video border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-orange-500 transition-all bg-gray-50">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">クリックして画像をアップロード</span></p>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF (推奨サイズ: 1280x720px)</p>
                                </div>
                                <input type="file" id="imageInput" name="thumbnail" class="hidden" accept="image/*" onchange="handleImageUpload(event)">
                            </label>
                            @error('thumbnail')
                                <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                            @enderror
                            <p class="text-sm text-gray-500 mt-2">サービスの内容が伝わる魅力的な画像を選びましょう</p>
                        </div>
                    </div>

                    <!-- Service Features -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-bold text-orange-600 mb-4 pb-2 border-b-2 border-orange-600">このサービスの特徴</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center gap-3 p-4 bg-orange-50 rounded-lg">
                                <input type="checkbox" id="feature1" name="features[]" value="fast_response" class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                <label for="feature1" class="font-medium text-gray-900">即レス対応</label>
                            </div>
                            <div class="flex items-center gap-3 p-4 bg-orange-50 rounded-lg">
                                <input type="checkbox" id="feature2" name="features[]" value="unlimited_revisions" class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                <label for="feature2" class="font-medium text-gray-900">修正無制限</label>
                            </div>
                            <div class="flex items-center gap-3 p-4 bg-orange-50 rounded-lg">
                                <input type="checkbox" id="feature3" name="features[]" value="commercial_use" class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                <label for="feature3" class="font-medium text-gray-900">商用利用可</label>
                            </div>
                            <div class="flex items-center gap-3 p-4 bg-orange-50 rounded-lg">
                                <input type="checkbox" id="feature4" name="features[]" value="copyright_transfer" class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                <label for="feature4" class="font-medium text-gray-900">著作権譲渡</label>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons (Mobile) -->
                    <div class="lg:hidden bg-white rounded-lg shadow-sm p-6">
                        <div class="space-y-3">
                            <button onclick="handlePreview()" type="button" class="w-full py-3 border-2 border-orange-500 text-orange-600 rounded-lg font-semibold hover:bg-orange-50 transition-all flex items-center justify-center gap-2">プレビュー</button>
                            <button type="submit" class="w-full py-3 bg-orange-500 text-white rounded-lg font-bold shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">出品する</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let imageData = null;
    /** true のときはブラウザ標準の submit をそのまま通す（requestSubmit 2回目） */
    let skillFormAllowNativeSubmit = false;

    document.addEventListener('DOMContentLoaded', function() {
        const tagItemsContainer = document.getElementById('skill-tag-items-container');
        const addTagRowBtn = document.getElementById('add-skill-tags-row');
        const removeTagRowBtn = document.getElementById('remove-skill-tags-row');

        const MAX_ROWS = 4; // 16 slots (4 inputs per row)
        const MIN_ROWS = 1; // 4 slots  (1 row)

        function buildTagRow() {
            const row = document.createElement('div');
            row.className = 'skill-tag-input-row grid grid-cols-2 sm:grid-cols-4 gap-3';

            for (let col = 0; col < 4; col++) {
                const input = document.createElement('input');
                input.className = 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent';
                input.name = 'skill_names[]';
                input.type = 'text';
                input.placeholder = '例: Laravel';
                row.appendChild(input);
            }
            return row;
        }

        function syncTagRowButtons() {
            if (!tagItemsContainer || !addTagRowBtn || !removeTagRowBtn) return;
            const rowCount = tagItemsContainer.querySelectorAll('.skill-tag-input-row').length;
            addTagRowBtn.disabled = rowCount >= MAX_ROWS;
            removeTagRowBtn.disabled = rowCount <= MIN_ROWS;
            addTagRowBtn.setAttribute('aria-disabled', String(rowCount >= MAX_ROWS));
            removeTagRowBtn.setAttribute('aria-disabled', String(rowCount <= MIN_ROWS));
        }

        if (tagItemsContainer && addTagRowBtn && removeTagRowBtn) {
            addTagRowBtn.addEventListener('click', function () {
                const rowCount = tagItemsContainer.querySelectorAll('.skill-tag-input-row').length;
                if (rowCount >= MAX_ROWS) return;
                tagItemsContainer.appendChild(buildTagRow());
                syncTagRowButtons();
            });

            removeTagRowBtn.addEventListener('click', function () {
                const rows = tagItemsContainer.querySelectorAll('.skill-tag-input-row');
                if (rows.length <= MIN_ROWS) return;
                const last = rows[rows.length - 1];
                if (last) last.remove();
                syncTagRowButtons();
            });

            syncTagRowButtons();
        }

        const skillForm = document.getElementById('skillForm');
        if (skillForm) {
            skillForm.addEventListener('submit', function(e) {
                if (skillFormAllowNativeSubmit) {
                    skillFormAllowNativeSubmit = false;
                    return;
                }
                e.preventDefault();
                runSkillFormValidationAndSubmit();
            });
        }
    });

    function scrollToSection(sectionId) {
        const element = document.getElementById(sectionId);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function handleImageUpload(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imageData = e.target.result;
                document.getElementById('previewImg').src = imageData;
                document.getElementById('imagePreview').style.display = 'block';
                document.getElementById('uploadLabel').style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    }

    function removeImage() {
        imageData = null;
        document.getElementById('imagePreview').style.display = 'none';
        document.getElementById('uploadLabel').style.display = 'flex';
        const input = document.getElementById('imageInput');
        if (input) input.value = '';
    }

    function handlePreview() {
        alert('プレビュー機能は準備中です');
    }

    function runSkillFormValidationAndSubmit() {
        const title = document.getElementById('title').value;
        const description = document.getElementById('description').value;
        const price = document.getElementById('price').value;
        const duration = document.getElementById('duration').value;

        const form = document.getElementById('skillForm');
        if (!form) return;

        skillFormAllowNativeSubmit = true;
        form.requestSubmit();
    }

    function handleSubmit() {
        runSkillFormValidationAndSubmit();
    }
</script>
</push>

@endsection
