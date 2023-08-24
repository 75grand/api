@php
    $slogans = [
        'The Macalester App',
        'Your Portal to Macalester',
        'Macalester & More',
        'More Than Macalester'
    ];

    $slogan = $slogans[array_rand($slogans)];
@endphp

<x-html title="75grand: The Macalester App">
    <div class="p-16 flex flex-col items-center text-center gap-16">
        <div class="w-40">
            <x-animated-icon text="{!! $slogan !!}"/>
        </div>

        <div class="space-y-4">
            <h1 class="text-4xl font-semibold">The Macalester App</h1>

            <h2 class="text-xl text-gray-500 max-w-md [text-wrap:balance]">
                Menus,
                campus events,
                classifieds,
                Moodle,
                building hours,
                MacPass,
                P.O. box combination
            </h2>
            <h1 class="text-4xl font-semibold">{{ $slogan }}</h1>
        </div>

        <div class="flex gap-4">
            <x-app-store-badge platform="ios"/>
            <x-app-store-badge platform="android"/>
        </div>

        <div class="max-w-md">
            <x-phone src="/assets/screenshots/home.png"/>
        </div>

        <footer>
            <p>
                <span>by</span>
                <a class="text-accent hover:underline font-semibold" target="_blank" href="https://jero.zone">Jerome Paulos</a>
                <span>’26</span>
            </p>

            <p>
                <a class="text-accent hover:underline font-semibold" href="{{ route('privacy') }}">Privacy Policy</a>
                <span>•</span>
                <a class="text-accent hover:underline font-semibold" href="{{ route('terms') }}">Terms of Service</a>
            </p>
        </footer>
    </div>
</x-html>