@php
    $style = $style ?? 'gray';
    $classes = match($style) {
        'gray' => 'bg-gray-200',
        'accent' => 'bg-accent text-white'
    }
@endphp

<a class="{{ $classes }} py-2 px-5 rounded-xl font-semibold block w-fit active:scale-95 transition-transform" href="{{ $href }}">
    {{ $slot }}
</a>