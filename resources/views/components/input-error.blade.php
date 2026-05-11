@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'mt-1.5 space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <li class="flex items-center gap-1.5 text-[11px] text-red-300 font-medium">
                <span>⚠</span>
                <span>{{ $message }}</span>
            </li>
        @endforeach
    </ul>
@endif