<?php

namespace App\Listeners;

use App\Events\AddNewComment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendCommentInfo
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
     * @param  \App\Events\AddNewComment  $event
     * @return void
     */
    public function handle(AddNewComment $event)
    {
        //Log::info('Add new user ' . $event->comment->body);
    }
}
