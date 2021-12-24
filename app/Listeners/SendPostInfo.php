<?php

namespace App\Listeners;

use App\Events\AddNewPost;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendPostInfo
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
     * @param  \App\Events\AddNewPost  $event
     * @return void
     */
    public function handle(AddNewPost $event)
    {
        var_dump('post->' . $event->post);
    }
}
