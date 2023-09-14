<?php

namespace App\Jobs;

use App\Models\Term;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class RefreshTerms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $terms = Http::get(
            'https://macadmsys.macalester.edu/StudentRegistrationSsb/ssb/classSearch/getTerms?offset=1&max=500'
        )->json();

        foreach ($terms as $term) {
            Term::updateOrCreate([
                'code' => $term['code'],
            ], [
                'name' => Str::replace(' (View Only)', '', $term['description']),
            ]);
        }
    }
}
