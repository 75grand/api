<x-html title="{{ $title }}">
    <main class="sm:p-16 p-8 max-w-screen-sm mx-auto flex flex-col gap-16 items-center">
        <a draggable="false" class="block w-fit active:scale-95 transition-transform" href="{{ route('home') }}">
            <img draggable="false" width="512" height="121.441" class="w-32" src="{{ url('/assets/logo.svg') }}" alt="">
        </a>

        <hr class="border-t w-full">

        <h1 class="text-4xl font-semibold text-center -mt-1">
            {{ $heading ?? $title }}
        </h1>

        <article class="w-full">
            <x-prose>
                {!! Str::markdown($slot) !!}
            </x-prose>
        </article>

        <hr class="border-t w-full">

        <x-footer/>
    </main>
</x-html>