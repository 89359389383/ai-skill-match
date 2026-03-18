@extends('layouts.public')

@section('title', 'AIスキルマッチ - AI人材と企業をつなぐ')

@push('styles')
<style>
/* Reset and Base Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    line-height: 1.6;
    color: #1f2937;
    background-color: #ffffff;
}

.container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 1rem;
}

@media (min-width: 640px) {
    .container {
        padding: 0 1.5rem;
    }
}

@media (min-width: 1024px) {
    .container {
        padding: 0 2rem;
    }
}

/* Hero Section */
.hero-section {
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #003b7a 0%, #004d99 50%, #003366 100%);
    min-height: 500px;
}

@media (min-width: 1024px) {
    .hero-section {
        min-height: 600px;
    }
}

.hero-nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

.hero-logo {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.hero-logo-small {
    font-size: 0.75rem;
    color: white;
    font-weight: 500;
}

.hero-logo-main {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
}

.hero-logo-main .logo-ai {
    color: white;
}

.hero-logo-main .logo-skill-match {
    color: #ff6347;
}

.hero-logo-badge {
    width: 20px;
    height: 20px;
    background: linear-gradient(135deg, #ff1493, #ff69b4);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.hero-nav-menu {
    display: none;
    gap: 2rem;
    align-items: center;
}

@media (min-width: 768px) {
    .hero-nav-menu {
        display: flex;
    }
}

.hero-nav-link {
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    font-size: 0.95rem;
    font-weight: 500;
    transition: color 0.3s;
}

.hero-nav-link:hover {
    color: white;
}

.hero-nav-cta {
    padding: 0.75rem 1.5rem;
    background: #4169e1;
    color: white;
    border-radius: 0.5rem;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 600;
    transition: all 0.3s;
}

.hero-nav-cta:hover {
    background: #3151c1;
    transform: translateY(-2px);
}

.hero-main {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2rem;
    padding: 3rem 2rem;
    max-width: 1400px;
    margin: 0 auto;
    align-items: center;
}

@media (min-width: 1024px) {
    .hero-main {
        grid-template-columns: 1fr 1fr;
        padding: 2rem 2rem 4rem;
    }
}

.hero-content-left {
    z-index: 10;
}

.hero-main-title {
    font-size: 2rem;
    line-height: 1.4;
    color: white;
    font-weight: 700;
    margin-bottom: 1.5rem;
}

@media (min-width: 1024px) {
    .hero-main-title {
        font-size: 2.75rem;
    }
}

.hero-highlight-orange {
    background: linear-gradient(90deg, #ff4500, #ff6347);
    padding: 0.25rem 1rem;
    border-radius: 0.25rem;
    display: inline-block;
}

.hero-highlight-cyan {
    background: linear-gradient(90deg, #00bfff, #1e90ff);
    padding: 0.25rem 1rem;
    border-radius: 0.25rem;
    display: inline-block;
}

.hero-subtitle {
    color: rgba(255, 255, 255, 0.95);
    font-size: 1rem;
    line-height: 1.8;
    margin-bottom: 2rem;
}

.hero-features-boxes {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 2.5rem;
}

.hero-feature-box {
    background: white;
    padding: 1.25rem 0.75rem;
    border-radius: 0.75rem;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.hero-feature-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #4169e1;
    margin-bottom: 0.5rem;
}

.hero-feature-subtitle {
    font-size: 1rem;
    color: #333;
    font-weight: 500;
}

.hero-cta-button {
    display: inline-flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem 3rem;
    background: linear-gradient(90deg, #da70d6, #ba55d3);
    color: white;
    border-radius: 3rem;
    text-decoration: none;
    font-size: 1.3rem;
    font-weight: 600;
    box-shadow: 0 8px 16px rgba(218, 112, 214, 0.3);
    transition: all 0.3s;
}

.hero-cta-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 24px rgba(218, 112, 214, 0.4);
}

.hero-image-container {
    position: relative;
    display: none;
}

@media (min-width: 1024px) {
    .hero-image-container {
        display: block;
    }
}

.hero-image {
    width: 100%;
    height: auto;
    object-fit: cover;
    border-radius: 1rem;
}

/* Section Common Styles */
section {
    padding: 5rem 0;
}

.section-header {
    text-align: center;
    margin-bottom: 4rem;
}

.section-title {
    font-size: 1.875rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 1rem;
}

@media (min-width: 640px) {
    .section-title {
        font-size: 2.25rem;
    }
}

.section-subtitle {
    font-size: 1.25rem;
    color: #4b5563;
}

.section-header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.link-with-arrow {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #4f46e5;
    font-weight: 500;
    text-decoration: none;
    transition: color 0.3s;
}

.link-with-arrow:hover {
    color: #4338ca;
}

.arrow-icon {
    width: 1rem;
    height: 1rem;
}

/* Features Section */
.features-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}

@media (min-width: 768px) {
    .features-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1024px) {
    .features-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

.feature-card {
    padding: 1.5rem;
    background-color: white;
    border-radius: 1rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    transition: all 0.3s;
}

.feature-card:hover {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    transform: translateY(-0.5rem);
}

.feature-icon {
    width: 3.5rem;
    height: 3.5rem;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s;
}

.feature-card:hover .feature-icon {
    transform: scale(1.1);
}

.feature-icon.bg-blue {
    background: linear-gradient(to bottom right, #3b82f6, #06b6d4);
}

.feature-icon.bg-purple {
    background: linear-gradient(to bottom right, #a855f7, #ec4899);
}

.feature-icon.bg-orange {
    background: linear-gradient(to bottom right, #f97316, #ef4444);
}

.feature-icon.bg-green {
    background: linear-gradient(to bottom right, #22c55e, #10b981);
}

.icon {
    width: 1.75rem;
    height: 1.75rem;
    color: white;
}

.feature-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.feature-description {
    color: #4b5563;
}

/* Benefits Section */
.benefits-section {
    background: linear-gradient(to bottom right, #eef2ff, #faf5ff);
}

.benefits-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2rem;
}

@media (min-width: 768px) {
    .benefits-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

.benefit-card {
    padding: 2rem;
    background-color: white;
    border-radius: 1rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.benefit-icon {
    width: 4rem;
    height: 4rem;
    background: linear-gradient(to bottom right, #6366f1, #9333ea);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.benefit-icon .icon {
    width: 2rem;
    height: 2rem;
}

.benefit-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 0.75rem;
}

.benefit-description {
    color: #4b5563;
}

/* Cards Grid */
.cards-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}

@media (min-width: 768px) {
    .cards-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1024px) {
    .cards-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

/* Freelancer Card */
.freelancer-card {
    display: block;
    padding: 1.5rem;
    background-color: white;
    border-radius: 0.75rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    transition: all 0.3s;
    text-decoration: none;
    color: inherit;
}

.freelancer-card:hover {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    transform: translateY(-0.25rem);
}

.freelancer-header {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1rem;
}

.freelancer-avatar {
    width: 4rem;
    height: 4rem;
    border-radius: 50%;
    object-fit: cover;
}

.freelancer-info {
    flex: 1;
}

.freelancer-name {
    font-size: 1.125rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.freelancer-role {
    font-size: 0.875rem;
    color: #4b5563;
    margin-bottom: 0.5rem;
}

.freelancer-rate {
    font-size: 0.875rem;
    color: #4b5563;
}

.freelancer-rate span {
    font-weight: 700;
    color: #4f46e5;
}

.freelancer-bio {
    color: #4b5563;
    font-size: 0.875rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin-bottom: 1rem;
}

.skills-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.skill-tag {
    padding: 0.25rem 0.75rem;
    background-color: #e0e7ff;
    color: #4338ca;
    font-size: 0.75rem;
    font-weight: 500;
    border-radius: 9999px;
}

.skill-tag.more {
    background-color: #f3f4f6;
    color: #4b5563;
}

.freelancer-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.875rem;
    color: #4b5563;
}

.rating {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.star-icon {
    width: 1rem;
    height: 1rem;
    color: #fbbf24;
    fill: #fbbf24;
}

/* Question Card */
.question-card {
    display: block;
    padding: 1.5rem;
    background-color: white;
    border-radius: 0.75rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    transition: all 0.3s;
    text-decoration: none;
    color: inherit;
}

.question-card:hover {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    transform: translateY(-0.25rem);
}

.question-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
}

.category-badge {
    padding: 0.25rem 0.75rem;
    background-color: #e0e7ff;
    color: #4338ca;
    font-size: 0.75rem;
    font-weight: 500;
    border-radius: 9999px;
}

.answer-count {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.875rem;
    color: #6b7280;
}

.question-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 0.5rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.question-content {
    color: #4b5563;
    font-size: 0.875rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin-bottom: 1rem;
}

.question-author {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.author-avatar {
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    object-fit: cover;
}

.author-name {
    font-weight: 500;
    font-size: 0.875rem;
    color: #1f2937;
}

.question-date {
    font-size: 0.75rem;
    color: #6b7280;
}

/* Skill Card */
.skill-card {
    display: block;
    background-color: white;
    border-radius: 0.75rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s;
    text-decoration: none;
    color: inherit;
}

.skill-card:hover {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    transform: translateY(-0.25rem);
}

.skill-image {
    width: 100%;
    aspect-ratio: 16 / 9;
    object-fit: cover;
}

.skill-content {
    padding: 1.5rem;
}

.seller-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
}

.seller-avatar {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    object-fit: cover;
}

.seller-name {
    font-weight: 500;
    font-size: 0.875rem;
    color: #1f2937;
}

.seller-role {
    font-size: 0.75rem;
    color: #6b7280;
}

.skill-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 0.5rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.skill-description {
    color: #4b5563;
    font-size: 0.875rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin-bottom: 1rem;
}

.skill-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.skill-rating {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.skill-rating-value {
    font-weight: 700;
    color: #1f2937;
}

.skill-rating-count {
    font-size: 0.875rem;
    color: #6b7280;
}

.skill-price {
    font-size: 1.25rem;
    font-weight: 700;
    color: #4f46e5;
}

/* Article Card */
.article-card {
    display: block;
    background-color: white;
    border-radius: 0.75rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s;
    text-decoration: none;
    color: inherit;
}

.article-card:hover {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    transform: translateY(-0.25rem);
}

.article-image {
    width: 100%;
    aspect-ratio: 16 / 9;
    object-fit: cover;
}

.article-content {
    padding: 1.5rem;
}

.article-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 0.5rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.article-excerpt {
    color: #4b5563;
    font-size: 0.875rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin-bottom: 1rem;
}

.article-author {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

/* Section Backgrounds */
.freelancers-section {
    background-color: #ffffff;
}

.questions-section {
    background: linear-gradient(to bottom right, #eff6ff, #ecfeff);
}

.skills-section {
    background: linear-gradient(to bottom right, #faf5ff, #fce7f3);
}

.articles-section {
    background-color: #ffffff;
}

/* CTA Section */
.cta-section {
    background: linear-gradient(to bottom right, #4f46e5, #9333ea, #ec4899);
    padding: 5rem 0;
}

.cta-content {
    text-align: center;
}

.cta-title {
    font-size: 1.875rem;
    font-weight: 700;
    color: white;
    margin-bottom: 1.5rem;
}

@media (min-width: 640px) {
    .cta-title {
        font-size: 2.25rem;
    }
}

.cta-subtitle {
    font-size: 1.25rem;
    color: #e0e7ff;
    margin-bottom: 2rem;
    max-width: 42rem;
    margin-left: auto;
    margin-right: auto;
}

.btn-cta {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 2rem;
    background-color: white;
    color: #4f46e5;
    border-radius: 0.75rem;
    font-weight: 600;
    text-decoration: none;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    transition: all 0.3s;
}

.btn-cta:hover {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    transform: translateY(-0.25rem);
}
</style>
@endpush

@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-nav">
            <div class="hero-logo">
                <p class="hero-logo-small">AIプロ人材をご紹介</p>
                <div class="hero-logo-main">
                    <span class="logo-ai">AI </span><span class="logo-skill-match">Skill Match</span>
                    <div class="hero-logo-badge"></div>
                </div>
            </div>
            <div class="hero-nav-menu">
                <a href="{{ route('top') }}" class="hero-nav-link">ホーム</a>
                <a href="#reasons" class="hero-nav-link">選ばれる理由</a>
                <a href="#faq" class="hero-nav-link">よくある質問</a>
                <a href="{{ route('auth.login.form') }}" class="hero-nav-cta">無料相談を予約する</a>
            </div>
        </div>
        <div class="hero-main">
            <div class="hero-content-left">
                <h1 class="hero-main-title">
                    AIエンジニアに <span class="hero-highlight-orange">月10万円〜</span> で<br>
                    <span class="hero-highlight-cyan">業務自動化</span> を依頼する。
                </h1>
                <p class="hero-subtitle">
                    AIで業務を自動化し、人件費・外注費を削減することで、<br>
                    営業利益の創出にコミットします。
                </p>
                <div class="hero-features-boxes">
                    <div class="hero-feature-box">
                        <h3 class="hero-feature-title">業界最安</h3>
                        <p class="hero-feature-subtitle">水準でプロをアサイン</p>
                    </div>
                    <div class="hero-feature-box">
                        <h3 class="hero-feature-title">最短5日</h3>
                        <p class="hero-feature-subtitle">でプロをアサイン</p>
                    </div>
                    <div class="hero-feature-box">
                        <h3 class="hero-feature-title">利益創出</h3>
                        <p class="hero-feature-subtitle">にコミット</p>
                    </div>
                </div>
                <a href="{{ route('auth.login.form') }}" class="hero-cta-button">
                    無料相談を予約する
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
            </div>
            <div class="hero-image-container">
                <img src="https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=800&h=800&fit=crop" alt="AI Professional" class="hero-image">
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">充実の機能</h2>
                <p class="section-subtitle">AIスキルマッチングに必要な全てが揃っています</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon bg-blue">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            <circle cx="12" cy="11" r="1"></circle>
                            <circle cx="8" cy="11" r="1"></circle>
                            <circle cx="16" cy="11" r="1"></circle>
                        </svg>
                    </div>
                    <h3 class="feature-title">AI知恵袋</h3>
                    <p class="feature-description">AIに関する質問を投稿し、コミュニティから回答を得られます</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon bg-purple">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                        </svg>
                    </div>
                    <h3 class="feature-title">スキル販売</h3>
                    <p class="feature-description">あなたのAIスキルをサービスとして販売できます</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon bg-orange">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <h3 class="feature-title">プロフィール公開</h3>
                    <p class="feature-description">フリーランスとして実績とスキルを公開できます</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon bg-green">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                    </div>
                    <h3 class="feature-title">記事投稿</h3>
                    <p class="feature-description">AI関連の知識や経験を記事として共有できます</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="benefits-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">選ばれる理由</h2>
                <p class="section-subtitle">AIスキルマッチが提供する価値</p>
            </div>
            <div class="benefits-grid">
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                        </svg>
                    </div>
                    <h3 class="benefit-title">即座にマッチング</h3>
                    <p class="benefit-description">AIスキルを持つ人材と企業を瞬時に結びつけます</p>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                    </div>
                    <h3 class="benefit-title">安心の取引</h3>
                    <p class="benefit-description">プラットフォーム上での安全な取引をサポート</p>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <circle cx="12" cy="12" r="6"></circle>
                            <circle cx="12" cy="12" r="2"></circle>
                        </svg>
                    </div>
                    <h3 class="benefit-title">成長支援</h3>
                    <p class="benefit-description">継続的なスキルアップと案件獲得を支援</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Latest Freelancers -->
    <section class="freelancers-section">
        <div class="container">
            <div class="section-header-row">
                <div>
                    <h2 class="section-title">新着フリーランス</h2>
                    <p class="section-subtitle">スキルと実績を持つAI人材</p>
                </div>
                <a href="{{ route('profiles.index') }}" class="link-with-arrow">
                    すべて見る
                    <svg class="arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>
            <div class="cards-grid">
                @forelse($freelancers ?? collect() as $f)
                    <a href="{{ route('profiles.show', ['user' => $f->user_id]) }}" class="freelancer-card">
                        <div class="freelancer-header">
                            <img src="{{ $f->icon_path ?? 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop' }}" alt="{{ $f->display_name }}" class="freelancer-avatar">
                            <div class="freelancer-info">
                                <h3 class="freelancer-name">{{ $f->display_name ?? '名前未設定' }}</h3>
                                <p class="freelancer-role">職種: {{ $f->job_title ?? '未設定' }}</p>
                                <p class="freelancer-rate">希望時給単価: <span>¥{{ number_format($f->min_rate ?? 0) }}/時間</span></p>
                            </div>
                        </div>
                        <p class="freelancer-bio">{{ Str::limit($f->bio ?? '', 100) }}</p>
                        <div class="skills-tags">
                            @foreach($f->skills->take(3) as $skill)
                                <span class="skill-tag">{{ $skill->name }}</span>
                            @endforeach
                            @if($f->skills->count() > 3)
                                <span class="skill-tag more">+{{ $f->skills->count() - 3 }}</span>
                            @endif
                        </div>
                        <div class="freelancer-footer">
                            <div class="rating">
                                <svg class="star-icon" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                評価データなし
                            </div>
                        </div>
                    </a>
                @empty
                    <p class="text-gray-500 col-span-full text-center py-8">新着フリーランスはまだいません</p>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Latest Questions -->
    <section class="questions-section">
        <div class="container">
            <div class="section-header-row">
                <div>
                    <h2 class="section-title">最新の質問</h2>
                    <p class="section-subtitle">コミュニティで活発に議論されている質問</p>
                </div>
                <a href="{{ route('questions.index') }}" class="link-with-arrow">
                    すべて見る
                    <svg class="arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>
            <div class="cards-grid">
                @forelse($questions ?? collect() as $q)
                    <a href="{{ route('questions.show', ['question' => $q->id]) }}" class="question-card">
                        <div class="question-meta">
                            <span class="category-badge">{{ $q->category ?? 'その他' }}</span>
                            <div class="answer-count">
                                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                回答数 {{ $q->answers_count ?? 0 }}
                            </div>
                        </div>
                        <h3 class="question-title">{{ Str::limit($q->title, 60) }}</h3>
                        <p class="question-content">{{ Str::limit($q->content, 80) }}</p>
                        <div class="question-author">
                            @php $authorF = $q->user?->freelancer; @endphp
                            <img src="{{ $authorF?->icon_path ?? 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop' }}" alt="" class="author-avatar">
                            <div>
                                <div class="author-name">{{ $authorF?->display_name ?? $q->user?->email ?? '匿名' }}</div>
                                <div class="question-date">{{ $q->created_at?->format('Y/m/d') }}</div>
                            </div>
                        </div>
                    </a>
                @empty
                    <p class="text-gray-500 col-span-full text-center py-8">まだ質問はありません</p>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Popular Skills -->
    <section class="skills-section">
        <div class="container">
            <div class="section-header-row">
                <div>
                    <h2 class="section-title">人気のスキル</h2>
                    <p class="section-subtitle">需要の高いAIスキルサービス</p>
                </div>
                <a href="{{ route('skills.index') }}" class="link-with-arrow">
                    すべて見る
                    <svg class="arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>
            <div class="cards-grid">
                @forelse($listings ?? collect() as $l)
                    <a href="{{ route('skills.show', ['skill_listing' => $l->id]) }}" class="skill-card">
                        <img src="{{ $l->thumbnail_url ?? 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=800&h=600&fit=crop' }}" alt="{{ $l->title }}" class="skill-image">
                        <div class="skill-content">
                            <div class="seller-info">
                                @php $seller = $l->freelancer; @endphp
                                <img src="{{ $seller->icon_path ?? 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=400&h=400&fit=crop' }}" alt="{{ $seller->display_name }}" class="seller-avatar">
                                <div>
                                    <div class="seller-name">{{ $seller->display_name ?? '出品者' }}</div>
                                    <div class="seller-role">職種: {{ $seller->job_title ?? '-' }}</div>
                                </div>
                            </div>
                            <h3 class="skill-title">{{ Str::limit($l->title, 60) }}</h3>
                            <p class="skill-description">{{ Str::limit($l->description, 80) }}</p>
                            <div class="skill-footer">
                                <div class="skill-rating">
                                    <svg class="star-icon" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                    <span class="skill-rating-value">{{ $l->rating_average ?? '0' }}</span>
                                    <span class="skill-rating-count">({{ $l->reviews_count ?? 0 }})</span>
                                </div>
                                <div class="skill-price">¥{{ number_format($l->price) }}</div>
                            </div>
                        </div>
                    </a>
                @empty
                    <p class="text-gray-500 col-span-full text-center py-8">まだスキルは出品されていません</p>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Latest Articles -->
    <section class="articles-section">
        <div class="container">
            <div class="section-header-row">
                <div>
                    <h2 class="section-title">注目の記事</h2>
                    <p class="section-subtitle">コミュニティメンバーが発信するAI情報</p>
                </div>
                <a href="{{ route('articles.index') }}" class="link-with-arrow">
                    すべて見る
                    <svg class="arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>
            <div class="cards-grid">
                @forelse($articles ?? collect() as $a)
                    <a href="{{ route('articles.show', ['article' => $a->id]) }}" class="article-card">
                        <img src="{{ $a->eyecatch_image_url ?? 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=1200&h=630&fit=crop' }}" alt="{{ $a->title }}" class="article-image">
                        <div class="article-content">
                            <h3 class="article-title">{{ Str::limit($a->title, 60) }}</h3>
                            <p class="article-excerpt">{{ Str::limit($a->excerpt ?? '', 80) }}</p>
                            <div class="article-author">
                                @php $authorF = $a->user?->freelancer; @endphp
                                <img src="{{ $authorF?->icon_path ?? 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop' }}" alt="" class="author-avatar">
                                <div>
                                    <div class="author-name">{{ $authorF?->display_name ?? $a->user?->email ?? '匿名' }}</div>
                                    <div class="question-date">{{ $a->published_at?->format('Y/m/d') ?? $a->created_at?->format('Y/m/d') }}</div>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <p class="text-gray-500 col-span-full text-center py-8">まだ記事はありません</p>
                @endforelse
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">今すぐAIスキルマッチを始めよう</h2>
                <p class="cta-subtitle">無料登録で、知識の共有、スキルの販売、キャリアアップの全てが可能に</p>
                <a href="{{ route('auth.login.form') }}" class="btn btn-cta">
                    無料で始める
                    <svg class="arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>
        </div>
    </section>

@endsection

