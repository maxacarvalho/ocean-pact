<?php

namespace App\Providers;

use App\Events\QuotePortal\BuyerCreatedEvent;
use App\Events\QuotePortal\QuoteCreatedEvent;
use App\Events\QuotePortal\QuoteRespondedEvent;
use App\Listeners\QuotesPortal\BuyerCreatedEventListener;
use App\Listeners\QuotesPortal\PrepareRespondedQuoteForCollectionListener;
use App\Listeners\QuotesPortal\QuoteCreatedEventListener;
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
        ],
        QuoteRespondedEvent::class => [
            PrepareRespondedQuoteForCollectionListener::class,
        ],
        QuoteCreatedEvent::class => [
            QuoteCreatedEventListener::class,
        ],
        BuyerCreatedEvent::class => [
            BuyerCreatedEventListener::class,
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
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
