@php($scale = 200)

<div class="relative">
    <img class="absolute scale-[67%]" src="{{ url('/assets/icon.svg') }}" alt="">

    <svg class="animate-spin [animation-duration:20s] [animation-delay:-5s]" viewBox="0 0 {{ $scale }} {{ $scale }}">
        <defs>
            <circle id="circle" r="{{ $scale * 0.37 }}" cx="{{ $scale / 2 }}" cy="{{ $scale / 2 }}"/>
        </defs>
    
        <text>
            <textPath href="#circle">
                {{ Str::upper($text) }}
            </textPath>
        </text>
    </svg>
</div>