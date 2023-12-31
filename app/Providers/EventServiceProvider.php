<?php

namespace App\Providers;

use App\Events\BookCheckedIn;
use App\Events\BookCheckedOut;
use App\Listeners\CreateMemberOnRegistration;
use App\Listeners\ReserveReservation;
use App\Listeners\SendBookCheckedIn;
use App\Listeners\SendBookCheckedOut;
use App\Listeners\ServeReservation;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
            CreateMemberOnRegistration::class
        ],
        BookCheckedIn::class => [
            ReserveReservation::class,
            SendBookCheckedIn::class
        ],
        BookCheckedOut::class => [
            ServeReservation::class,
            SendBookCheckedOut::class
        ]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
