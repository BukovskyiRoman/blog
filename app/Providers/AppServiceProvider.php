<?php

namespace App\Providers;

use App\Services\CommentService;
use App\Services\CookieService;
use App\Services\Interfaces\CommentServiceInterface;
use App\Services\Interfaces\CookieServiceInterface;
use App\Services\Interfaces\PostServiceInterface;
use App\Services\PostService;
use Illuminate\Support\Facades\URL;
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
//        if ($this->app->isLocal())
//        {
//            $this->app['request']->server->set('http', true);
//        }
//        else
//        {
//            $this->app['request']->server->set('https', true);
//        }
        $this->app->bind(CookieServiceInterface::class, CookieService::class);
        $this->app->bind(CookieService::class, function () {
            return new CookieService();
        });
        $this->app->bind(CommentServiceInterface::class, function () {
            return new CommentService();
        });
        $this->app->bind(PostServiceInterface::class, function () {
            return new PostService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }
}
