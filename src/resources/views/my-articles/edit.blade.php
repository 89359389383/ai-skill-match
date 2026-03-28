@extends('layouts.public')

@section('title', '記事を編集 - AIスキルマッチ')

@push('styles')
<style>
    /* 本文エディタ */
    #bodyEditor {
        min-height: 360px;
        padding: 1rem;
        outline: none;
        line-height: 1.75;
    }
    #bodyEditor:empty::before {
        content: attr(data-placeholder);
        color: #9ca3af;
    }

    /* 目次（ToC） */
    .article-toc {
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        padding: 1rem;
        border-radius: 0.75rem;
        margin: 1rem 0;
    }
    .article-toc ul {
        list-style: none;
        padding-left: 0;
        margin: 0.5rem 0 0 0;
    }
    .article-toc li { margin: 0.25rem 0; }
    .article-toc a {
        color: #4f46e5;
        text-decoration: underline;
    }

    /* 挿入メニュー（note風） */
    #insertMenu {
        position: fixed;
        z-index: 9999;
        width: 320px;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -4px rgba(0,0,0,0.1);
        overflow: hidden;
        display: none;
    }
    #insertMenu .menu-section-title {
        padding: 0.5rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 800;
        color: #6b7280;
        border-bottom: 1px solid #f3f4f6;
        background: #f9fafb;
    }
    #insertMenu .menu-item {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.6rem 0.75rem;
        cursor: pointer;
        user-select: none;
    }
    #insertMenu .menu-item:hover { background: #f3f4f6; }
    #insertMenu .menu-item .menu-icon {
        width: 24px;
        height: 24px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
        flex: 0 0 auto;
    }
    #insertMenu .menu-sep { height: 1px; background: #f3f4f6; }
</style>
@endpush

@section('content')
<div class="min-h-screen py-12 bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('my-articles.index') }}" class="flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-4 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span class="font-medium">投稿記事一覧に戻る</span>
            </a>

            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <h1 class="text-4xl font-bold text-gray-900">記事を編集</h1>
                <button type="button" onclick="handlePreview()" class="flex items-center gap-2 px-4 py-2 border-2 border-green-600 text-green-600 rounded-xl font-semibold hover:bg-green-50 transition-all">
                    プレビュー
                </button>
            </div>
        </div>

        <form id="articleForm" action="{{ route('my-articles.update', ['article' => $article->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            @include('partials.error-panel')

            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">基本情報</h2>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">タイトル <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title', $article->title) }}" maxlength="255"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent @error('title') border-red-500 @enderror"
                        placeholder="記事のタイトルを入力してください">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 概要（excerpt）は任意なので、編集UIから非表示にしています --}}
                {{-- <div class="mb-6"> --}}
                {{--     <label class="block text-sm font-semibold text-gray-700 mb-2">概要 <span class="text-red-500">*</span></label> --}}
                {{--     <textarea name="excerpt" id="excerpt" rows="3" maxlength="200" --}}
                {{--         class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent @error('excerpt') border-red-500 @enderror" --}}
                {{--         placeholder="記事の概要を入力してください">{{ old('excerpt', $article->excerpt) }}</textarea> --}}
                {{--     @error('excerpt') --}}
                {{--         <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p> --}}
                {{--     @enderror --}}
                {{-- </div> --}}

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        カテゴリー <span class="text-red-500">*</span>
                    </label>
                    <select name="category" id="category" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent @error('category') border-red-500 @enderror">
                        <option value="">選択してください</option>
                        <option value="n8n" {{ old('category', $article->category) === 'n8n' ? 'selected' : '' }}>n8n</option>
                        <option value="AIツール" {{ old('category', $article->category) === 'AIツール' ? 'selected' : '' }}>AIツール</option>
                        <option value="自動化" {{ old('category', $article->category) === '自動化' ? 'selected' : '' }}>自動化</option>
                        <option value="プログラミング" {{ old('category', $article->category) === 'プログラミング' ? 'selected' : '' }}>プログラミング</option>
                        <option value="ビジネス活用" {{ old('category', $article->category) === 'ビジネス活用' ? 'selected' : '' }}>ビジネス活用</option>
                        <option value="副業・フリーランス" {{ old('category', $article->category) === '副業・フリーランス' ? 'selected' : '' }}>副業・フリーランス</option>
                        <option value="その他" {{ old('category', $article->category) === 'その他' ? 'selected' : '' }}>その他</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    @php
                        $tagSlots = old('tags', $article->tags->pluck('name')->toArray());
                        if (!is_array($tagSlots)) {
                            $tagSlots = $tagSlots ? [$tagSlots] : [];
                        }
                        $tagSlots = array_values($tagSlots);
                        $tagSlots = array_slice($tagSlots, 0, 16);

                        $minSlots = 4;
                        $maxSlots = 16;
                        $styleRows = (int) max(1, ceil(max(count($tagSlots), $minSlots) / 4));
                        $styleRows = min(4, $styleRows);
                        $tagSlots = array_pad($tagSlots, $styleRows * 4, '');
                    @endphp

                    <label class="block text-sm font-semibold text-gray-700 mb-2">タグ</label>

                    <div id="article-tag-items-container" class="space-y-3">
                        @for($row = 0; $row < $styleRows; $row++)
                            <div class="article-tag-input-row grid grid-cols-2 sm:grid-cols-4 gap-3">
                                @for($col = 0; $col < 4; $col++)
                                    @php $idx = $row * 4 + $col; @endphp
                                    <input
                                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        name="tags[]"
                                        type="text"
                                        value="{{ $tagSlots[$idx] ?? '' }}"
                                        placeholder="例: API"
                                    >
                                @endfor
                            </div>
                        @endfor
                    </div>

                    <div style="display:flex; gap:0.75rem; flex-wrap:wrap; margin-top:0.75rem;">
                        <button type="button" id="add-article-tags-row" class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors">
                            追加
                        </button>
                        <button type="button" id="remove-article-tags-row" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors" aria-label="タグ入力行を減らす">
                            ×
                        </button>
                    </div>

                    <p class="text-sm text-gray-500 mt-2">1行4件で入力できます（4〜16件）</p>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        本文 <span class="text-red-500">*</span>
                    </label>
                    @error('body_html')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror

                    <div class="relative">
                        <div class="flex items-center gap-3 mb-3">
                            <button
                                type="button"
                                id="insertMenuToggle"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition-colors border border-gray-200"
                                aria-haspopup="dialog"
                                aria-expanded="false"
                            >
                                ＋ 挿入
                            </button>

                            <div id="insertMenu" role="dialog" aria-label="挿入メニュー">
                                <div class="menu-section-title">インライン/ブロック</div>
                                <div class="menu-item" data-action="image">
                                    <span class="menu-icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                            <path d="M21 15l-5-5L5 21"></path>
                                        </svg>
                                    </span>
                                    画像
                                </div>
                                <div class="menu-item" data-action="embed">
                                    <span class="menu-icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="2" y="2" width="20" height="20" rx="2" ry="2"></rect>
                                            <path d="M7 7l10 5-10 5V7z"></path>
                                        </svg>
                                    </span>
                                    埋め込み
                                </div>
                                <div class="menu-item" data-action="file">
                                    <span class="menu-icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <path d="M14 2v6h6"></path>
                                        </svg>
                                    </span>
                                    ファイル
                                </div>

                                <div class="menu-sep"></div>
                                <div class="menu-section-title">見出し/リスト</div>
                                <div class="menu-item" data-action="heading-large">
                                    <span class="menu-icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 6h16"></path>
                                            <path d="M4 18h16"></path>
                                            <path d="M8 6v12"></path>
                                        </svg>
                                    </span>
                                    大見出し
                                </div>
                                <div class="menu-item" data-action="heading-small">
                                    <span class="menu-icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 6h16"></path>
                                            <path d="M4 18h16"></path>
                                            <path d="M6 6v12"></path>
                                        </svg>
                                    </span>
                                    小見出し
                                </div>
                                <div class="menu-item" data-action="list-bullet">
                                    <span class="menu-icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="8" y1="6" x2="21" y2="6"></line>
                                            <circle cx="4" cy="6" r="1"></circle>
                                            <line x1="8" y1="12" x2="21" y2="12"></line>
                                            <circle cx="4" cy="12" r="1"></circle>
                                            <line x1="8" y1="18" x2="21" y2="18"></line>
                                            <circle cx="4" cy="18" r="1"></circle>
                                        </svg>
                                    </span>
                                    箇条書きリスト
                                </div>
                                <div class="menu-item" data-action="list-ordered">
                                    <span class="menu-icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="8" y1="6" x2="21" y2="6"></line>
                                            <line x1="8" y1="12" x2="21" y2="12"></line>
                                            <line x1="8" y1="18" x2="21" y2="18"></line>
                                            <path d="M4 7h1v-1h-1"></path>
                                            <path d="M4 12h1v-1h-1"></path>
                                            <path d="M4 17h1v-1h-1"></path>
                                        </svg>
                                    </span>
                                    番号付きリスト
                                </div>

                                <div class="menu-sep"></div>
                                <div class="menu-section-title">装飾/その他</div>
                                <div class="menu-item" data-action="blockquote">
                                    <span class="menu-icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 4h6v6H4z"></path>
                                            <path d="M14 4h6v6h-6z"></path>
                                            <path d="M4 14c0 5 4 6 6 6"></path>
                                            <path d="M14 14c0 5 4 6 6 6"></path>
                                        </svg>
                                    </span>
                                    引用
                                </div>
                                <div class="menu-item" data-action="toc">
                                    <span class="menu-icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 6h16"></path>
                                            <path d="M4 10h16"></path>
                                            <path d="M4 14h10"></path>
                                            <path d="M4 18h7"></path>
                                        </svg>
                                    </span>
                                    目次
                                </div>
                                <div class="menu-item" data-action="hr">
                                    <span class="menu-icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="3" y1="12" x2="21" y2="12"></line>
                                        </svg>
                                    </span>
                                    区切り線
                                </div>
                            </div>
                        </div>

                        <div id="bodyEditor" class="bg-white rounded-xl border border-gray-300" contenteditable="true" data-placeholder="本文を入力してください">{!! old('body_html', filled($article->body_html) ? $article->body_html : $article->editorInitialHtml()) !!}</div>
                        <textarea
                            id="body_html"
                            name="body_html"
                            maxlength="50000"
                            class="hidden"
                        >{{ old('body_html', filled($article->body_html) ? $article->body_html : $article->editorInitialHtml()) }}</textarea>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">アイキャッチ画像（任意）</label>
                    <div id="imagePreview" style="display: none;" class="relative mb-4">
                        <img id="previewImg" src="" alt="Preview" class="w-full aspect-video object-cover rounded-lg">
                        <button type="button" onclick="removeImage()" class="absolute top-2 right-2 p-2 bg-red-500 text-white rounded-full hover:bg-red-600 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- 編集時：既存アイキャッチ画像を×で削除するためのフラグ --}}
                    <input type="hidden" id="eyecatch_image_remove" name="eyecatch_image_remove" value="0">

                    <label id="uploadLabel" class="flex flex-col items-center justify-center w-full aspect-video border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-green-500 transition-all bg-gray-50">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">クリックして画像をアップロード</span></p>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF</p>
                        </div>
                        <input type="file" id="imageInput" name="eyecatch_image" class="hidden" accept="image/*" onchange="handleImageUpload(event)">
                    </label>
                    @error('eyecatch_image')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">公開設定</label>
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="is_published" value="1" {{ old('is_published', $article->published_at ? '1' : '0') == '1' ? 'checked' : '' }} class="w-4 h-4 text-green-600 focus:ring-green-500 border-gray-300">
                            <span class="text-gray-700">
                                <span class="font-medium">公開</span>
                                <span class="text-sm text-gray-500 ml-1">（誰でも閲覧できます）</span>
                            </span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="is_published" value="0" {{ old('is_published', $article->published_at ? '1' : '0') == '0' ? 'checked' : '' }} class="w-4 h-4 text-green-600 focus:ring-green-500 border-gray-300">
                            <span class="text-gray-700">
                                <span class="font-medium">非公開</span>
                                <span class="text-sm text-gray-500 ml-1">（自分のみ閲覧できます）</span>
                            </span>
                        </label>
                    </div>
                    @error('is_published')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-wrap justify-end gap-4 pt-2">
                <a href="{{ route('my-articles.index') }}" class="px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-all">
                    キャンセル
                </a>
                <button type="submit" class="px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all">
                    更新
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
<script>
    var existingEyecatchUrl = @json($article->eyecatch_image_url);
    var currentEyecatchUrl = existingEyecatchUrl;

    document.addEventListener('DOMContentLoaded', function() {
        // -----------------------------
        // ＋ 挿入メニュー（Quillが未ロードでも開く）
        // -----------------------------
        const insertMenuToggle = document.getElementById('insertMenuToggle');
        const insertMenu = document.getElementById('insertMenu');
        const bodyEditor = document.getElementById('bodyEditor');
        const bodyInput = document.getElementById('body_html');

        function syncBodyEditor() {
            if (!bodyEditor || !bodyInput) return;
            bodyInput.value = bodyEditor.innerHTML;
        }

        function restoreEditorContent() {
            if (!bodyEditor || !bodyInput) return;
            const value = (bodyInput.value || '').trim();
            if (value) {
                bodyEditor.innerHTML = value;
            }
            syncBodyEditor();
        }

        if (bodyEditor && bodyInput) {
            restoreEditorContent();
            bodyEditor.addEventListener('input', syncBodyEditor);
            bodyEditor.addEventListener('blur', syncBodyEditor);
            bodyEditor.addEventListener('paste', function() {
                window.setTimeout(syncBodyEditor, 0);
            });
        }

        function closeInsertMenuOuter() {
            if (insertMenu) insertMenu.style.display = 'none';
            if (insertMenuToggle) insertMenuToggle.setAttribute('aria-expanded', 'false');
        }
        function openInsertMenuOuter() {
            if (!insertMenu || !insertMenuToggle) return;
            insertMenu.style.display = 'block';
            insertMenuToggle.setAttribute('aria-expanded', 'true');

            const rect = insertMenuToggle.getBoundingClientRect();
            const w = insertMenu.offsetWidth;
            const h = insertMenu.offsetHeight;
            const left = Math.min(rect.left, window.innerWidth - w - 12);
            insertMenu.style.left = left + 'px';
            // ボタンの下に出すのではなく、上方向に配置して全項目が見切れないようにする
            insertMenu.style.top = Math.max(8, rect.top - h - 8) + 'px';
        }

        if (insertMenuToggle && insertMenu) {
            closeInsertMenuOuter();

            insertMenuToggle.onclick = function(e) {
                e.stopPropagation();
                const isOpen = insertMenu.style.display === 'block';
                if (isOpen) closeInsertMenuOuter();
                else openInsertMenuOuter();
            };

            document.addEventListener('click', function(e) {
                if (!insertMenu) return;
                const target = e.target;
                if (insertMenuToggle && insertMenuToggle.contains(target)) return;
                if (insertMenu.contains(target)) return;
                closeInsertMenuOuter();
            });
        }

        function getEditorSelection() {
            if (!bodyEditor) return null;
            const selection = window.getSelection();
            if (!selection || selection.rangeCount === 0) return null;
            const range = selection.getRangeAt(0);
            if (!bodyEditor.contains(range.commonAncestorContainer)) return null;
            return range;
        }

        function insertHtmlIntoEditor(html) {
            if (!bodyEditor) return;
            bodyEditor.focus();
            const selection = window.getSelection();
            let range = getEditorSelection();
            if (!range) {
                range = document.createRange();
                range.selectNodeContents(bodyEditor);
                range.collapse(false);
            }

            range.deleteContents();
            const fragment = range.createContextualFragment(html);
            const lastNode = fragment.lastChild;
            range.insertNode(fragment);

            if (lastNode) {
                range.setStartAfter(lastNode);
                range.collapse(true);
                selection.removeAllRanges();
                selection.addRange(range);
            }
            syncBodyEditor();
        }

        function formatBlock(tagName) {
            if (!bodyEditor) return;
            bodyEditor.focus();
            document.execCommand('formatBlock', false, tagName);
            syncBodyEditor();
        }

        function toggleList(type) {
            if (!bodyEditor) return;
            bodyEditor.focus();
            document.execCommand(type === 'ordered' ? 'insertOrderedList' : 'insertUnorderedList', false, null);
            syncBodyEditor();
        }

        function toggleQuote() {
            if (!bodyEditor) return;
            bodyEditor.focus();
            document.execCommand('formatBlock', false, 'blockquote');
            syncBodyEditor();
        }

        function insertHorizontalRule() {
            insertHtmlIntoEditor('<hr>');
        }

        function insertImageFromFile(file) {
            if (!file || !file.type || !file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                insertHtmlIntoEditor('<p><img src="' + e.target.result + '" alt="' + (file.name || 'image') + '" style="max-width:100%;height:auto;"></p>');
            };
            reader.readAsDataURL(file);
        }

        function insertFileLinkFromFile(file) {
            if (!file) return;
            const MAX_BYTES = 2 * 1024 * 1024;
            if (file.size > MAX_BYTES) {
                alert('ファイルが大きすぎます（2MBまで）。別途サーバアップロード機能が必要です。');
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                const safeName = String(file.name || 'file')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;');
                insertHtmlIntoEditor('<p><a href="' + e.target.result + '" download="' + safeName + '" class="text-indigo-600 underline">' + safeName + '</a></p>');
            };
            reader.readAsDataURL(file);
        }

        function insertEmbedUrl(url) {
            const trimmed = (url || '').trim();
            if (!trimmed) return;
            const ytMatch = trimmed.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([A-Za-z0-9_-]{6,})/);
            if (ytMatch && ytMatch[1]) {
                const videoId = ytMatch[1];
                insertHtmlIntoEditor('<div class="my-4"><iframe width="560" height="315" src="https://www.youtube.com/embed/' + videoId + '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>');
                return;
            }
            insertHtmlIntoEditor('<div class="my-4"><iframe src="' + trimmed + '" style="width:100%; min-height:320px;" frameborder="0" allowfullscreen></iframe></div>');
        }

        function buildTocHtml() {
            if (!bodyEditor) return null;
            const headings = Array.from(bodyEditor.querySelectorAll('h2, h3'));
            if (headings.length === 0) {
                alert('見出し（大見出し/小見出し）を先に入力してください。');
                return null;
            }

            function slugify(s) {
                // Blade/JSの正規表現を壊しやすいので、まずは英数字だけで安全に作る
                return (String(s || '') || '')
                    .toLowerCase()
                    .trim()
                    .replace(/\s+/g, '-')
                    .replace(/[^a-z0-9\-]/g, '')
                    .slice(0, 40) || 'section';
            }

            const used = new Set();
            headings.forEach((h) => {
                let id = h.getAttribute('id');
                if (!id) {
                    id = slugify(h.textContent) || 'section';
                    let base = id;
                    let i = 2;
                    while (used.has(id)) {
                        id = base + '-' + i;
                        i++;
                    }
                    h.setAttribute('id', id);
                }
                used.add(id);
            });

            const items = headings.map((h) => {
                const level = h.tagName.toLowerCase() === 'h2' ? 2 : 3;
                const id = h.getAttribute('id');
                const text = (h.textContent || '').trim();
                const indent = level === 3 ? ' style="padding-left: 1rem;"' : '';
                return '<li' + indent + '><a href="#' + id + '">' + (text || '') + '</a></li>';
            }).join('');

            return '<div class="article-toc"><div class="font-bold text-gray-900">目次</div><ul>' + items + '</ul></div>';
        }

        if (insertMenu) {
            insertMenu.addEventListener('click', function(e) {
                const item = e.target.closest('.menu-item');
                if (!item) return;
                const action = item.getAttribute('data-action');
                if (!action) return;
                closeInsertMenuOuter();

                if (action === 'image') {
                    const file = document.createElement('input');
                    file.type = 'file';
                    file.accept = 'image/*';
                    file.style.display = 'none';
                    document.body.appendChild(file);
                    file.addEventListener('change', function() {
                        insertImageFromFile(file.files && file.files[0]);
                        file.remove();
                    }, { once: true });
                    file.click();
                    return;
                }

                if (action === 'file') {
                    const file = document.createElement('input');
                    file.type = 'file';
                    file.style.display = 'none';
                    document.body.appendChild(file);
                    file.addEventListener('change', function() {
                        insertFileLinkFromFile(file.files && file.files[0]);
                        file.remove();
                    }, { once: true });
                    file.click();
                    return;
                }

                if (action === 'embed') {
                    const url = prompt('埋め込みURLを入力してください（例：YouTube）');
                    if (url) insertEmbedUrl(url);
                    return;
                }

                if (action === 'toc') {
                    const tocHtml = buildTocHtml();
                    if (tocHtml) insertHtmlIntoEditor(tocHtml);
                    return;
                }

                if (action === 'heading-large') return formatBlock('H2');
                if (action === 'heading-small') return formatBlock('H3');
                if (action === 'list-bullet') return toggleList('bullet');
                if (action === 'list-ordered') return toggleList('ordered');
                if (action === 'blockquote') return toggleQuote();
                if (action === 'hr') return insertHorizontalRule();
            });
        }

        // -----------------------------
        // Quill リッチエディタ（記事本文）
        // -----------------------------
        /* function initQuill() {
            const quillEditorEl = document.getElementById('quillEditor');
            const bodyInput = document.getElementById('body_html');
            if (!quillEditorEl || !bodyInput || !window.Quill) return;

            const quill = new Quill('#quillEditor', {
                theme: 'snow',
                modules: { toolbar: false, clipboard: { matchVisual: false } },
                placeholder: '本文を入力してください'
            });

            const initialHtml = (bodyInput.value || '').trim();
            if (initialHtml) {
                quill.clipboard.dangerouslyPasteHTML(initialHtml);
            }

            const syncToTextarea = () => {
                bodyInput.value = quill.root.innerHTML;
            };
            quill.on('text-change', syncToTextarea);
            syncToTextarea();

            const insertMenuToggle = document.getElementById('insertMenuToggle');
            const insertMenu = document.getElementById('insertMenu');

            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.accept = 'image/*';
            fileInput.style.display = 'none';
            document.body.appendChild(fileInput);

            const genericFileInput = document.createElement('input');
            genericFileInput.type = 'file';
            genericFileInput.style.display = 'none';
            document.body.appendChild(genericFileInput);

            function closeMenu() {
                if (insertMenu) insertMenu.style.display = 'none';
                if (insertMenuToggle) insertMenuToggle.setAttribute('aria-expanded', 'false');
            }

            function openMenu() {
                if (!insertMenu || !insertMenuToggle) return;
                insertMenu.style.display = 'block';
                insertMenuToggle.setAttribute('aria-expanded', 'true');

                const rect = insertMenuToggle.getBoundingClientRect();
                const w = insertMenu.offsetWidth;
                const left = Math.min(rect.left, window.innerWidth - w - 12);
                insertMenu.style.left = left + 'px';
                insertMenu.style.top = (rect.bottom + 8) + 'px';
            }

            // メニュー開閉（＋ 挿入ボタン）は外側で制御しています

            function getCursorIndex() {
                const range = quill.getSelection(true);
                if (!range) return quill.getLength();
                return range.index;
            }

            function insertHtmlAtCursor(html) {
                const index = getCursorIndex();
                quill.clipboard.dangerouslyPasteHTML(index, html, 'user');
                quill.setSelection(Math.min(index + 1, quill.getLength()), 0, 'silent');
            }

            function insertImageDataUrl(dataUrl) {
                const index = getCursorIndex();
                quill.insertEmbed(index, 'image', dataUrl, 'user');
                quill.setSelection(index + 1, 0, 'silent');
            }

            function readFileAsDataURL(file, cb) {
                const reader = new FileReader();
                reader.onload = () => cb(reader.result);
                reader.readAsDataURL(file);
            }

            function insertEmbedFromUrl(url) {
                const trimmed = (url || '').trim();
                if (!trimmed) return;

                const ytMatch = trimmed.match(/(?:youtube\\.com\\/watch\\?v=|youtu\\.be\\/)([A-Za-z0-9_-]{6,})/);
                if (ytMatch && ytMatch[1]) {
                    const videoId = ytMatch[1];
                    const html = '<div class="my-4"><iframe width="560" height="315" src="https://www.youtube.com/embed/' + videoId + '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>';
                    insertHtmlAtCursor(html);
                    return;
                }

                const html = '<div class="my-4"><iframe src="' + trimmed + '" style="width:100%; min-height:320px;" frameborder="0" allowfullscreen></iframe></div>';
                insertHtmlAtCursor(html);
            }

            function buildTocHtml() {
                const headings = Array.from(quill.root.querySelectorAll('h2, h3'));
                if (headings.length === 0) {
                    alert('見出し（大見出し/小見出し）を先に入力してください。');
                    return null;
                }

                function slugify(s) {
                    // Quill側でも同じ安全版
                    return (String(s || '') || '')
                        .toLowerCase()
                        .trim()
                        .replace(/\s+/g, '-')
                        .replace(/[^a-z0-9\-]/g, '')
                        .slice(0, 40) || 'section';
                }

                const used = new Set();
                headings.forEach((h) => {
                    let id = h.getAttribute('id');
                    if (!id) {
                        id = slugify(h.textContent) || 'section';
                        let base = id;
                        let i = 2;
                        while (used.has(id)) {
                            id = base + '-' + i;
                            i++;
                        }
                        h.setAttribute('id', id);
                    }
                    used.add(id);
                });

                const items = headings.map((h) => {
                    const level = h.tagName.toLowerCase() === 'h2' ? 2 : 3;
                    const id = h.getAttribute('id');
                    const text = (h.textContent || '').trim();
                    const indent = level === 3 ? ' style="padding-left: 1rem;"' : '';
                    return '<li' + indent + '><a href="#' + id + '">' + (text || '') + '</a></li>';
                }).join('');

                return '<div class="article-toc"><div class="font-bold text-gray-900">目次</div><ul>' + items + '</ul></div>';
            }

            if (insertMenu) {
                insertMenu.addEventListener('click', function(e) {
                    const item = e.target.closest('.menu-item');
                    if (!item) return;
                    const action = item.getAttribute('data-action');
                    if (!action) return;

                    closeMenu();

                    if (action === 'image') {
                        fileInput.click();
                        fileInput.onchange = function() {
                            const file = fileInput.files && fileInput.files[0];
                            if (!file) return;
                            if (!file.type || !file.type.startsWith('image/')) return;
                            readFileAsDataURL(file, function(dataUrl) {
                                insertImageDataUrl(dataUrl);
                            });
                        };
                        return;
                    }

                    if (action === 'file') {
                        genericFileInput.click();
                        genericFileInput.onchange = function() {
                            const file = genericFileInput.files && genericFileInput.files[0];
                            if (!file) return;
                            const MAX_BYTES = 2 * 1024 * 1024; // 2MB
                            if (file.size > MAX_BYTES) {
                                alert('ファイルが大きすぎます（2MBまで）。別途サーバアップロード機能が必要です。');
                                return;
                            }
                            readFileAsDataURL(file, function(dataUrl) {
                                const safeName = String(file.name || 'file')
                                    .replace(/</g, '&lt;')
                                    .replace(/>/g, '&gt;');
                                const html = '<p><a href="' + dataUrl + '" download="' + safeName + '" class="text-indigo-600 underline">' + safeName + '</a></p>';
                                insertHtmlAtCursor(html);
                            });
                        };
                        return;
                    }

                    if (action === 'embed') {
                        const url = prompt('埋め込みURLを入力してください（例：YouTube）');
                        if (url) insertEmbedFromUrl(url);
                        return;
                    }

                    if (action === 'toc') {
                        const tocHtml = buildTocHtml();
                        if (tocHtml) insertHtmlAtCursor(tocHtml);
                        return;
                    }

                    if (action === 'heading-large') {
                        const idx = quill.getSelection(true)?.index ?? quill.getLength();
                        quill.formatLine(idx, 1, 'header', 2);
                        return;
                    }

                    if (action === 'heading-small') {
                        const idx = quill.getSelection(true)?.index ?? quill.getLength();
                        quill.formatLine(idx, 1, 'header', 3);
                        return;
                    }

                    if (action === 'list-bullet') {
                        quill.format('list', 'bullet');
                        return;
                    }

                    if (action === 'list-ordered') {
                        quill.format('list', 'ordered');
                        return;
                    }

                    if (action === 'blockquote') {
                        quill.format('blockquote', true);
                        return;
                    }

                    if (action === 'hr') {
                        insertHtmlAtCursor('<hr />');
                        return;
                    }
                });
            }
        }

        if (!window.Quill) {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js';
            script.onload = initQuill;
            document.head.appendChild(script);
        } else {
            initQuill();
        }
        */

        const tagItemsContainer = document.getElementById('article-tag-items-container');
        const addTagRowBtn = document.getElementById('add-article-tags-row');
        const removeTagRowBtn = document.getElementById('remove-article-tags-row');

        const MAX_ROWS = 4; // 16 slots (4 inputs per row)
        const MIN_ROWS = 1; // 4 slots  (1 row)

        function buildTagRow() {
            const row = document.createElement('div');
            row.className = 'article-tag-input-row grid grid-cols-2 sm:grid-cols-4 gap-3';

            for (let col = 0; col < 4; col++) {
                const input = document.createElement('input');
                input.className = 'w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent';
                input.name = 'tags[]';
                input.type = 'text';
                input.placeholder = '例: API';
                row.appendChild(input);
            }
            return row;
        }

        function syncTagRowButtons() {
            if (!tagItemsContainer || !addTagRowBtn || !removeTagRowBtn) return;
            const rowCount = tagItemsContainer.querySelectorAll('.article-tag-input-row').length;
            addTagRowBtn.disabled = rowCount >= MAX_ROWS;
            removeTagRowBtn.disabled = rowCount <= MIN_ROWS;
            addTagRowBtn.setAttribute('aria-disabled', String(rowCount >= MAX_ROWS));
            removeTagRowBtn.setAttribute('aria-disabled', String(rowCount <= MIN_ROWS));
        }

        if (tagItemsContainer && addTagRowBtn && removeTagRowBtn) {
            addTagRowBtn.addEventListener('click', function () {
                const rowCount = tagItemsContainer.querySelectorAll('.article-tag-input-row').length;
                if (rowCount >= MAX_ROWS) return;
                tagItemsContainer.appendChild(buildTagRow());
                syncTagRowButtons();
            });

            removeTagRowBtn.addEventListener('click', function () {
                const rows = tagItemsContainer.querySelectorAll('.article-tag-input-row');
                if (rows.length <= MIN_ROWS) return;
                const last = rows[rows.length - 1];
                if (last) last.remove();
                syncTagRowButtons();
            });

            syncTagRowButtons();
        }

        // 既存のアイキャッチ画像を、skills/create と同じDOMに反映
        var imageInput = document.getElementById('imageInput');
        var imagePreview = document.getElementById('imagePreview');
        var previewImg = document.getElementById('previewImg');
        var uploadLabel = document.getElementById('uploadLabel');

        function setPreviewFromUrl(url) {
            if (!imagePreview || !previewImg || !uploadLabel || !url) return;
            previewImg.src = url;
            imagePreview.style.display = 'block';
            uploadLabel.style.display = 'none';
            currentEyecatchUrl = url;
        }

        if (existingEyecatchUrl && imagePreview && previewImg && uploadLabel && (!imageInput || !imageInput.files || !imageInput.files.length)) {
            setPreviewFromUrl(existingEyecatchUrl);
        }
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

    function buildPreviewHtml(imageUrl) {
        var title = document.getElementById('title').value;
        var excerptEl = document.getElementById('excerpt');
        var excerpt = excerptEl ? excerptEl.value : '';
        var category = document.getElementById('category').value;
        var bodyHtml = document.getElementById('body_html').value;

        var html = '';
        if (imageUrl) html += '<div class="mb-6"><img src="' + escapeAttr(imageUrl) + '" alt="" class="w-full h-48 object-cover rounded-xl"></div>';
        html += '<h1 class="text-2xl font-bold text-gray-900 mb-4">' + escapeHtml(title || '（タイトル未入力）') + '</h1>';
        html += '<p class="text-gray-600 mb-4">' + escapeHtml(excerpt || '（概要未入力）') + '</p>';
        html += '<span class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm mb-4">' + escapeHtml(category || '（カテゴリー未選択）') + '</span>';
        var currentTags = Array.from(document.querySelectorAll('input[name="tags[]"]'))
            .map(function(el) { return (el.value || '').trim(); })
            .filter(function(v) { return v.length > 0; });
        html += '<div class="flex flex-wrap gap-2 mb-6">' + currentTags.map(function(t) {
            return '<span class="px-3 py-1 bg-gray-100 rounded-full text-sm">#' + escapeHtml(t) + '</span>';
        }).join('') + '</div>';
        html += '<div class="article-body-preview text-gray-800 text-base leading-relaxed">' + bodyHtml + '</div>';
        return html;
    }

    function handlePreview() {
        var fileInput = document.getElementById('imageInput');
        if (fileInput && fileInput.files && fileInput.files[0]) {
            var reader = new FileReader();
            reader.onload = function(ev) {
                currentEyecatchUrl = ev.target.result;
                document.getElementById('previewContent').innerHTML = buildPreviewHtml(ev.target.result);
                document.getElementById('previewModal').classList.remove('hidden');
            };
            reader.readAsDataURL(fileInput.files[0]);
            return;
        }

        document.getElementById('previewContent').innerHTML = buildPreviewHtml(currentEyecatchUrl || '');
        document.getElementById('previewModal').classList.remove('hidden');
    }

    function closePreview() {
        document.getElementById('previewModal').classList.add('hidden');
    }

    // skills/create と同等のプレビュー制御（編集画面でも削除・再選択できるように）
    function handleImageUpload(event) {
        var file = event && event.target && event.target.files ? event.target.files[0] : null;
        if (!file) return;
        if (!file.type || !file.type.startsWith('image/')) return;

        var reader = new FileReader();
        reader.onload = function(e) {
            currentEyecatchUrl = e.target.result;

            // 新しい画像を選んだので「削除」フラグは解除
            var removeFlag = document.getElementById('eyecatch_image_remove');
            if (removeFlag) removeFlag.value = '0';

            var previewImg = document.getElementById('previewImg');
            var imagePreview = document.getElementById('imagePreview');
            var uploadLabel = document.getElementById('uploadLabel');

            if (previewImg) previewImg.src = currentEyecatchUrl;
            if (imagePreview) imagePreview.style.display = 'block';
            if (uploadLabel) uploadLabel.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }

    function removeImage() {
        currentEyecatchUrl = '';

        // 既存アイキャッチ画像を削除する意思をサーバへ送る
        var removeFlag = document.getElementById('eyecatch_image_remove');
        if (removeFlag) removeFlag.value = '1';

        var previewImg = document.getElementById('previewImg');
        var imagePreview = document.getElementById('imagePreview');
        var uploadLabel = document.getElementById('uploadLabel');
        var input = document.getElementById('imageInput');

        if (previewImg) previewImg.src = '';
        if (imagePreview) imagePreview.style.display = 'none';
        if (uploadLabel) uploadLabel.style.display = 'flex';
        if (input) input.value = '';
    }
</script>
@endpush
