<?php

// Trong Laravel, Service Pattern thường được sử dụng để tạo các lớp service, giúp tách biệt logic của ứng dụng khỏi controller.

namespace App\Services\Order;

use App\Services\BaseService;
use Illuminate\Support\Facades\Mail;

use App\Mail\RequestStatusNotification;
use App\Mail\sendOrderStatusChangeRequestEmail;
use App\Services\Interfaces\Order\OrderStatusServiceInterface;
use App\Repositories\Interfaces\Order\OrderRepositoryInterface;
use App\Repositories\Interfaces\Order\OrderStatusRepositoryInterface;


class OrderStatusService extends BaseService implements OrderStatusServiceInterface
{

    public function __construct(
        protected OrderStatusRepositoryInterface $orderStatusRepository,
        protected OrderRepositoryInterface $orderRepository
    ) {}

    public function paginate()
    {
        $request = request();

        $condition = [
            'search'  => addslashes($request->search),
            'publish' => $request->publish,
            'archive' => $request->boolean('archive'),
        ];

        $select = ['id', 'order_id', 'requested_by', 'current_status', 'requested_status', 'reason', 'rejection_reason', 'status', 'approved_by', 'approved_at'];
        $pageSize = $request->pageSize;

        $data = $pageSize && $request->page
            ? $this->orderStatusRepository->pagination($select, $condition, $pageSize, [], [], ['order', 'requester'])
            : $this->orderStatusRepository->findByWhere(['publish' => 1], $select, [], true);

        return $data;
    }

    public function create()
    {
        return $this->executeInTransaction(function () {

            if (auth()->user()->user_catalogue_id == 1) return errorResponse('Tài khoản admin có thể cập nhật trạng thái lùi về sau trực tiếp!');

            $payload = $this->payload();

            $order = $this->orderRepository->findById($payload['order_id']);

            $isForwardStatus = $this->isForwardStatus($order->order_status, $payload['requested_status']);

            if (!$isForwardStatus) {
                return errorResponse('Bạn có thể trực tiếp cập nhật trạng thái tiến lên mà không cần tạo yêu cầu');
            }

            $existingRequest = $this->orderStatusRepository->findByWhere(['order_id' => $payload['order_id'], 'status' => 'pending']);

            if ($existingRequest) {
                return errorResponse('Đã có một yêu cầu đang chờ xử lý cho đơn hàng này');
            }

            $payload['requested_by'] = auth()->id();

            $payload['name'] = auth()->user()->fullname;

            $payload['current_status'] = $order->order_status;

            $payload['code'] = $order->code;

            $payload['url'] = env('VUE_APP_URL') . '/order/update/' . $order->code;

            $this->orderStatusRepository->create($payload);

            Mail::send(new sendOrderStatusChangeRequestEmail($payload));

            return successResponse('Yêu cầu cập nhật trạng thái đơn hàng thành công');
        }, __('messages.create.error'));
    }

    public function update()
    {
        return $this->executeInTransaction(function () {

            if (auth()->user()->user_catalogue_id != 1) return errorResponse('Bạn không có đủ thẩm quyền để thực hiện chức năng này!');

            $payload = $this->payload();

            $orderStatus = $this->orderStatusRepository->findById($payload['id']);

            if ($orderStatus->status !== 'pending') {
                return errorResponse('Yêu cầu không được xử lý');
            }

            $order = $this->orderRepository->findByWhere(['code' => $orderStatus->order->code]);


            $order->order_status = $orderStatus->requested_status;
            $order->save();

            $orderStatus->status = 'approved';
            $orderStatus->approved_by = auth()->id();
            $orderStatus->approved_at = now();
            $orderStatus->save();

            return successResponse('Yêu cầu cập nhật trạng thái đơn hàng thành công');
        }, 'Cập nhật không thành công!');
    }

    private function payload()
    {
        return request()->except('_token', '_method');
    }

    private function isForwardStatus($order_status, $requested_status)
    {

        $statuses = ['pending', 'processing', 'delivering', 'completed', 'cancelled', 'returned'];


        $currentStatusIndex = array_search($order_status, $statuses);
        $requestedStatusIndex = array_search($requested_status, $statuses);


        if ($requestedStatusIndex >= $currentStatusIndex) {
            return false;
        }

        return true;
    }

    public function cancel()
    {
        return $this->executeInTransaction(function () {

            if (auth()->user()->user_catalogue_id != 1) return errorResponse('Bạn không có đủ thẩm quyền để thực hiện chức năng này!');

            $payload = $this->payload();

            $orderStatus = $this->orderStatusRepository->findById($payload['id'], ['*'], ['order', 'requester']);

            if ($orderStatus->status !== 'pending') {
                return errorResponse('Yêu cầu không được xử lý');
            }

            $orderStatus->status = 'cancelled';
            $orderStatus->rejection_reason = $payload['rejection_reason'];
            $orderStatus->approved_by = auth()->id();
            $orderStatus->approved_at = now();
            $orderStatus->save();

            Mail::to($orderStatus->requester->email)->send(new RequestStatusNotification($orderStatus));

            return successResponse('Đã huy yêu cầu cập nhật trạng thái đơn hàng');
        }, 'Huy yêu cầu cập nhật trạng thái đơn hàng thất bại!');
    }
}
