<?php

namespace App\Http\Resources\Order;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderStatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'key'                   => $this->id,
            'code'                  => $this->order->code,
            'requested_by'          => [
                'name'  => $this->requester->fullname,
                'email' => $this->requester->email,
            ],
            'current_status'        => $this->current_status,
            'requested_status'      => $this->requested_status,
            'current_status_text'   => Order::ORDER_STATUS[$this->current_status],
            'requested_status_text' => Order::ORDER_STATUS[$this->requested_status],
            'reason'                => $this->reason,
            'rejection_reason'      => $this->rejection_reason,
            'status'                => $this->getStatus(),
            'approved_by'           => $this->approved_by ? $this->approver->fullname . ' - ' . $this->approver->email : null,
            'approved_at'           => $this->approved_at ? $this->approved_at->format('d/m/Y H:i:s') : null,
        ];
    }

    private function getStatus()
    {
        if ($this->status === 'pending') {
            return [
                'color' => 'warning',
                'text'  => 'Chờ xét duyệt',
            ];
        } elseif ($this->status === 'approved') {
            return [
                'color' => 'success',
                'text'  => 'Đã xét duyệt',
            ];
        }

        return [
            'color' => 'red',
            'text'  => 'Từ chối',
        ];
    }
}
