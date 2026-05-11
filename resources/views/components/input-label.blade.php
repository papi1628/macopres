@props(['value'])

<label {{ $attributes->merge([
    'class' => 'block text-[11px] font-semibold text-blue-100 uppercase tracking-[.07em] mb-1.5'
]) }}>
    {{ $value ?? $slot }}
</label>