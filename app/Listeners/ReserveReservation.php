<?php

namespace App\Listeners;

use App\Events\BookCheckedIn;
use App\Models\Reservation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

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
        $reservation = Reservation::whereBookId($event->book->id)->oldest()->pending()->first();
        if (!$reservation) {
            return;
        }
        //todo: notify user
        Log::info($event->book->title);
        $reservation->reserve($event->book);
    }
}
