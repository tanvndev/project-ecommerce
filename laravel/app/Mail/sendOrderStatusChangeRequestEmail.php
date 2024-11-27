<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class sendOrderStatusChangeRequestEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        $email = env('MAIL_FROM_ADDRESS');

        return $this->from( auth()->user()->email)
            ->to($email)
            ->subject('Gửi email yêu cầu thay đổi trạng thái đơn hàng')
            ->view('emails.orders.order-status-change-request');
    }
}
