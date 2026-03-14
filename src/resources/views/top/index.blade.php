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
                <a href="/" class="hero-nav-link">ホーム</a>
                <a href="#reasons" class="hero-nav-link">選ばれる理由</a>
                <a href="#faq" class="hero-nav-link">よくある質問</a>
                <a href="/login" class="hero-nav-cta">無料相談を予約する</a>
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
                <a href="/login" class="hero-cta-button">
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
                <a href="/profiles" class="link-with-arrow">
                    すべて見る
                    <svg class="arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>
            <div class="cards-grid" id="freelancers-container"></div>
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
                <a href="/questions" class="link-with-arrow">
                    すべて見る
                    <svg class="arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>
            <div class="cards-grid" id="questions-container"></div>
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
                <a href="/skills" class="link-with-arrow">
                    すべて見る
                    <svg class="arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>
            <div class="cards-grid" id="skills-container"></div>
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
                <a href="/articles" class="link-with-arrow">
                    すべて見る
                    <svg class="arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>
            <div class="cards-grid" id="articles-container"></div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">今すぐAIスキルマッチを始めよう</h2>
                <p class="cta-subtitle">無料登録で、知識の共有、スキルの販売、キャリアアップの全てが可能に</p>
                <a href="/login" class="btn btn-cta">
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

@push('scripts')
    <script>
// Mock Data
const mockProfiles = [
  {
    id: "user-007",
    name: "山本AI太郎",
    avatar: "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=400&h=400&fit=crop",
    role: "AIコンサルタント",
    hourlyRate: "¥8,000/時間",
    bio: "大手IT企業でAI開発に5年従事した後、独立。ChatGPT、Claude、Geminiなど最新のLLMを活用した業務効率化支援を得意としています。500社以上の導入実績あり。",
    skills: ["ChatGPT", "プロンプトエンジニアリング", "業務コンサルティング", "社員研修", "AI戦略"],
    rating: 4.9,
    reviews: 127
  },
  {
    id: "user-008",
    name: "中村自動化子",
    avatar: "https://images.unsplash.com/photo-1580489944761-15a19d654956?w=400&h=400&fit=crop",
    role: "自動化エンジニア",
    hourlyRate: "¥7,000/時間",
    bio: "n8n、Zapier、Make.comなどのノーコードツールを駆使して、複雑な業務フローを自動化します。中小企業から大企業まで幅広く対応。月間100時間以上の工数削減実績多数。",
    skills: ["n8n", "Zapier", "Make.com", "API連携", "業務自動化"],
    rating: 5.0,
    reviews: 89
  },
  {
    id: "user-009",
    name: "小林データ郎",
    avatar: "https://images.unsplash.com/photo-1599566150163-29194dcaad36?w=400&h=400&fit=crop",
    role: "データサイエンティスト",
    hourlyRate: "¥9,000/時間",
    bio: "Python、R、SQLを用いたデータ分析・機械学習モデル構築が専門。Kaggleコンペティション入賞経験あり。ビジネス課題をデータで解決します。",
    skills: ["Python", "機械学習", "データ分析", "TensorFlow", "PyTorch"],
    rating: 4.8,
    reviews: 156
  }
];

const mockQuestions = [
  {
    id: "q-001",
    title: "n8nでSlackとGoogleスプレッドシートを連携させる方法",
    content: "n8nを使ってSlackのメッセージを自動でGoogleスプレッドシートに記録する自動化を作りたいのですが、どのように設定すれば良いでしょうか？具体的な手順を教えていただけますと幸いです。",
    category: "n8n",
    author: {
      name: "田中一郎",
      avatar: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop"
    },
    answers: 5,
    createdAt: "2026-03-05T10:30:00Z"
  },
  {
    id: "q-002",
    title: "ChatGPT APIの料金を抑える効果的なプロンプト設計",
    content: "ChatGPT APIを業務で利用していますが、トークン数が多くなり料金が高騰しています。効果的なプロンプト設計でコストを削減する方法はありますか？",
    category: "ChatGPT",
    author: {
      name: "佐藤花子",
      avatar: "https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400&h=400&fit=crop"
    },
    answers: 8,
    createdAt: "2026-03-04T15:20:00Z"
  },
  {
    id: "q-003",
    title: "Pythonで画像認識AIを実装するための最適なライブラリ",
    content: "Python初心者です。画像認識のAIモデルを実装したいのですが、TensorFlowとPyTorchどちらがおすすめでしょうか？それぞれのメリット・デメリットを教えてください。",
    category: "Python",
    author: {
      name: "鈴木健太",
      avatar: "https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&h=400&fit=crop"
    },
    answers: 12,
    createdAt: "2026-03-03T09:15:00Z"
  }
];

const mockSkills = [
  {
    id: "skill-001",
    title: "ChatGPT業務活用プロンプト作成代行",
    description: "あなたのビジネスに最適化されたChatGPTプロンプトを作成します。営業メール、提案書作成、データ分析など、様々な業務シーンに対応。初回ヒアリングから納品まで丁寧にサポートいたします。",
    price: "¥15,000",
    rating: 4.9,
    reviewCount: 127,
    seller: {
      name: "山本AI太郎",
      avatar: "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=400&h=400&fit=crop",
      role: "AIコンサルタント"
    },
    image: "https://images.unsplash.com/photo-1677442136019-21780ecad995?w=800&h=600&fit=crop"
  },
  {
    id: "skill-002",
    title: "n8n完全自動化ワークフロー構築",
    description: "n8nを使った業務自動化システムを構築します。Slack、Gmail、Notion、スプレッドシートなど100以上のツールとの連携が可能。定型業務を完全自動化してコスト削減を実現します。",
    price: "¥50,000",
    rating: 5.0,
    reviewCount: 89,
    seller: {
      name: "中村自動化子",
      avatar: "https://images.unsplash.com/photo-1580489944761-15a19d654956?w=400&h=400&fit=crop",
      role: "自動化エンジニア"
    },
    image: "https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800&h=600&fit=crop"
  },
  {
    id: "skill-003",
    title: "Python AI開発・データ分析サポート",
    description: "PythonでのAI開発やデータ分析をサポートします。機械学習モデルの構築、データの可視化、自動化スクリプトの作成など幅広く対応。初心者の方への技術指導も可能です。",
    price: "¥30,000",
    rating: 4.8,
    reviewCount: 156,
    seller: {
      name: "小林データ郎",
      avatar: "https://images.unsplash.com/photo-1599566150163-29194dcaad36?w=400&h=400&fit=crop",
      role: "データサイエンティスト"
    },
    image: "https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=800&h=600&fit=crop"
  }
];

const mockArticles = [
  {
    id: "article-001",
    title: "ChatGPT APIを業務に活用するための完全ガイド【2026年版】",
    excerpt: "ChatGPT APIの基本的な使い方から、実践的な業務活用事例、コスト最適化のテクニックまで徹底解説します。",
    author: {
      name: "山本AI太郎",
      avatar: "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=400&h=400&fit=crop"
    },
    image: "https://images.unsplash.com/photo-1677442136019-21780ecad995?w=1200&h=630&fit=crop",
    createdAt: "2026-03-07T10:00:00Z"
  },
  {
    id: "article-002",
    title: "n8nで始める業務自動化：初心者でもできる実践ガイド",
    excerpt: "ノーコードツールn8nを使って、誰でも簡単に業務自動化を実現する方法を具体例とともに紹介します。",
    author: {
      name: "中村自動化子",
      avatar: "https://images.unsplash.com/photo-1580489944761-15a19d654956?w=400&h=400&fit=crop"
    },
    image: "https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=1200&h=630&fit=crop",
    createdAt: "2026-03-06T14:30:00Z"
  },
  {
    id: "article-003",
    title: "Python機械学習入門：データ分析の始め方",
    excerpt: "Pythonを使った機械学習の基礎知識から、実際のデータ分析プロジェクトの進め方まで解説します。",
    author: {
      name: "小林データ郎",
      avatar: "https://images.unsplash.com/photo-1599566150163-29194dcaad36?w=400&h=400&fit=crop"
    },
    image: "https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=1200&h=630&fit=crop",
    createdAt: "2026-03-05T09:00:00Z"
  }
];

// Helper Functions
function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString('ja-JP', { year: 'numeric', month: '2-digit', day: '2-digit' });
}

function createStarIcon() {
  const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
  svg.setAttribute('class', 'star-icon');
  svg.setAttribute('viewBox', '0 0 24 24');
  svg.setAttribute('fill', 'none');
  svg.setAttribute('stroke', 'currentColor');
  svg.setAttribute('stroke-width', '2');
  
  const polygon = document.createElementNS('http://www.w3.org/2000/svg', 'polygon');
  polygon.setAttribute('points', '12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2');
  polygon.setAttribute('fill', 'currentColor');
  
  svg.appendChild(polygon);
  return svg;
}

function createMessageIcon() {
  const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
  svg.setAttribute('class', 'icon');
  svg.setAttribute('viewBox', '0 0 24 24');
  svg.setAttribute('fill', 'none');
  svg.setAttribute('stroke', 'currentColor');
  svg.setAttribute('stroke-width', '2');
  
  const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
  path.setAttribute('d', 'M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z');
  
  svg.appendChild(path);
  return svg;
}

// Render Functions
function renderFreelancers() {
  const container = document.getElementById('freelancers-container');
  container.innerHTML = '';
  
  mockProfiles.forEach(profile => {
    const card = document.createElement('a');
    card.href = `/profiles/${profile.id}`;
    card.className = 'freelancer-card';
    
    const skillsHTML = profile.skills.slice(0, 3).map(skill => 
      `<span class="skill-tag">${skill}</span>`
    ).join('') + (profile.skills.length > 3 ? `<span class="skill-tag more">+${profile.skills.length - 3}</span>` : '');
    
    card.innerHTML = `
      <div class="freelancer-header">
        <img src="${profile.avatar}" alt="${profile.name}" class="freelancer-avatar">
        <div class="freelancer-info">
          <h3 class="freelancer-name">${profile.name}</h3>
          <p class="freelancer-role">職種: ${profile.role}</p>
          <p class="freelancer-rate">希望時給単価: <span>${profile.hourlyRate}</span></p>
        </div>
      </div>
      <p class="freelancer-bio">${profile.bio}</p>
      <div class="skills-tags">${skillsHTML}</div>
      <div class="freelancer-footer">
        <div class="rating">
          ${createStarIcon().outerHTML}
          ${profile.rating} (${profile.reviews}件)
        </div>
      </div>
    `;
    
    container.appendChild(card);
  });
}

function renderQuestions() {
  const container = document.getElementById('questions-container');
  container.innerHTML = '';
  
  mockQuestions.forEach(question => {
    const card = document.createElement('a');
    card.href = `/questions/${question.id}`;
    card.className = 'question-card';
    
    card.innerHTML = `
      <div class="question-meta">
        <span class="category-badge">${question.category}</span>
        <div class="answer-count">
          ${createMessageIcon().outerHTML}
          ${question.answers}
        </div>
      </div>
      <h3 class="question-title">${question.title}</h3>
      <p class="question-content">${question.content}</p>
      <div class="question-author">
        <img src="${question.author.avatar}" alt="${question.author.name}" class="author-avatar">
        <div>
          <div class="author-name">${question.author.name}</div>
          <div class="question-date">${formatDate(question.createdAt)}</div>
        </div>
      </div>
    `;
    
    container.appendChild(card);
  });
}

function renderSkills() {
  const container = document.getElementById('skills-container');
  container.innerHTML = '';
  
  mockSkills.forEach(skill => {
    const card = document.createElement('a');
    card.href = `/skills/${skill.id}`;
    card.className = 'skill-card';
    
    card.innerHTML = `
      <img src="${skill.image}" alt="${skill.title}" class="skill-image">
      <div class="skill-content">
        <div class="seller-info">
          <img src="${skill.seller.avatar}" alt="${skill.seller.name}" class="seller-avatar">
          <div>
            <div class="seller-name">${skill.seller.name}</div>
            <div class="seller-role">職種: ${skill.seller.role}</div>
          </div>
        </div>
        <h3 class="skill-title">${skill.title}</h3>
        <p class="skill-description">${skill.description}</p>
        <div class="skill-footer">
          <div class="skill-rating">
            ${createStarIcon().outerHTML}
            <span class="skill-rating-value">${skill.rating}</span>
            <span class="skill-rating-count">(${skill.reviewCount})</span>
          </div>
          <div class="skill-price">${skill.price}</div>
        </div>
      </div>
    `;
    
    container.appendChild(card);
  });
}

function renderArticles() {
  const container = document.getElementById('articles-container');
  container.innerHTML = '';
  
  mockArticles.forEach(article => {
    const card = document.createElement('a');
    card.href = `/articles/${article.id}`;
    card.className = 'article-card';
    
    card.innerHTML = `
      <img src="${article.image}" alt="${article.title}" class="article-image">
      <div class="article-content">
        <h3 class="article-title">${article.title}</h3>
        <p class="article-excerpt">${article.excerpt}</p>
        <div class="article-author">
          <img src="${article.author.avatar}" alt="${article.author.name}" class="author-avatar">
          <div>
            <div class="author-name">${article.author.name}</div>
            <div class="question-date">${formatDate(article.createdAt)}</div>
          </div>
        </div>
      </div>
    `;
    
    container.appendChild(card);
  });
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
  renderFreelancers();
  renderQuestions();
  renderSkills();
  renderArticles();
});
    </script>
@endpush
