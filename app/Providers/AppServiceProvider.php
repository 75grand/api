<?php

namespace App\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        JsonResource::withoutWrapping();

        Http::globalRequestMiddleware(function($request) {
            return $request->withHeader('User-Agent', 'api@75grand.net');
        });

        Queue::failing(function(JobFailed $event) {
            webhook_alert('Job Failed', [
                'Name' => $event->job->getName(),
                'Body' => '```' . $event->job->getRawBody() . '```',
                'Connection' => $event->connectionName
            ]);
        });
    }
}
