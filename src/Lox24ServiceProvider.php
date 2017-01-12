<?php

namespace NotificationChannels\Lox24;

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
                return new Lox24(
                    config('broadcasting.connections.lox24.accountID'),
                    config('broadcasting.connections.lox24.password'),
                    config('broadcasting.connections.lox24.from')
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
