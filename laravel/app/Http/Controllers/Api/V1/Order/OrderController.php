<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Classes\Momo;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Classes\Vnpay;
use App\Classes\Paypal;
use App\Enums\ResponseEnum;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CreateOrderRequest;
use App\Http\Resources\Order\OrderResource;
use App\Http\Resources\Order\OrderCollection;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Resources\Order\Client\ClientOrderResource;
use App\Services\Interfaces\Order\OrderServiceInterface;
use App\Http\Resources\Order\Client\ClientOrderCollection;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(
        OrderServiceInterface $orderService,
    ) {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $order = $this->orderService->paginate();

        $data = new OrderCollection($order ?? []);

        return successResponse('', $data, true);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $orderCode
     */
    public function show($orderCode): JsonResponse
    {
        $order = $this->orderService->getOrder($orderCode);

        $data = is_null($order) ? null : new OrderResource($order ?? []);

        return successResponse('', $data, true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->create();
        if (empty($order) || $order['status'] == 'error') {
            return errorResponse(__('messages.order.error.create'), true);
        }

        $response = $this->handlePaymentMethod($order);

        return handleResponse($response, ResponseEnum::CREATED);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, string $id): JsonResponse
    {
        $response = $this->orderService->update($id);

        return handleResponse($response);
    }

    /**
     * Handle order payment.
     *
     * @param  string  $orderCode  The order code.
     */
    public function handleOrderPayment(string $orderCode): JsonResponse
    {
        $order = $this->orderService->getOrderUserByCode($orderCode);

        if (!$order) {
            $response = [
                'status' => 'error',
                'messages' => __('messages.order.error.create'),
                'url' => env('NUXT_APP_URL') . '/payment-fail',
            ];

            return handleResponse($response);
        }

        $response = $this->handlePaymentMethod($order);

        return handleResponse($response);
    }

    /**
     * Handle payment method.
     */
    private function handlePaymentMethod($order)
    {
        switch ($order->payment_method_id) {
            case PaymentMethod::VNPAY_ID:
                $response = Vnpay::payment($order);

                break;
            case PaymentMethod::MOMO_ID:
                $response = Momo::payment($order);

                break;
            case 'paypal_payment':
                $response = Paypal::payment($order);

                break;
            case PaymentMethod::COD_ID:
                $response = [
                    'status' => 'success',
                    'messages' => __('messages.order.success.create'),
                    'url' => env('NUXT_APP_URL') . '/order-success?code=' . $order->code,
                ];
            default:

                break;
        }

        return $response;
    }

    /**
     * Get an order by its code.
     *
     * @param  string  $orderCode  The order code.
     */
    public function getOrder(string $orderCode): JsonResponse
    {
        $order = $this->orderService->getOrderUserByCode($orderCode);

        $data = is_null($order) ? null : new ClientOrderResource($order ?? []);

        return successResponse('', $data, true);
    }

    /**
     * Get all orders of the current user.
     */
    public function getOrderByUser(): JsonResponse
    {
        $orders = $this->orderService->getOrderByUser();

        $data = new ClientOrderCollection($orders);

        return successResponse('', $data, true);
    }

    /**
     * Update the order status to completed.
     *
     * @param  string  $id  The order id.
     */
    public function updateCompletedOrder(string $id): JsonResponse
    {
        $response = $this->orderService->updateStatusOrderToCompleted($id);

        return handleResponse($response);
    }

    /**
     * Update the order status to cancelled.
     *
     * @param  string  $id  The order id.
     */
    public function updateCancelledOrder(string $id): JsonResponse
    {

        $response = $this->orderService->updateStatusOrderToCancelled($id);

        return handleResponse($response);
    }

    public function createOrder(CreateOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->createNewOrder();

        if (empty($order) || $order['status'] == 'error') {
            return errorResponse(__('messages.order.error.create'), true);
        }

        $response = $this->handlePaymentMethod($order);

        return handleResponse($response, ResponseEnum::CREATED);
    }
}
