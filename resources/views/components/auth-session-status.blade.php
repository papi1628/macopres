@props(['status'])

@if ($status)
    <div {{ $attributes->merge([
        'class' => 'flex items-center gap-2 px-4 py-3 rounded-xl text-xs font-medium text-green-300 mb-4'
    ]) }}
    style="background:rgba(59,109,17,.2); border:1px solid rgba(59,109,17,.35);">
        <span>✓</span>
        <span>{{ $status }}</span>
    </div>
@endif