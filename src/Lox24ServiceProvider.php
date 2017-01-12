<?php

namespace NotificationChannels\Lox24;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class Lox24ServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->app->when(Lox24Channel::class)
            ->needs(Lox24::class)
            ->give(function () {
                $lox24Config = config('broadcasting.connections.lox24');

                return new Lox24Channel(
                    $lox24Config['accountId'],
                    $lox24Config['password']
                );
            });
    }

    /**
     * Register the application services.
     */
    public function register()
    {
    }
}
