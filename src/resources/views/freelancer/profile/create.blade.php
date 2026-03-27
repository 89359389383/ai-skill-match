<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール作成 - AITECH</title>
    <link rel="icon" href="{{ asset('aifavicon.png') }}">
    <style>

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { font-size: 97.5%; }
        body {
            font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #fafbfc;
            color: #24292e;
            line-height: 1.5;
        }

        /* Tailwind utility equivalents (for partials.error-panel) */
        .mb-4 { margin-bottom: 1rem; }
        .mt-2 { margin-top: 0.5rem; }
        .rounded-lg { border-radius: 0.5rem; }
        .border { border: 1px solid transparent; }
        .border-red-200 { border-color: #fecaca; }
        .bg-red-50 { background-color: #fef2f2; }
        .px-4 { padding-left: 1rem; padding-right: 1rem; }
        .py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
        .text-sm { font-size: 0.875rem; line-height: 1.25rem; }
        .text-red-800 { color: #991b1b; }
        .font-extrabold { font-weight: 800; }
        .font-bold { font-weight: 700; }
        .list-disc { list-style-type: disc; }
        .pl-5 { padding-left: 1.25rem; }
        .space-y-1 > :not([hidden]) ~ :not([hidden]) { margin-top: 0.25rem; }


        /* Layout */
        .main-content {
            display: flex;
            max-width: 1600px;
            margin: 0 auto;
            padding: 3rem;
            /* public-header が fixed のため、ヘッダー分だけ本文を下げる */
            padding-top: calc(4rem + 1.5rem);
            gap: 3rem;
            position: relative;
        }
        @media (min-width: 768px) {
            .main-content { padding-top: calc(4rem + 2.5rem); }
        }
        .content-area { flex: 1; min-width: 0; }
        .sidebar {
            width: 360px;
            flex-shrink: 0;
            position: sticky;
            top: calc(4rem + 1.5rem);
            height: fit-content;
            align-self: flex-start;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
            color: #24292e;
            letter-spacing: -0.025em;
        }
        .page-subtitle {
            color: #6a737d;
            font-size: 1rem;
            margin-bottom: 2.25rem;
        }

        .panel {
            background-color: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
            border: 1px solid #e1e4e8;
            margin-bottom: 2rem;
        }
        .panel-title {
            font-size: 1.1rem;
            font-weight: 900;
            margin-bottom: 1.25rem;
            color: #24292e;
            letter-spacing: -0.01em;
        }

        .form { display: grid; gap: 1.25rem; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; align-items: start; }
        .row { display: grid; gap: 0.6rem; }
        .label {
            font-weight: 900;
            color: #586069;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .required {
            font-size: 0.75rem;
            font-weight: 900;
            color: white;
            background: #d73a49;
            border-radius: 999px;
            padding: 0.15rem 0.55rem;
            letter-spacing: 0.02em;
        }
        .input, .textarea, .select {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e1e4e8;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.15s ease;
            background-color: #fafbfc;
        }
        .textarea { min-height: 160px; resize: vertical; line-height: 1.6; }
        .input:focus, .textarea:focus, .select:focus {
            outline: none;
            border-color: #0366d6;
            box-shadow: 0 0 0 3px rgba(3, 102, 214, 0.1);
            background-color: white;
        }
        .help {
            color: #6a737d;
            font-size: 0.85rem;
            line-height: 1.5;
        }
        .error-message {
            color: #d73a49;
            font-size: 0.85rem;
            margin-top: 0.5rem;
            display: block;
        }
        .input.is-invalid, .textarea.is-invalid, .select.is-invalid {
            border-color: #d73a49;
        }

        .skills {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        .skill-tag {
            background-color: #f1f8ff;
            color: #0366d6;
            padding: 0.375rem 0.875rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 700;
            border: 1px solid #c8e1ff;
        }
        .tag-input {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }
        .tag-input .input { flex: 1; }

        .divider {
            height: 1px;
            background: #e1e4e8;
            margin: 0.5rem 0;
        }

        .actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            padding-top: 1rem;
            border-top: 1px solid #e1e4e8;
            flex-wrap: wrap;
        }
        .btn {
            padding: 15px 60px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
            cursor: pointer;
            border: none;
            font-size: 20px;
            letter-spacing: -0.01em;
            white-space: nowrap;
        }
        .btn-primary { background-color: #0366d6; color: white; }
        .btn-primary:hover { background-color: #0256cc; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(3, 102, 214, 0.3); }
        .btn-secondary { background-color: #586069; color: white; }
        .btn-secondary:hover { background-color: #4c5561; transform: translateY(-1px); }
        .btn-outline {
            background-color: transparent;
            color: #0366d6;
            border: 2px solid #0366d6;
            padding: 8px;
        }
        .btn-outline:hover {
            background-color: #f1f8ff;
            color: #0256cc;
            border-color: #0256cc;
        }
        .skills-container {
            display: grid;
            gap: 0.75rem;
        }
        .skill-input-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
        }
        .portfolio-container {
            display: grid;
            gap: 0.75rem;
        }
        .portfolio-input-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
        }

        /* Preview */
        .profile-card {
            position: relative;
            overflow: hidden;
        }
        .profile-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        }
        .profile-head {
            display: flex;
            gap: 1rem;
            align-items: center;
            margin-bottom: 1rem;
        }
        .big-avatar {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: grid;
            place-items: center;
            color: white;
            font-weight: 900;
            font-size: 1.1rem;
            overflow: hidden;
            object-fit: cover;
        }
        .big-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .name { font-weight: 900; font-size: 1.15rem; }
        .headline { color: #586069; font-weight: 800; font-size: 0.9rem; }
        .kv {
            display: grid;
            grid-template-columns: 120px 1fr;
            gap: 0.75rem 1rem;
            margin-top: 1rem;
        }
        .k { color: #6a737d; font-weight: 900; font-size: 0.85rem; }
        .v { color: #24292e; font-weight: 900; font-size: 0.9rem; }

        /* Responsive: breakpoints 1200 / 992 / 768 / 576 */
        @media (max-width: 1200px) {
            .main-content { padding: 2rem; gap: 2rem; }
            .sidebar { width: 300px; }
            .page-title { font-size: 1.75rem; }
        }
        @media (max-width: 992px) {
            .main-content { padding: 1.5rem; gap: 1.5rem; }
            .sidebar { width: 280px; top: 2rem; }
            .grid-2 { grid-template-columns: 1fr; }
            .panel { padding: 1.5rem; }
        }
        @media (max-width: 768px) {
            .main-content { flex-direction: column; padding: 1.25rem; }
            .sidebar { width: 100%; order: -1; position: static; top: auto; }
            .grid-2 { grid-template-columns: 1fr; }
            .kv { grid-template-columns: 1fr; }
            .actions { flex-direction: column; }
            .btn { width: 100%; font-size: 18px; padding: 12px 20px; }
        }
        @media (max-width: 576px) {
            html { font-size: 95%; }
            .main-content { padding: 1rem; gap: 1rem; }
            .panel { padding: 1rem; border-radius: 12px; }
            .big-avatar { width: 48px; height: 48px; font-size: 1rem; }
            .btn { padding: 10px 14px; font-size: 16px; }
        }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    @include('partials.public-header')
    <main class="main-content">
        <!-- Sidebar preview -->
        <aside class="sidebar">
            <div class="panel profile-card">
                <div class="panel-title">プレビュー</div>
                <div class="profile-head">
                    <div class="big-avatar" id="preview-avatar">{{ mb_substr($user->email ?? 'U', 0, 1) }}</div>
                    <div style="min-width:0;">
                        <div class="name" id="preview-name">未入力</div>
                        <div class="headline" id="preview-headline">未入力</div>
                    </div>
                </div>
                <div class="skills" id="preview-skills" aria-label="スキル">
                </div>
                <div class="divider"></div>
                <div class="kv" aria-label="条件">
                    <div class="k">希望単価</div>
                    <div class="v" id="preview-rate">未設定</div>
                    <div class="k">稼働</div>
                    <div class="v" id="preview-hours">未設定</div>
                    <div class="k">日</div>
                    <div class="v" id="preview-days">未設定</div>
                </div>
                <p class="help" style="margin-top:1rem;">プロフィールが充実しているほどスカウトが届きやすくなります。</p>
            </div>
        </aside>

        <!-- Form -->
        <div class="content-area">
            <h1 class="page-title">プロフィール作成</h1>
            @include('partials.error-panel')

            <div class="panel">
                <div class="panel-title">基本情報</div>
                <form class="form" action="{{ route('freelancer.profile.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="grid-2">
                        <div class="row">
                            <label class="label" for="display_name">表示名 <span class="required">必須</span></label>
                            <input class="input @error('display_name') is-invalid @enderror" id="display_name" name="display_name" type="text" maxlength="255" value="{{ old('display_name') }}" placeholder="例: 山田 太郎">
                            @error('display_name')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row">
                            <label class="label" for="job_title">職種（自由入力） <span class="required">必須</span></label>
                            <input class="input @error('job_title') is-invalid @enderror" id="job_title" name="job_title" type="text" maxlength="255" value="{{ old('job_title') }}" placeholder="例: AI自動化エンジニア">
                            @error('job_title')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <label class="label" for="bio">自己紹介文 <span class="required">必須</span></label>
                        <textarea class="textarea @error('bio') is-invalid @enderror" id="bio" name="bio" maxlength="5000">{{ old('bio') }}</textarea>
                        @error('bio')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                        <div class="help">成果（数値/期間/担当範囲）を入れると伝わりやすいです。</div>
                    </div>

                    <div class="row" style="margin-top:1.25rem;">
                        <label class="label" for="work_style_text">担当業務・得意業務（自由入力テキスト）</label>
                        <textarea class="textarea @error('work_style_text') is-invalid @enderror" id="work_style_text" name="work_style_text" placeholder="例) 担当：要件整理〜設計・実装まで\n得意：Web/DB設計など">{{ old('work_style_text') }}</textarea>
                        @error('work_style_text')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    @php
                        // 対応業務 / 得意業種 / 資格: 複数入力（min=4, max=16 / 各カテゴリ）
                        $servicesOfferedRaw = old('services_offered', '');
                        $industrySpecialtiesRaw = old('industry_specialties', '');
                        $certificationsRaw = old('certifications', '');

                        $splitByCommaLike = function ($raw) {
                            $raw = (string) $raw;
                            if ($raw === '') return [];
                            // '、' と ',' の両方を区切りとして扱う
                            $parts = preg_split('/[、,]/u', $raw, -1, PREG_SPLIT_NO_EMPTY);
                            return array_values(array_filter(array_map('trim', $parts), fn($s) => $s !== ''));
                        };

                        $splitByNewline = function ($raw) {
                            $raw = (string) $raw;
                            if ($raw === '') return [];
                            $parts = preg_split("/\r\n|\n|\r/u", $raw, -1, PREG_SPLIT_NO_EMPTY);
                            return array_values(array_filter(array_map('trim', $parts), fn($s) => $s !== ''));
                        };

                        $servicesOfferedValues = $splitByCommaLike($servicesOfferedRaw);
                        $industrySpecialtiesValues = $splitByCommaLike($industrySpecialtiesRaw);
                        $certificationsValues = $splitByNewline($certificationsRaw);

                        $maxCount = max(4, count($servicesOfferedValues), count($industrySpecialtiesValues), count($certificationsValues));
                        $styleRows = min(4, max(1, (int) ceil($maxCount / 4)));

                        $servicesOfferedValues = array_pad(array_slice($servicesOfferedValues, 0, 16), $styleRows * 4, '');
                        $industrySpecialtiesValues = array_pad(array_slice($industrySpecialtiesValues, 0, 16), $styleRows * 4, '');
                        $certificationsValues = array_pad(array_slice($certificationsValues, 0, 16), $styleRows * 4, '');
                    @endphp

                    <div class="row">
                        <label class="label" for="services_offered_hidden">対応業務（任意）</label>
                        <input type="hidden" id="services_offered_hidden" name="services_offered" value="{{ old('services_offered', '') }}">
                        <div id="services_offered_items_container">
                            @for($row = 0; $row < $styleRows; $row++)
                                <div class="style-input-row grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    @for($col = 0; $col < 4; $col++)
                                        @php $idx = $row * 4 + $col; @endphp
                                        <input
                                            class="input services-offered-item"
                                            name="services_offered_items[]"
                                            type="text"
                                            value="{{ $servicesOfferedValues[$idx] ?? '' }}"
                                            placeholder="{{ $idx === 0 ? '例: Webアプリ開発' : '' }}"
                                        >
                                    @endfor
                                </div>
                            @endfor
                        </div>
                        @error('services_offered')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        
                        <div style="display:flex; gap:0.75rem; flex-wrap:wrap; margin-top:0.75rem;">
                            <button type="button" class="btn btn-outline" id="add-services-row">追加する</button>
                            <button type="button" class="btn btn-outline" id="remove-services-row" aria-label="対応業務の入力行を減らす">×</button>
                        </div>
                    </div>

                    <div class="row">
                        <label class="label" for="industry_specialties_hidden">得意業界（任意）</label>
                        <input type="hidden" id="industry_specialties_hidden" name="industry_specialties" value="{{ old('industry_specialties', '') }}">
                        <div id="industry_specialties_items_container">
                            @for($row = 0; $row < $styleRows; $row++)
                                <div class="style-input-row grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    @for($col = 0; $col < 4; $col++)
                                        @php $idx = $row * 4 + $col; @endphp
                                        <input
                                            class="input industry-specialties-item"
                                            name="industry_specialties_items[]"
                                            type="text"
                                            value="{{ $industrySpecialtiesValues[$idx] ?? '' }}"
                                            placeholder="{{ $idx === 0 ? '例: IT・Web' : '' }}"
                                        >
                                    @endfor
                                </div>
                            @endfor
                        </div>
                        @error('industry_specialties')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        
                        <div style="display:flex; gap:0.75rem; flex-wrap:wrap; margin-top:0.75rem;">
                            <button type="button" class="btn btn-outline" id="add-industry-row">追加する</button>
                            <button type="button" class="btn btn-outline" id="remove-industry-row" aria-label="得意業界の入力行を減らす">×</button>
                        </div>
                    </div>

                    <div class="row">
                        <label class="label" for="prefecture">在住都道府県（任意）</label>
                        <input class="input @error('prefecture') is-invalid @enderror" id="prefecture" name="prefecture" type="text" value="{{ old('prefecture') }}" placeholder="例: 東京都">
                        @error('prefecture')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- 単一フォーム（担当業務・得意業務）に統合するため、experience_companies 用入力欄は削除 --}}

                    <div class="row">
                        <label class="label" for="certifications_hidden">資格（任意）</label>
                        <input type="hidden" id="certifications_hidden" name="certifications" value="{{ old('certifications', '') }}">
                        <div id="certifications_items_container">
                            @for($row = 0; $row < $styleRows; $row++)
                                <div class="style-input-row grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    @for($col = 0; $col < 4; $col++)
                                        @php $idx = $row * 4 + $col; @endphp
                                        <input
                                            class="input certifications-item"
                                            name="certifications_items[]"
                                            type="text"
                                            value="{{ $certificationsValues[$idx] ?? '' }}"
                                            placeholder="{{ $idx === 0 ? '例: 基本情報技術者' : '' }}"
                                        >
                                    @endfor
                                </div>
                            @endfor
                        </div>
                        @error('certifications')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        
                        <div style="display:flex; gap:0.75rem; flex-wrap:wrap; margin-top:0.75rem;">
                            <button type="button" class="btn btn-outline" id="add-certifications-row">追加する</button>
                            <button type="button" class="btn btn-outline" id="remove-certifications-row" aria-label="資格の入力行を減らす">×</button>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <div class="panel-title" style="margin-bottom:1rem;">連絡先（任意）</div>
                    <div class="grid-2">
                        <div class="row">
                            <label class="label" for="phone">電話番号</label>
                            <input class="input @error('phone') is-invalid @enderror" id="phone" name="phone" type="tel" value="{{ old('phone') }}" placeholder="例: 090-1234-5678">
                            @error('phone')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row">
                            <label class="label" for="line_id">LINE（友達追加URL）</label>
                            <input class="input @error('line_id') is-invalid @enderror" id="line_id" name="line_id" type="text" value="{{ old('line_id') }}" placeholder="LINE友達追加URL（例: https://line.me/ti/p/~xxxx または https://lin.ee/xxxx）">
                            @error('line_id')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <label class="label" for="twitter_url">Twitter（プロフィールURL）</label>
                        <input class="input @error('twitter_url') is-invalid @enderror" id="twitter_url" name="twitter_url" type="url" value="{{ old('twitter_url') }}" placeholder="TwitterプロフィールURL（例: https://twitter.com/username または https://x.com/username）">
                        @error('twitter_url')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="divider"></div>

                    <div class="panel-title" style="margin-bottom:1rem;">スキル / 実績</div>
                    <div class="row">
                        <label class="label">スキル（1つ以上必須）</label>
                        <div class="help">複数入力できます。</div>
                        @php
                            // custom_skills 未入力時に入力欄を赤枠にする
                            // （min/required_without のエラーは custom_skills に乗る想定）
                            $skillsInvalid = $errors->has('custom_skills');
                        @endphp
                        <div class="skills-container" id="skills-container">
                            <div class="skill-input-row">
                                <input class="input skill-input {{ $skillsInvalid ? 'is-invalid' : '' }}" name="custom_skills[]" type="text" value="{{ old('custom_skills.0') }}" placeholder="例: Laravel">
                                <input class="input skill-input" name="custom_skills[]" type="text" value="{{ old('custom_skills.1') }}" placeholder="例: Vue.js">
                            </div>
                            <div class="skill-input-row">
                                <input class="input skill-input" name="custom_skills[]" type="text" value="{{ old('custom_skills.2') }}" placeholder="例: MySQL">
                                <input class="input skill-input" name="custom_skills[]" type="text" value="{{ old('custom_skills.3') }}" placeholder="例: Docker">
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline" id="add-skill-btn" style="margin-top:0.75rem;">追加する</button>
                        @error('custom_skills')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                        @error('custom_skills.*')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row">
                        <label class="label">ポートフォリオURL（任意・複数）</label>
                        <div class="help">複数入力できます。</div>
                        <div class="portfolio-container" id="portfolio-container">
                            <div class="portfolio-input-row">
                                <input class="input portfolio-input" name="portfolio_urls[]" type="url" value="{{ old('portfolio_urls.0') }}" placeholder="例: https://example.com/portfolio">
                                <input class="input portfolio-input" name="portfolio_urls[]" type="url" value="{{ old('portfolio_urls.1') }}" placeholder="例: https://github.com/yourname">
                            </div>
                            <div class="portfolio-input-row">
                                <input class="input portfolio-input" name="portfolio_urls[]" type="url" value="{{ old('portfolio_urls.2') }}" placeholder="例: https://...">
                                <input class="input portfolio-input" name="portfolio_urls[]" type="url" value="{{ old('portfolio_urls.3') }}" placeholder="例: https://...">
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline" id="add-portfolio-btn" style="margin-top:0.75rem;">追加する</button>
                        @error('portfolio_urls.*')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="divider"></div>

                    <div class="panel-title" style="margin-bottom:1rem;">稼働条件</div>
                    <div class="row">
                        <div class="grid-2">
                            <div class="row" style="align-self: start;">
                                <label class="label" for="min_rate">希望単価（下限〜上限）</label>
                                <div class="help">未入力の場合は「未設定」として登録されます（あとから変更できます）。</div>
                                <div class="grid-2" style="align-items: stretch;">
                                    <input class="input @error('min_rate') is-invalid @enderror" id="min_rate" name="min_rate" type="number" value="{{ old('min_rate') }}" placeholder="下限（万円）" min="0" max="100000000" step="1">
                                    <input class="input @error('max_rate') is-invalid @enderror" name="max_rate" type="number" value="{{ old('max_rate') }}" placeholder="上限（万円）" min="0" max="100000000" step="1">
                                </div>
                                @error('min_rate')
                                <span class="error-message">{{ $message }}</span>
                                @enderror
                                @error('max_rate')
                                <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="row" style="align-self: start;">
                                <label class="label">稼働可能時間 <span class="required">必須</span></label>
                                <div class="help" style="margin-bottom:0.75rem;">1週間あたりの稼働可能時間の範囲と、1日あたりの稼働時間・稼働日数を入力してください。</div>
                                <div style="margin-bottom:0.75rem;">
                                    <div style="font-weight: 700; color: #586069; font-size: 0.85rem; margin-bottom:0.5rem;">週間稼働時間</div>
                                    <div class="grid-2" style="align-items: stretch;">
                                        <div class="row" style="gap:0.4rem;">
                                            <input class="input @error('min_hours_per_week') is-invalid @enderror" name="min_hours_per_week" type="number" value="{{ old('min_hours_per_week') }}" placeholder="例: 20" min="0" max="168" step="1">
                                            <div class="help" style="margin:0; font-size:0.8rem;">週間の最小稼働時間（時間）</div>
                                            @error('min_hours_per_week')
                                            <span class="error-message">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="row" style="gap:0.4rem;">
                                            <input class="input @error('max_hours_per_week') is-invalid @enderror" name="max_hours_per_week" type="number" value="{{ old('max_hours_per_week') }}" placeholder="例: 40" min="0" max="168" step="1">
                                            <div class="help" style="margin:0; font-size:0.8rem;">週間の最大稼働時間（時間）</div>
                                            @error('max_hours_per_week')
                                            <span class="error-message">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div style="font-weight: 700; color: #586069; font-size: 0.85rem; margin-bottom:0.5rem;">1日あたりの稼働時間・稼働日数</div>
                                    <div class="grid-2" style="align-items: stretch;">
                                        <div class="row" style="gap:0.4rem;">
                                            <input class="input @error('hours_per_day') is-invalid @enderror" name="hours_per_day" type="number" value="{{ old('hours_per_day') }}" placeholder="例: 8" min="0" max="24" step="1">
                                            <div class="help" style="margin:0; font-size:0.8rem;">1日あたりの稼働時間（時間）</div>
                                            @error('hours_per_day')
                                            <span class="error-message">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="row" style="gap:0.4rem;">
                                            <input class="input @error('days_per_week') is-invalid @enderror" name="days_per_week" type="number" value="{{ old('days_per_week') }}" placeholder="例: 5" min="0" max="7" step="1">
                                            <div class="help" style="margin:0; font-size:0.8rem;">1週間あたりの稼働日数（日）</div>
                                            @error('days_per_week')
                                            <span class="error-message">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row" style="margin-top:1.25rem;">
                        <label class="label" for="icon">ユーザーアイコン（任意）</label>
                        <input class="input @error('icon') is-invalid @enderror" id="icon" name="icon" type="file" accept="image/*">
                        @error('icon')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                        <div class="help">画像を選択してください（最大5MB）。未設定でも登録できます。</div>
                    </div>

                    <div class="actions">
                        <a class="btn btn-secondary" href="{{ route('freelancer.jobs.index') }}" role="button">キャンセル</a>
                        <button class="btn btn-primary" type="submit">登録</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>

        // アイコンファイルプレビュー機能（サイドバー＋共通ヘッダー右のアバター）
        (function () {
            const iconInput = document.getElementById('icon');
            const previewAvatar = document.getElementById('preview-avatar');
            const defaultAvatarText = @json(mb_substr($user->email ?? 'U', 0, 1));
            const headerToggle = document.getElementById('publicFreelancerUserDropdownToggle');
            const headerDefaultInitial = @json($userInitial ?? mb_substr($user->email ?? 'U', 0, 1));

            function setHeaderAvatarFromDataUrl(dataUrl) {
                if (!headerToggle) return;
                const img = document.createElement('img');
                img.src = dataUrl;
                img.alt = 'プロフィール画像';
                img.setAttribute('style', 'width: 100%; height: 100%; object-fit: cover; border-radius: 50%;');
                headerToggle.replaceChildren(img);
            }

            function resetHeaderAvatarToInitial() {
                if (!headerToggle) return;
                headerToggle.textContent = headerDefaultInitial;
            }

            if (iconInput && previewAvatar) {
                iconInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file && file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            // 既存の画像を削除
                            const existingImg = previewAvatar.querySelector('img');
                            if (existingImg) {
                                existingImg.remove();
                            }
                            // テキストを削除
                            previewAvatar.textContent = '';
                            // 新しい画像を追加
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.alt = 'プレビュー画像';
                            previewAvatar.appendChild(img);
                            setHeaderAvatarFromDataUrl(e.target.result);
                        };
                        reader.readAsDataURL(file);
                    } else {
                        // ファイルが選択されていない場合、デフォルトに戻す
                        const existingImg = previewAvatar.querySelector('img');
                        if (existingImg) {
                            existingImg.remove();
                        }
                        previewAvatar.textContent = defaultAvatarText;
                        resetHeaderAvatarToInitial();
                    }
                });
            }
        })();

        // スキル入力欄追加機能
        (function () {
            const addSkillBtn = document.getElementById('add-skill-btn');
            const skillsContainer = document.getElementById('skills-container');
            let skillCount = 4; // 初期の4つの入力欄

            if (addSkillBtn && skillsContainer) {
                addSkillBtn.addEventListener('click', function() {
                    // 最後の行を取得
                    const lastRow = skillsContainer.lastElementChild;
                    const inputsInLastRow = lastRow.querySelectorAll('.skill-input');
                    
                    // 最後の行に空きがある場合はそこに追加、なければ新しい行を作成
                    if (inputsInLastRow.length < 2) {
                        // 最後の行に追加
                        const newInput = document.createElement('input');
                        newInput.className = 'input skill-input';
                        newInput.name = 'custom_skills[]';
                        newInput.type = 'text';
                        newInput.placeholder = '例: スキル名';
                        lastRow.appendChild(newInput);
                    } else {
                        // 新しい行を作成
                        const newRow = document.createElement('div');
                        newRow.className = 'skill-input-row';
                        const newInput = document.createElement('input');
                        newInput.className = 'input skill-input';
                        newInput.name = 'custom_skills[]';
                        newInput.type = 'text';
                        newInput.placeholder = '例: スキル名';
                        newRow.appendChild(newInput);
                        skillsContainer.appendChild(newRow);
                    }
                    
                    skillCount++;
                    
                    // 新しく追加した入力欄にもイベントリスナーを追加
                    const newInputs = skillsContainer.querySelectorAll('.skill-input');
                    newInputs.forEach(input => {
                        // 既にリスナーが追加されていない場合のみ追加
                        if (!input.hasAttribute('data-listener-added')) {
                            input.addEventListener('input', updatePreview);
                            input.addEventListener('change', updatePreview);
                            input.setAttribute('data-listener-added', 'true');
                        }
                    });
                });
            }
        })();

        // ポートフォリオURL入力欄追加機能
        (function () {
            const addPortfolioBtn = document.getElementById('add-portfolio-btn');
            const portfolioContainer = document.getElementById('portfolio-container');
            let portfolioCount = 4; // 初期の4つの入力欄

            if (addPortfolioBtn && portfolioContainer) {
                addPortfolioBtn.addEventListener('click', function() {
                    // 最後の行を取得
                    const lastRow = portfolioContainer.lastElementChild;
                    const inputsInLastRow = lastRow.querySelectorAll('.portfolio-input');

                    // 最後の行に空きがある場合はそこに追加、なければ新しい行を作成
                    if (inputsInLastRow.length < 2) {
                        // 最後の行に追加
                        const newInput = document.createElement('input');
                        newInput.className = 'input portfolio-input';
                        newInput.name = 'portfolio_urls[]';
                        newInput.type = 'url';
                        newInput.placeholder = '例: https://...';
                        lastRow.appendChild(newInput);
                    } else {
                        // 新しい行を作成
                        const newRow = document.createElement('div');
                        newRow.className = 'portfolio-input-row';
                        const newInput = document.createElement('input');
                        newInput.className = 'input portfolio-input';
                        newInput.name = 'portfolio_urls[]';
                        newInput.type = 'url';
                        newInput.placeholder = '例: https://...';
                        newRow.appendChild(newInput);
                        portfolioContainer.appendChild(newRow);
                    }

                    portfolioCount++;
                });
            }
        })();

        // 対応業務 / 得意業種 / 資格: 複数入力（min=4, max=16 / 各項目独立）
        (function () {
            const servicesContainer = document.getElementById('services_offered_items_container');
            const industryContainer = document.getElementById('industry_specialties_items_container');
            const certificationsContainer = document.getElementById('certifications_items_container');

            const hiddenServices = document.getElementById('services_offered_hidden');
            const hiddenIndustry = document.getElementById('industry_specialties_hidden');
            const hiddenCertifications = document.getElementById('certifications_hidden');

            const addServicesBtn = document.getElementById('add-services-row');
            const removeServicesBtn = document.getElementById('remove-services-row');
            const addIndustryBtn = document.getElementById('add-industry-row');
            const removeIndustryBtn = document.getElementById('remove-industry-row');
            const addCertBtn = document.getElementById('add-certifications-row');
            const removeCertBtn = document.getElementById('remove-certifications-row');

            const MAX_ROWS = 4;
            const MIN_ROWS = 1;

            if (!servicesContainer || !industryContainer || !certificationsContainer || !hiddenServices || !hiddenIndustry || !hiddenCertifications) {
                return;
            }

            function buildRow({ inputClass, inputName, placeholder }) {
                const row = document.createElement('div');
                row.className = 'style-input-row grid grid-cols-2 sm:grid-cols-4 gap-3';
                for (let col = 0; col < 4; col++) {
                    const input = document.createElement('input');
                    input.className = 'input ' + inputClass;
                    input.name = inputName;
                    input.type = 'text';
                    input.placeholder = placeholder;
                    row.appendChild(input);
                }
                return row;
            }

            function syncHiddenFields() {
                const services = Array.from(servicesContainer.querySelectorAll('.services-offered-item'))
                    .map(el => (el.value || '').trim())
                    .filter(Boolean);
                const industries = Array.from(industryContainer.querySelectorAll('.industry-specialties-item'))
                    .map(el => (el.value || '').trim())
                    .filter(Boolean);
                const certifications = Array.from(certificationsContainer.querySelectorAll('.certifications-item'))
                    .map(el => (el.value || '').trim())
                    .filter(Boolean);

                hiddenServices.value = services.join(',');
                hiddenIndustry.value = industries.join(',');
                hiddenCertifications.value = certifications.join('\n');
            }

            function setRowButtonState(container, addBtn, removeBtn) {
                const rowCount = container.querySelectorAll('.style-input-row').length;
                if (addBtn) addBtn.disabled = rowCount >= MAX_ROWS;
                if (removeBtn) removeBtn.disabled = rowCount <= MIN_ROWS;
            }

            function bindControls({ container, addBtn, removeBtn, rowTemplate }) {
                if (!container) return;

                if (addBtn) {
                    addBtn.addEventListener('click', function () {
                        const rowCount = container.querySelectorAll('.style-input-row').length;
                        if (rowCount >= MAX_ROWS) return;
                        container.appendChild(buildRow(rowTemplate));
                        setRowButtonState(container, addBtn, removeBtn);
                        syncHiddenFields();
                    });
                }

                if (removeBtn) {
                    removeBtn.addEventListener('click', function () {
                        const rows = container.querySelectorAll('.style-input-row');
                        rows.forEach((row, idx) => {
                            if (idx >= MIN_ROWS) row.remove();
                        });
                        setRowButtonState(container, addBtn, removeBtn);
                        syncHiddenFields();
                    });
                }

                container.addEventListener('input', syncHiddenFields);
            }

            bindControls({
                container: servicesContainer,
                addBtn: addServicesBtn,
                removeBtn: removeServicesBtn,
                rowTemplate: { inputClass: 'services-offered-item', inputName: 'services_offered_items[]', placeholder: '例: Webアプリ開発' }
            });
            bindControls({
                container: industryContainer,
                addBtn: addIndustryBtn,
                removeBtn: removeIndustryBtn,
                rowTemplate: { inputClass: 'industry-specialties-item', inputName: 'industry_specialties_items[]', placeholder: '例: IT・Web' }
            });
            bindControls({
                container: certificationsContainer,
                addBtn: addCertBtn,
                removeBtn: removeCertBtn,
                rowTemplate: { inputClass: 'certifications-item', inputName: 'certifications_items[]', placeholder: '例: 基本情報技術者' }
            });

            setRowButtonState(servicesContainer, addServicesBtn, removeServicesBtn);
            setRowButtonState(industryContainer, addIndustryBtn, removeIndustryBtn);
            setRowButtonState(certificationsContainer, addCertBtn, removeCertBtn);
            syncHiddenFields();

            const form = document.querySelector('form[action="{{ route('freelancer.profile.store') }}"]');
            if (form) {
                form.addEventListener('submit', function () {
                    syncHiddenFields();
                });
            }
        })();

        // プレビュー更新機能
        const updatePreview = function () {
            // 表示名
            const displayName = document.getElementById('display_name').value.trim();
            const previewAvatar = document.getElementById('preview-avatar');
            if (displayName) {
                document.getElementById('preview-name').textContent = displayName;
                // 画像が表示されていない場合のみテキストを更新
                if (!previewAvatar.querySelector('img')) {
                    const firstChar = displayName.charAt(0);
                    previewAvatar.textContent = firstChar;
                }
            } else {
                document.getElementById('preview-name').textContent = '未入力';
                // 画像が表示されていない場合のみテキストを更新
                if (!previewAvatar.querySelector('img')) {
                    previewAvatar.textContent = '{{ mb_substr($user->email ?? "U", 0, 1) }}';
                }
            }

            // 職種
            const jobTitle = document.getElementById('job_title').value.trim();
            document.getElementById('preview-headline').textContent = jobTitle || '未入力';

            // スキル
            const skillInputs = document.querySelectorAll('input[name="custom_skills[]"]');
            const skillsContainer = document.getElementById('preview-skills');
            skillsContainer.innerHTML = '';
            skillInputs.forEach(input => {
                const value = input.value.trim();
                if (value) {
                    const tag = document.createElement('span');
                    tag.className = 'skill-tag';
                    tag.textContent = value;
                    skillsContainer.appendChild(tag);
                }
            });

            // 希望単価
            const minRate = document.getElementById('min_rate').value;
            const maxRate = document.querySelector('input[name="max_rate"]').value;
            if (minRate || maxRate) {
                const rateText = minRate && maxRate ? `${minRate}〜${maxRate}万円` : (minRate || maxRate ? `${minRate || maxRate}万円` : '未設定');
                document.getElementById('preview-rate').textContent = rateText;
            } else {
                document.getElementById('preview-rate').textContent = '未設定';
            }

            // 稼働時間
            const minHours = document.querySelector('input[name="min_hours_per_week"]').value;
            const maxHours = document.querySelector('input[name="max_hours_per_week"]').value;
            if (minHours || maxHours) {
                const hoursText = minHours && maxHours ? `週${minHours}〜${maxHours}h` : (minHours || maxHours ? `週${minHours || maxHours}h` : '未設定');
                document.getElementById('preview-hours').textContent = hoursText;
            } else {
                document.getElementById('preview-hours').textContent = '未設定';
            }

            // 日数
            const hoursPerDay = document.querySelector('input[name="hours_per_day"]').value;
            const daysPerWeek = document.querySelector('input[name="days_per_week"]').value;
            if (hoursPerDay || daysPerWeek) {
                const daysText = hoursPerDay && daysPerWeek ? `${hoursPerDay}h/day・${daysPerWeek}日/week` : (hoursPerDay || daysPerWeek || '未設定');
                document.getElementById('preview-days').textContent = daysText;
            } else {
                document.getElementById('preview-days').textContent = '未設定';
            }
        };

        // 数値入力を即座に min/max の範囲へクランプ（不正値が入った瞬間に補正）
        function clampNumericInput(el) {
            if (!el) return;
            if (el.type !== 'number') return;

            const minAttr = el.getAttribute('min');
            const maxAttr = el.getAttribute('max');
            const min = minAttr === null || minAttr === '' ? null : Number(minAttr);
            const max = maxAttr === null || maxAttr === '' ? null : Number(maxAttr);

            const raw = el.value;
            if (raw === '' || raw === null || raw === undefined) return;

            const val = Number(raw);
            if (Number.isNaN(val)) return;

            let next = val;
            if (min !== null && next < min) next = min;
            if (max !== null && next > max) next = max;

            if (next !== val) el.value = String(next);
        }

        // フォーム入力時にプレビューを更新
        (function () {
            const formInputs = document.querySelectorAll('input[type="text"], input[type="number"], textarea');
            formInputs.forEach(input => {
                input.addEventListener('input', function() {
                    clampNumericInput(input);
                    // テキスト上限も念のため即時クランプ（貼り付け等でズレるケース対策）
                    if (input.maxLength && Number(input.maxLength) > 0 && input.value && input.value.length > Number(input.maxLength)) {
                        input.value = input.value.slice(0, Number(input.maxLength));
                    }
                    updatePreview();
                });
                input.addEventListener('change', function() {
                    clampNumericInput(input);
                    if (input.maxLength && Number(input.maxLength) > 0 && input.value && input.value.length > Number(input.maxLength)) {
                        input.value = input.value.slice(0, Number(input.maxLength));
                    }
                    updatePreview();
                });
                input.setAttribute('data-listener-added', 'true');
            });

            // 初期プレビュー更新
            updatePreview();
        })();
    </script>
</body>
</html>