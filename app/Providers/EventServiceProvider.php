<?php

namespace App\Providers;

use App\Events\AddNewComment;
use App\Events\AddNewPost;
use App\Events\UserAdd;
use App\Events\UserLike;
use App\Listeners\SendLikeInfo;
use App\Listeners\SendPostInfo;
use App\Listeners\SendUserInfo;
use App\Listeners\SendCommentInfo;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        UserAdd::class => [
            SendUserInfo::class,
        ],

//        UserLike::class => [
//            SendLikeInfo::class,
//        ],

        AddNewComment::class => [
            SendCommentInfo::class
        ],

        AddNewPost::class => [
            SendPostInfo::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Определить, должны ли автоматически обнаруживаться события и слушатели.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return true;
    }
}
