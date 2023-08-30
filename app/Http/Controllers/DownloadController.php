<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DownloadController extends Controller
{
    public function automatic()
    {
        $userAgent = request()->userAgent();

        if(Str::contains($userAgent, 'Android', true)) {
            return $this->android();
        }

        if(Str::contains($userAgent, ['iPad', 'iPhone'], true)) {
            return $this->ios();
        }
        
        return redirect()->route('home');
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
