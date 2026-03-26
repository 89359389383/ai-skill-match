<!-- Footer -->
<footer class="bg-gradient-to-br from-[#FC4C0C] via-orange-500 to-amber-500 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
            <!-- Brand Section -->
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <div class="p-2 bg-white/10 rounded-lg backdrop-blur-sm">
                        <svg class="w-6 h-6 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-white">
                        AIスキルマッチ
                    </span>
                </div>
                <p class="text-gray-300 text-sm leading-relaxed">
                    AIスキルを持つ人材と企業を結びつけるプラットフォーム。
                    知識を共有し、スキルを販売し、未来を創造する。
                </p>
            </div>

            <!-- Service -->
            <div class="space-y-4">
                <h3 class="font-semibold text-lg text-white">サービス</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="https://aitech-japan.co.jp/#service" target="_blank" class="text-gray-300 hover:text-white text-sm transition-colors">
                            サービス
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Support -->
            <div class="space-y-4">
                <h3 class="font-semibold text-lg text-white">サポート</h3>
                <ul class="space-y-2">
                    <li><a href="https://aitech-japan.co.jp/contact" target="_blank" class="text-gray-300 hover:text-white text-sm">お問い合わせ</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white text-sm">利用規約</a></li>
                    <li><a href="https://aitech-japan.co.jp/3" target="_blank" class="text-gray-300 hover:text-white text-sm">プライバシーポリシー</a></li>
                </ul>
            </div>

            <!-- Company -->
            <div class="space-y-4">
                <h3 class="font-semibold text-lg text-white">企業情報</h3>
                <ul class="space-y-2">
                    <li><a href="https://aitech-japan.co.jp/company" target="_blank" class="text-gray-300 hover:text-white text-sm">運営会社</a></li>
                    <li><a href="https://aitech-japan.co.jp/careers" target="_blank" class="text-gray-300 hover:text-white text-sm">採用情報</a></li>
                    <li><a href="https://aitech-japan.co.jp/#news" target="_blank" class="text-gray-300 hover:text-white text-sm">プレスリリース</a></li>
                </ul>
            </div>
        </div>

        <!-- Bottom -->
        <div class="pt-8 border-t border-white/10">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-gray-300 text-sm">
                    © <span id="publicCurrentYear"></span> AIスキルマッチ. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</footer>

<script>
    (function () {
        const el = document.getElementById('publicCurrentYear');
        if (el) el.textContent = new Date().getFullYear();
    })();
</script>