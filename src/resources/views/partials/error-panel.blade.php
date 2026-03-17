@if ($errors->any())
    <div class="error-panel-box mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
        <div class="error-panel-title font-extrabold">入力内容をご確認ください</div>
        <ul class="error-panel-list mt-2 list-disc space-y-1 pl-5 font-bold">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    <style>
        .error-panel-box { margin-bottom: 1rem; padding: 1rem 1.25rem; border-radius: 0.5rem; border: 1px solid #fecaca; background: #fef2f2; color: #991b1b; font-size: 0.875rem; }
        .error-panel-box .error-panel-title { font-weight: 800; }
        .error-panel-box .error-panel-list { margin-top: 0.5rem; list-style: disc; padding-left: 1.25rem; font-weight: 700; }
        .error-panel-box .error-panel-list li { margin-bottom: 0.25rem; }
    </style>
@endif

