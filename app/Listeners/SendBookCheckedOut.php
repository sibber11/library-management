<?php

namespace App\Listeners;

use App\Events\BookCheckedOut;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendBookCheckedOut
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BookCheckedOut $event): void
    {
        $event->member->user->notify(new \App\Notifications\BookCheckedOut($event->book));
    }
}
