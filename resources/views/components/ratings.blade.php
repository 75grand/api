@php
    use App\Models\User;

    $data = cache()->remember('ratings-data', now()->addDay(), function() {
        $data = Http::get('https://itunes.apple.com/lookup?id=6462052792');

        return [
            'avgRating' => $data['results'][0]['averageUserRating'],
            'numRatings' => $data['results'][0]['userRatingCount'],
            'users' => floor(User::count() / 50) * 50
        ];
    });
@endphp

<div>
    <div class="text-2xl inline-flex">
        @for($i = 1; $i <= 5; $i++)
            <span @class([
                'text-yellow' => $i <= round($data['avgRating']),
                'text-gray-300' => $i > round($data['avgRating'])
            ])>★</span>
        @endfor
    </div>

    <div class="text-gray-500">
        {{ $data['users'] }}+ users
    </div>
</div>