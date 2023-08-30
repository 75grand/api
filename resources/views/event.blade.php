<x-html title="Event: “{{ $event->title }}” — 75grand" image="{{ $event->image_url }}">
    <main class="sm:p-16 p-8 max-w-screen-sm mx-auto space-y-8">
        <a draggable="false" class="px-8 block w-fit active:scale-95 transition-transform" href="{{ route('home') }}">
            <img draggable="false" width="512" height="121.441" class="w-32" src="{{ url('/assets/logo.svg') }}" alt="">
        </a>

        <div>
            @if($event->image_url && $event->calendar_name !== 'Sports')
                <div class="h-64 -mb-12">
                    <img class="rounded-xl object-cover w-full h-full" src="{{ $event->image_url }}" alt="">
                </div>
            @endif
    
            <div class="w-24 h-24 text-center flex flex-col leading-none mx-8 shadow-xl rounded-xl">
                <div class="bg-accent text-white uppercase text-ms p-2 rounded-t-xl">
                    {{ $event->start_date->format('M') }}
                </div>
    
                <div class="bg-white text-4xl font-semibold border-2 border-t-0 h-full rounded-b-xl grid place-items-center">
                    {{ $event->start_date->format('j') }}
                </div>
            </div>
        </div>

        <div class="px-8 space-y-8">
            <h1 class="text-2xl font-semibold">{{ $event->title }}</h1>

            <div>
                <p class="font-semibold">{{ $event->formatDuration() }}</p>
                <p>{{ $event->start_date->format('l, F j, Y') }}</p>
            </div>
    
            @if($event->location)
                <x-metadata label="Location">
                    <p>{{ $event->location }}</p>
                </x-metadata>
            @endif

            @if($event->url)
                <x-metadata label="Website">
                    <a href="{{ $event->url }}" class="font-semibold text-accent hover:underline">
                        {{ parse_url($event->url, PHP_URL_HOST) }}
                    </a>
                </x-metadata>
            @endif
    
            @if($event->description)
                <x-metadata label="Description">
                    <x-prose>
                        {{ $event->description }}
                    </x-prose>
                </x-metadata>
            @endif

            <div class="flex gap-2 max-sm:flex-col text-sm">
                <a class="bg-accent text-white py-2 px-5 rounded-xl font-semibold block w-fit active:scale-95 transition-transform"
                    href="{{ route('home') }}">Download 75grand</a>
    
                <a class="bg-gray-200 py-2 px-5 rounded-xl font-semibold block w-fit active:scale-95 transition-transform"
                    href="grand://calendar/{{ $event->id }}">Open in 75grand</a>
            </div>
        </div>
    </main>
</x-html>