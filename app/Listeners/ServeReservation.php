<?php

namespace App\Listeners;

use App\Events\BookCheckedOut;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class ServeReservation
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
        $oldest_reservation = $event->book->reservations()->whereMemberId($event->member->id)->reserved()->oldest()->first();
        if (!empty($oldest_reservation)) {
            $oldest_reservation->complete();
            Log::info("Reservation completed!");
        }
    }
}
