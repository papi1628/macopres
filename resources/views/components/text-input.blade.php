@props(['disabled' => false])

<input
    @disabled($disabled)
    {{ $attributes->merge([
        'class' => '
            w-full h-[46px]
            bg-white/[.07] border border-white/[.12]
            rounded-xl
            px-4 text-sm text-white placeholder-white/25
            font-medium
            transition duration-200
            focus:outline-none focus:border-blue-400 focus:bg-white/10
            focus:ring-2 focus:ring-blue-400/25
            disabled:opacity-50 disabled:cursor-not-allowed
        '
    ]) }}
>