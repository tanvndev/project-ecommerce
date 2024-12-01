<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\User\UserResource;
use App\Models\Order;
use App\Models\PaymentMethod;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                          => $this->id ?? '',
            'key'                         => $this->id,
            'code'                        => $this->code,
            'customer_name'               => $this->customer_name,
            'customer_email'              => $this->customer_email,
            'customer_phone'              => $this->customer_phone,
            'shipping_address'            => $this->shipping_address,
            'payment_method_name'         => $this->payment_method->name,
            'order_status'                => Order::ORDER_STATUS[$this->order_status],
            'order_status_code'           => $this->order_status,
            'order_status_code_next'      => $this->getOrderStatusValueNext($this->order_status),
            'order_status_color'          => $this->getOrderStatusColor(),
            'payment_status'              => Order::PAYMENT_STATUS[$this->payment_status],
            'payment_status_code'         => $this->payment_status,
            'payment_status_color'        => $this->getPaymentStatusColor(),
            'payment_method_id'           => $this->payment_method_id,
            'total_price'                 => $this->total_price,
            'shipping_fee'                => $this->shipping_fee,
            'discount'                    => $this->discount,
            'final_price'                 => $this->final_price,
            'ordered_at'                  => Carbon::parse($this->ordered_at)->format('d/m/Y H:i'),
            'paid_at'                     => $this->paid_at,
            'additional_details'          => $this->additional_details,
            'province_name'               => $this->province->full_name,
            'district_name'               => $this->district->full_name,
            'ward_name'                   => $this->ward->full_name,
            'province_code'               => $this->province->code,
            'district_code'               => $this->district->code,
            'ward_code'                   => $this->ward->code,
            'note'                        => $this->note,
            'user'                        => new UserResource($this->user),
            'order_items'                 => OrderItemResource::collection($this->order_items),
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

    /**
     * Get the color of the payment status.
     *
     * If the payment status is paid, return green. Otherwise, return red.
     */
    private function getPaymentStatusColor(): string
    {
        switch ($this->payment_status) {
            case Order::PAYMENT_STATUS_PAID:
                return 'green';

            default:
                return 'red';
        }
    }

    private function getOrderStatusValueNext($orderStatus)
    {
        $statusMap = [
            Order::ORDER_STATUS_PENDING => Order::ORDER_STATUS_PROCESSING,
            Order::ORDER_STATUS_PROCESSING => Order::ORDER_STATUS_DELIVERING,
            Order::ORDER_STATUS_DELIVERING => Order::ORDER_STATUS_COMPLETED,
        ];

        return $statusMap[$orderStatus] ?? $orderStatus;
    }
}
