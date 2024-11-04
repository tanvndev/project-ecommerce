<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        'Registered'::class => [
            'Illuminate\Auth\Listeners\SendEmailVerificationNotification',
        ],
        'App\Events\Auth\AuthRegisteredEvent'::class => [
            'App\Listeners\Auth\SendEmailRegisterVerification',
        ],
        'App\Events\Auth\AuthForgotEvent' => [
            'App\Listeners\Auth\SendEmailResetPassword',
        ],
        'App\Events\Order\OrderShippedEvent' => [
            'App\Listeners\Order\SendOrderShippedEmailListener',
        ],
        'App\Events\Order\OrderCreatedEvent' => [
            'App\Listeners\Order\SendMailOrderCreatedListener',
        ],
        'App\Events\Order\OrderUpdatePaymentEvent' => [
            'App\Listeners\Order\SendMailOrderUpdatePaymentListener',
        ],
        'App\Events\Order\OrderCompletedEvent' => [
            'App\Listeners\Order\SendMailOrderCompletedListener',
        ],
        'App\Events\Order\OrderCancelledEvent' => [
            'App\Listeners\Order\SendMailOrderCancelledListener',
        ],
        'App\Events\Wishlist\WishListEvent' => [
            'App\Listeners\Wishlist\SendWishListEmailListener',
        ],
        'App\Events\Voucher\VoucherCreatedEvent' => [
            'App\Listeners\Voucher\SendVoucherNotificationListener',
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void {}

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
