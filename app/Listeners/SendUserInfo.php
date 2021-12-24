<?php

namespace App\Listeners;

use App\Events\UserAdd;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendUserInfo
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\UserAdd  $event
     * @return void
     */
    public function handle(UserAdd $event)
    {

        Log::info('Add new user ' . $event->user->name);
    }
}
