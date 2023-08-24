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
    <div class="sm:p-16 p-8 flex flex-col items-center text-center gap-16">
        <div class="w-40">
            <x-animated-icon text="{!! $slogan !!}"/>
        </div>

        <div class="space-y-4">
            <h1 class="text-4xl font-semibold">{{ $slogan }}</h1>
            <x-animated-subtitle/>
        </div>

        <div class="flex gap-4">
            <x-app-store-badge platform="ios"/>
            <x-app-store-badge platform="android"/>
        </div>

        <div class="max-w-md">
            <x-phone src="/assets/screenshots/home.png"/>
        </div>

        <footer class="space-y-1">
            <p>
                <span>by</span>
                <a class="text-accent hover:underline font-semibold" href="https://jero.zone">Jerome Paulos</a>
                <span>’26</span>
            </p>

            <p>
                <a class="text-accent hover:underline font-semibold" href="https://github.com/75grand">GitHub</a>
                <span>•</span>
                <a class="text-accent hover:underline font-semibold" href="https://www.instagram.com/75grand_net">Instagram</a>
                <span>•</span>
                <a class="text-accent hover:underline font-semibold" href="mailto:support@75grand.net">Email</a>
            </p>

            <p>
                <a class="text-accent hover:underline font-semibold" href="{{ route('privacy') }}">Privacy Policy</a>
                <span>•</span>
                <a class="text-accent hover:underline font-semibold" href="{{ route('terms') }}">Terms of Service</a>
            </p>
        </footer>
    </div>
</x-html>