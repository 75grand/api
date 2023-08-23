<x-html title="75grand: The Macalester App">
    <div class="flex gap-8 p-8 items-center">
        <div class="h-[80vh]">
            <x-phone src="/assets/screenshots/home.png"/>
        </div>

        <div class="space-y-8">
            <div class="w-48">
                <x-animated-icon text="Your Portal to Macalester"/>
            </div>

            <div class="flex gap-2">
                <a target="_blank" href="https://play.google.com/store/apps/details?id=zone.jero.grand">
                    <img class="h-12" src="{{ url('/assets/google-play-badge.svg') }}" alt="">
                </a>

                <a target="_blank" href="https://apps.apple.com/us/app/75grand-the-macalester-app/id6462052792">
                    <img class="h-12" src="{{ url('/assets/app-store-badge.svg') }}" alt="">
                </a>
            </div>
        </div>
    </div>
</x-html>