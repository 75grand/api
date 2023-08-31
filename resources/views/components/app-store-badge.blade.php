<div class="group relative">
    @if($platform === 'ios')
        <a draggable="false" target="_blank" href="{{ route('download.ios') }}">
            <img draggable="false" class="h-12 active:scale-95 transition-transform" src="{{ url('/assets/app-store-badge.svg') }}" alt="">
        </a>
    @else
        <a draggable="false" target="_blank" href="https://eepurl.com/ic-0qz">
            <img draggable="false" class="h-12 active:scale-95 transition-transform" src="{{ url('/assets/google-play-badge.svg') }}" alt="">
        </a>
    @endif

    @if($platform === 'ios')
        <div class="
            max-sm:hidden
            opacity-0 group-hover:opacity-100 transition-opacity
            pointer-events-none group-hover:pointer-events-auto
            absolute z-50 left-1/2 -translate-x-1/2 pt-2
        ">
            <div class="bg-white p-2 border-2 rounded-lg shadow-md">
                <img class="w-[6rem] min-w-[6rem] aspect-square [image-rendering:pixelated]" src="{{ url('/assets/qr-ios.png') }}" alt="">
            </div>
        </div>
    @endif
</div>

{{-- <div class="group relative">
    @if($platform === 'ios')
        <a draggable="false" target="_blank" href="{{ route('download.ios') }}">
            <img draggable="false" class="h-12 active:scale-95 transition-transform" src="{{ url('/assets/app-store-badge.svg') }}" alt="">
        </a>
    @else
        <a draggable="false" target="_blank" href="{{ route('download.android') }}">
            <img draggable="false" class="h-12 active:scale-95 transition-transform" src="{{ url('/assets/google-play-badge.svg') }}" alt="">
        </a>
    @endif

    <div class="
        max-sm:hidden
        opacity-0 group-hover:opacity-100 transition-opacity
        pointer-events-none group-hover:pointer-events-auto
        absolute z-50 left-1/2 -translate-x-1/2 pt-2
    ">
        <div class="bg-white p-2 border-2 rounded-lg shadow-md">
            @if($platform === 'ios')
                <img class="w-[6rem] min-w-[6rem] aspect-square [image-rendering:pixelated]" src="{{ url('/assets/qr-ios.png') }}" alt="">
            @else
                <img class="w-[6rem] min-w-[6rem] aspect-square [image-rendering:pixelated]" src="{{ url('/assets/qr-android.png') }}" alt="">
            @endif
        </div>
    </div>
</div> --}}