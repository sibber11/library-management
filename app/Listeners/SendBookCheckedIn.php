<?php

namespace App\Listeners;

use App\Events\BookCheckedIn;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\BookCheckedIn as BookCheckedInNotification;

class SendBookCheckedIn
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
    public function handle(BookCheckedIn $event): void
    {
        $event->checkout->member->user->notify(new BookCheckedInNotification($event->checkout->book));
    }
}
