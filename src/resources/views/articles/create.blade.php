@extends('layouts.public')

@section('title', '記事を投稿 - AIスキルマッチ')

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

    /* 見出しの見た目（大見出し/小見出しを視覚的に分かりやすく） */
    #bodyEditor h2 {
        font-size: 1.875rem; /* 見出し（大） */
        font-weight: 800;
        margin: 1.25rem 0 0.75rem;
        line-height: 1.2;
    }
    #bodyEditor h3 {
        font-size: 1.2rem; /* 見出し（小）：本文より少しだけ大きく */
        font-weight: 800;
        margin: 1.15rem 0 0.65rem;
        line-height: 1.25;
    }

    /* 箇条書き/番号付きリストを視覚的に分かりやすく */
    #bodyEditor ul {
        list-style: disc;
        padding-left: 1.5rem;
        margin: 0.75rem 0;
    }
    #bodyEditor ol {
        list-style: decimal;
        padding-left: 1.5rem;
        margin: 0.75rem 0;
    }
    #bodyEditor li {
        margin: 0.35rem 0;
        line-height: 1.6;
    }

    /* プレビュー表示（本文HTMLをそのまま流すので、list-style を明示） */
    .article-body-preview ul {
        list-style: disc;
        padding-left: 1.5rem;
        margin: 0.75rem 0;
    }
    .article-body-preview ol {
        list-style: decimal;
        padding-left: 1.5rem;
        margin: 0.75rem 0;
    }
    .article-body-preview li {
        margin: 0.35rem 0;
        line-height: 1.6;
        white-space: normal;
    }

    /* 見出しもプレビューで投稿後と同じ見た目にする */
    .article-body-preview h2 {
        font-size: 1.875rem;
        font-weight: 800;
        margin: 1.25rem 0 0.75rem;
        line-height: 1.2;
    }
    .article-body-preview h3 {
        font-size: 1.2rem;
        font-weight: 800;
        margin: 1.15rem 0 0.65rem;
        line-height: 1.25;
    }

    /* 引用の見た目（クリックしてブロックが現れたときに分かりやすく） */
    #bodyEditor blockquote {
        border-left: 4px solid #4f46e5;
        background: #f5f3ff;
        padding: 0.75rem 1rem;
        margin: 1rem 0;
        border-radius: 0.5rem;
        line-height: 1.6;
    }
    #bodyEditor blockquote p {
        margin: 0;
    }

    /* 目次（ToC）削除ボタン（本文クリック時にだけ表示） */
    #tocDeleteBtn {
        position: fixed;
        z-index: 100000;
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: white;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -4px rgba(0,0,0,0.1);
        display: none;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    #tocDeleteBtn.show { display: flex; }

    /* OGPリンクカード（note風） */
    #bodyEditor .ogp-card {
        display: flex;
        flex-direction: row;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        text-decoration: none;
        color: inherit;
        transition: box-shadow 0.2s ease, transform 0.2s ease;
    }
    #bodyEditor .ogp-card:hover {
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.10);
        transform: translateY(-1px);
    }
    #bodyEditor .ogp-card-media {
        width: 176px;
        min-height: 132px;
        background: #f3f4f6;
        flex-shrink: 0;
        overflow: hidden;
    }
    #bodyEditor .ogp-card-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    #bodyEditor .ogp-card-media-placeholder {
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    }
    #bodyEditor .ogp-card-content {
        min-width: 0;
        flex: 1;
        padding: 16px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 4px;
    }
    #bodyEditor .ogp-card-site {
        font-size: 12px;
        font-weight: 700;
        color: #4f46e5;
        line-height: 1.2;
    }
    #bodyEditor .ogp-card-title {
        font-size: 15px;
        font-weight: 800;
        color: #111827;
        line-height: 1.45;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    #bodyEditor .ogp-card-description {
        font-size: 14px;
        color: #6b7280;
        line-height: 1.45;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    #bodyEditor .ogp-card-domain {
        font-size: 12px;
        color: #6b7280;
        line-height: 1.2;
        margin-top: 2px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    #bodyEditor .ogp-card-loading {
        cursor: default;
    }
    #bodyEditor .ogp-skeleton {
        background: #e5e7eb;
        border-radius: 9999px;
        overflow: hidden;
        position: relative;
    }
    #bodyEditor .ogp-skeleton::after {
        content: '';
        position: absolute;
        inset: 0;
        transform: translateX(-100%);
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.7), transparent);
        animation: ogp-shimmer 1.3s infinite;
    }
    #bodyEditor .ogp-skeleton-title {
        width: 75%;
        height: 18px;
        margin-top: 2px;
    }
    #bodyEditor .ogp-skeleton-line {
        width: 100%;
        height: 12px;
    }
    #bodyEditor .ogp-skeleton-line.short {
        width: 55%;
    }
    @keyframes ogp-shimmer {
        100% {
            transform: translateX(100%);
        }
    }
    @media (max-width: 640px) {
        #bodyEditor .ogp-card {
            flex-direction: column;
        }
        #bodyEditor .ogp-card-media {
            width: 100%;
            height: 180px;
        }
    }

    /* 投稿前プレビュー（previewModal）の ogp-card も同じ見た目にする */
    .article-body-preview .ogp-card {
        display: flex;
        flex-direction: row;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        text-decoration: none;
        color: inherit;
        transition: box-shadow 0.2s ease, transform 0.2s ease;
    }
    .article-body-preview .ogp-card:hover {
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.10);
        transform: translateY(-1px);
    }
    .article-body-preview .ogp-card-media {
        width: 176px;
        min-height: 132px;
        background: #f3f4f6;
        flex-shrink: 0;
        overflow: hidden;
    }
    .article-body-preview .ogp-card-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .article-body-preview .ogp-card-media-placeholder {
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    }
    .article-body-preview .ogp-card-content {
        min-width: 0;
        flex: 1;
        padding: 16px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 4px;
    }
    .article-body-preview .ogp-card-site {
        font-size: 12px;
        font-weight: 700;
        color: #4f46e5;
        line-height: 1.2;
    }
    .article-body-preview .ogp-card-title {
        font-size: 15px;
        font-weight: 800;
        color: #111827;
        line-height: 1.45;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    .article-body-preview .ogp-card-description {
        font-size: 14px;
        color: #6b7280;
        line-height: 1.45;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    .article-body-preview .ogp-card-domain {
        font-size: 12px;
        color: #6b7280;
        line-height: 1.2;
        margin-top: 2px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    @media (max-width: 640px) {
        .article-body-preview .ogp-card {
            flex-direction: column;
        }

        .article-body-preview .ogp-card-media {
            width: 100%;
            height: 180px;
        }
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
        list-style: none !important;
        list-style-type: none !important;
        padding-left: 0 !important;
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
        max-height: calc(100vh - 16px);
        overflow-y: auto;
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

    /* 本文：コードブロック表示 */
    #bodyEditor pre.editor-code-block {
        background: #111827;
        color: #e5e7eb;
        padding: 12px 14px;
        border-radius: 0.75rem;
        overflow: auto;
        margin: 1rem 0;
        white-space: pre;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        font-size: 0.95rem;
        line-height: 1.6;
    }

    /* 本文：コードブロック（contenteditable対応） */
    #bodyEditor pre.editor-code-block[contenteditable="true"] {
        outline: none;
        cursor: text;
    }

    /* 全画面エディタ（新規投稿のみ） */
    #fullscreenArticleEditorOverlay {
        position: fixed;
        inset: 0;
        z-index: 99999;
        background: rgba(255, 255, 255, 0.98);
        display: none;
        overflow-y: auto;
        padding-bottom: 320px; /* 入力内容が少ない時でもスクロールできるようにする */
        box-sizing: border-box;
    }
    #fullscreenArticleEditorOverlay.hidden { display: none; }
    #fullscreenArticleEditorOverlay:not(.hidden) { display: block; }

    #fullscreenArticleEditorOverlay #bodyEditor {
        /* 全画面表示では最低でもこの高さを確保する（入力が少ない時の縦幅不足を防ぐ） */
        min-height: max(1200px, calc(100vh - 160px)) !important;
    }

    /* 記事本文：画像編集ツール（クリックで表示） */
    #imageEditorToolbar {
        position: fixed;
        z-index: 100003;
        display: none;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -4px rgba(0,0,0,0.1);
        padding: 10px 12px;
        width: 290px;
    }

    #imageEditorToolbar.show { display: block; }

    #imageEditorToolbar .toolbar-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 8px;
    }

    #imageEditorToolbar .toolbar-title {
        font-weight: 800;
        color: #111827;
        font-size: 0.9rem;
    }

    #imageEditorToolbar .tool-btn {
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        color: #111827;
        font-weight: 700;
        padding: 8px 10px;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: background-color 0.15s ease, transform 0.05s ease;
        user-select: none;
        white-space: nowrap;
    }

    #imageEditorToolbar .tool-btn:hover { background: #f3f4f6; }
    #imageEditorToolbar .tool-btn:active { transform: translateY(1px); }

    #imageEditorToolbar .tool-btn.danger {
        border-color: #fecaca;
        background: #fef2f2;
        color: #991b1b;
    }
    #imageEditorToolbar .tool-btn.danger:hover { background: #fee2e2; }

    #imageEditorToolbar .slider-wrap { display: flex; align-items: center; gap: 10px; }
    #imageEditorToolbar input[type="range"] { width: 140px; }

    #imageEditorToolbar .align-options {
        display: none;
        gap: 8px;
        margin-top: 6px;
    }
    #imageEditorToolbar.show .align-options.open { display: flex; }

    #imageEditorToolbar .align-btn {
        flex: 1 1 0;
        text-align: center;
        padding: 8px 8px;
        border-radius: 0.5rem;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        cursor: pointer;
        font-weight: 800;
        color: #111827;
        user-select: none;
    }

    #imageEditorToolbar .align-btn:hover { background: #f3f4f6; }

    /* 本文内の画像編集対象（クリック時の見た目崩れ対策） */
    #bodyEditor figure.editor-image-block {
        margin: 0.75rem 0;
        overflow: visible;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
    }

    #bodyEditor img.editor-image {
        cursor: grab;
        user-select: none;
        touch-action: none;
    }

    #bodyEditor figcaption.editor-image-caption {
        width: 100%;
        max-width: 100%;
        min-height: 1.6em;
        margin-top: 0.75rem;
        padding: 0 0.25rem;
        outline: none;
        text-align: center;
        color: #6b7280;
        font-size: 0.95rem;
        line-height: 1.6;
        word-break: break-word;
    }

    #bodyEditor figcaption.editor-image-caption:empty::before {
        content: attr(data-placeholder);
        color: #9ca3af;
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
                    <button
                        type="button"
                        onclick="handlePreview()"
                        class="flex items-center gap-2 px-4 py-2 border-2 border-green-600 text-green-600 rounded-xl font-semibold hover:bg-green-50 transition-all"
                    >
                        プレビュー
                    </button>
                    <button type="submit" form="articleForm" class="flex items-center gap-2 px-6 py-2 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        投稿する
                    </button>
                </div>
            </div>
        </div>

        <form id="articleForm" action="{{ route('articles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
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

                {{-- 概要（excerpt）は必須ではないため、投稿/編集UIから非表示にしています --}}
                {{-- <div class="mb-6"> --}}
                {{--     <label class="block text-sm font-semibold text-gray-700 mb-2"> --}}
                {{--         概要 <span class="text-red-500">*</span> --}}
                {{--     </label> --}}
                {{--     <textarea name="excerpt" id="excerpt" placeholder="記事の概要を入力してください（2-3文程度）" rows="3" maxlength="200" --}}
                {{--         class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none @error('excerpt') border-red-500 @enderror">{{ old('excerpt') }}</textarea> --}}
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
                        <option value="n8n" {{ old('category') === 'n8n' ? 'selected' : '' }}>n8n</option>
                        <option value="AIツール" {{ old('category') === 'AIツール' ? 'selected' : '' }}>AIツール</option>
                        <option value="自動化" {{ old('category') === '自動化' ? 'selected' : '' }}>自動化</option>
                        <option value="プログラミング" {{ old('category') === 'プログラミング' ? 'selected' : '' }}>プログラミング</option>
                        <option value="ビジネス活用" {{ old('category') === 'ビジネス活用' ? 'selected' : '' }}>ビジネス活用</option>
                        <option value="副業・フリーランス" {{ old('category') === '副業・フリーランス' ? 'selected' : '' }}>副業・フリーランス</option>
                        <option value="その他" {{ old('category', '') === 'その他' ? 'selected' : '' }}>その他</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    @php
                        $tagSlots = old('tags', []);
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
                        <button type="button" id="add-article-tags-row" class="px-4 py-2 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl hover:shadow-lg transition-colors">
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
                        <div id="editorInsertControlsWrapper" class="flex items-center gap-3 mb-3 justify-end">
                            <button
                                type="button"
                                id="insertMenuToggle"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition-colors border border-gray-200"
                                aria-haspopup="dialog"
                                aria-expanded="false"
                            >
                                ＋ 挿入
                            </button>

                            <button
                                type="button"
                                id="fullscreenEditorToggle"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition-colors border border-gray-200"
                            >
                                全画面表示
                            </button>

                            <div id="insertMenu" role="dialog" aria-label="挿入メニュー">
                                <div class="menu-section-title">インライン/ブロック</div>
                                <div class="menu-item" data-action="paragraph">
                                    <span class="menu-icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 6h16"></path>
                                            <path d="M4 12h10"></path>
                                            <path d="M4 18h16"></path>
                                        </svg>
                                    </span>
                                    通常の文字
                                </div>
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

                                <div class="menu-item" data-action="code">
                                    <span class="menu-icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M16 18l6-6-6-6"></path>
                                            <path d="M8 6l-6 6 6 6"></path>
                                        </svg>
                                    </span>
                                    コード
                                </div>
                            </div>
                        </div>

                        <div id="bodyEditor" class="bg-white rounded-xl border border-gray-300" contenteditable="true" data-placeholder="本文を入力してください">{!! old('body_html') !!}</div>
                        <textarea
                            id="body_html"
                            name="body_html"
                            maxlength="10000000"
                            class="hidden"
                        >{{ old('body_html') }}</textarea>
                    </div>

                    {{-- 全画面エディタ（新規投稿のみ） --}}
                    <div id="fullscreenArticleEditorOverlay" class="hidden overflow-y-auto">
                        <div class="flex items-center justify-between p-4 border-b border-gray-200">
                            <div class="text-sm font-semibold text-gray-700">記事本文（全画面）</div>
                            <button
                                type="button"
                                id="fullscreenArticleEditorCloseBtn"
                                class="px-4 py-2 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700 transition-colors"
                            >
                                閉じる
                            </button>
                        </div>

                        <div class="p-4 max-w-[900px] mx-auto">
                            <div id="fullscreenArticleEditorInsertControlsSlot" class="mb-3"></div>
                            <div id="fullscreenArticleEditorBodySlot"></div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">アイキャッチ画像 <span class="text-red-500">*</span></label>
                    <div id="imagePreview" style="display: none;" class="relative mb-4">
                        <img id="previewImg" src="" alt="Preview" class="w-full aspect-video object-cover rounded-lg">
                        <button type="button" onclick="removeImage()" class="absolute top-2 right-2 p-2 bg-red-500 text-white rounded-full hover:bg-red-600 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

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
                            <input type="radio" name="is_published" value="1" checked class="w-4 h-4 text-green-600 focus:ring-green-500 border-gray-300">
                            <span class="text-gray-700">
                                <span class="font-medium">公開</span>
                                <span class="text-sm text-gray-500 ml-1">（誰でも閲覧できます）</span>
                            </span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="is_published" value="0" class="w-4 h-4 text-green-600 focus:ring-green-500 border-gray-300">
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

            <div class="flex justify-end gap-4">
                <a href="{{ route('articles.index') }}" class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-all">
                    キャンセル
                </a>
                <button type="submit" class="flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // -----------------------------
        // ＋ 挿入メニュー（Quillが未ロードでも開く）
        // -----------------------------
        const insertMenuToggle = document.getElementById('insertMenuToggle');
        const insertMenu = document.getElementById('insertMenu');
        const bodyEditor = document.getElementById('bodyEditor');
        const bodyInput = document.getElementById('body_html');

        // -----------------------------
        // 目次（ToC）削除UI（目次がある間は常に表示）
        // -----------------------------
        let tocElToDelete = null;
        let tocDeleteBtn = document.getElementById('tocDeleteBtn');
        if (!tocDeleteBtn) {
            tocDeleteBtn = document.createElement('button');
            tocDeleteBtn.id = 'tocDeleteBtn';
            tocDeleteBtn.type = 'button';
            tocDeleteBtn.innerHTML = `
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                    <path d="M10 11v6"></path>
                    <path d="M14 11v6"></path>
                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path>
                </svg>
            `;
            document.body.appendChild(tocDeleteBtn);
        }

        function hideTocDeleteBtn() {
            tocElToDelete = null;
            if (tocDeleteBtn) tocDeleteBtn.classList.remove('show');
        }

        function positionTocDeleteBtn(tocEl) {
            if (!tocDeleteBtn || !tocEl) return;
            const rect = tocEl.getBoundingClientRect();
            const btnSize = 42;
            const padding = 10;
            const left = Math.min(Math.max(rect.right - btnSize, padding), window.innerWidth - btnSize - padding);
            const top = Math.min(Math.max(rect.top - btnSize - 6, padding), window.innerHeight - btnSize - padding);
            tocDeleteBtn.style.left = left + 'px';
            tocDeleteBtn.style.top = top + 'px';
            tocDeleteBtn.classList.add('show');
        }

        function syncTocDeleteBtn() {
            if (!bodyEditor) return;
            const tocEl = bodyEditor.querySelector('.article-toc');
            if (!tocEl) {
                hideTocDeleteBtn();
                return;
            }
            tocElToDelete = tocEl;
            positionTocDeleteBtn(tocEl);
        }

        if (tocDeleteBtn) tocDeleteBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (!tocElToDelete) return;
            tocElToDelete.remove();
            tocElToDelete = null;
            hideTocDeleteBtn();
            syncBodyEditor();
        });

        // 初期状態（old() / 既存内容に目次があれば表示）
        syncTocDeleteBtn();

        // 目次が挿入/削除されるタイミングに追従
        if (bodyEditor) {
            bodyEditor.addEventListener('input', function() {
                syncTocDeleteBtn();
            });
            bodyEditor.addEventListener('blur', function() {
                syncTocDeleteBtn();
            });
        }

        function syncBodyEditor() {
            if (!bodyEditor || !bodyInput) return;
            bodyInput.value = bodyEditor.innerHTML;
        }

        // figure の直後に、左寄せの通常段落（p）を挿入する
        // h2/h3 の「内側」に入ってしまうと、見出しフォントが継承されてしまうため排除する
        function insertLeftAlignedParagraphAfterFigure(figureEl) {
            if (!figureEl || !bodyEditor) return null;

            const p = document.createElement('p');
            p.style.textAlign = 'left';
            p.innerHTML = '<br>';

            // figure が h2/h3 の内側にいる場合は、その h2/h3 の直後へ挿入する
            const headingAncestor = figureEl.closest('h2, h3');
            const insertAfterEl = headingAncestor ? headingAncestor : figureEl;

            if (!insertAfterEl || !insertAfterEl.parentNode) return null;

            if (insertAfterEl.nextSibling) {
                insertAfterEl.parentNode.insertBefore(p, insertAfterEl.nextSibling);
            } else {
                insertAfterEl.parentNode.appendChild(p);
            }

            syncBodyEditor();
            return p;
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

        // Shift+Enter: どの状態でも「通常の文字入力」に強制的に戻す
        if (bodyEditor) {
            bodyEditor.addEventListener('keydown', function(e) {
                if (!e.shiftKey || e.key !== 'Enter') return;

                const selection = window.getSelection();
                if (!selection || selection.rangeCount === 0) return;

                const anchorNode = selection.anchorNode;
                const li = anchorNode && anchorNode.closest
                    ? anchorNode.closest('li')
                    : (anchorNode?.parentElement ? anchorNode.parentElement.closest('li') : null);

                const figCaption = (anchorNode && anchorNode.closest)
                    ? anchorNode.closest('figcaption.editor-image-caption')
                    : (anchorNode?.parentElement ? anchorNode.parentElement.closest('figcaption.editor-image-caption') : null);

                if (li) {
                    e.preventDefault();
                    exitListToParagraph();
                    return;
                }

                // 画像キャプション中なら、図の直後に通常の段落を作って移動
                if (figCaption) {
                    const fig = figCaption.closest('figure.editor-image-block');
                    if (fig) {
                        e.preventDefault();
                        const range = document.createRange();
                        range.setStartAfter(fig);
                        range.collapse(true);
                        const sel = window.getSelection();
                        sel.removeAllRanges();
                        sel.addRange(range);

                        document.execCommand('formatBlock', false, 'P');
                        document.execCommand('removeFormat', false, null);
                        insertHtmlIntoEditor('<p><br></p>');
                        return;
                    }
                }

                // それ以外も通常の段落に寄せる
                e.preventDefault();
                exitListToParagraph();
            });
        }

        // Enter(Shiftなし): 画像キャプション入力中でも通常段落へ戻す
        if (bodyEditor) {
            bodyEditor.addEventListener('keydown', function(e) {
                // `contenteditable` の中だと `e.target` が TextNode になることがあり、
                // TextNode は `closest()` を持たないため判定が外れる。そのため selection の anchorNode から拾う
                let captionEl = null;
                try {
                    const selection = window.getSelection();
                    const anchorNode = selection ? selection.anchorNode : null;
                    const anchorEl =
                        anchorNode && anchorNode.nodeType === 1
                            ? anchorNode
                            : (anchorNode && anchorNode.parentElement ? anchorNode.parentElement : null);

                    if (anchorEl && anchorEl.closest) {
                        captionEl = anchorEl.closest('figcaption.editor-image-caption');
                    }
                } catch (err) {}
                if (!captionEl) return;
                if (e.key !== 'Enter') return;
                if (e.shiftKey) return;

                const figureEl = captionEl.closest('figure.editor-image-block');
                if (!figureEl) return;

                e.preventDefault();
                e.stopPropagation();

                const p = insertLeftAlignedParagraphAfterFigure(figureEl);
                if (!p) return;

                window.setTimeout(function() {
                    bodyEditor.focus();
                    const selection = window.getSelection();
                    selection.removeAllRanges();
                    const range = document.createRange();
                    range.selectNodeContents(p);
                    range.collapse(true);
                    selection.addRange(range);

                    // 念のため P へ整形（見出し継承の回避）
                    try {
                        document.execCommand('formatBlock', false, 'P');
                        document.execCommand('removeFormat', false, null);
                    } catch (err) {}
                }, 0);
            }, true);
        }

        // -----------------------------
        // 本文：画像クリックで編集ツール表示
        // -----------------------------
        const imageEditorToolbar = document.getElementById('imageEditorToolbar') || (function() {
            const el = document.createElement('div');
            el.id = 'imageEditorToolbar';
            el.innerHTML = `
                <div class="toolbar-row">
                    <div class="toolbar-title">画像編集</div>
                    <button type="button" class="tool-btn danger" data-action="delete">削除</button>
                </div>
                <div class="toolbar-row">
                    <div class="slider-wrap">
                        <button type="button" class="tool-btn" data-action="zoom-out">−</button>
                        <input type="range" id="imageZoomRange" min="50" max="100" step="1" value="100" />
                        <button type="button" class="tool-btn" data-action="zoom-in">＋</button>
                    </div>
                </div>
            `;
            document.body.appendChild(el);
            return el;
        })();

        const imageZoomRange = imageEditorToolbar.querySelector('#imageZoomRange');
        let activeEditorImage = null;
        let imageDragState = null;
        let suppressToolbarHideOnce = false;

        function hideImageEditorToolbar() {
            imageEditorToolbar.classList.remove('show');
            imageEditorToolbar.style.left = '';
            imageEditorToolbar.style.top = '';
            activeEditorImage = null;
        }

        function getEditorImageFigure(imgEl) {
            return imgEl?.closest('figure.editor-image-block') || imgEl?.parentElement || null;
        }

        function applyZoomToImage(imgEl, percent) {
            if (!imgEl) return;
            const p = Math.max(50, Math.min(100, parseInt(percent, 10) || 100));
            imgEl.dataset.zoom = String(p);
            const currentOffset = parseInt(imgEl.dataset.offsetX || '0', 10) || 0;
            const clamp = getImageHorizontalClamp(imgEl);
            imgEl.dataset.offsetX = String(Math.max(-clamp, Math.min(clamp, currentOffset)));
            applyImageTransform(imgEl);
            imgEl.style.display = 'block';
        }

        function applyImageTransform(imgEl) {
            if (!imgEl) return;
            const zoom = Math.max(50, Math.min(100, parseInt(imgEl.dataset.zoom || '100', 10) || 100));
            const offsetX = parseInt(imgEl.dataset.offsetX || '0', 10) || 0;
            const fig = getEditorImageFigure(imgEl);

            if (fig) {
                fig.style.width = zoom + '%';
                fig.style.maxWidth = '100%';
                fig.style.marginLeft = 'auto';
                fig.style.marginRight = 'auto';
                fig.style.transformOrigin = 'top center';
                // 画像とキャプションを一緒に動かす
                fig.style.transform = 'translateX(' + offsetX + 'px)';
            }

            imgEl.style.width = '100%';
            imgEl.style.maxWidth = '100%';
            imgEl.style.height = 'auto';
            imgEl.style.transform = 'none';
        }

        function getImageHorizontalClamp(imgEl) {
            if (!imgEl || !bodyEditor) {
                return 0;
            }

            // bodyEditor の横幅を基準に、現在の画像の表示幅がはみ出さない範囲へ制限する
            const editorWidth = bodyEditor.clientWidth || 0;
            const fig = getEditorImageFigure(imgEl);
            const displayedWidth = (fig ? fig.getBoundingClientRect().width : 0) || imgEl.getBoundingClientRect().width || imgEl.offsetWidth || 0;
            const clamp = Math.max(0, (editorWidth - displayedWidth) / 2);

            return Math.floor(clamp);
        }

        function applyHorizontalOffset(imgEl, offsetX) {
            if (!imgEl) return;
            const clamp = getImageHorizontalClamp(imgEl);
            const nextOffset = Math.max(-clamp, Math.min(clamp, Math.round(offsetX || 0)));
            imgEl.dataset.offsetX = String(nextOffset);
            applyImageTransform(imgEl);
        }

        function positionImageEditorToolbarFor(imgEl) {
            if (!imgEl) return;
            const tbRect = imageEditorToolbar.getBoundingClientRect();
            const margin = 10;

            // 画像の近くではなく、ページ上部へ固定して重なりを避ける
            let left = window.innerWidth / 2 - tbRect.width / 2;
            left = Math.max(margin, Math.min(left, window.innerWidth - tbRect.width - margin));

            const top = margin;

            imageEditorToolbar.style.left = left + 'px';
            imageEditorToolbar.style.top = top + 'px';
        }

        function showImageEditorToolbar(imgEl) {
            if (!imgEl) return;
            activeEditorImage = imgEl;

            const fig = getEditorImageFigure(imgEl);
            if (fig && (fig.style.textAlign || '').trim() === '') {
                fig.style.textAlign = 'center';
            }

            // 現在ズーム値でスライダー同期
            const currentZoom = parseInt(imgEl.dataset.zoom || '100', 10);
            if (imageZoomRange) imageZoomRange.value = String(currentZoom);

            // ドラッグ位置に応じた表示同期
            applyImageTransform(imgEl);

            positionImageEditorToolbarFor(imgEl);
            imageEditorToolbar.classList.add('show');
        }

        if (bodyEditor) {
            bodyEditor.addEventListener('click', function(e) {
                const targetImg = e.target.closest ? e.target.closest('img.editor-image') : null;
                if (targetImg && bodyEditor.contains(targetImg)) {
                    e.stopPropagation();
                    showImageEditorToolbar(targetImg);
                    return;
                }
                hideImageEditorToolbar();
            });
        }

        if (bodyEditor) {
            bodyEditor.addEventListener('pointerdown', function(e) {
                const targetImg = e.target.closest ? e.target.closest('img.editor-image') : null;
                if (!targetImg || !bodyEditor.contains(targetImg)) return;

                const fig = getEditorImageFigure(targetImg);
                if (fig) {
                    fig.setAttribute('data-dragging', '1');
                }
                showImageEditorToolbar(targetImg);

                imageDragState = {
                    imgEl: targetImg,
                    pointerId: e.pointerId,
                    startX: e.clientX,
                    startY: e.clientY,
                    startOffsetX: parseInt(targetImg.dataset.offsetX || '0', 10) || 0,
                    moved: false
                };

                try {
                    targetImg.setPointerCapture(e.pointerId);
                } catch (err) {}

                e.preventDefault();
            });

            bodyEditor.addEventListener('pointermove', function(e) {
                if (!imageDragState || !activeEditorImage) return;
                if (e.pointerId !== imageDragState.pointerId) return;
                if (imageDragState.imgEl !== activeEditorImage) return;

                const deltaX = e.clientX - imageDragState.startX;
                const deltaY = e.clientY - imageDragState.startY;
                if (!imageDragState.moved && Math.abs(deltaX) < 3 && Math.abs(deltaY) < 3) {
                    return;
                }

                imageDragState.moved = true;
                const nextOffsetX = imageDragState.startOffsetX + deltaX;
                applyHorizontalOffset(activeEditorImage, nextOffsetX);
                positionImageEditorToolbarFor(activeEditorImage);
                syncBodyEditor();
                e.preventDefault();
            });

            function endImageDrag(e) {
                if (!imageDragState) return;
                if (e.pointerId !== imageDragState.pointerId) return;

                const fig = getEditorImageFigure(imageDragState.imgEl);
                if (fig) fig.removeAttribute('data-dragging');

                if (imageDragState.moved) {
                    suppressToolbarHideOnce = true;
                    syncBodyEditor();
                }

                imageDragState = null;
            }

            bodyEditor.addEventListener('pointerup', endImageDrag);
            bodyEditor.addEventListener('pointercancel', endImageDrag);
            bodyEditor.addEventListener('lostpointercapture', endImageDrag);
        }

        // ツールバー側クリックで閉じない
        document.addEventListener('click', function(e) {
            if (suppressToolbarHideOnce) {
                suppressToolbarHideOnce = false;
                return;
            }
            const clickTargetNode = (e.target instanceof Node) ? e.target : (e.target?.parentElement || null);
            if (imageEditorToolbar.classList.contains('show') && clickTargetNode && imageEditorToolbar.contains(clickTargetNode)) {
                return;
            }
            hideImageEditorToolbar();
        });

        if (imageEditorToolbar) {
            imageEditorToolbar.addEventListener('click', function(e) {
                const baseEl = (e.target instanceof Element)
                    ? e.target
                    : (e.target?.parentElement || null);
                const actionBtn = baseEl ? baseEl.closest('.tool-btn,[role="button"]') : null;
                if (!actionBtn) return;
                const action = actionBtn.getAttribute('data-action');

                if (!activeEditorImage) return;

                if (action === 'delete') {
                    const fig = getEditorImageFigure(activeEditorImage);
                    if (fig) fig.remove();
                    hideImageEditorToolbar();
                    syncBodyEditor();
                    return;
                }

                if (action === 'zoom-in' || action === 'zoom-out') {
                    const cur = parseInt(imageZoomRange?.value || '100', 10);
                    const next = cur + (action === 'zoom-in' ? 5 : -5);
                    if (imageZoomRange) imageZoomRange.value = String(Math.max(50, Math.min(100, next)));
                    applyZoomToImage(activeEditorImage, imageZoomRange.value);
                    syncBodyEditor();
                    return;
                }

            });
        }

        if (imageZoomRange) {
            imageZoomRange.addEventListener('input', function() {
                if (!activeEditorImage) return;
                applyZoomToImage(activeEditorImage, this.value);
                syncBodyEditor();
            });
        }

        // -----------------------------
        // ＋ ボタンを「クリック位置の左」に常に表示
        // -----------------------------
        function getCaretClientRect() {
            if (!bodyEditor) return null;
            const selection = window.getSelection();
            if (!selection || selection.rangeCount === 0) return null;
            const range = selection.getRangeAt(0);
            if (!bodyEditor.contains(range.commonAncestorContainer)) return null;

            const rect = range.getBoundingClientRect();
            if (rect && (rect.width > 0 || rect.height > 0)) return rect;

            // 幅/高さが 0 の場合は clientRects で拾う
            const rects = range.getClientRects ? range.getClientRects() : [];
            if (rects && rects.length > 0) return rects[0];
            return null;
        }

        function positionInsertMenuToggleAtCaret() {
            if (!insertMenuToggle || !bodyEditor) return;
            const rect = getCaretClientRect();
            if (!rect) return;

            const btnRect = insertMenuToggle.getBoundingClientRect();
            const bodyRect = bodyEditor.getBoundingClientRect();
            insertMenuToggle.style.position = 'fixed';
            insertMenuToggle.style.zIndex = '100002';

            // 「ボタンが本文入力エリアに被らない」ように、ボタン右端が bodyEditor 左端より左に来る位置へ補正する
            const desiredLeft = rect.left - btnRect.width - 12;
            const maxRightEdge = bodyRect.left - 8; // bodyEditor の内側に入らない目安
            const correctedLeft = (desiredLeft + btnRect.width > maxRightEdge)
                ? (maxRightEdge - btnRect.width)
                : desiredLeft;

            const clampedLeft = Math.max(8, Math.min(correctedLeft, window.innerWidth - btnRect.width - 8));
            const clampedTop = Math.max(8, Math.min(rect.top, window.innerHeight - btnRect.height - 8));

            insertMenuToggle.style.left = clampedLeft + 'px';
            insertMenuToggle.style.top = clampedTop + 'px';
        }

        if (bodyEditor) {
            bodyEditor.addEventListener('click', function() {
                positionInsertMenuToggleAtCaret();
            });
        }

        document.addEventListener('selectionchange', function() {
            positionInsertMenuToggleAtCaret();
        });

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
            // ボタンの右側にメニューが出るように配置
            const desiredLeft = rect.right + 8;
            const maxLeft = window.innerWidth - w - 8;
            const left = Math.max(8, Math.min(desiredLeft, maxLeft));

            // 縦位置はボタンの上端に合わせる（画面外ならクランプ）
            const desiredTop = rect.top;
            const maxTop = window.innerHeight - h - 8;
            const top = Math.max(8, Math.min(desiredTop, maxTop));

            insertMenu.style.left = left + 'px';
            insertMenu.style.top = top + 'px';
        }

        // -----------------------------
        // 全画面エディタ（新規投稿のみ）
        // -----------------------------
        const fullscreenEditorToggle = document.getElementById('fullscreenEditorToggle');
        const fullscreenArticleEditorOverlay = document.getElementById('fullscreenArticleEditorOverlay');
        const fullscreenArticleEditorCloseBtn = document.getElementById('fullscreenArticleEditorCloseBtn');
        const fullscreenArticleEditorInsertControlsSlot = document.getElementById('fullscreenArticleEditorInsertControlsSlot');
        const fullscreenArticleEditorBodySlot = document.getElementById('fullscreenArticleEditorBodySlot');
        const editorInsertControlsWrapper = document.getElementById('editorInsertControlsWrapper');

        // textarea はフォームのままなので、そこを基準に戻す
        const editorAreaParent = bodyInput ? bodyInput.parentNode : null;
        const editorAreaInsertAnchor = bodyInput || null; // bodyEditor を textarea の直前へ戻す
        // wrapper は bodyEditor の直前に戻す

        let isFullscreenArticleEditor = false;

        function enterFullscreenArticleEditor() {
            if (!fullscreenArticleEditorOverlay || !fullscreenArticleEditorInsertControlsSlot || !fullscreenArticleEditorBodySlot) return;
            if (!editorInsertControlsWrapper || !bodyEditor) return;

            closeInsertMenuOuter();
            syncBodyEditor();

            fullscreenArticleEditorInsertControlsSlot.appendChild(editorInsertControlsWrapper);
            fullscreenArticleEditorBodySlot.appendChild(bodyEditor);

            fullscreenArticleEditorOverlay.classList.remove('hidden');
            isFullscreenArticleEditor = true;

            window.setTimeout(function() {
                if (bodyEditor) bodyEditor.focus();
            }, 0);
        }

        function exitFullscreenArticleEditor() {
            if (!fullscreenArticleEditorOverlay || !editorAreaParent) return;
            if (!editorInsertControlsWrapper || !bodyEditor) return;

            closeInsertMenuOuter();
            syncBodyEditor();

            // textarea の直前に戻す（順序崩れ防止）
            editorAreaParent.insertBefore(bodyEditor, editorAreaInsertAnchor);
            // wrapper は bodyEditor の直前に戻す
            editorAreaParent.insertBefore(editorInsertControlsWrapper, bodyEditor);

            fullscreenArticleEditorOverlay.classList.add('hidden');
            isFullscreenArticleEditor = false;
        }

        if (fullscreenEditorToggle && fullscreenArticleEditorOverlay && fullscreenArticleEditorCloseBtn) {
            fullscreenEditorToggle.addEventListener('click', function() {
                if (isFullscreenArticleEditor) return;
                enterFullscreenArticleEditor();
            });

            fullscreenArticleEditorCloseBtn.addEventListener('click', function() {
                if (!isFullscreenArticleEditor) return;
                exitFullscreenArticleEditor();
            });
        }

        if (insertMenuToggle && insertMenu) {
            // 初期状態：閉じる
            closeInsertMenuOuter();

            insertMenuToggle.onclick = function(e) {
                e.stopPropagation();
                const isOpen = insertMenu.style.display === 'block';

                // キャプション内にカーソルがある場合は、段落に抜けて通常入力状態へ戻す
                try {
                    const selection = window.getSelection();
                    const node = selection && selection.rangeCount > 0 ? selection.getRangeAt(0).commonAncestorContainer : null;
                    const el = node && node.nodeType === 1 ? node : (node && node.parentElement ? node.parentElement : null);
                    const captionEl = el && el.closest ? el.closest('figcaption.editor-image-caption') : null;
                    if (captionEl && bodyEditor) {
                        const figureEl = captionEl.closest('figure.editor-image-block');
                        if (figureEl) {
                            const p = insertLeftAlignedParagraphAfterFigure(figureEl);
                            if (p) {
                                window.setTimeout(function() {
                                    bodyEditor.focus();
                                    const selection2 = window.getSelection();
                                    selection2.removeAllRanges();
                                    const range = document.createRange();
                                    range.selectNodeContents(p);
                                    range.collapse(true);
                                    selection2.addRange(range);
                                }, 0);
                            }
                        }
                    }
                } catch (err) {}

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

        function focusEditorAtEnd() {
            if (!bodyEditor) return;
            bodyEditor.focus();
            const range = document.createRange();
            range.selectNodeContents(bodyEditor);
            range.collapse(false);
            const selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(range);
        }

        function insertHtmlIntoEditor(html) {
            if (!bodyEditor) return;
            bodyEditor.focus();
            const selection = window.getSelection();
            const range = getEditorSelection() || document.createRange();

            if (!getEditorSelection()) {
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

        // 「引用」をクリックしたときに、カーソルが引用ブロック内に入るように挿入する
        function insertBlockquote() {
            if (!bodyEditor) return;
            bodyEditor.focus();

            let range = getEditorSelection() || null;
            if (!range) {
                range = document.createRange();
                range.selectNodeContents(bodyEditor);
                range.collapse(false);
            }

            // 現在位置の選択範囲を消してからブロックを挿入
            range.deleteContents();

            const blockquote = document.createElement('blockquote');
            blockquote.innerHTML = '<p><br></p>';

            range.insertNode(blockquote);

            const p = blockquote.querySelector('p') || blockquote;
            const newRange = document.createRange();
            newRange.selectNodeContents(p);
            newRange.collapse(true);

            const selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(newRange);

            syncBodyEditor();
        }

        function insertHorizontalRule() {
            insertHtmlIntoEditor('<hr>');
        }

        function insertImageFromFile(file) {
            if (!file || !file.type || !file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                const safeAlt = (file.name || 'image') + '';
                // 画像編集UI（クリックで操作）用にラッパークラス付与
                insertHtmlIntoEditor(
                    '<figure class="editor-image-block">' +
                        '<img class="editor-image" contenteditable="false" src="' + e.target.result + '" alt="' + safeAlt + '"' +
                            ' style="width:100%;max-width:100%;height:auto;display:block;margin-left:auto;margin-right:auto;"' +
                        '>' +
                        '<figcaption class="editor-image-caption" contenteditable="true" spellcheck="false" data-placeholder="キャプションを入力"></figcaption>' +
                    '</figure>'
                );

                window.setTimeout(function() {
                    const captions = bodyEditor ? bodyEditor.querySelectorAll('figcaption.editor-image-caption') : [];
                    const caption = captions && captions.length > 0 ? captions[captions.length - 1] : null;
                    if (!caption) return;

                    caption.focus();
                    const range = document.createRange();
                    range.selectNodeContents(caption);
                    range.collapse(false);
                    const selection = window.getSelection();
                    selection.removeAllRanges();
                    selection.addRange(range);
                }, 0);
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

        const OGP_API_ENDPOINT = '/api/ogp';
        const ogpClientCache = new Map();

        function escapeHtmlForOgp(str) {
            return String(str || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function normalizeUrlForCache(url) {
            try {
                const u = new URL(String(url).trim(), window.location.origin);
                u.hash = '';
                return u.toString();
            } catch (e) {
                return String(url || '').trim();
            }
        }

        function getDomainLabel(url) {
            try {
                return new URL(url).hostname.replace(/^www\./i, '');
            } catch (e) {
                return String(url || '');
            }
        }

        function buildOgpCardHtml(payload, fallbackUrl, state = 'loaded') {
            const title = (payload && payload.title) ? payload.title : getDomainLabel(fallbackUrl);
            const description = (payload && payload.description !== undefined && payload.description !== null && payload.description !== '')
                ? payload.description
                : 'リンク先の説明文を取得できませんでした。';
            const image = payload && payload.image ? payload.image : '';
            const canonicalUrl = (payload && payload.url) ? payload.url : fallbackUrl;
            const siteName = (payload && payload.site_name) ? payload.site_name : getDomainLabel(canonicalUrl);
            const domain = getDomainLabel(canonicalUrl);
            const statusText = state === 'error'
                ? 'リンクカードを取得できませんでした'
                : (state === 'loading' ? 'リンクカードを読み込み中' : '');
            const statusTone = state === 'error' ? 'text-red-600' : 'text-indigo-600';

            const imageHtml = image
                ? '<div class="ogp-card-media"><img src="' + escapeHtmlForOgp(image) + '" alt="" loading="lazy" referrerpolicy="no-referrer"></div>'
                : '';

            const subtitleHtml = state === 'error'
                ? '<div class="mt-1 text-sm text-red-600">一時的にプレビューを取得できませんでした。URL はそのまま有効です。</div>'
                : '<div class="ogp-card-description">' + escapeHtmlForOgp(description) + '</div>';

            return (
                '<div id="' + escapeHtmlForOgp((payload && payload.card_id) ? payload.card_id : '') + '" class="my-4" contenteditable="false">' +
                    '<a class="ogp-card" href="' + escapeHtmlForOgp(canonicalUrl) + '" target="_blank" rel="noopener noreferrer">' +
                        imageHtml +
                        '<div class="ogp-card-content">' +
                            (statusText ? '<div class="ogp-card-site ' + statusTone + '">' + escapeHtmlForOgp(statusText) + '</div>' : '') +
                            '<div class="ogp-card-site">' + escapeHtmlForOgp(siteName) + '</div>' +
                            '<div class="ogp-card-title">' + escapeHtmlForOgp(title) + '</div>' +
                            subtitleHtml +
                            '<div class="ogp-card-domain">' + escapeHtmlForOgp(domain) + '</div>' +
                        '</div>' +
                    '</a>' +
                '</div>'
            );
        }

        function buildOgpLoadingHtml(url, cardId) {
            const domain = getDomainLabel(url);

            return (
                '<div id="' + escapeHtmlForOgp(cardId) + '" class="my-4" contenteditable="false">' +
                    '<div class="ogp-card ogp-card-loading">' +
                        '<div class="ogp-card-media ogp-card-media-placeholder"></div>' +
                        '<div class="ogp-card-content">' +
                            '<div class="ogp-card-site">' + escapeHtmlForOgp(domain) + '</div>' +
                            '<div class="ogp-skeleton ogp-skeleton-title"></div>' +
                            '<div class="ogp-skeleton ogp-skeleton-line"></div>' +
                            '<div class="ogp-skeleton ogp-skeleton-line short"></div>' +
                        '</div>' +
                    '</div>' +
                '</div>'
            );
        }

        function buildOgpErrorHtml(url, cardId) {
            const domain = getDomainLabel(url);

            return (
                '<div id="' + escapeHtmlForOgp(cardId) + '" class="my-4" contenteditable="false">' +
                    '<a class="ogp-card ogp-card-error" href="' + escapeHtmlForOgp(url) + '" target="_blank" rel="noopener noreferrer">' +
                        '<div class="ogp-card-content">' +
                            '<div class="ogp-card-site text-red-600">OGP取得に失敗</div>' +
                            '<div class="ogp-card-title">リンクカードを取得できませんでした</div>' +
                            '<div class="mt-1 text-sm text-gray-600">一時的な通信エラー、タイムアウト、サイト側の制限の可能性があります。</div>' +
                            '<div class="ogp-card-domain">' + escapeHtmlForOgp(domain) + '</div>' +
                        '</div>' +
                    '</a>' +
                '</div>'
            );
        }

        async function fetchOgpJson(url) {
            const normalized = normalizeUrlForCache(url);

            if (ogpClientCache.has(normalized)) {
                return ogpClientCache.get(normalized);
            }

            const promise = fetch(OGP_API_ENDPOINT + '?url=' + encodeURIComponent(normalized), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                },
            }).then(async (response) => {
                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    const message = (data && data.message) ? data.message : 'OGP取得に失敗しました。';
                    throw new Error(message);
                }

                return data;
            });

            ogpClientCache.set(normalized, promise);

            try {
                const data = await promise;
                ogpClientCache.set(normalized, Promise.resolve(data));
                return data;
            } catch (e) {
                ogpClientCache.delete(normalized);
                throw e;
            }
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

            let parsed;
            try {
                parsed = new URL(trimmed);
            } catch (e) {
                alert('正しいURLを入力してください。');
                return;
            }

            if (!['http:', 'https:'].includes(parsed.protocol)) {
                alert('http または https のURLのみ対応しています。');
                return;
            }

            const cardId = 'ogp_' + Date.now() + '_' + Math.random().toString(16).slice(2);
            insertHtmlIntoEditor(buildOgpLoadingHtml(trimmed, cardId));

            fetchOgpJson(trimmed)
                .then(function(data) {
                    const el = document.getElementById(cardId);
                    if (!el) return;
                    el.outerHTML = buildOgpCardHtml(Object.assign({}, data, { card_id: cardId }), trimmed, 'loaded');
                })
                .catch(function() {
                    const el = document.getElementById(cardId);
                    if (!el) return;
                    el.outerHTML = buildOgpErrorHtml(trimmed, cardId);
                });
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

        // -----------------------------
        // コードブロック挿入（貼り付けOK）
        // -----------------------------
        let codeInsertSavedRange = null;

        function escapeHtmlForCode(str) {
            return String(str || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function insertHtmlIntoSavedRange(html) {
            if (!bodyEditor) return;
            bodyEditor.focus();

            // saved range がない場合は通常の挿入
            if (!codeInsertSavedRange) {
                insertHtmlIntoEditor(html);
                return;
            }

            const range = codeInsertSavedRange;
            try {
                range.deleteContents();
                const fragment = range.createContextualFragment(html);
                const lastNode = fragment.lastChild;
                range.insertNode(fragment);
                if (lastNode) {
                    // `pre[contenteditable]` の中にカーソルが残り続けるケースがあるため、
                    // コードブロックの直後に通常段落を明示的に作り、そこへカーソルを移す
                    const selection = window.getSelection();
                    selection.removeAllRanges();

                    let targetNode = null;
                    try {
                        if (lastNode.parentNode) {
                            const p = document.createElement('p');
                            p.style.textAlign = 'left';
                            p.innerHTML = '<br>';
                            lastNode.parentNode.insertBefore(p, lastNode.nextSibling);
                            targetNode = p;
                        }
                    } catch (e) {}

                    const newRange = document.createRange();
                    if (targetNode) {
                        newRange.selectNodeContents(targetNode);
                        newRange.collapse(true);
                    } else {
                        // フォールバック：コード直後へ
                        range.setStartAfter(lastNode);
                        range.collapse(true);
                        selection.addRange(range);
                        syncBodyEditor();
                        return;
                    }

                    selection.addRange(newRange);
                }
            } catch (e) {
                // range が無効になっているケースはフォールバック
                insertHtmlIntoEditor(html);
            }

            syncBodyEditor();
        }

        function openCodeInsertModal() {
            codeInsertSavedRange = (getEditorSelection() ? getEditorSelection().cloneRange() : null);

            let modal = document.getElementById('codeInsertModal');
            if (!modal) {
                modal = document.createElement('div');
                modal.id = 'codeInsertModal';
                modal.className = 'fixed inset-0 z-[100000] bg-black/50 flex items-center justify-center p-4';
                modal.innerHTML = `
                    <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full overflow-hidden">
                        <div class="flex items-center justify-between p-4 border-b border-gray-200">
                            <div class="text-sm font-semibold text-gray-700">コードを入力</div>
                            <button type="button" id="codeInsertCancelBtn" class="p-2 text-gray-500 hover:text-gray-700 rounded-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <div class="p-4">
                            <textarea id="codeInsertTextarea" class="w-full h-64 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none" placeholder="ここにコードを貼り付けてください"></textarea>
                            <div class="flex justify-end gap-3 mt-4">
                                <button type="button" id="codeInsertCancelBtn2" class="px-6 py-3 rounded-xl border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition-colors">
                                    キャンセル
                                </button>
                                <button type="button" id="codeInsertConfirmBtn" class="px-6 py-3 rounded-xl bg-gradient-to-r from-orange-500 to-red-500 text-white font-semibold shadow-lg hover:shadow-xl transition-colors">
                                    挿入
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);
            }

            modal.style.display = 'flex';

            const textarea = document.getElementById('codeInsertTextarea');
            const cancelBtn = document.getElementById('codeInsertCancelBtn');
            const cancelBtn2 = document.getElementById('codeInsertCancelBtn2');
            const confirmBtn = document.getElementById('codeInsertConfirmBtn');

            if (textarea) textarea.focus();

            function close() {
                if (modal) modal.remove();
                codeInsertSavedRange = null;
            }

            const onCancel = function() { close(); };
            cancelBtn?.addEventListener('click', onCancel, { once: true });
            cancelBtn2?.addEventListener('click', onCancel, { once: true });

            modal.addEventListener('click', function(e) {
                if (e.target === modal) close();
            }, { once: true });

            confirmBtn?.addEventListener('click', function() {
                const value = textarea ? textarea.value : '';
                const escaped = escapeHtmlForCode(value);
                insertHtmlIntoSavedRange('<pre class="editor-code-block" contenteditable="true"><code>' + escaped + '</code></pre>');
                close();
            }, { once: true });
        }

        // -----------------------------
        // リストから通常の段落へ戻す
        // -----------------------------
        function exitListToParagraph() {
            if (!bodyEditor) return;
            bodyEditor.focus();

            const selection = window.getSelection();
            if (!selection || selection.rangeCount === 0) return;

            const anchorNode = selection.anchorNode;
            const li = anchorNode && anchorNode.closest
                ? anchorNode.closest('li')
                : (anchorNode?.parentElement ? anchorNode.parentElement.closest('li') : null);

            // リスト内なら階層を抜けて通常の段落に寄せる
            if (li) {
                document.execCommand('insertParagraph', false, null);
                for (let i = 0; i < 4; i++) {
                    try { document.execCommand('outdent', false, null); } catch (e) {}
                }
            }

            // 常に P + removeFormat に寄せる（通常文字入力状態）
            document.execCommand('formatBlock', false, 'P');
            document.execCommand('removeFormat', false, null);
            syncBodyEditor();
        }

        if (insertMenu) {
            insertMenu.addEventListener('click', function(e) {
                const item = e.target.closest('.menu-item');
                if (!item) return;
                const action = item.getAttribute('data-action');
                if (!action) return;
                closeInsertMenuOuter();

                if (action === 'code') {
                    openCodeInsertModal();
                    return;
                }

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
                    if (tocHtml) {
                        insertHtmlIntoEditor(tocHtml);
                        // 目次を挿入したので削除ボタンも同期
                        syncTocDeleteBtn();
                    }
                    return;
                }

                if (action === 'paragraph') {
                    // リスト内から「通常の文字」に戻す
                    exitListToParagraph();
                    return;
                }

                if (action === 'heading-large') return formatBlock('H2');
                if (action === 'heading-small') return formatBlock('H3');
                if (action === 'list-bullet') return toggleList('bullet');
                if (action === 'list-ordered') return toggleList('ordered');
                if (action === 'blockquote') return insertBlockquote();
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

            // 初期HTML（old()）
            const initialHtml = (bodyInput.value || '').trim();
            if (initialHtml) {
                quill.clipboard.dangerouslyPasteHTML(initialHtml);
            }

            // 保存用inputに同期
            const syncToTextarea = () => {
                bodyInput.value = quill.root.innerHTML;
            };
            quill.on('text-change', syncToTextarea);
            syncToTextarea();

            // メニュー（表示制御は外側で行う。ここでは挿入処理用）
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

            function getCursorIndex() {
                const range = quill.getSelection(true);
                if (!range) return quill.getLength();
                return range.index;
            }

            function insertHtmlAtCursor(html) {
                const index = getCursorIndex();
                quill.clipboard.dangerouslyPasteHTML(index, html, 'user');
                // dangerousPasteHTML後はカーソル位置が不安定なので、内容再描画後に最小限同期
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
                    // Quill側のToC生成でも同じ安全版を使う
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

        // Quill の読み込み（CDN）→ init
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
    });

    // スキル出品画面と同等のUIで、アイキャッチ画像をクライアントプレビュー表示
    let imageData = null;
    let currentEyecatchUrl = null;

    function handleImageUpload(event) {
        const file = event.target.files && event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            imageData = e.target.result;
            currentEyecatchUrl = imageData;

            const previewImg = document.getElementById('previewImg');
            const imagePreview = document.getElementById('imagePreview');
            const uploadLabel = document.getElementById('uploadLabel');

            if (previewImg) previewImg.src = imageData;
            if (imagePreview) imagePreview.style.display = 'block';
            if (uploadLabel) uploadLabel.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }

    function removeImage() {
        imageData = null;
        currentEyecatchUrl = null;

        const imagePreview = document.getElementById('imagePreview');
        const uploadLabel = document.getElementById('uploadLabel');
        if (imagePreview) imagePreview.style.display = 'none';
        if (uploadLabel) uploadLabel.style.display = 'flex';

        const input = document.getElementById('imageInput');
        if (input) input.value = '';
    }

    function escapeAttr(s) {
        if (!s) return '';
        return String(s).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function escapeHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function buildPreviewHtml(imageUrl) {
        var title = document.getElementById('title').value;
        var category = document.getElementById('category').value;
        var bodyHtml = document.getElementById('body_html').value;

        var html = '';
        if (imageUrl) html += '<div class="mb-6"><img src="' + escapeAttr(imageUrl) + '" alt="" class="w-full h-48 object-cover rounded-xl"></div>';

        html += '<h1 class="text-2xl font-bold text-gray-900 mb-4">' + escapeHtml(title || '（タイトル未入力）') + '</h1>';
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
</script>
@endpush
