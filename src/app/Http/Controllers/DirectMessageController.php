<?php

namespace App\Http\Controllers;

use App\Http\Requests\DirectMessageRequest;
use App\Models\DirectConversation;
use App\Models\DirectConversationMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class DirectMessageController extends Controller
{
    // #region debug NDJSON logger
    private function debugNDJSON(string $hypothesisId, string $location, string $message, array $data = []): void
    {
        try {
            $debugLogPath = base_path('debug-f98607.log');
            $payload = [
                'sessionId' => 'f98607',
                'id' => uniqid('log_', true),
                'timestamp' => (int) floor(microtime(true) * 1000),
                'location' => $location,
                'message' => $message,
                'data' => $data,
                'runId' => 'pre_403_check',
                'hypothesisId' => $hypothesisId,
            ];

            file_put_contents(
                $debugLogPath,
                json_encode($payload, JSON_UNESCAPED_UNICODE) . PHP_EOL,
                FILE_APPEND | LOCK_EX
            );
        } catch (\Throwable $e) {
            // 計測失敗は本番挙動に影響させない
        }
    }
    // #endregion debug NDJSON logger

    public function index(Request $request)
    {
        $user = Auth::user();
        Log::info('[DirectMessageController@index] メッセージ一覧開始', [
            'user_id' => $user?->id,
            'user_role' => $user?->role,
        ]);

        if (!$user) {
            Log::warning('[DirectMessageController@index] 未ログイン');
            return redirect()->route('auth.login.form');
        }

        $role = $this->resolveRole($user);
        if ($role === null) {
            Log::error('[DirectMessageController@index] ロール解決失敗');
            abort(403);
        }

        $profile = $role === 'company' ? $user->company : $user->freelancer;
        Log::info('[DirectMessageController@index] プロフィール取得', [
            'role' => $role,
            'has_profile' => $profile !== null,
            'profile_id' => $profile?->id,
        ]);

        if ($profile === null) {
            Log::warning('[DirectMessageController@index] プロフィール未登録');
            return redirect($role === 'company' ? '/company/profile' : '/freelancer/profile')
                ->with('error', '先にプロフィール登録が必要です');
        }

        $filter = $request->query('filter', 'all');

        // 同じrole同士の会話も検索できるように、company_id / freelancer_id / initiator_id をチェック
        $profileClass = get_class($profile);
        $profileType = $profileClass === 'App\Models\Company' ? 'company' : 'freelancer';

        // 重要:
        // companyが閲覧しているのに freelancer_id を company.id で突合してしまうと、
        // 別ユーザー側の会話まで混ざる（ID空間が一致/重複し得るため）。
        // そのため、viewerが関与する参加軸（company_id or freelancer_id）を role ごとに固定する。
        $baseQuery = DirectConversation::query()
            ->where(function ($q) use ($profile) {
                if ($profile instanceof \App\Models\Company) {
                    $q->where('company_id', $profile->id)
                      ->orWhere(function ($sq) use ($profile) {
                          $sq->where('initiator_id', $profile->id)
                             ->where('initiator_type', 'company');
                      });
                } else {
                    $q->where('freelancer_id', $profile->id)
                      ->orWhere(function ($sq) use ($profile) {
                          $sq->where('initiator_id', $profile->id)
                             ->where('initiator_type', 'freelancer');
                      });
                }
            })
            ->with([
                'company',
                'freelancer',
                'messages' => function ($q) {
                    $q->orderBy('sent_at');
                },
            ]);

        $allCount = (clone $baseQuery)->count();
        $unreadCountQuery = clone $baseQuery;

        if ($role === 'freelancer') {
            // `is_unread_for_freelancer` は「最新メッセージの受信者」が未読かどうか。
            // 一覧/未読数では、受信者が“閲覧者本人”かを追加で判定する。
            $unreadCountQuery
                ->where('is_unread_for_freelancer', true)
                ->whereRaw(
                    'CASE
                        WHEN company_id IS NULL THEN
                            CASE
                                WHEN latest_sender_id = freelancer_id THEN initiator_id
                                ELSE freelancer_id
                            END
                        ELSE freelancer_id
                    END = ?',
                    [$profile->id]
                );
        } else {
            // 企業側: viewer companyが関与している会話のみを未読判定する
            // （freelancer_id = company.id を使う分岐は削除して漏れを防ぐ）
            $unreadCountQuery = (clone $baseQuery)
                ->where('is_unread_for_company', true)
                ->where(function ($q) use ($profile) {
                    $q->where('company_id', $profile->id)
                      ->orWhere(function ($sq) use ($profile) {
                          $sq->where('initiator_id', $profile->id)
                             ->where('initiator_type', 'company');
                      });
                });
        }

        $unreadCount = $unreadCountQuery->count();

        $query = (clone $baseQuery)
            ->orderByDesc('latest_message_at')
            ->orderByDesc('id');

        if ($filter === 'unread') {
            if ($role === 'freelancer') {
                $query->where('is_unread_for_freelancer', true)
                    ->whereRaw(
                        'CASE
                            WHEN company_id IS NULL THEN
                                CASE
                                    WHEN latest_sender_id = freelancer_id THEN initiator_id
                                    ELSE freelancer_id
                                END
                            ELSE freelancer_id
                        END = ?',
                        [$profile->id]
                    );
            } else {
                // 企業側の未読フィルタも同様に viewer company が関与しているものだけに限定
                $query->where('is_unread_for_company', true)
                    ->where(function ($q) use ($profile) {
                        $q->where('company_id', $profile->id)
                          ->orWhere(function ($sq) use ($profile) {
                              $sq->where('initiator_id', $profile->id)
                                 ->where('initiator_type', 'company');
                          });
                    });
            }
        }

        $conversations = $query->paginate(20)->withQueryString();

        Log::info('[DirectMessageController@index] 一覧取得完了', [
            'conversation_count' => $conversations->count(),
            'all_count' => $allCount,
            'unread_count' => $unreadCount,
            'viewer_role' => $role,
        ]);

        // #region agent debug log - view selection
        $this->debugNDJSON(
            'H403_view_selection',
            'DirectMessageController@index',
            'view_selection',
            [
                'viewer_role' => $role,
                'selected_view' => $role === 'company' ? 'company.direct-messages.index' : 'freelancer.direct-messages.index',
            ]
        );
        // #endregion agent debug log

        // roleに応じたビューを返す
        $viewName = $role === 'company' ? 'company.direct-messages.index' : 'freelancer.direct-messages.index';

        return view($viewName, [
            'conversations' => $conversations,
            'filter' => $filter,
            'viewerRole' => $role,
            'viewerProfile' => $profile,
            'allCount' => $allCount,
            'unreadCount' => $unreadCount,
        ]);
    }

    public function show(Request $request, DirectConversation $directConversation)
    {
        $user = Auth::user();
        Log::info('[DirectMessageController@show] 会話表示開始', [
            'user_id' => $user?->id,
            'user_role' => $user?->role,
            'conversation_id' => $directConversation->id,
            'company_id' => $directConversation->company_id,
            'freelancer_id' => $directConversation->freelancer_id,
        ]);

        if (!$user) {
            Log::warning('[DirectMessageController@show] 未ログイン');
            return redirect()->route('auth.login.form');
        }

        $role = $this->resolveRole($user);
        if ($role === null) {
            Log::error('[DirectMessageController@show] ロール解決失敗', [
                'user_id' => $user->id,
                'user_role' => $user->role,
            ]);
            abort(403);
        }

        $profile = $role === 'company' ? $user->company : $user->freelancer;
        Log::info('[DirectMessageController@show] プロフィール取得', [
            'role' => $role,
            'has_profile' => $profile !== null,
            'profile_id' => $profile?->id,
        ]);

        if ($profile === null) {
            Log::warning('[DirectMessageController@show] プロフィール未登録');
            return redirect($role === 'company' ? '/company/profile' : '/freelancer/profile')
                ->with('error', '先にプロフィール登録が必要です');
        }

        $isParticipant = $this->checkParticipant($directConversation, $role, $profile->id);
        Log::info('[DirectMessageController@show] 参加権限チェック', [
            'is_participant' => $isParticipant,
            'role' => $role,
            'profile_id' => $profile->id,
        ]);

        // #region agent debug log
        $this->debugNDJSON(
            'H403_show_participant_inputs',
            'DirectMessageController@show',
            'participant_check_inputs',
            [
                'conversation_id' => $directConversation->id,
                'viewer_role' => $role,
                'viewer_profile_id' => $profile->id,
                'company_id' => $directConversation->company_id,
                'freelancer_id' => $directConversation->freelancer_id,
                'initiator_type' => $directConversation->initiator_type,
                'initiator_id' => $directConversation->initiator_id,
                'latest_sender_type' => $directConversation->latest_sender_type,
                'latest_sender_id' => $directConversation->latest_sender_id,
                'is_participant' => $isParticipant,
            ]
        );
        // #endregion agent debug log

        if (!$isParticipant) {
            Log::error('[DirectMessageController@show] 参加権限なし', [
                'user_id' => $user->id,
                'profile_id' => $profile->id,
                'role' => $role,
                'conversation_company_id' => $directConversation->company_id,
                'conversation_freelancer_id' => $directConversation->freelancer_id,
            ]);
            abort(403);
        }

        $directConversation->load([
            'company',
            'freelancer',
            'messages',
        ]);

        $this->markRead($directConversation, $role, $profile->id);

        Log::info('[DirectMessageController@show] 会話表示完了', [
            'conversation_id' => $directConversation->id,
            'message_count' => $directConversation->messages->count(),
            'viewer_role' => $role,
        ]);

        // #region agent debug log - view selection
        $this->debugNDJSON(
            'H403_view_selection_show',
            'DirectMessageController@show',
            'view_selection',
            [
                'viewer_role' => $role,
                'selected_view' => $role === 'company' ? 'company.direct-messages.show' : 'freelancer.direct-messages.show',
            ]
        );
        // #endregion agent debug log

        // roleに応じたビューを返す
        $viewName = $role === 'company' ? 'company.direct-messages.show' : 'freelancer.direct-messages.show';

        return view($viewName, [
            'conversation' => $directConversation,
            'messages' => $directConversation->messages,
            'viewerRole' => $role,
            'viewerProfile' => $profile,
        ]);
    }

    public function start(DirectMessageRequest $request, User $user)
    {
        $currentUser = Auth::user();
        Log::info('[DirectMessageController@start] メッセージ送信開始', [
            'current_user_id' => $currentUser?->id,
            'current_user_role' => $currentUser?->role,
            'target_user_id' => $user->id,
            'target_user_role' => $user->role,
        ]);

        // #region agent debug log
        $this->debugNDJSON(
            'H403_flow_start_called',
            'DirectMessageController@start',
            'start_called',
            [
                'current_user_id' => $currentUser?->id,
                'current_user_role' => $currentUser?->role,
                'target_user_id' => $user->id,
                'target_user_role' => $user->role,
            ]
        );
        // #endregion agent debug log

        if (!$currentUser) {
            Log::warning('[DirectMessageController@start] 未ログイン状態でアクセス');
            return redirect()->route('auth.login.form');
        }

        $currentRole = $this->resolveRole($currentUser);
        Log::info('[DirectMessageController@start] 現在のユーザーロール解決', [
            'current_role' => $currentRole,
        ]);

        if ($currentRole === null) {
            Log::error('[DirectMessageController@start] ロール解決失敗', [
                'user_id' => $currentUser->id,
                'user_role' => $currentUser->role,
            ]);
            abort(403);
        }

        $currentProfile = $currentRole === 'company' ? $currentUser->company : $currentUser->freelancer;
        Log::info('[DirectMessageController@start] プロフィール取得', [
            'current_role' => $currentRole,
            'has_profile' => $currentProfile !== null,
            'profile_id' => $currentProfile?->id,
        ]);

        if ($currentProfile === null) {
            Log::warning('[DirectMessageController@start] プロフィール未登録', [
                'user_id' => $currentUser->id,
                'role' => $currentRole,
            ]);
            return redirect($currentRole === 'company' ? '/company/profile' : '/freelancer/profile')
                ->with('error', '先にプロフィール登録が必要です');
        }

        $counterpart = $this->resolveCounterpart($currentRole, $user);
        Log::info('[DirectMessageController@start] 相手プロフィール解決', [
            'counterpart_found' => $counterpart !== null,
            'counterpart_type' => $counterpart ? get_class($counterpart) : null,
            'counterpart_id' => $counterpart?->id,
        ]);

        if ($counterpart === null) {
            Log::error('[DirectMessageController@start] 相手プロフィールが見つからない', [
                'current_role' => $currentRole,
                'target_user_id' => $user->id,
                'target_user_role' => $user->role,
            ]);
            abort(404);
        }

        $validated = $request->validated();
        Log::info('[DirectMessageController@start] バリデーション通過', [
            'content_length' => strlen($validated['content'] ?? ''),
        ]);

        // #region agent debug log
        $this->debugNDJSON(
            'H403_flow_validated',
            'DirectMessageController@start',
            'validated',
            [
                'content_length' => strlen($validated['content'] ?? ''),
                'current_role' => $currentRole,
                'current_profile_id' => $currentProfile?->id,
                'counterpart_user_id' => $user->id,
                'counterpart_user_role' => $user->role,
                'counterpart_profile_id' => $counterpart?->id,
                'counterpart_profile_class' => $counterpart ? get_class($counterpart) : null,
            ]
        );
        // #endregion agent debug log

        $conversation = DB::transaction(function () use ($currentRole, $currentProfile, $counterpart, $validated) {
            // 異なるrole間の場合: 従来通り
            // 同じrole同士の場合: 片方をNULLにする（テーブル制約対応）
            $companyId = null;
            $freelancerId = null;
            $isSameRoleConversation = false;

            if ($currentProfile instanceof \App\Models\Company && $counterpart instanceof \App\Models\Company) {
                // 企業→企業: company_idに相手を入れる、freelancer_idはNULL
                $companyId = $counterpart->id;
                $freelancerId = null;
                $isSameRoleConversation = true;
                Log::info('[DirectMessageController@start] 企業→企業', [
                    'company_id' => $companyId,
                    'freelancer_id' => $freelancerId,
                ]);
            } elseif ($currentProfile instanceof \App\Models\Freelancer && $counterpart instanceof \App\Models\Freelancer) {
                // フリーランス→フリーランス: freelancer_idに相手を入れる、company_idはNULL
                $companyId = null;
                $freelancerId = $counterpart->id;
                $isSameRoleConversation = true;
                Log::info('[DirectMessageController@start] フリーランス→フリーランス', [
                    'company_id' => $companyId,
                    'freelancer_id' => $freelancerId,
                ]);
            } else {
                // 異なるrole間（従来通り）
                $companyId = $currentProfile instanceof \App\Models\Company ? $currentProfile->id : $counterpart->id;
                $freelancerId = $currentProfile instanceof \App\Models\Freelancer ? $currentProfile->id : $counterpart->id;
                Log::info('[DirectMessageController@start] 異なるrole間', [
                    'company_id' => $companyId,
                    'freelancer_id' => $freelancerId,
                ]);
            }

            $senderId = $currentProfile->id;

            Log::info('[DirectMessageController@start] 会話作成/検索', [
                'company_id' => $companyId,
                'freelancer_id' => $freelancerId,
                'is_same_role' => $isSameRoleConversation,
            ]);

            // #region agent debug log
            $this->debugNDJSON(
                'H403_flow_conversation_key',
                'DirectMessageController@start',
                'conversation_key_pre',
                [
                    'company_id' => $companyId,
                    'freelancer_id' => $freelancerId,
                    'is_same_role' => $isSameRoleConversation,
                    'sender_profile_id' => $senderId,
                    'current_role' => $currentRole,
                    'counterpart_profile_id' => $counterpart->id,
                    'counterpart_profile_class' => get_class($counterpart),
                ]
            );
            // #endregion agent debug log

            // 同じrole同士の場合、一意性制約が効かないため手動で検索
            // ※送信者を initiator_id として別カラムで追跡し、参加者権限チェックで使用
            if ($isSameRoleConversation) {
                $query = DirectConversation::query()
                    ->where('company_id', $companyId)
                    ->where('freelancer_id', $freelancerId);

                // 送信者が自分の場合の追加条件（双方向の会話を区別）
                $query->where(function ($q) use ($currentRole, $senderId) {
                    $q->where('latest_sender_type', $currentRole)
                      ->where('latest_sender_id', $senderId);
                });

                $conversation = $query->first();

                if (!$conversation) {
                    // 同じrole同士の場合、initiatorとして送信者を保存し、相手はcompany_id/freelancer_idに保存
                    $conversation = DirectConversation::create([
                        'company_id' => $companyId,
                        'freelancer_id' => $freelancerId,
                        'initiator_type' => $currentRole,
                        'initiator_id' => $senderId,
                        'latest_sender_type' => $currentRole,
                        'latest_sender_id' => $senderId,
                        'latest_message_at' => Carbon::now(),
                        'is_unread_for_company' => $currentRole !== 'company',
                        'is_unread_for_freelancer' => $currentRole !== 'freelancer',
                    ]);
                    Log::info('[DirectMessageController@start] 新規会話作成（同じrole）', [
                        'conversation_id' => $conversation->id,
                        'saved_company_id' => $companyId,
                        'saved_freelancer_id' => $freelancerId,
                        'initiator_id' => $senderId,
                        'initiator_type' => $currentRole,
                    ]);
                } else {
                    Log::info('[DirectMessageController@start] 既存会話発見（同じrole）', [
                        'conversation_id' => $conversation->id,
                    ]);
                }
            } else {
                // 異なるrole間は従来通りfirstOrCreate
                $conversation = DirectConversation::query()->firstOrCreate(
                    [
                        'company_id' => $companyId,
                        'freelancer_id' => $freelancerId,
                    ],
                    [
                        'latest_sender_type' => $currentRole,
                        'latest_sender_id' => $senderId,
                        'latest_message_at' => Carbon::now(),
                        'is_unread_for_company' => $currentRole !== 'company',
                        'is_unread_for_freelancer' => $currentRole !== 'freelancer',
                    ]
                );

                Log::info('[DirectMessageController@start] 会話取得/作成完了', [
                    'conversation_id' => $conversation->id,
                    'was_recently_created' => $conversation->wasRecentlyCreated,
                ]);
            }

            // #region agent debug log
            $this->debugNDJSON(
                'H403_flow_conversation_after',
                'DirectMessageController@start',
                'conversation_after_create_or_find',
                [
                    'conversation_id' => $conversation->id,
                    'company_id' => $conversation->company_id,
                    'freelancer_id' => $conversation->freelancer_id,
                    'initiator_type' => $conversation->initiator_type,
                    'initiator_id' => $conversation->initiator_id,
                    'latest_sender_type' => $conversation->latest_sender_type,
                    'latest_sender_id' => $conversation->latest_sender_id,
                    'is_same_role' => $isSameRoleConversation,
                ]
            );
            // #endregion agent debug log

            $this->sendMessage($conversation, $currentRole, $senderId, $validated['content']);

            return $conversation;
        });

        $redirectUrl = route('direct-messages.show', ['direct_conversation' => $conversation->id]);
        Log::info('[DirectMessageController@start] リダイレクト', [
            'conversation_id' => $conversation->id,
            'redirect_url' => $redirectUrl,
        ]);

        // #region agent debug log
        $this->debugNDJSON(
            'H403_flow_redirect_after_send',
            'DirectMessageController@start',
            'redirect_after_send',
            [
                'conversation_id' => $conversation->id,
                'redirect_url' => $redirectUrl,
            ]
        );
        // #endregion agent debug log

        return redirect()
            ->route('direct-messages.show', ['direct_conversation' => $conversation->id])
            ->with('success', 'メッセージを送信しました');
    }

    public function reply(DirectMessageRequest $request, DirectConversation $directConversation)
    {
        $user = Auth::user();
        Log::info('[DirectMessageController@reply] 返信開始', [
            'user_id' => $user?->id,
            'conversation_id' => $directConversation->id,
        ]);

        if (!$user) {
            Log::warning('[DirectMessageController@reply] 未ログイン');
            return redirect()->route('auth.login.form');
        }

        $role = $this->resolveRole($user);
        if ($role === null) {
            Log::error('[DirectMessageController@reply] ロール解決失敗');
            abort(403);
        }

        $profile = $role === 'company' ? $user->company : $user->freelancer;
        if ($profile === null) {
            Log::warning('[DirectMessageController@reply] プロフィール未登録');
            return redirect($role === 'company' ? '/company/profile' : '/freelancer/profile')
                ->with('error', '先にプロフィール登録が必要です');
        }

        $this->assertParticipant($directConversation, $role, $profile->id);

        $validated = $request->validated();
        Log::info('[DirectMessageController@reply] 返信処理', [
            'profile_id' => $profile->id,
            'role' => $role,
            'content_length' => strlen($validated['content'] ?? ''),
        ]);

        DB::transaction(function () use ($directConversation, $role, $profile, $validated) {
            $this->sendMessage($directConversation, $role, $profile->id, $validated['content']);
        });

        Log::info('[DirectMessageController@reply] 返信完了');

        return redirect()
            ->route('direct-messages.show', ['direct_conversation' => $directConversation->id])
            ->with('success', 'メッセージを送信しました');
    }

    private function resolveRole(User $user): ?string
    {
        if ($user->role === 'company') {
            return 'company';
        }

        if ($user->role === 'freelancer') {
            return 'freelancer';
        }

        return null;
    }

    private function resolveCounterpart(string $currentRole, User $user): ?object
    {
        Log::debug('[DirectMessageController@resolveCounterpart] 相手解決開始', [
            'current_role' => $currentRole,
            'target_user_id' => $user->id,
            'target_user_role' => $user->role,
        ]);

        // フリーランス→フリーランス
        if ($currentRole === 'freelancer' && $user->role === 'freelancer') {
            $profile = $user->freelancer;
            Log::debug('[DirectMessageController@resolveCounterpart] フリーランス→フリーランス', [
                'found' => $profile !== null,
                'profile_id' => $profile?->id,
            ]);
            return $profile;
        }

        // 企業→企業
        if ($currentRole === 'company' && $user->role === 'company') {
            $profile = $user->company;
            Log::debug('[DirectMessageController@resolveCounterpart] 企業→企業', [
                'found' => $profile !== null,
                'profile_id' => $profile?->id,
            ]);
            return $profile;
        }

        // 企業→フリーランス
        if ($currentRole === 'company' && $user->role === 'freelancer') {
            $profile = $user->freelancer;
            Log::debug('[DirectMessageController@resolveCounterpart] 企業→フリーランス', [
                'found' => $profile !== null,
                'profile_id' => $profile?->id,
            ]);
            return $profile;
        }

        // フリーランス→企業
        if ($currentRole === 'freelancer' && $user->role === 'company') {
            $profile = $user->company;
            Log::debug('[DirectMessageController@resolveCounterpart] フリーランス→企業', [
                'found' => $profile !== null,
                'profile_id' => $profile?->id,
            ]);
            return $profile;
        }

        Log::warning('[DirectMessageController@resolveCounterpart] 不明な組み合わせ', [
            'current_role' => $currentRole,
            'target_user_role' => $user->role,
        ]);

        return null;
    }

    private function assertParticipant(DirectConversation $conversation, string $role, int $profileId): void
    {
        if (!$this->checkParticipant($conversation, $role, $profileId)) {
            Log::error('[DirectMessageController@assertParticipant] 参加権限なし', [
                'conversation_id' => $conversation->id,
                'role' => $role,
                'profile_id' => $profileId,
                'conversation_company_id' => $conversation->company_id,
                'conversation_freelancer_id' => $conversation->freelancer_id,
                'conversation_initiator_id' => $conversation->initiator_id,
                'conversation_initiator_type' => $conversation->initiator_type,
            ]);
            abort(403);
        }
    }

    private function checkParticipant(DirectConversation $conversation, string $role, int $profileId): bool
    {
        // company_id または freelancer_id が一致すれば参加権限あり
        // NULLの場合は比較しない
        $companyId = $conversation->company_id;
        $freelancerId = $conversation->freelancer_id;

        $isCompanyMatch = $companyId !== null && (int) $companyId === $profileId;
        $isFreelancerMatch = $freelancerId !== null && (int) $freelancerId === $profileId;

        // 同じrole同士の会話の場合、initiator_id もチェック
        $isInitiatorMatch = false;
        if ($conversation->initiator_id !== null && $conversation->initiator_type === $role) {
            $isInitiatorMatch = (int) $conversation->initiator_id === $profileId;
        }

        // ひとまず緩める：送信者（latest_sender_*）として紐づいていれば参加可にする
        // （会話作成直後・同ロール同士でDBの双方IDの持ち方が揺れる可能性があるため）
        $isLatestSenderMatch = false;
        if (!empty($conversation->latest_sender_type) && (int) $conversation->latest_sender_id === $profileId) {
            // latest_sender_type が viewer role と一致する場合のみ採用
            $isLatestSenderMatch = $conversation->latest_sender_type === $role;
        }

        Log::debug('[DirectMessageController@checkParticipant] 権限チェック', [
            'conversation_id' => $conversation->id,
            'profile_id' => $profileId,
            'role' => $role,
            'company_id' => $companyId,
            'freelancer_id' => $freelancerId,
            'initiator_id' => $conversation->initiator_id,
            'initiator_type' => $conversation->initiator_type,
            'is_company_match' => $isCompanyMatch,
            'is_freelancer_match' => $isFreelancerMatch,
            'is_initiator_match' => $isInitiatorMatch,
            'latest_sender_type' => $conversation->latest_sender_type,
            'latest_sender_id' => $conversation->latest_sender_id,
            'is_latest_sender_match' => $isLatestSenderMatch,
        ]);

        // #region agent debug log
        $this->debugNDJSON(
            'H403_checkParticipant_result',
            'DirectMessageController@checkParticipant',
            'participant_check_result',
            [
                'conversation_id' => $conversation->id,
                'viewer_role' => $role,
                'viewer_profile_id' => $profileId,
                'company_id' => $companyId,
                'freelancer_id' => $freelancerId,
                'initiator_type' => $conversation->initiator_type,
                'initiator_id' => $conversation->initiator_id,
                'latest_sender_type' => $conversation->latest_sender_type,
                'latest_sender_id' => $conversation->latest_sender_id,
                'is_company_match' => $isCompanyMatch,
                'is_freelancer_match' => $isFreelancerMatch,
                'is_initiator_match' => $isInitiatorMatch,
                'result' => ($isCompanyMatch || $isFreelancerMatch || $isInitiatorMatch),
            ]
        );
        // #endregion agent debug log

        return $isCompanyMatch || $isFreelancerMatch || $isInitiatorMatch || $isLatestSenderMatch;
    }

    private function markRead(DirectConversation $conversation, string $role, int $viewerProfileId): void
    {
        // initiatorベースの会話の場合、initiator_typeに応じて既読処理
        $isInitiatorBased = $conversation->initiator_id !== null && $conversation->initiator_type !== null;

        if ($role === 'company') {
            // 通常の会話: 自分が最後の送信者でなければ既読
            // initiatorベースの会話: initiatorがfreelancerならcompany側は常に既読可能
            if ($conversation->latest_sender_type !== 'company') {
                $conversation->forceFill(['is_unread_for_company' => false])->save();
                Log::debug('[DirectMessageController@markRead] company既読', [
                    'conversation_id' => $conversation->id,
                    'is_initiator_based' => $isInitiatorBased,
                ]);
            }
        }

        if ($role === 'freelancer') {
            if ($conversation->is_unread_for_freelancer) {
                // 「最新メッセージの受信者」が閲覧者なら既読にする。
                $receiverId = null;
                if ($conversation->company_id !== null) {
                    // 企業⇔フリーランス: 受信者は freelancer_id
                    $receiverId = (int) $conversation->freelancer_id;
                } else {
                    // フリーランス⇔フリーランス: 受信者は latest_sender_id と freelancer_id から決定
                    $receiverId = ((int) $conversation->latest_sender_id === (int) $conversation->freelancer_id)
                        ? (int) $conversation->initiator_id
                        : (int) $conversation->freelancer_id;
                }

                if ($receiverId !== null && (int) $viewerProfileId === (int) $receiverId) {
                    $conversation->forceFill(['is_unread_for_freelancer' => false])->save();
                    Log::debug('[DirectMessageController@markRead] freelancer既読（受信者）', [
                        'conversation_id' => $conversation->id,
                        'is_initiator_based' => $isInitiatorBased,
                        'receiver_id' => $receiverId,
                        'viewer_id' => $viewerProfileId,
                    ]);
                }
            }
        }
    }

    private function sendMessage(DirectConversation $conversation, string $senderType, int $senderId, string $body): DirectConversationMessage
    {
        $body = trim($body);

        if ($body === '') {
            Log::warning('[DirectMessageController@sendMessage] 空のメッセージ');
            throw ValidationException::withMessages([
                'content' => 'メッセージを入力してください。',
            ]);
        }

        $now = Carbon::now();

        Log::info('[DirectMessageController@sendMessage] メッセージ作成', [
            'conversation_id' => $conversation->id,
            'sender_type' => $senderType,
            'sender_id' => $senderId,
            'body_length' => strlen($body),
        ]);

        $message = DirectConversationMessage::create([
            'direct_conversation_id' => $conversation->id,
            'sender_type' => $senderType,
            'sender_id' => $senderId,
            'body' => $body,
            'sent_at' => $now,
        ]);

        // 未読状態の計算
        // 通常の会話: senderTypeと異なるroleが未読
        // initiatorベースの会話: initiatorと異なる参加者が未読
        $isUnreadForCompany = $senderType !== 'company';
        $isUnreadForFreelancer = $senderType !== 'freelancer';

        // initiatorベースの会話の場合、initiator_typeに応じて未読状態を調整
        if ($conversation->initiator_id !== null && $conversation->initiator_type !== null) {
            if ($conversation->initiator_type === 'company') {
                // initiatorがcompanyの場合、company側がsenderならinitiatorは未読ではない
                // freelancer側（またはnullの場合の相手）は未読
                $isUnreadForCompany = ($senderType !== 'company');
                $isUnreadForFreelancer = true; // 相手側は常に未読にする
            } elseif ($conversation->initiator_type === 'freelancer') {
                // フリーランス同士: `is_unread_for_freelancer` は「最新メッセージの受信者」が未読かどうか。
                // 送信時点では、受信者はまだページを開いていない前提なので常に true にする。
                $isUnreadForCompany = true;
                $isUnreadForFreelancer = true;
            }
        }

        $conversation->forceFill([
            'latest_sender_type' => $senderType,
            'latest_sender_id' => $senderId,
            'latest_message_at' => $now,
            'is_unread_for_company' => $isUnreadForCompany,
            'is_unread_for_freelancer' => $isUnreadForFreelancer,
        ])->save();

        Log::info('[DirectMessageController@sendMessage] メッセージ作成完了', [
            'message_id' => $message->id,
        ]);

        return $message;
    }
}
