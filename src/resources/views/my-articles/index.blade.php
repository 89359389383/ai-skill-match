@extends('layouts.public')

@section('title', '投稿記事一覧 - AIスキルマッチ')

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush

@section('content')
<div class="min-h-screen py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">投稿記事一覧</h1>
                    <p class="text-gray-600">あなたが投稿した記事の一覧です</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('articles.index') }}" class="flex items-center gap-2 px-5 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-all">
                        公開記事一覧へ
                    </a>
                    <a href="{{ route('articles.create') }}" class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                        記事を投稿
                    </a>
                </div>
            </div>
        </div>

        @if(session('status'))
            <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 font-medium">
                {{ session('status') }}
            </div>
        @endif

        @if($articles->isEmpty())
            <div class="text-center py-16 bg-white rounded-2xl shadow">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-16 h-16 text-gray-300 mx-auto mb-4"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                <h3 class="text-xl font-bold text-gray-900 mb-2">まだ記事を投稿していません</h3>
                <p class="text-gray-600 mb-6">最初の記事を投稿してみましょう</p>
                <a href="{{ route('articles.create') }}" class="inline-block px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-semibold hover:shadow-lg transition-all">
                    記事を投稿する
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($articles as $a)
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 overflow-hidden flex flex-col">
                        <a href="{{ route('articles.show', ['article' => $a->id]) }}" class="block flex-1">
                            <div class="relative h-48 overflow-hidden">
                                <img src="{{ $a->eyecatch_image_url ?? 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=800&h=600&fit=crop' }}" alt="{{ $a->title }}" class="w-full h-full object-cover">
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-[auto_1fr] items-start gap-3 mb-2">
                                    <div class="flex flex-wrap gap-2">
                                        <span class="px-3 py-1 bg-indigo-100 text-indigo-700 text-xs font-medium rounded-full">{{ $a->category ?? 'その他' }}</span>
                                        @if($a->status === 1)
                                            <span class="px-3 py-1 bg-green-100 text-green-700 text-xs rounded-full">公開中</span>
                                        @else
                                            <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">下書き</span>
                                        @endif
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($a->tags->take(2) as $tag)
                                            <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">#{{ $tag->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">{{ $a->title }}</h3>
                                <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ Str::limit($a->excerpt ?? '', 80) }}</p>
                                <div class="text-sm text-gray-500">{{ $a->created_at?->format('Y/m/d') }}</div>
                            </div>
                        </a>
                        <div class="px-6 pb-4 pt-0 flex flex-wrap gap-2 border-t border-gray-100 mt-auto">
                            <a href="{{ route('my-articles.edit', ['article' => $a->id]) }}" class="inline-flex items-center gap-1 px-3 py-2 text-sm font-semibold rounded-lg bg-gray-100 text-gray-800 hover:bg-gray-200 transition-colors">
                                編集
                            </a>
                            <button type="button" onclick="openMyArticleDeleteModal({{ $a->id }})" class="inline-flex items-center gap-1 px-3 py-2 text-sm font-semibold rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition-colors">
                                削除
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $articles->links() }}
            </div>
        @endif
    </div>
</div>

<div id="myArticleDeleteModal" class="fixed inset-0 z-[60] hidden items-center justify-center p-4 bg-black/50">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-2">記事を削除しますか？</h3>
        <p class="text-sm text-gray-600 mb-6">この操作は取り消せません。</p>
        <div class="flex justify-end gap-3">
            <button type="button" onclick="closeMyArticleDeleteModal()" class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">キャンセル</button>
            <form id="myArticleDeleteForm" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 rounded-xl bg-red-600 text-white font-semibold hover:bg-red-700">削除する</button>
            </form>
        </div>
    </div>
</div>

<script>
function openMyArticleDeleteModal(articleId) {
    var form = document.getElementById('myArticleDeleteForm');
    form.action = '{{ url('/my-articles') }}/' + articleId;
    var modal = document.getElementById('myArticleDeleteModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeMyArticleDeleteModal() {
    var modal = document.getElementById('myArticleDeleteModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
document.getElementById('myArticleDeleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeMyArticleDeleteModal();
});
</script>
@endsection
