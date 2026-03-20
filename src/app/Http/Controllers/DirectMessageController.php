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
use Illuminate\Validation\ValidationException;

class DirectMessageController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('auth.login.form');
        }

        $role = $this->resolveRole($user);
        if ($role === null) {
            abort(403);
        }

        $profile = $role === 'company' ? $user->company : $user->freelancer;
        if ($profile === null) {
            return redirect($role === 'company' ? '/company/profile' : '/freelancer/profile')
                ->with('error', '先にプロフィール登録が必要です');
        }

        $filter = $request->query('filter', 'all');

        $baseQuery = DirectConversation::query()
            ->where($role === 'company' ? 'company_id' : 'freelancer_id', $profile->id)
            ->with([
                'company',
                'freelancer',
                'messages' => function ($q) {
                    $q->orderBy('sent_at');
                },
            ]);

        $allCount = (clone $baseQuery)->count();
        $unreadCount = (clone $baseQuery)
            ->where($role === 'company' ? 'is_unread_for_company' : 'is_unread_for_freelancer', true)
            ->count();

        $query = (clone $baseQuery)
            ->orderByDesc('latest_message_at')
            ->orderByDesc('id');

        if ($filter === 'unread') {
            $query->where($role === 'company' ? 'is_unread_for_company' : 'is_unread_for_freelancer', true);
        }

        $conversations = $query->paginate(20)->withQueryString();

        return view('direct_messages.index', [
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
        if (!$user) {
            return redirect()->route('auth.login.form');
        }

        $role = $this->resolveRole($user);
        if ($role === null) {
            abort(403);
        }

        $profile = $role === 'company' ? $user->company : $user->freelancer;
        if ($profile === null) {
            return redirect($role === 'company' ? '/company/profile' : '/freelancer/profile')
                ->with('error', '先にプロフィール登録が必要です');
        }

        $this->assertParticipant($directConversation, $role, $profile->id);

        $directConversation->load([
            'company',
            'freelancer',
            'messages',
        ]);

        $this->markRead($directConversation, $role);

        return view('direct_messages.show', [
            'conversation' => $directConversation,
            'messages' => $directConversation->messages,
            'viewerRole' => $role,
            'viewerProfile' => $profile,
        ]);
    }

    public function start(DirectMessageRequest $request, User $user)
    {
        $currentUser = Auth::user();
        if (!$currentUser) {
            return redirect()->route('auth.login.form');
        }

        $currentRole = $this->resolveRole($currentUser);
        if ($currentRole === null) {
            abort(403);
        }

        $currentProfile = $currentRole === 'company' ? $currentUser->company : $currentUser->freelancer;
        if ($currentProfile === null) {
            return redirect($currentRole === 'company' ? '/company/profile' : '/freelancer/profile')
                ->with('error', '先にプロフィール登録が必要です');
        }

        $counterpart = $this->resolveCounterpart($currentRole, $user);
        if ($counterpart === null) {
            abort(404);
        }

        $validated = $request->validated();

        $conversation = DB::transaction(function () use ($currentRole, $currentProfile, $counterpart, $validated) {
            $companyId = $currentRole === 'company' ? $currentProfile->id : $counterpart->id;
            $freelancerId = $currentRole === 'freelancer' ? $currentProfile->id : $counterpart->id;
            $senderId = $currentProfile->id;

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

            $this->sendMessage($conversation, $currentRole, $senderId, $validated['content']);

            return $conversation;
        });

        return redirect()
            ->route('direct-messages.show', ['direct_conversation' => $conversation->id])
            ->with('success', 'メッセージを送信しました');
    }

    public function reply(DirectMessageRequest $request, DirectConversation $directConversation)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('auth.login.form');
        }

        $role = $this->resolveRole($user);
        if ($role === null) {
            abort(403);
        }

        $profile = $role === 'company' ? $user->company : $user->freelancer;
        if ($profile === null) {
            return redirect($role === 'company' ? '/company/profile' : '/freelancer/profile')
                ->with('error', '先にプロフィール登録が必要です');
        }

        $this->assertParticipant($directConversation, $role, $profile->id);

        $validated = $request->validated();

        DB::transaction(function () use ($directConversation, $role, $profile, $validated) {
            $this->sendMessage($directConversation, $role, $profile->id, $validated['content']);
        });

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
        if ($currentRole === 'company') {
            return $user->role === 'freelancer' ? $user->freelancer : null;
        }

        return $user->role === 'company' ? $user->company : null;
    }

    private function assertParticipant(DirectConversation $conversation, string $role, int $profileId): void
    {
        if ($role === 'company' && (int) $conversation->company_id !== $profileId) {
            abort(403);
        }

        if ($role === 'freelancer' && (int) $conversation->freelancer_id !== $profileId) {
            abort(403);
        }
    }

    private function markRead(DirectConversation $conversation, string $role): void
    {
        if ($role === 'company' && $conversation->latest_sender_type !== 'company') {
            $conversation->forceFill(['is_unread_for_company' => false])->save();
        }

        if ($role === 'freelancer' && $conversation->latest_sender_type !== 'freelancer') {
            $conversation->forceFill(['is_unread_for_freelancer' => false])->save();
        }
    }

    private function sendMessage(DirectConversation $conversation, string $senderType, int $senderId, string $body): DirectConversationMessage
    {
        $body = trim($body);

        if ($body === '') {
            throw ValidationException::withMessages([
                'content' => 'メッセージを入力してください。',
            ]);
        }

        $now = Carbon::now();

        $message = DirectConversationMessage::create([
            'direct_conversation_id' => $conversation->id,
            'sender_type' => $senderType,
            'sender_id' => $senderId,
            'body' => $body,
            'sent_at' => $now,
        ]);

        $conversation->forceFill([
            'latest_sender_type' => $senderType,
            'latest_sender_id' => $senderId,
            'latest_message_at' => $now,
            'is_unread_for_company' => $senderType !== 'company',
            'is_unread_for_freelancer' => $senderType !== 'freelancer',
        ])->save();

        return $message;
    }
}
