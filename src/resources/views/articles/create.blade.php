@extends('layouts.public')

@section('title', '記事を投稿 - AIスキルマッチ')

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    /* エディタラッパー */
    #editor-wrapper {
        position: relative;
    }
    /* エディタコンテナ */
    #editor {
        min-height: 300px;
    }
    /* Quillコンテナ */
    .ql-container.ql-snow {
        border-radius: 0 0 0.75rem 0.75rem;
        border-color: #d1d5db;
        min-height: 300px;
        height: 300px;
    }
    /* ツールバー */
    .ql-toolbar.ql-snow {
        border-radius: 0.75rem 0.75rem 0 0;
        border-color: #d1d5db;
    }
    /* エディタ本体 - クリック可能に */
    .ql-editor {
        min-height: 280px;
        font-size: 16px;
        pointer-events: auto !important;
        cursor: text;
    }
    /* フォントサイズクラス */
    .ql-snow .ql-editor .ql-size-small { font-size: 0.75em; }
    .ql-snow .ql-editor .ql-size-large { font-size: 1.5em; }
    .ql-snow .ql-editor .ql-size-huge { font-size: 2em; }
    /* placeholderスタイル */
    .ql-editor.ql-blank::before {
        color: #9ca3af;
        font-style: normal;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen py-12 bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('articles.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-4 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                記事一覧に戻る
            </a>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <h1 class="text-4xl font-bold text-gray-900">記事を投稿</h1>
                <div class="flex flex-wrap gap-3">
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

            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">基本情報</h2>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        タイトル <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" placeholder="記事のタイトルを入力してください" maxlength="255"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent @error('title') border-red-500 @enderror">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        概要 <span class="text-red-500">*</span>
                    </label>
                    <textarea name="excerpt" id="excerpt" placeholder="記事の概要を入力してください（2-3文程度）" rows="3" maxlength="200"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none @error('excerpt') border-red-500 @enderror">{{ old('excerpt') }}</textarea>
                    @error('excerpt')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        カテゴリー <span class="text-red-500">*</span>
                    </label>
                    <select name="category" id="category" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent @error('category') border-red-500 @enderror">
                        <option value="ChatGPT" {{ old('category') === 'ChatGPT' ? 'selected' : '' }}>ChatGPT</option>
                        <option value="n8n" {{ old('category') === 'n8n' ? 'selected' : '' }}>n8n</option>
                        <option value="Python" {{ old('category') === 'Python' ? 'selected' : '' }}>Python</option>
                        <option value="その他" {{ old('category', '') === 'その他' ? 'selected' : '' }}>その他</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

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

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        本文 <span class="text-red-500">*</span>
                    </label>
                    <div id="editor-wrapper" class="bg-white rounded-xl border border-gray-300">
                        <div id="editor"></div>
                    </div>
                    <input type="hidden" name="body_html" id="article_body_html" value="{{ old('body_html') }}">
                    @error('body_html')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">アイキャッチ画像URL</label>
                    <div class="flex gap-2">
                        <input type="url" name="eyecatch_image_url" id="imageUrl" value="{{ old('eyecatch_image_url') }}" placeholder="https://example.com/image.jpg"
                            class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent @error('eyecatch_image_url') border-red-500 @enderror">
                    </div>
                    <div id="imagePreviewContainer"></div>
                    @error('eyecatch_image_url')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end gap-4">
                <a href="{{ route('articles.index') }}" class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-all">
                    キャンセル
                </a>
                <button type="submit" class="flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                    投稿する
                </button>
            </div>
        </form>
    </div>
</div>

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
            <div id="previewContent"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
    let tags = [];
    let quill;

    document.addEventListener('DOMContentLoaded', function() {
        @if(is_array(old('tags')))
            tags = @json(old('tags'));
        @endif
        renderTags();

        // エディタ要素の確認
        var editorElement = document.getElementById('editor');
        if (!editorElement) {
            console.error('Editor element not found');
            return;
        }

        // Quill初期化
        try {
            quill = new Quill('#editor', {
                theme: 'snow',
                placeholder: '本文を入力してください...',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{ 'header': [1, 2, 3, false] }],
                        [{ 'size': ['small', false, 'large', 'huge'] }],
                        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                        [{ 'color': [] }, { 'background': [] }],
                        ['clean']
                    ]
                }
            });

            // 初期コンテンツ設定
            var initial = document.getElementById('article_body_html').value;
            if (initial) {
                quill.root.innerHTML = initial;
            }

            // エディタが初期化されたことを確認
            console.log('Quill initialized successfully');
        } catch (e) {
            console.error('Quill initialization failed:', e);
        }

        document.getElementById('tagInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); addTag(); }
        });

        document.getElementById('imageUrl').addEventListener('input', function(e) {
            var url = e.target.value;
            var container = document.getElementById('imagePreviewContainer');
            if (url) {
                container.innerHTML = '<div class="mt-3 rounded-xl overflow-hidden"><img src="'+escapeAttr(url)+'" alt="Preview" class="w-full h-48 object-cover" onerror="this.parentElement.innerHTML=\'<p class=\\'text-red-500 text-sm\\'>画像を読み込めません</p>\'"></div>';
            } else {
                container.innerHTML = '';
            }
        });

        document.getElementById('articleForm').addEventListener('submit', function() {
            document.getElementById('article_body_html').value = quill.root.innerHTML;
            prepareFormData();
        });
    });

    function escapeAttr(s) {
        if (!s) return '';
        return String(s).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    function escapeHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function addTag() {
        var input = document.getElementById('tagInput');
        var tag = input.value.trim();
        if (tag && !tags.includes(tag) && tag.length <= 50 && tags.length < 5) {
            tags.push(tag);
            renderTags();
            input.value = '';
        }
    }

    function renderTags() {
        var container = document.getElementById('tagsContainer');
        container.innerHTML = tags.map(function(tag, i) {
            return '<span class="inline-flex items-center gap-2 px-3 py-1 bg-gray-100 text-gray-700 rounded-full">#' + escapeHtml(tag) +
                '<button type="button" onclick="removeTagByIndex(' + i + ')" class="text-gray-500 hover:text-red-600">' +
                '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button></span>';
        }).join('');
    }

    function removeTagByIndex(i) {
        if (tags[i]) {
            tags.splice(i, 1);
            renderTags();
        }
    }

    function prepareFormData() {
        var form = document.getElementById('articleForm');
        document.querySelectorAll('[name^="tags["]').forEach(function(el) { el.remove(); });
        tags.forEach(function(tag, i) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'tags[' + i + ']';
            input.value = tag;
            form.appendChild(input);
        });
    }

    function handlePreview() {
        var title = document.getElementById('title').value;
        var excerpt = document.getElementById('excerpt').value;
        var category = document.getElementById('category').value;
        var imageUrl = document.getElementById('imageUrl').value;
        var bodyHtml = quill.root.innerHTML;

        var html = '';
        if (imageUrl) html += '<div class="mb-6"><img src="' + escapeAttr(imageUrl) + '" alt="" class="w-full h-48 object-cover rounded-xl"></div>';
        html += '<h1 class="text-2xl font-bold text-gray-900 mb-4">' + escapeHtml(title || '（タイトル未入力）') + '</h1>';
        html += '<p class="text-gray-600 mb-4">' + escapeHtml(excerpt || '（概要未入力）') + '</p>';
        html += '<span class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm mb-4">' + escapeHtml(category) + '</span>';
        html += '<div class="flex flex-wrap gap-2 mb-6">' + tags.map(function(t) {
            return '<span class="px-3 py-1 bg-gray-100 rounded-full text-sm">#' + escapeHtml(t) + '</span>';
        }).join('') + '</div>';
        html += '<div class="ql-snow border-0"><div class="ql-editor" style="min-height:auto;padding:0;">' + bodyHtml + '</div></div>';

        document.getElementById('previewContent').innerHTML = html;
        document.getElementById('previewModal').classList.remove('hidden');
    }

    function closePreview() {
        document.getElementById('previewModal').classList.add('hidden');
    }
</script>
@endpush
