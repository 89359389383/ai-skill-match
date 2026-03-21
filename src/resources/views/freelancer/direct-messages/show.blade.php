<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>フリーランス チャット - AIスキルマッチ</title>
    @auth('freelancer')
        @include('partials.freelancer-header-style')
    @endauth
    {{-- ヘッダーに必要なスタイルのみ --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    @include('partials.public-header')
    <main class="pt-24">
        @php
            $viewerId = (int) ($viewerProfile->id ?? 0);

            if ($conversation->company_id !== null) {
                $counterpartName = $conversation->company?->name ?? '企業';
                $counterpartRole = '企業';
                $counterpartIcon = null;
                $counterpartUserId = null;
            } elseif (
                $conversation->initiator_type === 'freelancer'
                && $conversation->initiator_id !== null
                && $conversation->freelancer_id !== null
            ) {
                $counterpartFreelancerId = ($viewerId === (int) $conversation->freelancer_id)
                    ? (int) $conversation->initiator_id
                    : (int) $conversation->freelancer_id;
                $counterpartFreelancer = \App\Models\Freelancer::find($counterpartFreelancerId);
                $counterpartName = $counterpartFreelancer?->display_name ?? 'フリーランス';
                $counterpartRole = 'フリーランス';
                $counterpartIcon = $counterpartFreelancer?->icon_path ?? null;
                $counterpartUserId = $counterpartFreelancer?->user_id;
            } else {
                $counterpartFreelancer = $conversation->freelancer;
                if ($counterpartFreelancer && (int) $counterpartFreelancer->id === $viewerId) {
                    $counterpartName = 'フリーランス';
                    $counterpartRole = 'フリーランス';
                    $counterpartIcon = null;
                    $counterpartUserId = null;
                } else {
                    $counterpartName = $counterpartFreelancer?->display_name ?? 'フリーランス';
                    $counterpartRole = 'フリーランス';
                    $counterpartIcon = $counterpartFreelancer?->icon_path ?? null;
                    $counterpartUserId = $counterpartFreelancer?->user_id;
                }
            }

            $avatarSrc = !empty($counterpartIcon)
                ? (str_starts_with($counterpartIcon, 'http') ? $counterpartIcon : asset('storage/' . ltrim($counterpartIcon, '/')))
                : null;

            $messages = ($messages ?? collect())->whereNull('deleted_at')->sortBy('sent_at')->values();
            $latestMessage = $messages->last();
            $viewerName = $viewerProfile?->display_name ?? 'フリーランス';
            $viewerInitial = mb_substr($viewerName, 0, 1);
        @endphp

        <div class="container mx-auto px-4 py-6">
            @if (session('success') || session('error'))
                <div class="mb-4 space-y-2">
                    @if (session('success'))
                        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800">{{ session('error') }}</div>
                    @endif
                </div>
            @endif

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                {{-- ヘッダー --}}
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between gap-4 bg-gradient-to-b from-white to-gray-50">
                    <div class="flex items-center gap-3 min-w-0">
                        <a href="{{ route('direct-messages.index') }}"
                           class="w-10 h-10 rounded-full border border-gray-200 bg-white flex items-center justify-center text-gray-600 hover:bg-gray-50 flex-shrink-0">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>

                        @if($avatarSrc)
                            <img src="{{ $avatarSrc }}" alt="{{ $counterpartName }}" class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                        @else
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center font-bold flex-shrink-0">
                                {{ mb_substr($counterpartName, 0, 1) }}
                            </div>
                        @endif

                        <div class="min-w-0">
                            <div class="font-bold text-gray-900 truncate">{{ $counterpartName }}</div>
                            @if($counterpartUserId)
                                <div class="text-sm text-gray-500">
                                    ・<a href="{{ route('profiles.show', $counterpartUserId) }}" class="text-blue-600 hover:underline font-medium">プロフィールを見る</a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="text-sm font-medium text-gray-500 flex-shrink-0">
                        {{ $conversation->latest_message_at?->format('Y/m/d H:i') ?? '-' }}
                    </div>
                </div>

                {{-- メッセージ一覧 --}}
                <div class="p-4 max-h-[600px] overflow-y-auto bg-gradient-to-b from-white to-gray-50" id="dmMessages">
                    @forelse($messages as $message)
                        @php
                            $isMe = $message->sender_type === 'freelancer' && $message->sender_id === $viewerProfile->id;
                            $sentAt = $message->sent_at?->format('Y/m/d H:i') ?? '';
                            $senderName = $isMe ? $viewerName : $counterpartName;
                        @endphp

                        <div class="flex gap-3 mb-4 {{ $isMe ? 'justify-end' : '' }}">
                            @if(!$isMe)
                                @if($avatarSrc)
                                    <img src="{{ $avatarSrc }}" alt="{{ $senderName }}" class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center font-bold flex-shrink-0">
                                        {{ mb_substr($senderName, 0, 1) }}
                                    </div>
                                @endif
                            @endif

                            <div class="max-w-[75%] rounded-xl px-4 py-3 border {{ $isMe ? 'bg-blue-50 border-blue-200' : 'bg-white border-gray-200' }}">
                                <div class="font-bold text-gray-900 mb-1">{{ $senderName }}</div>
                                <div class="text-gray-700 whitespace-pre-wrap">{{ $message->body }}</div>
                                <div class="text-xs text-gray-500 mt-1 text-right">{{ $sentAt }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-500 py-8">まだメッセージはありません。</div>
                    @endforelse
                </div>

                {{-- 送信フォーム --}}
                <div class="border-t border-gray-200 p-4 bg-white">
                    <form method="POST"
                          action="{{ route('direct-messages.reply', ['direct_conversation' => $conversation->id]) }}"
                          class="space-y-3">
                        @csrf
                        <textarea
                            id="dmContent"
                            name="content"
                            rows="4"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none resize-vertical @error('content') border-red-500 ring-2 ring-red-100 @enderror"
                            placeholder="メッセージを入力..."
                        >{{ old('content') }}</textarea>
                        @error('content')
                            <div class="text-red-600 text-sm font-medium">{{ $message }}</div>
                        @enderror

                        <div class="flex justify-end">
                            <button id="dmSendButton" type="submit"
                                    class="px-6 py-2 bg-orange-500 text-white font-bold rounded-lg hover:bg-orange-600 disabled:bg-gray-300 disabled:cursor-not-allowed"
                                    disabled>
                                送信
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script>
            (function () {
                const messages = document.getElementById('dmMessages');
                if (messages) messages.scrollTop = messages.scrollHeight;

                const textarea = document.getElementById('dmContent');
                const button = document.getElementById('dmSendButton');
                if (!textarea || !button) return;

                const toggle = () => {
                    button.disabled = !textarea.value || !textarea.value.trim();
                };

                textarea.addEventListener('input', toggle);
                toggle();
            })();
        </script>
    </main>
</body>
</html>

