<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DownloadController extends Controller
{
    public function automatic()
    {
        return match(true) {
            Str::contains(request()->userAgent(), 'Android') => $this->android(),
            Str::contains(request()->userAgent(), ['iPad', 'iPhone']) => $this->ios(),
            default => redirect()->route('home')
        };
    }

    public function ios()
    {
        return redirect('https://apps.apple.com/us/app/75grand-the-macalester-app/id6462052792');
    }

    public function android()
    {
        return redirect('https://play.google.com/store/apps/details?id=zone.jero.grand');
    }
}
