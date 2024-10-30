<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderCompletedEvent;
use App\Mail\OrderCompletedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendMailOrderCompletedListener implements ShouldQueue
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
    public function handle(OrderCompletedEvent $event): void
    {
        Mail::to($event->order->customer_email)->queue(new OrderCompletedMail($event->order));
    }
}
