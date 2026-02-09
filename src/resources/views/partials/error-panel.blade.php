@if ($errors->any())
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
        <div class="font-extrabold">入力内容をご確認ください</div>
        <ul class="mt-2 list-disc space-y-1 pl-5 font-bold">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

