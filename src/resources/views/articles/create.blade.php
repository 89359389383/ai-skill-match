@extends('layouts.public')

@section('title', '記事を投稿 - AIスキルマッチ')

@push('styles')
<style>
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Noto Sans JP', sans-serif; }
</style>
@endpush

@section('content')
<div class="min-h-screen py-12 bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('articles.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-4 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                記事一覧に戻る
            </a>
            <div class="flex justify-between items-center">
                <h1 class="text-4xl font-bold text-gray-900">記事を投稿</h1>
                <div class="flex gap-3">
                    <button type="button" onclick="handlePreview()" class="flex items-center gap-2 px-4 py-2 border-2 border-green-600 text-green-600 rounded-xl font-semibold hover:bg-green-50 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        プレビュー
                    </button>
                    <button type="submit" form="articleForm" class="flex items-center gap-2 px-6 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        投稿する
                    </button>
                </div>
            </div>
        </div>

        <form id="articleForm" action="{{ route('articles.store') }}" method="POST" class="space-y-6">
            @csrf
            @include('partials.error-panel')

            <!-- Basic Info -->
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">基本情報</h2>

                <!-- Title -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        タイトル <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" placeholder="記事のタイトルを入力してください" maxlength="255"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent @error('title') border-red-500 @enderror" required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Excerpt -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        概要 <span class="text-red-500">*</span>
                    </label>
                    <textarea name="excerpt" id="excerpt" placeholder="記事の概要を入力してください（2-3文程度）" rows="3" maxlength="200"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none @error('excerpt') border-red-500 @enderror" required>{{ old('excerpt') }}</textarea>
                    @error('excerpt')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        カテゴリー <span class="text-red-500">*</span>
                    </label>
                    <select name="category" id="category" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent @error('category') border-red-500 @enderror" required>
                        <option value="ChatGPT" {{ old('category') === 'ChatGPT' ? 'selected' : '' }}>ChatGPT</option>
                        <option value="n8n" {{ old('category') === 'n8n' ? 'selected' : '' }}>n8n</option>
                        <option value="Python" {{ old('category') === 'Python' ? 'selected' : '' }}>Python</option>
                        <option value="その他" {{ old('category', '') === 'その他' ? 'selected' : '' }}>その他</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tags -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">タグ</label>
                    <div class="flex gap-2 mb-3">
                        <input type="text" id="tagInput" placeholder="タグを入力してEnter" class="flex-1 px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <button type="button" onclick="addTag()" class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors">
                            追加
                        </button>
                    </div>
                    <div id="tagsContainer" class="flex flex-wrap gap-2"></div>
                </div>

                <!-- Image URL -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">アイキャッチ画像URL</label>
                    <div class="flex gap-2">
                        <input type="url" name="eyecatch_image_url" id="imageUrl" value="{{ old('eyecatch_image_url') }}" placeholder="https://example.com/image.jpg"
                            class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent @error('eyecatch_image_url') border-red-500 @enderror">
                        <button type="button" class="px-4 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors" title="URLを入力するとプレビューが表示されます">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </button>
                    </div>
                    <div id="imagePreviewContainer"></div>
                    @error('eyecatch_image_url')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Content Sections -->
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">記事の構成（目次）</h2>
                    <button type="button" onclick="addSection()" class="flex items-center gap-2 px-4 py-2 bg-amber-600 text-white rounded-xl hover:bg-amber-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        大項目を追加
                    </button>
                </div>

                <div id="sectionsContainer" class="space-y-6"></div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-4">
                <a href="{{ route('articles.index') }}" class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-all">
                    キャンセル
                </a>
                <button type="submit" class="flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    投稿する
                </button>
            </div>
        </form>
    </div>
</div>

{{-- プレビューモーダル --}}
<div id="previewModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50" onclick="closePreview()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto p-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900">プレビュー</h3>
                <button type="button" onclick="closePreview()" class="p-2 text-gray-500 hover:text-gray-700 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div id="previewContent" class="prose max-w-none"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let tags = [];
    let sections = [{ id: 1, title: '', subsections: [] }];
    let sectionCounter = 1;
    let subsectionCounters = {};

        // バリデーションエラー時の復元
        document.addEventListener('DOMContentLoaded', function() {
        @if(old('tags'))
            tags = @json(old('tags'));
        @endif
        @php
            $oldStructure = old('structure');
        @endphp
        @if(!empty($oldStructure) && is_array($oldStructure))
            (function() {
                const raw = @json($oldStructure);
                sectionCounter = 0;
                subsectionCounters = {};
                sections = raw.map((s) => {
                    sectionCounter++;
                    const sectionId = sectionCounter;
                    subsectionCounters[sectionId] = 0;
                    const subs = (s.subsections || []).map((sub) => {
                        subsectionCounters[sectionId]++;
                        return { id: subsectionCounters[sectionId], title: sub.title || '', content: sub.content || '' };
                    });
                    return { id: sectionId, title: s.title || '', subsections: subs };
                });
            })();
        @endif
        renderSections();
        renderTags();

        document.getElementById('tagInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); addTag(); }
        });

        document.getElementById('imageUrl').addEventListener('input', function(e) {
            const url = e.target.value;
            const container = document.getElementById('imagePreviewContainer');
            if (url) {
                container.innerHTML = '<div class="mt-3 rounded-xl overflow-hidden"><img src="'+url+'" alt="Preview" class="w-full h-48 object-cover" onerror="this.parentElement.innerHTML=\'<p class=\\'text-red-500 text-sm\\'>画像を読み込めません</p>\'"></div>';
            } else {
                container.innerHTML = '';
            }
        });

        document.getElementById('articleForm').addEventListener('submit', function(e) {
            prepareFormData();
        });
    });

    function addTag() {
        const input = document.getElementById('tagInput');
        const tag = input.value.trim();
        if (tag && !tags.includes(tag) && tag.length <= 50) {
            tags.push(tag);
            renderTags();
            input.value = '';
        }
    }

    function renderTags() {
        const container = document.getElementById('tagsContainer');
        container.innerHTML = tags.map((tag, i) => `
            <span class="inline-flex items-center gap-2 px-3 py-1 bg-gray-100 text-gray-700 rounded-full">
                #${escapeHtml(tag)}
                <button type="button" onclick="removeTagByIndex(${i})" class="text-gray-500 hover:text-red-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </span>
        `).join('');
    }

    function removeTagByIndex(i) {
        if (tags[i]) {
            tags.splice(i, 1);
            renderTags();
        }
    }

    function addSection() {
        sectionCounter++;
        sections.push({ id: sectionCounter, title: '', subsections: [] });
        renderSections();
    }

    function removeSection(id) {
        sections = sections.filter(s => s.id !== id);
        renderSections();
    }

    function updateSectionTitle(id, title) {
        const section = sections.find(s => s.id === id);
        if (section) section.title = title;
    }

    function addSubsection(sectionId) {
        const section = sections.find(s => s.id === sectionId);
        if (!section) return;
        if (!subsectionCounters[sectionId]) subsectionCounters[sectionId] = 0;
        subsectionCounters[sectionId]++;
        section.subsections = section.subsections || [];
        section.subsections.push({ id: subsectionCounters[sectionId], title: '', content: '' });
        renderSections();
    }

    function removeSubsection(sectionId, subsectionId) {
        const section = sections.find(s => s.id === sectionId);
        if (section) {
            section.subsections = (section.subsections || []).filter(sub => sub.id !== subsectionId);
            renderSections();
        }
    }

    function updateSubsection(sectionId, subsectionId, field, value) {
        const section = sections.find(s => s.id === sectionId);
        if (section) {
            const subsection = (section.subsections || []).find(sub => sub.id === subsectionId);
            if (subsection) subsection[field] = value;
        }
    }

    function renderSections() {
        const container = document.getElementById('sectionsContainer');
        container.innerHTML = sections.map((section, sectionIndex) => `
            <div class="border-l-4 border-amber-500 bg-amber-50 rounded-r-xl p-6">
                <div class="flex items-start gap-4 mb-4">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-amber-900 mb-2">大項目 ${sectionIndex + 1}</label>
                        <input type="text" value="${escapeHtml(section.title || '')}" oninput="updateSectionTitle(${section.id}, this.value)"
                            placeholder="例: 企業が得られるメリット"
                            class="w-full px-4 py-3 border border-amber-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent bg-white">
                    </div>
                    <button type="button" onclick="removeSection(${section.id})" class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors mt-7">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="ml-6 space-y-4">
                    ${(section.subsections || []).map((subsection, subIndex) => `
                        <div class="bg-white border border-amber-200 rounded-xl p-4">
                            <div class="flex items-start gap-4 mb-3">
                                <div class="flex-1">
                                    <label class="block text-sm font-semibold text-amber-800 mb-2">中項目 ${sectionIndex + 1}-${subIndex + 1}</label>
                                    <input type="text" value="${escapeHtml(subsection.title || '')}" oninput="updateSubsection(${section.id}, ${subsection.id}, 'title', this.value)"
                                        placeholder="例: DEIを「実態」として示せる"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                </div>
                                <button type="button" onclick="removeSubsection(${section.id}, ${subsection.id})" class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors mt-6">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">内容</label>
                                <textarea oninput="updateSubsection(${section.id}, ${subsection.id}, 'content', this.value)"
                                    placeholder="この中項目の詳細な内容を入力してください..." rows="4"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent resize-none">${escapeHtml(subsection.content || '')}</textarea>
                            </div>
                        </div>
                    `).join('')}
                    <button type="button" onclick="addSubsection(${section.id})" class="flex items-center gap-2 px-4 py-2 border-2 border-amber-600 text-amber-600 rounded-xl hover:bg-amber-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        中項目を追加
                    </button>
                </div>
            </div>
        `).join('');
    }

    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function prepareFormData() {
        const form = document.getElementById('articleForm');
        document.querySelectorAll('[name^="tags["]').forEach(el => el.remove());
        document.querySelectorAll('[name^="structure["]').forEach(el => el.remove());

        tags.forEach((tag, i) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'tags[' + i + ']';
            input.value = tag;
            form.appendChild(input);
        });

        const structure = sections.map(s => ({
            title: s.title || '',
            subsections: (s.subsections || []).map(sub => ({ title: sub.title || '', content: sub.content || '' }))
        })).filter(s => s.title || (s.subsections && s.subsections.some(sub => sub.title || sub.content)));

        structure.forEach((section, i) => {
            const titleInput = document.createElement('input');
            titleInput.type = 'hidden';
            titleInput.name = 'structure[' + i + '][title]';
            titleInput.value = section.title;
            form.appendChild(titleInput);
            (section.subsections || []).forEach((sub, j) => {
                const subTitleInput = document.createElement('input');
                subTitleInput.type = 'hidden';
                subTitleInput.name = 'structure[' + i + '][subsections][' + j + '][title]';
                subTitleInput.value = sub.title;
                form.appendChild(subTitleInput);
                const subContentInput = document.createElement('input');
                subContentInput.type = 'hidden';
                subContentInput.name = 'structure[' + i + '][subsections][' + j + '][content]';
                subContentInput.value = sub.content;
                form.appendChild(subContentInput);
            });
        });
    }

    function handlePreview() {
        const title = document.getElementById('title').value;
        const excerpt = document.getElementById('excerpt').value;
        const category = document.getElementById('category').value;
        const imageUrl = document.getElementById('imageUrl').value;

        let html = '';
        if (imageUrl) html += '<div class="mb-6"><img src="'+escapeHtml(imageUrl)+'" alt="" class="w-full h-48 object-cover rounded-xl"></div>';
        html += '<h1 class="text-2xl font-bold text-gray-900 mb-4">'+escapeHtml(title || '（タイトル未入力）')+'</h1>';
        html += '<p class="text-gray-600 mb-4">'+escapeHtml(excerpt || '（概要未入力）')+'</p>';
        html += '<span class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm mb-6">'+escapeHtml(category)+'</span>';
        html += '<div class="flex flex-wrap gap-2 mb-6">'+tags.map(t=>'<span class="px-3 py-1 bg-gray-100 rounded-full text-sm">#'+escapeHtml(t)+'</span>').join('')+'</div>';

        sections.forEach(s => {
            if (s.title || (s.subsections && s.subsections.length)) {
                html += '<div class="mb-6"><h2 class="text-xl font-bold text-amber-800 mb-3">'+escapeHtml(s.title || '（大項目）')+'</h2>';
                (s.subsections || []).forEach(sub => {
                    html += '<div class="ml-4 mb-4"><h3 class="font-semibold text-gray-800 mb-2">'+escapeHtml(sub.title || '（中項目）')+'</h3>';
                    html += '<p class="text-gray-700 whitespace-pre-wrap">'+escapeHtml(sub.content || '')+'</p></div>';
                });
                html += '</div>';
            }
        });

        document.getElementById('previewContent').innerHTML = html || '<p class="text-gray-500">プレビューする内容がありません</p>';
        document.getElementById('previewModal').classList.remove('hidden');
    }

    function closePreview() {
        document.getElementById('previewModal').classList.add('hidden');
    }
</script>
@endpush