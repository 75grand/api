<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
use Psr\Http\Message\RequestInterface;

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
        Model::unguard();

        JsonResource::withoutWrapping();

        Sanctum::getAccessTokenFromRequestUsing(
            fn(Request $request) => $request->token ?? $request->bearerToken()
        );

        // DB::listen(function($query) {
        //     Log::info(
        //         $query->sql,
        //         [
        //             'bindings' => $query->bindings,
        //             'time' => $query->time
        //         ]
        //     );
        // });

        Http::globalRequestMiddleware(function(RequestInterface $request) {
            DB::table('requests')->insertOrIgnore([
                'method' => $request->getMethod(),
                'host' => $request->getUri()->getHost(),
                'date' => now()
            ]);

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
