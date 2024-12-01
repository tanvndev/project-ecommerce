<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Enums\ResponseEnum;
use App\Http\Resources\Order\OrderStatusCollection;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderStatusRequest;
use App\Services\Interfaces\Order\OrderStatusServiceInterface;
use App\Repositories\Interfaces\Order\OrderStatusRepositoryInterface;
use Illuminate\Http\Request;

class OrderStatusController extends Controller
{

    public function __construct(
        protected OrderStatusServiceInterface $orderStatusService,
        protected  OrderStatusRepositoryInterface $orderStatusRepository
    ) {}

    /**
     * Display a listing of the brands.
     */
    public function index(): JsonResponse
    {
        $paginator = $this->orderStatusService->paginate();

        $data = new OrderStatusCollection($paginator);

        return successResponse('', $data, true);
    }

    /**
     * Store a newly created brand in storage.
     */
    public function store(StoreOrderStatusRequest $request): JsonResponse
    {
        $response = $this->orderStatusService->create();

        return handleResponse($response, ResponseEnum::CREATED);
    }

    /**
     * Update the specified brand in storage.
     */
    public function update(Request $request): JsonResponse
    {
        $response = $this->orderStatusService->update();

        return handleResponse($response);
    }

    public function cancel(Request $request): JsonResponse
    {
        $response = $this->orderStatusService->cancel();

        return handleResponse($response);
    }
}
