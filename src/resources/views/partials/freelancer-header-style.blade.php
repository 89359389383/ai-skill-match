<style>
    :root {
        --header-height: 72px;
        --header-height-mobile: 72px;
        --header-height-sm: 72px;
        --header-height-md: 72px;
        --header-height-lg: 72px;
        --header-height-xl: 72px;
        --header-height-current: var(--header-height-mobile);
        --header-padding-x: 1rem;
    }
    @media (min-width: 640px) {
        :root {
            --header-padding-x: 1.5rem;
            --header-height-current: var(--header-height-sm);
        }
    }
    @media (min-width: 768px) {
        :root {
            --header-height-current: var(--header-height-md);
            --header-padding-x: 2rem;
        }
    }
    @media (min-width: 1024px) {
        :root {
            --header-padding-x: 2.5rem;
            --header-height-current: var(--header-height-lg);
        }
    }
    @media (min-width: 1280px) {
        :root {
            --header-padding-x: 3rem;
            --header-height-current: var(--header-height-xl);
        }
    }

    /* Header (shared partial) */
    .header {
        background-color: #ffffff;
        border-bottom: 1px solid #e1e4e8;
        padding: 0 var(--header-padding-x);
        position: sticky;
        top: 0;
        z-index: 100;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        min-height: var(--header-height-current);
    }
    .header-content {
        max-width: 1600px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr auto; /* mobile: ロゴ / 右側 */
        align-items: center;
        gap: 0.5rem;
        height: var(--header-height-current);
        position: relative;
        min-width: 0;
        padding: 0.25rem 0; /* 縦余白を確保 */
    }

    /* md以上: ロゴ / 中央ナビ / 右側 (ユーザー) */
    @media (min-width: 768px) {
        .header-content { grid-template-columns: auto 1fr auto; gap: 1rem; }
    }

    /* lg: より広く間隔を取る */
    @media (min-width: 1024px) {
        .header-content { gap: 1.5rem; padding: 0.5rem 0; }
    }

    .header-left { display: flex; align-items: center; gap: 0.75rem; min-width: 0; }
    .header-right { display: flex; align-items: center; justify-content: flex-end; min-width: 0; gap: 0.75rem; }

    /* ロゴ（左） */
    .logo { display: flex; align-items: center; gap: 8px; min-width: 0; }
    .logo-text {
        font-weight: 900;
        font-size: 18px;
        margin-left: 0;
        color: #111827;
        letter-spacing: 1px;
        white-space: nowrap;
    }
    @media (min-width: 640px) { .logo-text { font-size: 20px; } }
    @media (min-width: 768px) { .logo-text { font-size: 22px; } }
    @media (min-width: 1024px) { .logo-text { font-size: 24px; } }
    @media (min-width: 1280px) { .logo-text { font-size: 26px; } }

    /* Mobile nav toggle */
    .nav-toggle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 10px;
        border: 1px solid #e1e4e8;
        background: #fff;
        cursor: pointer;
        transition: all 0.15s ease;
        flex: 0 0 auto;
    }
    .nav-toggle:hover { background: #f6f8fa; }
    .nav-toggle:focus-visible { outline: none; box-shadow: 0 0 0 3px rgba(3, 102, 214, 0.15); }
    .nav-toggle svg { width: 22px; height: 22px; color: #24292e; }
    @media (min-width: 768px) { .nav-toggle { display: none; } }

    .nav-links {
        display: none; /* mobile: hidden (use hamburger) */
        align-items: center;
        justify-content: center;
        flex-wrap: nowrap;
        min-width: 0;
        overflow: hidden;
        gap: 1.25rem;
    }
    @media (min-width: 640px) { .nav-links { display: none; } } /* smではまだ省スペースにすることが多い */
    @media (min-width: 768px) { .nav-links { display: flex; gap: 1.25rem; } }
    @media (min-width: 1024px) { .nav-links { gap: 2rem; } }
    @media (min-width: 1280px) { .nav-links { gap: 3rem; } }

    .nav-link {
        text-decoration: none;
        color: #586069;
        font-weight: 500;
        font-size: 1.05rem;
        padding: 0.6rem 1rem;
        border-radius: 8px;
        transition: all 0.15s ease;
        position: relative;
        letter-spacing: -0.01em;
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
    }
    @media (min-width: 768px) { .nav-link { font-size: 1.1rem; padding: 0.75rem 1.25rem; } }
    @media (min-width: 1280px) { .nav-link { font-size: 1.15rem; } }
    .nav-link.has-badge { padding-right: 3rem; }
    .nav-link:hover { background-color: #f6f8fa; color: #24292e; }
    .nav-link.active {
        background-color: #0366d6;
        color: white;
        box-shadow: 0 2px 8px rgba(3, 102, 214, 0.3);
    }
    .badge {
        background-color: #d73a49;
        color: white;
        border-radius: 50%;
        padding: 0.15rem 0.45rem;
        font-size: 0.7rem;
        font-weight: 600;
        min-width: 18px;
        height: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 1px 3px rgba(209, 58, 73, 0.3);
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
    }
    .user-menu { display: flex; align-items: center; position: static; transform: none; }

    /* Mobile nav menu */
    .mobile-nav {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        border-bottom: 1px solid #e1e4e8;
        box-shadow: 0 16px 40px rgba(0,0,0,0.10);
        padding: 0.75rem var(--header-padding-x);
        display: none;
        z-index: 110;
    }
    .header.is-mobile-nav-open .mobile-nav { display: block; }
    @media (min-width: 768px) { .mobile-nav { display: none !important; } }
    .mobile-nav-inner {
        max-width: 1600px;
        margin: 0 auto;
        display: grid;
        gap: 0.5rem;
    }
    .mobile-nav .nav-link {
        width: 100%;
        justify-content: flex-start;
        background: #fafbfc;
        border: 1px solid #e1e4e8;
        padding: 0.875rem 1rem;
    }
    .mobile-nav .nav-link:hover { background: #f6f8fa; }
    .mobile-nav .nav-link.active {
        background-color: #0366d6;
        color: #fff;
        border-color: #0366d6;
    }
    .mobile-nav .nav-link.has-badge { padding-right: 1rem; }
    .mobile-nav .badge {
        position: static;
        transform: none;
        margin-left: auto;
        margin-right: 0;
    }

    /* Dropdown */
    .dropdown { position: relative; }
    .dropdown-content {
        display: none;
        position: absolute;
        right: 0;
        top: calc(100% + 0.5rem);
        background-color: #ffffff;
        min-width: 300px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.12), 0 2px 8px rgba(0,0,0,0.08);
        border-radius: 12px;
        z-index: 1000;
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }
    .dropdown.is-open .dropdown-content { display: block; }

    /* ドロップダウン：プロフィールヘッダー */
    .dropdown-profile {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
        background: #fff;
        border-bottom: 1px solid #f3f4f6;
    }
    .dropdown-profile-avatar {
        width: 44px;
        height: 44px;
        min-width: 44px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }
    .dropdown-profile-avatar-initial {
        width: 44px;
        height: 44px;
        min-width: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 1rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        flex-shrink: 0;
    }
    .dropdown-profile-info { min-width: 0; }
    .dropdown-profile-name {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
        line-height: 1.3;
    }
    .dropdown-profile-role {
        font-size: 0.8125rem;
        color: #6b7280;
        margin-top: 0.125rem;
    }

    /* ドロップダウン：ナビゲーション */
    .dropdown-nav { padding: 0.5rem 0; }
    .dropdown-item {
        display: flex;
        align-items: center;
        flex-wrap: nowrap;
        gap: 0.75rem;
        padding: 0.75rem 1.25rem;
        text-decoration: none;
        color: #374151;
        transition: all 0.15s ease;
        white-space: nowrap;
        font-size: 0.9375rem;
        font-weight: 500;
    }
    .dropdown-item:hover { background-color: #f9fafb; color: #111827; }
    .dropdown-item-icon {
        width: 20px;
        height: 20px;
        min-width: 20px;
        flex-shrink: 0;
        color: #6b7280;
    }
    .dropdown-item:hover .dropdown-item-icon { color: #374151; }
    .dropdown-item-text { flex: 1; min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .dropdown-item-badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        flex-shrink: 0;
    }
    .dropdown-item-badge-green { background: #22c55e; color: white; }
    .dropdown-item-badge-blue { background: #3b82f6; color: white; }
    .dropdown-item-badge-purple { background: #8b5cf6; color: white; }

    .dropdown-divider {
        height: 1px;
        background-color: #e5e7eb;
        margin: 0.5rem 1rem;
    }

    /* ログアウト（赤） */
    .dropdown-item-form { display: block; }
    .dropdown-item-logout {
        width: 100%;
        text-align: left;
        background: none;
        border: none;
        padding: 0.75rem 1.25rem;
        cursor: pointer;
        font-size: inherit;
        font-family: inherit;
        display: flex;
        align-items: center;
        flex-wrap: nowrap;
        gap: 0.75rem;
        white-space: nowrap;
        color: #dc2626 !important;
    }
    .dropdown-item-logout:hover { background-color: #fef2f2 !important; color: #b91c1c !important; }
    .dropdown-item-logout .dropdown-item-icon { color: #dc2626; }

    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border: none;
        padding: 0;
        appearance: none;
        overflow: hidden;
    }
    .user-avatar:hover { transform: scale(1.08); box-shadow: 0 4px 16px rgba(0,0,0,0.2); }
</style>
