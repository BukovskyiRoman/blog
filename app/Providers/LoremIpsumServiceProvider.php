<?php

namespace App\Providers;

use App\Http\Controllers\PostController;
use App\Services\LoremIpsumService;
use Illuminate\Support\ServiceProvider;

class LoremIpsumServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(LoremIpsumService::class, function () {
                return new LoremIpsumService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
