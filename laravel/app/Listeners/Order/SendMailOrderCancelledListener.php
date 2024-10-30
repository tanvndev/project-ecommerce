<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderCancelledEvent;
use App\Mail\OrderCancelledMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendMailOrderCancelledListener implements ShouldQueue
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
    public function handle(OrderCancelledEvent $event): void
    {
        Mail::to($event->order->customer_email)->queue(new OrderCancelledMail($event->order));
    }
}
