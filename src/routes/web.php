<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FreelancerProfileController;
use App\Http\Controllers\FreelancerJobController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\FreelancerApplicationController;
use App\Http\Controllers\FreelancerMessageController;
use App\Http\Controllers\FreelancerScoutController;
use App\Http\Controllers\CompanyProfileController;
use App\Http\Controllers\CompanyFreelancerController;
use App\Http\Controllers\CompanyJobController;
use App\Http\Controllers\ScoutController;
use App\Http\Controllers\CompanyApplicationController;
use App\Http\Controllers\CompanyMessageController;
use App\Http\Controllers\TopController;
use App\Http\Controllers\SkillListingController;
use App\Http\Controllers\SkillOrderController;
use App\Http\Controllers\SkillInquiryController;
use App\Http\Controllers\SkillTransactionController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\MyArticleController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\AnswerController;
use App\Http\Controllers\AnswerCommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DirectMessageController;
use App\Http\Controllers\StripeCheckoutController;
use App\Http\Controllers\StripeWebhookController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Stripe webhook（決済確定は webhook を正とする）
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->name('stripe.webhook');

// =========================
// 新機能（トップ / スキル / 記事 / 質問 / プロフィール）
// ※ Controller を実装し、ビューは存在する場合のみ利用（無ければ welcome へフォールバック）
// =========================

// トップページ
Route::get('/top', [TopController::class, 'index'])->name('top');

// スキル（販売一覧/詳細：閲覧はログイン不要）
Route::get('/skills', [SkillListingController::class, 'index'])->name('skills.index');

Route::get('/skills/{skill_listing}', [SkillListingController::class, 'show'])
    ->whereNumber('skill_listing')
    ->name('skills.show');

// 記事（一覧/詳細：閲覧はログイン不要）
Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');

Route::get('/articles/{article}', [ArticleController::class, 'show'])
    ->whereNumber('article')
    ->name('articles.show');

// 質問（一覧/詳細：閲覧はログイン不要）
Route::get('/questions', [QuestionController::class, 'index'])->name('questions.index');

Route::get('/questions/{question}', [QuestionController::class, 'show'])
    ->whereNumber('question')
    ->name('questions.show');

// プロフィール（一覧/詳細：閲覧はログイン不要）
Route::get('/profiles', [ProfileController::class, 'index'])->name('profiles.index');

Route::get('/profiles/{user}', [ProfileController::class, 'show'])
    ->whereNumber('user')
    ->name('profiles.show');

// フリーランスプロフィール詳細から辿る「そのフリーランスのスキル一覧」
Route::get('/profiles/{user}/skills', [SkillListingController::class, 'skillsByFreelancer'])
    ->whereNumber('user')
    ->name('profiles.skills.index');

// ダイレクトチャット（一覧/詳細：buyer は専用URLに誘導するため別グループで扱う）
Route::middleware(['auth.any:freelancer,company'])->group(function () {
    Route::get('/messages', [DirectMessageController::class, 'index'])
        ->name('direct-messages.index');

    Route::get('/messages/{direct_conversation}', [DirectMessageController::class, 'show'])
        ->whereNumber('direct_conversation')
        ->name('direct-messages.show');
});

// ダイレクトチャット（開始/返信：buyer もOK）
Route::middleware(['auth.any:freelancer,company,buyer'])->group(function () {
    Route::post('/profiles/{user}/messages', [DirectMessageController::class, 'start'])
        ->whereNumber('user')
        ->name('direct-messages.start');

    Route::post('/messages/{direct_conversation}/messages', [DirectMessageController::class, 'reply'])
        ->whereNumber('direct_conversation')
        ->name('direct-messages.reply');
});

// buyer 専用ダイレクトチャットURL
Route::middleware(['auth:buyer', 'buyer'])->group(function () {
    Route::get('/buyer/direct-messages', [DirectMessageController::class, 'index'])
        ->name('buyer.direct-messages.index');

    Route::get('/buyer/direct-messages/{direct_conversation}', [DirectMessageController::class, 'show'])
        ->whereNumber('direct_conversation')
        ->name('buyer.direct-messages.show');
});

/*
|--------------------------------------------------------------------------
| フリーランスマッチングプラチE��フォーム�E�ルーチE��ング�E�機�Eフロー対応！E|--------------------------------------------------------------------------
| 目皁E��どのURLが「どのController@method」に対応するかを�EかりめE��くすめE| 注意：コントローラー未実裁E��もルーチE��ング定義でアプリが落ちなぁE��ぁE��文字�E持E��で書ぁE*/

// ログイン画面表示
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('auth.login.form');

// ログイン処理（guard別）
Route::post('/login/freelancer', [AuthController::class, 'loginFreelancer'])->name('auth.login.freelancer');
Route::post('/login/company', [AuthController::class, 'loginCompany'])->name('auth.login.company');
Route::post('/login/buyer', [AuthController::class, 'loginBuyer'])->name('auth.login.buyer');

// ログアウト（クリック導線用：POST）
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

// パスワード再設定メール送信フォーム（メールアドレス入力画面）
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])
    ->name('password.request');

// パスワード再設定メール送信処理（リセットリンクメールを送る）
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])
    ->name('password.email');

// パスワード再設定ページ表示（メール内リンクからアクセスする画面）
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])
    ->name('password.reset');

// パスワード更新処理（新しいパスワードを保存する）
Route::post('/reset-password', [AuthController::class, 'resetPassword'])
    ->name('password.update');

// フリーランス 新規登録 表示（ログイン情報登録）
Route::get('/register/freelancer', [AuthController::class, 'showFreelancerRegister'])->name('auth.register.freelancer.form');

// フリーランス 新規登録 保存（ログイン情報登録）
Route::post('/register/freelancer', [AuthController::class, 'storeFreelancer'])->name('auth.register.freelancer.store');

// 企業 新規登録 表示（ログイン情報登録）
Route::get('/register/company', [AuthController::class, 'showCompanyRegister'])->name('auth.register.company.form');

// 企業 新規登録 保存（ログイン情報登録）
Route::post('/register/company', [AuthController::class, 'storeCompany'])->name('auth.register.company.store');

// 購入者 新規登録 表示（ログイン情報登録）
Route::get('/register/buyer', [AuthController::class, 'showBuyerRegister'])->name('auth.register.buyer.form');

// 購入者 新規登録 保存（ログイン情報登録）
Route::post('/register/buyer', [AuthController::class, 'storeBuyer'])->name('auth.register.buyer.store');

Route::middleware(['auth:freelancer', 'freelancer'])->group(function () {
    // フリーランス プロフィール 表示
    Route::get('/freelancer/profile', [FreelancerProfileController::class, 'create'])->name('freelancer.profile.create');

    // フリーランス プロフィール 保存・更新
    Route::post('/freelancer/profile', [FreelancerProfileController::class, 'store'])->name('freelancer.profile.store');

    // フリーランス プロフィール設定 表示
    Route::get('/freelancer/profile/settings', [FreelancerProfileController::class, 'edit'])->name('freelancer.profile.settings');

    // フリーランス プロフィール設定 保存・更新
    Route::post('/freelancer/profile/settings', [FreelancerProfileController::class, 'update'])->name('freelancer.profile.settings.update');

    // 案件一覧
    Route::get('/freelancer/jobs', [FreelancerJobController::class, 'index'])->name('freelancer.jobs.index');

    // 案件詳細
    Route::get('/freelancer/jobs/{job}', [FreelancerJobController::class, 'show'])->name('freelancer.jobs.show');

    // 応募入力画面
    Route::get('/freelancer/jobs/{job}/apply', [ApplicationController::class, 'create'])->name('freelancer.jobs.apply.create');

    // 応募処理
    Route::post('/freelancer/jobs/{job}/apply', [ApplicationController::class, 'store'])->name('freelancer.jobs.apply.store');

    // 応募一覧
    Route::get('/freelancer/applications', [FreelancerApplicationController::class, 'index'])->name('freelancer.applications.index');

    // チャット画面(応募・スカウト)
    Route::get('/freelancer/threads/{thread}', [FreelancerMessageController::class, 'show'])->name('freelancer.threads.show');

    // メッセージ送信
    Route::post('/freelancer/threads/{thread}/messages', [FreelancerMessageController::class, 'store'])->name('freelancer.threads.messages.store');

    // メッセージ削除
    Route::delete('/freelancer/messages/{message}', [FreelancerMessageController::class, 'destroy'])->name('freelancer.messages.destroy');

    // スカウト一覧
    Route::get('/freelancer/scouts', [FreelancerScoutController::class, 'index'])->name('freelancer.scouts.index');

    // スキル出品（フリーランスのみ）
    Route::get('/skills/new', [SkillListingController::class, 'create'])->name('skills.create');
    Route::post('/skills', [SkillListingController::class, 'store'])->name('skills.store');

    // スキル出品（編集/更新/削除）
    Route::get('/skills/{skill_listing}/edit', [SkillListingController::class, 'edit'])
        ->whereNumber('skill_listing')
        ->name('skills.edit');
    Route::match(['put', 'patch'], '/skills/{skill_listing}', [SkillListingController::class, 'update'])
        ->whereNumber('skill_listing')
        ->name('skills.update');
    Route::delete('/skills/{skill_listing}', [SkillListingController::class, 'destroy'])
        ->whereNumber('skill_listing')
        ->name('skills.destroy');
});

Route::middleware(['auth:company', 'company'])->group(function () {
    // 企業 プロフィール 表示
    Route::get('/company/profile', [CompanyProfileController::class, 'create'])->name('company.profile.create');

    // 企業 プロフィール 保存・更新
    Route::post('/company/profile', [CompanyProfileController::class, 'store'])->name('company.profile.store');

    // 企業 プロフィール設定 表示
    Route::get('/company/profile/settings', [CompanyProfileController::class, 'edit'])->name('company.profile.settings');

    // 企業 プロフィール設定 保存・更新
    Route::post('/company/profile/settings', [CompanyProfileController::class, 'update'])->name('company.profile.settings.update');

    // フリーランス一覧
    // NOTE:
    // 企業ログイン時だけ見せる予定だったが、未ログインでも表示できてしまう挙動があるため無効化します。
    // 公開フリーランス一覧へは /profiles（profiles.index）へ遷移する想定です。
    //
    // Route::get('/company/freelancers', [CompanyFreelancerController::class, 'index'])->name('company.freelancers.index');

    // フリーランス詳細
    // Route::get('/company/freelancers/{freelancer}', [CompanyFreelancerController::class, 'show'])->name('company.freelancers.show');

    // 案件一覧
    Route::get('/company/jobs', [CompanyJobController::class, 'index'])->name('company.jobs.index');

    // 案件 新規登録 表示
    Route::get('/company/jobs/create', [CompanyJobController::class, 'create'])->name('company.jobs.create');

    // 案件 新規登録 保存
    Route::post('/company/jobs', [CompanyJobController::class, 'store'])->name('company.jobs.store');

    // 案件 編集 表示
    Route::get('/company/jobs/{job}/edit', [CompanyJobController::class, 'edit'])->name('company.jobs.edit');

    // 案件 更新
    Route::match(['put', 'patch'], '/company/jobs/{job}', [CompanyJobController::class, 'update'])->name('company.jobs.update');

    // 案件ステータス更新
    Route::patch('/company/jobs/{job}/status', [CompanyJobController::class, 'updateStatus'])->name('company.jobs.status.update');

    // 案件削除
    Route::delete('/company/jobs/{job}', [CompanyJobController::class, 'destroy'])->name('company.jobs.destroy');

    // スカウト送信 表示
    Route::get('/company/scouts/create', [ScoutController::class, 'create'])->name('company.scouts.create');

    // スカウト送信 処理
    Route::post('/company/scouts', [ScoutController::class, 'store'])->name('company.scouts.store');

    // スカウト一覧
    Route::get('/company/scouts', [ScoutController::class, 'index'])->name('company.scouts.index');

    // 応募一覧
    Route::get('/company/applications', [CompanyApplicationController::class, 'index'])->name('company.applications.index');

    // 応募ステータス更新
    Route::patch('/company/applications/{application}', [CompanyApplicationController::class, 'update'])->name('company.applications.update');

    // チャット画面(応募・スカウト)
    Route::get('/company/threads/{thread}', [CompanyMessageController::class, 'show'])->name('company.threads.show');

    // メッセージ送信
    Route::post('/company/threads/{thread}/messages', [CompanyMessageController::class, 'store'])->name('company.threads.messages.store');

    // メッセージ削除
    Route::delete('/company/messages/{message}', [CompanyMessageController::class, 'destroy'])->name('company.messages.destroy');

    // 応募ステータス更新
    Route::patch('/company/threads/{thread}/application-status', [CompanyMessageController::class, 'updateApplicationStatus'])->name('company.threads.application-status.update');
});

// buyer のプロフィール（2段階目）
Route::middleware(['auth:buyer', 'buyer'])->group(function () {
    Route::get('/buyer/profile', [\App\Http\Controllers\BuyerProfileController::class, 'create'])
        ->name('buyer.profile.create');
    Route::post('/buyer/profile', [\App\Http\Controllers\BuyerProfileController::class, 'store'])
        ->name('buyer.profile.store');

    Route::get('/buyer/profile/settings', [\App\Http\Controllers\BuyerProfileController::class, 'edit'])
        ->name('buyer.profile.settings');
    Route::post('/buyer/profile/settings', [\App\Http\Controllers\BuyerProfileController::class, 'update'])
        ->name('buyer.profile.settings.update');
});

// 記事投稿/質問投稿/スキル購入（ログイン必須：フリーランス/企業/購入者どれでも可）
Route::middleware(['auth.any:freelancer,company,buyer'])->group(function () {
    // 記事投稿
    Route::get('/articles/new', [ArticleController::class, 'create'])->name('articles.create');
    Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');

    // 投稿記事一覧/詳細/編集（投稿者本人チェックはController側で実装）
    Route::get('/my-articles', [MyArticleController::class, 'index'])->name('my-articles.index');
    Route::get('/my-articles/{article}', [MyArticleController::class, 'show'])->whereNumber('article')->name('my-articles.show');
    Route::get('/my-articles/{article}/edit', [MyArticleController::class, 'edit'])->whereNumber('article')->name('my-articles.edit');
    Route::match(['put', 'patch'], '/my-articles/{article}', [MyArticleController::class, 'update'])->whereNumber('article')->name('my-articles.update');
    Route::delete('/my-articles/{article}', [MyArticleController::class, 'destroy'])->whereNumber('article')->name('my-articles.destroy');

    // 質問投稿
    Route::get('/questions/new', [QuestionController::class, 'create'])->name('questions.create');
    Route::post('/questions', [QuestionController::class, 'store'])->name('questions.store');

    // 自分の質問一覧（自分の質問だけ表示）
    Route::get('/my-questions', [QuestionController::class, 'myIndex'])->name('questions.my.index');

    // 質問削除（作成者本人のみ）
    Route::delete('/questions/{question}', [QuestionController::class, 'destroy'])
        ->whereNumber('question')
        ->name('questions.destroy');

    // 回答投稿（質問詳細から）
    Route::post('/questions/{question}/answers', [AnswerController::class, 'store'])
        ->whereNumber('question')
        ->name('questions.answers.store');

    Route::post('/questions/{question}/answers/{answer}/best', [QuestionController::class, 'setBestAnswer'])
        ->whereNumber('question')
        ->whereNumber('answer')
        ->name('questions.answers.best');

    // 回答へのコメント投稿
    Route::post('/questions/{question}/answers/{answer}/comments', [AnswerCommentController::class, 'store'])
        ->whereNumber('question')
        ->whereNumber('answer')
        ->name('questions.answers.comments.store');

    // スキル購入/問い合わせ（将来想定：現時点はルートのみ）
    Route::post('/skills/{skill_listing}/purchase', [SkillOrderController::class, 'store'])
        ->whereNumber('skill_listing')
        ->name('skills.purchase');

    Route::get('/skills/orders/{order}/checkout/success', [StripeCheckoutController::class, 'success'])
        ->whereNumber('order')
        ->name('skills.checkout.success');

    Route::get('/skills/orders/{order}/checkout/cancel', [StripeCheckoutController::class, 'cancel'])
        ->whereNumber('order')
        ->name('skills.checkout.cancel');

    Route::post('/skills/{skill_listing}/inquiry', [SkillInquiryController::class, 'store'])
        ->whereNumber('skill_listing')
        ->name('skills.inquiry');
});

// スキル購入後（取引管理・チャット：seller側画面。buyer は下の buyer 専用ルートへ）
Route::middleware(['auth.any:freelancer,company'])->group(function () {
    Route::get('/purchased-skills', [SkillTransactionController::class, 'purchasedSkills'])
        ->name('purchased-skills.index');

    Route::get('/sales-performance', [SkillTransactionController::class, 'salesPerformance'])
        ->name('sales-performance.index');

    Route::get('/transactions/{skill_order}', [SkillTransactionController::class, 'show'])
        ->whereNumber('skill_order')
        ->name('transactions.show');

    Route::post('/transactions/{skill_order}/messages', [SkillTransactionController::class, 'storeMessage'])
        ->whereNumber('skill_order')
        ->name('transactions.messages.store');

    Route::post('/transactions/{skill_order}/deliver', [SkillTransactionController::class, 'deliver'])
        ->whereNumber('skill_order')
        ->name('transactions.deliver');

    Route::post('/transactions/{skill_order}/complete', [SkillTransactionController::class, 'complete'])
        ->whereNumber('skill_order')
        ->name('transactions.complete');
});

// buyer のスキル購入後（取引管理）
Route::middleware(['auth:buyer', 'buyer'])->group(function () {
    Route::get('/buyer/purchased-skills', [SkillTransactionController::class, 'purchasedSkills'])
        ->name('buyer.purchased-skills.index');

    Route::get('/buyer/transactions/{skill_order}', [SkillTransactionController::class, 'show'])
        ->whereNumber('skill_order')
        ->name('buyer.transactions.show');

    Route::post('/buyer/transactions/{skill_order}/messages', [SkillTransactionController::class, 'storeMessage'])
        ->whereNumber('skill_order')
        ->name('buyer.transactions.messages.store');

    // buyer は「完了」側を操作する（deliver は seller 専用）
    Route::post('/buyer/transactions/{skill_order}/complete', [SkillTransactionController::class, 'complete'])
        ->whereNumber('skill_order')
        ->name('buyer.transactions.complete');
});