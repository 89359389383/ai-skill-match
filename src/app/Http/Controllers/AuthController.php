<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * ログイン画面を表示する（表示のみ）
     */
    public function showLoginForm(Request $request)
    {
        $redirect = $request->query('redirect');

        if (is_string($redirect) && $redirect !== '') {
            $host = parse_url($redirect, PHP_URL_HOST);
            $isInternal = str_starts_with($redirect, '/') || $host === $request->getHost();

            if ($isInternal) {
            $request->session()->put('url.intended', $redirect);
            }
        }

        // ログイン画面のBladeを返すだけ（認証処理はしない）
        return view('auth.login');
    }

    /**
     * フリーランスとしてログインする（freelancer guard）
     */
    public function loginFreelancer(LoginRequest $request)
    {
        // 入力を最低限チェックする（FormRequest に委譲）
        $credentials = $request->validated();

        // freelancer guard で認証を試みる
        if (!Auth::guard('freelancer')->attempt($credentials)) {
            throw ValidationException::withMessages(['email' => 'メールアドレスまたはパスワードが正しくありません']);
        }

        // セッション固定化攻撃を防ぐため、ログイン成功時にセッションIDを再生成します。
        // セッション固定化攻撃とは、攻撃者が事前に取得したセッションIDをユーザーに使用させ、
        // そのセッションIDでログインされた状態を乗っ取る攻撃手法です。
        // regenerate()を呼ぶことで、古いセッションIDは無効化され、新しいセッションIDが発行されます。
        // これにより、ログイン前のセッションIDではアクセスできなくなり、セキュリティが向上します。
        $request->session()->regenerate();

        // ログインしたユーザーを取得する
        /** @var User $user */
        $user = Auth::guard('freelancer')->user();

        // 想定外のroleなら安全側に倒してログアウトし、ログインへ戻す
        if (!$user || $user->role !== 'freelancer') {
            Auth::guard('freelancer')->logout();

            // セッションを無効化して安全にする
            $request->session()->invalidate();

            // CSRFトークンも再生成する
            $request->session()->regenerateToken();

            return redirect('/login')->withErrors(['email' => 'フリーランスアカウントではありません']);
        }

        // 元々アクセスしようとしていたURL（例: 質問投稿・記事投稿）があればそこへ、なければ案件一覧へ
        return redirect()->intended(route('freelancer.jobs.index'));
    }

    /**
     * 企業としてログインする（company guard）
     */
    public function loginCompany(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!Auth::guard('company')->attempt($credentials)) {
            throw ValidationException::withMessages(['email' => 'メールアドレスまたはパスワードが正しくありません']);
        }

        $request->session()->regenerate();

        /** @var User $user */
        $user = Auth::guard('company')->user();

        if (!$user || $user->role !== 'company') {
            Auth::guard('company')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/login')->withErrors(['email' => '企業アカウントではありません']);
        }

        // 元々アクセスしようとしていたURL（例: 質問投稿・記事投稿）があればそこへ、なければフリーランス一覧へ
        return redirect()->intended(route('company.freelancers.index'));
    }

    /**
     * フリーランス登録画面を表示する（表示のみ）
     */
    public function showFreelancerRegister()
    {
        // 登録画面のBladeを返すだけ（DB更新はしない）
        return view('auth.register.freelancer');
    }

    /**
     * フリーランスユーザーを作成し、プロフィール入力へ遷移する
     */
    public function storeFreelancer(RegisterRequest $request)
    {
        // 入力をバリデーションする（RegisterRequest に委譲）
        $validated = $request->validated();

        // ユーザーを作成する（role=freelancer）
        $user = User::create([
            // メールアドレスを保存する
            'email' => $validated['email'],
            // パスワードをハッシュ化して保存する（平文では保存しない）
            'password' => Hash::make($validated['password']),
            // 役割をフリーランスにする
            'role' => 'freelancer',
        ]);

        // 作成したユーザーでログインさせる
        Auth::guard('freelancer')->login($user);
        $request->session()->regenerate();

        // プロフィール登録画面へリダイレクトする（/freelancer/profile 相当）
        return redirect('/freelancer/profile');
    }

    /**
     * 企業登録画面を表示する（表示のみ）
     */
    public function showCompanyRegister(Request $request)
    {
        $sessionCookieName = (string) config('session.cookie');
        $resolvedSlot = $request->attributes->get('resolved_slot');
        $slotAttr = $request->attributes->get('slot');

        Log::info('[企業登録] 画面表示 GET /register/company 開始', [
            'timestamp' => now()->toDateTimeString(),
            'ip' => $request->ip(),
            'url' => $request->fullUrl(),
            'path' => $request->path(),
            'user_agent' => $request->userAgent(),
            'referer' => $request->header('Referer'),
            'accept_language' => $request->header('Accept-Language'),
            'query_keys' => array_keys($request->query->all()),
            'slot_query' => $request->query('slot'),
            'slot_sanitized_attr' => $slotAttr,
            'resolved_slot_raw' => is_string($resolvedSlot) ? $resolvedSlot : null,
            'session_cookie_name' => $sessionCookieName,
            'laravel_session_cookie_present' => $request->cookies->has($sessionCookieName),
        ]);

        // セッション情報（値は載せずキーと長さのみ・419 調査用）
        $sessionData = ['session_driver' => config('session.driver')];
        if ($request->hasSession()) {
            try {
                $all = $request->session()->all();
                $sessionData += [
                    'session_exists' => true,
                    'session_id' => $request->session()->getId(),
                    'session_data_keys' => $all ? array_keys($all) : [],
                    'csrf_token_length' => strlen((string) $request->session()->token()),
                ];
            } catch (\Throwable $e) {
                $sessionData += [
                    'session_exists' => true,
                    'session_read_error' => $e->getMessage(),
                ];
            }
        } else {
            $sessionData['session_exists'] = false;
        }
        Log::info('[企業登録] 画面表示 セッション概要', $sessionData);

        $isAuthenticated = Auth::check();
        $user = Auth::user();
        Log::info('[企業登録] 画面表示 認証状態', [
            'is_authenticated' => $isAuthenticated,
            'default_guard_check' => Auth::check(),
            'company_guard_check' => Auth::guard('company')->check(),
            'user_id' => $user ? $user->id : null,
            'user_role' => $user ? $user->role : null,
        ]);

        Log::info('[企業登録] 画面表示 Blade 返却', ['view' => 'auth.register.company']);

        return view('auth.register.company');
    }

    /**
     * 企業ユーザーを作成し、企業プロフィール入力へ遷移する
     */
    public function storeCompany(RegisterRequest $request)
    {
        $sessionCookieName = (string) config('session.cookie');

        // ※ FormRequest 検証・passedValidation ログの直後に実行される
        Log::info('[企業登録] アカウント作成 storeCompany 処理開始（RegisterRequest 検証済み）', [
            'timestamp' => now()->toDateTimeString(),
            'ip' => $request->ip(),
            'url' => $request->fullUrl(),
            'path' => $request->path(),
            'referer' => $request->header('Referer'),
            'user_agent' => $request->userAgent(),
            'slot_query' => $request->query('slot'),
            'slot_post_present' => $request->has('slot'),
            'slot_post_length' => $request->has('slot') ? strlen((string) $request->input('slot')) : 0,
            'session_cookie_name' => $sessionCookieName,
            'session_id_before' => $request->hasSession() ? $request->session()->getId() : null,
            'has__token' => $request->has('_token'),
            '_token_length' => $request->has('_token') ? strlen((string) $request->input('_token')) : 0,
            'csrf_token_length_session' => $request->hasSession() ? strlen((string) $request->session()->token()) : null,
        ]);

        // 入力をバリデーションする（RegisterRequest に委譲）
        $validated = $request->validated();

        Log::info('[企業登録] アカウント作成 バリデーション済み（パスワードはログ不出力）', [
            'email' => $validated['email'],
            'password_length' => strlen((string) $validated['password']),
        ]);

        // ユーザーを作成する（role=company）
        $user = User::create([
            // メールアドレスを保存する
            'email' => $validated['email'],
            // パスワードをハッシュ化して保存する
            'password' => Hash::make($validated['password']),
            // 役割を企業にする
            'role' => 'company',
        ]);

        Log::info('[企業登録] アカウント作成 User レコード作成完了', [
            'user_id' => $user->id,
            'role' => $user->role,
        ]);

        // 作成したユーザーでログインさせる
        Auth::guard('company')->login($user);
        $request->session()->regenerate();

        Log::info('[企業登録] アカウント作成 ログイン・セッション再生成完了', [
            'user_id' => $user->id,
            'session_id_after_regenerate' => $request->session()->getId(),
            'company_guard_check' => Auth::guard('company')->check(),
            'default_guard_user_id' => Auth::user()?->id,
        ]);

        $redirectTo = '/company/profile';
        Log::info('[企業登録] アカウント作成 リダイレクト', [
            'to' => $redirectTo,
            'intended' => $request->session()->get('url.intended'),
        ]);

        // 企業プロフィール登録画面へ遷移する（/company/profile 相当）
        return redirect($redirectTo);
    }

    /**
     * ログアウト処理
     */
    public function logout(Request $request)
    {
        // どちらでログインしていても確実に落とす（同時ログインは要件外だが、安全側）
        Auth::guard('freelancer')->logout();
        Auth::guard('company')->logout();
        Auth::logout();

        // セッションを無効化して安全にする
        $request->session()->invalidate();

        // CSRFトークンも再生成する
        $request->session()->regenerateToken();

        // ログイン画面へ戻す
        return redirect('/login');
    }

    /**
     * ===============================
     * パスワード再設定フォーム表示
     * URL: /forgot-password
     * メソッド: GET
     * ===============================
     */
    public function showForgotPasswordForm()
    {
        // パスワード再設定ページ（auth/forgot-password.blade.php）を表示します。
        return view('auth.forgot-password');
    }

    /**
     * ===============================
     * パスワード再設定リンク送信処理
     * URL: /forgot-password
     * メソッド: POST
     * ===============================
     */
    public function sendResetLink(Request $request)
    {
        // メソッド開始時のログ
        Log::info('パスワード再設定リンク送信処理: 開始', [
            'email' => $request->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toDateTimeString(),
        ]);

        try {
            // メール設定情報をログに記録（パスワードは除く）
            $mailConfig = config('mail');
            Log::info('パスワード再設定リンク送信処理: メール設定情報', [
                'mail_driver' => $mailConfig['default'] ?? 'unknown',
                'mail_host' => $mailConfig['mailers']['smtp']['host'] ?? 'unknown',
                'mail_port' => $mailConfig['mailers']['smtp']['port'] ?? 'unknown',
                'mail_username' => $mailConfig['mailers']['smtp']['username'] ?? 'unknown',
                'mail_encryption' => $mailConfig['mailers']['smtp']['encryption'] ?? 'unknown',
                'mail_from_address' => $mailConfig['from']['address'] ?? 'unknown',
                'mail_from_name' => $mailConfig['from']['name'] ?? 'unknown',
            ]);

            // 入力されたメールアドレスが正しい形式かどうかを検証します。
            Log::info('パスワード再設定リンク送信処理: バリデーション開始', [
                'email' => $request->email,
            ]);

            $request->validate([
                'email' => 'required|email' // 必須項目であり、メールアドレス形式である必要があります。
            ]);

            Log::info('パスワード再設定リンク送信処理: バリデーション成功', [
                'email' => $request->email,
            ]);

            // ユーザーが存在するか確認
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                Log::warning('パスワード再設定リンク送信処理: ユーザーが見つかりません', [
                    'email' => $request->email,
                ]);
            } else {
                Log::info('パスワード再設定リンク送信処理: ユーザーが見つかりました', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name,
                ]);
            }

            // パスワード再設定リンクを送信します。
            Log::info('パスワード再設定リンク送信処理: Password::sendResetLink呼び出し開始', [
                'email' => $request->email,
            ]);

            $status = Password::sendResetLink(
                $request->only('email') // リクエストからメールアドレスのみを取得します。
            );

            Log::info('パスワード再設定リンク送信処理: Password::sendResetLink呼び出し完了', [
                'email' => $request->email,
                'status' => $status,
                'status_code' => $status === Password::RESET_LINK_SENT ? 'RESET_LINK_SENT' : 'OTHER',
            ]);

            // リンク送信の結果に応じて、成功またはエラーメッセージを返します。
            if ($status === Password::RESET_LINK_SENT) {
                Log::info('パスワード再設定リンク送信処理: 成功', [
                    'email' => $request->email,
                    'status' => 'RESET_LINK_SENT',
                ]);
                return back()->with('status', 'パスワード再設定用メールを送信しました。'); // 成功時のメッセージ
            } else {
                Log::warning('パスワード再設定リンク送信処理: 失敗（メール送信失敗）', [
                    'email' => $request->email,
                    'status' => $status,
                ]);
                return back()->withErrors(['email' => 'メールアドレスが登録されていません。']); // 失敗時のエラーメッセージ
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            // バリデーションエラー
            Log::warning('パスワード再設定リンク送信処理: バリデーションエラー', [
                'email' => $request->email,
                'errors' => $e->errors(),
            ]);
            throw $e; // バリデーションエラーは再スロー
        } catch (\Swift_TransportException $e) {
            // SMTP関連のエラー（SwiftMailerの例外）
            Log::error('パスワード再設定リンク送信処理: SMTP送信エラー', [
                'email' => $request->email,
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString(),
                'mail_config' => [
                    'host' => config('mail.mailers.smtp.host'),
                    'port' => config('mail.mailers.smtp.port'),
                    'username' => config('mail.mailers.smtp.username'),
                    'encryption' => config('mail.mailers.smtp.encryption'),
                ],
            ]);
            return back()->withErrors(['email' => 'メール送信に失敗しました。しばらく時間をおいて再度お試しください。']);
        } catch (\Throwable $e) {
            // その他の予期しないエラー
            Log::error('パスワード再設定リンク送信処理: 予期しないエラー', [
                'email' => $request->email,
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString(),
                'error_class' => get_class($e),
                'request_data' => $request->all(),
            ]);
            return back()->withErrors(['email' => 'エラーが発生しました。しばらく時間をおいて再度お試しください。']);
        }
    }

    /**
     * ===============================
     * パスワードリセットフォーム表示
     * URL: /reset-password/{token}
     * メソッド: GET
     * ===============================
     */
    public function showResetForm($token)
    {
        // パスワードリセットページ（auth/reset-password.blade.php）を表示し、トークンを渡します。
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * ===============================
     * パスワードリセット処理
     * URL: /reset-password
     * メソッド: POST
     * ===============================
     */
    public function resetPassword(Request $request)
    {
        // 入力内容の検証を行います。
        Log::info('パスワードリセット処理: 入力内容の検証を開始', ['request_data' => $request->all()]);
        $request->validate([
            'token' => 'required', // パスワードリセット用のトークンが必須です。
            'email' => 'required|email', // メールアドレスが必須で、正しい形式である必要があります。
            'password' => 'required|min:8|confirmed', // パスワードは8文字以上で、確認用と一致する必要があります。
        ]);
        Log::info('パスワードリセット処理: 入力内容の検証が完了');

        // パスワードリセットを実行します。
        Log::info('パスワードリセット処理: リセット処理を開始', ['email' => $request->email]);
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'), // 必要なデータを取得
            function ($user, $password) {
                Log::info('パスワードリセット処理: ユーザーのパスワードを更新', ['user_id' => $user->id]);
                // ユーザーのパスワードを更新します。
                $user->password = bcrypt($password); // パスワードをハッシュ化して保存
                $user->save(); // ユーザー情報を保存
                Log::info('パスワードリセット処理: ユーザーのパスワードを保存完了', ['user_id' => $user->id]);
            }
        );

        // リセット処理の結果に応じて、成功またはエラーメッセージを返します。
        if ($status === Password::PASSWORD_RESET) {
            Log::info('パスワードリセット処理: 成功', ['email' => $request->email]);
            return redirect()->route('auth.login.form')->with('status', 'パスワードを再設定しました。')->with('alert', 'success'); // 成功時のリダイレクト
        } else {
            Log::warning('パスワードリセット処理: 失敗', ['email' => $request->email]);
            return back()->withErrors(['email' => 'エラーが発生しました。']); // 失敗時のエラーメッセージ
        }
    }
}