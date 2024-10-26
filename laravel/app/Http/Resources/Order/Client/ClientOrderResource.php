<?php

namespace App\Http\Resources\Order\Client;

use App\Models\Order;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                         => $this->id ?? '',
            'code'                       => $this->code,
            'customer_name'              => $this->customer_name,
            'customer_phone'             => $this->customer_phone,
            'customer_email'             => $this->customer_email,
            'shipping_address'           => $this->shipping_address,
            'order_status'               => Order::ORDER_STATUS[$this->order_status],
            'order_status_code'          => $this->order_status,
            'payment_status_code'        => $this->payment_status,
            'payment_method_id'          => $this->payment_method_id,
            'order_status_color'         => $this->getOrderStatusColor(),
            'payment_status'             => Order::PAYMENT_STATUS[$this->payment_status],
            'total_price'                => $this->total_price,
            'shipping_fee'               => $this->shipping_fee,
            'discount'                   => $this->discount,
            'final_price'                => $this->final_price,
            'ordered_at'                 => $this->ordered_at,
            'paid_at'                    => $this->paid_at,
            'additional_details'         => $this->additional_details,
            'additional_details'         => $this->additional_details,
            'note'                       => $this->note,
            'order_items'                => ClientOrderItemResource::collection($this->order_items),
        ];
    }

    /**
     * Get the color of the order status.
     *
     * If the order status is canceled, return red. If the order status is completed, return green.
     * If the payment method is not COD and the payment status is unpaid, return orange.
     * Otherwise, return orange.
     */
    private function getOrderStatusColor(): string
    {
        switch ($this->order_status) {
            case Order::ORDER_STATUS_CANCELED:
                return 'red';

            case Order::ORDER_STATUS_COMPLETED:
                return 'green';

            default:
                if (
                    $this->payment_method_id != PaymentMethod::COD_ID &&
                    $this->payment_status == Order::PAYMENT_STATUS_UNPAID
                ) {
                    return 'orange';
                }

                return 'orange';
        }
    }
}
