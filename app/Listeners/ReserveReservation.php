<?php

namespace App\Listeners;

use App\Models\Reservation;
use App\Events\BookCheckedIn;
use App\Notifications\BookReserved;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReserveReservation
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
        $reservation = Reservation::whereBookId($event->checkout->book->id)->oldest()->pending()->first();
        if (!$reservation) {
            return;
        }
        $reservation->reserve($event->checkout->book);
        $reservation->member->user->notify(new BookReserved($event->checkout->book));
    }
}
