<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Classes\Momo;
use App\Classes\Paypal;
use App\Classes\Vnpay;
use App\Enums\ResponseEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Resources\Order\Client\ClientOrderCollection;
use App\Http\Resources\Order\Client\ClientOrderResource;
use App\Http\Resources\Order\OrderCollection;
use App\Http\Resources\Order\OrderResource;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Services\Interfaces\Order\OrderServiceInterface;
use Illuminate\Http\Request;

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
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $order = $this->orderService->paginate();

        $data = new OrderCollection($order ?? []);

        return successResponse('', $data);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $orderCode
     * @return \Illuminate\Http\Response
     */
    public function show($orderCode)
    {
        $order = $this->orderService->getOrderUserByCode($orderCode);

        $data = is_null($order) ? null : new OrderResource($order ?? []);

        return successResponse('', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $order = $this->orderService->create();
        if (empty($order)) {
            return errorResponse(__('messages.order.error.create'));
        }

        $response = $this->handlePaymentMethod($order);

        return handleResponse($response, ResponseEnum::CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Order\UpdateOrderRequest  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrderRequest $request, string $id)
    {
        $response = $this->orderService->update($id);

        return handleResponse($response);
    }


    /**
     * Handle order payment.
     *
     * @param string $orderCode The order code.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleOrderPayment(string $orderCode)
    {
        $order = $this->orderService->getOrderUserByCode($orderCode);

        if (!$order) {
            $response = [
                'status'   => 'error',
                'messages' => __('messages.order.error.create'),
                'url'      => env('NUXT_APP_URL') . '/payment-fail',
            ];

            return handleResponse($response);
        }

        $response = $this->handlePaymentMethod($order);

        return handleResponse($response);
    }


    /**
     * Handle payment method.
     *
     * @param  \App\Models\Order  $order
     * @return array
     */
    private function handlePaymentMethod(Order $order): array
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
                    'status'   => 'success',
                    'messages' => __('messages.order.success.create'),
                    'url'      => env('NUXT_APP_URL') . '/order-success?code=' . $order->code,
                ];
            default:

                break;
        }

        return $response;
    }

    /**
     * Get an order by its code.
     *
     * @param string $orderCode The order code.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrder(string $orderCode)
    {
        $order = $this->orderService->getOrderUserByCode($orderCode);

        $data = is_null($order) ? null : new ClientOrderResource($order ?? []);

        return successResponse('', $data);
    }



    /**
     * Get all orders of the current user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrderByUser()
    {
        $orders = $this->orderService->getOrderByUser();

        $data = new ClientOrderCollection($orders);

        return successResponse('', $data);
    }


    /**
     * Update the order status to completed.
     *
     * @param string $id The order id.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCompletedOrder(string $id)
    {
        $response = $this->orderService->updateStatusOrderToCompleted($id);

        return handleResponse($response);
    }

    /**
     * Update the order status to cancelled.
     *
     * @param string $id The order id.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCancelledOrder(string $id)
    {

        $response = $this->orderService->updateStatusOrderToCancelled($id);

        return handleResponse($response);
    }
}
