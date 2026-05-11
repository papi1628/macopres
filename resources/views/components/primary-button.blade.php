@props([])

<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => '
        relative w-full h-12 overflow-hidden
        rounded-xl
        text-sm font-bold text-white tracking-wide
        transition-all duration-150
        hover:-translate-y-px
        active:translate-y-0
        focus:outline-none focus:ring-2 focus:ring-blue-400/40 focus:ring-offset-2 focus:ring-offset-transparent
        disabled:opacity-60 disabled:cursor-not-allowed
    '
]) }}
style="background: linear-gradient(135deg, #185FA5, #378ADD); box-shadow: 0 8px 24px rgba(12,68,124,.5), 0 0 0 1px rgba(255,255,255,.08);"
>
    {{-- Reflet supérieur --}}
    <span class="absolute inset-0 bg-gradient-to-b from-white/10 to-transparent pointer-events-none rounded-xl"></span>

    <span class="relative z-10">{{ $slot }}</span>
</button>