<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderUpdatePaymentEvent;
use App\Mail\OrderUpdatePaymentMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendMailOrderUpdatePaymentListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(OrderUpdatePaymentEvent $event): void
    {
        Mail::to($event->order->customer_email)->queue(new OrderUpdatePaymentMail($event->order));
    }
}
