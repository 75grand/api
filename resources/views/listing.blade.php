<x-html title="Listing: “{{ $listing->title }}” — 75grand" image="{{ $listing->image_url }}">
    <main class="sm:p-16 p-8 max-w-screen-sm mx-auto space-y-8">
        <a draggable="false" class="px-8 block w-fit active:scale-95 transition-transform" href="{{ route('home') }}">
            <img draggable="false" width="512" height="121.441" class="w-32" src="{{ url('/assets/logo.svg') }}" alt="">
        </a>

        <img class="rounded-xl w-full aspect-square object-cover bg-gray-300"
            src="{{ image_cdn_url($listing->image_url, 750, 750) }}" alt="">

        <div class="px-8 space-y-8">
            <h1 class="text-2xl font-semibold">{{ $listing->title }}</h1>

            <div>
                <p class="text-xl font-semibold">
                    {{ $listing->formatPrice() }}
                    —
                    {{ $listing->available ? 'Available' : 'Not Available' }}
                </p>
                
                <p class="text-gray-500">
                    {{ $listing->formatDistance() }}
                </p>
            </div>

            <div class="flex gap-3 items-center">
                <div class="w-12 h-12 rounded-full overflow-clip">
                    <img class="w-full h-full blur-sm bg-gray-300" src="https://thispersondoesnotexist.com/" alt="">
                </div>
    
                <div class="leading-tight" aria-hidden="true">
                    <p class="font-semibold blur-sm">
                        Anderson Cooper
                    </p>
    
                    @if($listing->user->position === 'student' && $listing->user->class_year)
                        <p class="text-gray-500">Class of {{ $listing->user->class_year }}</p>
                    @else
                        <p class="text-gray-500">{{ Str::ucfirst($listing->user->position) }}</p>
                    @endif
                </div>
            </div>

            @if($listing->description)
                <x-metadata label="Description">
                    <x-prose>
                        {{ $listing->description }}
                    </x-prose>
                </x-metadata>
            @endif

            <x-metadata label="Contact">
                See contact information in the app.
            </x-metadata>

            <div class="flex gap-2 max-sm:flex-col text-sm">
                <x-button style="accent" href="{{ route('download') }}">
                    Download 75grand
                </x-button>

                <x-button href="grand://marketplace/{{ $listing->id }}">
                    Open in 75grand
                </x-button>
            </div>
        </div>
    </main>
</x-html>