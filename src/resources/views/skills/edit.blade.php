@extends('layouts.public')

@section('title', ($listing->title ?? 'スキル編集') . ' - AIスキルマッチ')

@push('styles')
<style>
    html { scroll-padding-top: var(--main-pt-freelancer, 13rem); }
    #basicInfo, #details, #pricing, #image { scroll-margin-top: 13rem; }
    .skill-create-sidebar { top: var(--main-pt-freelancer, 20rem); }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <a href="{{ route('profiles.skills.index', ['user' => $listing->freelancer->user_id]) }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-6 transition-colors">
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
                        <h2 class="font-bold text-center">スキル出品（編集）</h2>
                    </div>
                    <nav class="border-b">
                        <button onclick="scrollToSection('basicInfo')" type="button" class="w-full text-left px-6 py-3 text-sm border-b transition-colors text-gray-700 hover:bg-gray-50">基本情報</button>
                        <button onclick="scrollToSection('details')" type="button" class="w-full text-left px-6 py-3 text-sm border-b transition-colors text-gray-700 hover:bg-gray-50">詳細説明</button>
                        <button onclick="scrollToSection('pricing')" type="button" class="w-full text-left px-6 py-3 text-sm border-b transition-colors text-gray-700 hover:bg-gray-50">料金・納期</button>
                        <button onclick="scrollToSection('image')" type="button" class="w-full text-left px-6 py-3 text-sm transition-colors text-gray-700 hover:bg-gray-50">画像</button>
                    </nav>
                    <div class="p-6 space-y-3">
                        <a href="javascript:void(0)" onclick="handlePreview()" class="block w-full py-3 border-2 border-orange-500 text-orange-600 rounded-lg font-semibold hover:bg-orange-50 transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            プレビュー
                        </a>
                        <a href="{{ route('profiles.skills.index', ['user' => $listing->freelancer->user_id]) }}"
                           class="block w-full py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-all text-center">
                            キャンセル
                        </a>
                        <button onclick="handleSubmit()" type="button" class="w-full py-3 bg-orange-500 text-white rounded-lg font-bold shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            更新する
                        </button>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3">
                @php
                    $slotQueryEdit = request()->filled('slot')
                        ? request('slot')
                        : request()->attributes->get('resolved_slot');
                    $skillsUpdateParams = ['skill_listing' => $listing->id];
                    if (is_string($slotQueryEdit) && $slotQueryEdit !== '') {
                        $skillsUpdateParams['slot'] = $slotQueryEdit;
                    }
                @endphp
                <form id="skillForm"
                      action="{{ route('skills.update', $skillsUpdateParams) }}"
                      method="POST"
                      class="space-y-6"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                    @include('partials.session-slot-field')
                    @include('partials.error-panel')

                    <input type="hidden" id="removeThumbnail" name="remove_thumbnail" value="0">

                    <!-- Basic Info Section -->
                    <div class="bg-white rounded-lg shadow-sm p-6" id="basicInfo">
                        <h2 class="text-xl font-bold text-orange-600 mb-4 pb-2 border-b-2 border-orange-600">基本情報</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">サービスタイトル <span class="text-red-500">*</span></label>
                                <input type="text" id="title" name="title"
                                       value="{{ old('title', $listing->title) }}"
                                       placeholder="例：ChatGPTを活用した業務効率化コンサルティング"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('title') border-red-500 @enderror">
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                                @enderror
                                <p class="text-sm text-gray-500 mt-1">50文字以内で、サービスの内容が分かりやすいタイトルをつけましょう</p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">タグ</label>
                                <div class="flex gap-2 mb-2">
                                    <input type="text" id="tagInput" placeholder="タグを入力してEnter"
                                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                    <button type="button" onclick="addTag()" class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:shadow-md transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </button>
                                </div>
                                <div id="tagsContainer" class="flex flex-wrap gap-2 mb-2"></div>
                                <p class="text-sm text-gray-500 mt-1">例：ChatGPT、プロンプト、業務効率化</p>
                            </div>
                        </div>
                    </div>

                    <!-- Details Section -->
                    <div class="bg-white rounded-lg shadow-sm p-6" id="details">
                        <h2 class="text-xl font-bold text-orange-600 mb-4 pb-2 border-b-2 border-orange-600">詳細説明</h2>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">サービス内容 <span class="text-red-500">*</span></label>
                            <textarea id="description" name="description"
                                      rows="12"
                                      placeholder="サービスの内容を詳しく説明してください。"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none @error('description') border-red-500 @enderror">{{ old('description', $listing->description) }}</textarea>
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
                                <input type="text" id="price" name="price"
                                       value="{{ old('price', $listing->price) }}"
                                       placeholder="例：30000"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('price') border-red-500 @enderror">
                                @error('price')
                                    <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                                @enderror
                                <p class="text-sm text-gray-500 mt-1">数値で入力してください（例：30000）</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">納期 <span class="text-red-500">*</span></label>
                                <input type="text" id="duration" name="delivery_days"
                                       value="{{ old('delivery_days', $listing->delivery_days) }}"
                                       placeholder="例：3"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('delivery_days') border-red-500 @enderror">
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

                            <div id="imagePreview" style="{{ $listing->thumbnail_url ? 'display:block;' : 'display:none;' }}" class="relative mb-4">
                                <img id="previewImg"
                                     src="{{ $listing->thumbnail_url ?? '' }}"
                                     alt="Preview"
                                     class="w-full aspect-video object-cover rounded-lg">
                                <button type="button"
                                        onclick="removeImage()"
                                        class="absolute top-2 right-2 p-2 bg-red-500 text-white rounded-full hover:bg-red-600 transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <label id="uploadLabel"
                                   class="flex flex-col items-center justify-center w-full aspect-video border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-orange-500 transition-all bg-gray-50"
                                   style="{{ $listing->thumbnail_url ? 'display:none;' : 'display:flex;' }}">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">クリックして画像をアップロード</span></p>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF (推奨サイズ: 1280x720px)</p>
                                </div>
                                <input type="file" id="imageInput" name="thumbnail" class="hidden" accept="image/*" onchange="handleImageUpload(event)">
                            </label>

                            <p class="text-sm text-gray-500 mt-2">サービスの内容が伝わる魅力的な画像を選びましょう</p>
                        </div>
                    </div>

                    <!-- Submit Buttons (Mobile) -->
                    <div class="lg:hidden bg-white rounded-lg shadow-sm p-6">
                        <div class="space-y-3">
                            <button onclick="handlePreview()" type="button" class="w-full py-3 border-2 border-orange-500 text-orange-600 rounded-lg font-semibold hover:bg-orange-50 transition-all flex items-center justify-center gap-2">プレビュー</button>
                            <a href="{{ route('profiles.skills.index', ['user' => $listing->freelancer->user_id]) }}"
                               class="w-full py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-all flex items-center justify-center">
                                キャンセル
                            </a>
                            <button type="submit" class="w-full py-3 bg-orange-500 text-white rounded-lg font-bold shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">更新する</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let tags = @json($listing->skills->pluck('name')->values()->all());
    let imageData = null;
    let skillFormAllowNativeSubmit = false;

    document.addEventListener('DOMContentLoaded', function() {
        const tagInput = document.getElementById('tagInput');
        if (tagInput) {
            tagInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    addTag();
                }
            });
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

        // 編集対象の既存タグを表示
        renderTags();
    });

    function scrollToSection(sectionId) {
        const element = document.getElementById(sectionId);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function addTag() {
        const input = document.getElementById('tagInput');
        const tag = input.value.trim();
        if (tag && !tags.includes(tag)) {
            tags.push(tag);
            renderTags();
            input.value = '';
        }
    }

    function removeTag(tag) {
        tags = tags.filter(t => t !== tag);
        renderTags();
    }

    function renderTags() {
        const container = document.getElementById('tagsContainer');
        if (!container) return;
        container.innerHTML = tags.map(tag => `
            <span class="px-3 py-1 bg-orange-100 text-orange-700 text-sm font-medium rounded-full flex items-center gap-2">
                ${tag}
                <button type="button" onclick="removeTag('${tag}')" class="hover:text-orange-900">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </span>
        `).join('');
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
        const removeThumb = document.getElementById('removeThumbnail');
        if (removeThumb) removeThumb.value = '1';
    }

    function handlePreview() {
        alert('プレビュー機能は準備中です');
    }

    function runSkillFormValidationAndSubmit() {
        const title = document.getElementById('title').value;
        const description = document.getElementById('description').value;
        const price = document.getElementById('price').value;
        const duration = document.getElementById('duration').value;

        if (!title || !description || !price || !duration) {
            alert('全ての必須項目を入力してください');
            return;
        }

        const form = document.getElementById('skillForm');
        if (!form) return;

        form.querySelectorAll('input[name="skill_names[]"]').forEach(el => el.remove());
        tags.forEach(t => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'skill_names[]';
            input.value = t;
            form.appendChild(input);
        });

        skillFormAllowNativeSubmit = true;
        form.requestSubmit();
    }

    function handleSubmit() {
        runSkillFormValidationAndSubmit();
    }
</script>
@endpush

@endsection

