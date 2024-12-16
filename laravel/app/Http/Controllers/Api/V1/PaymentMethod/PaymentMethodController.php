<?php

namespace App\Http\Controllers\Api\V1\PaymentMethod;

use App\Enums\ResponseEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentMethod\StorePaymentMethodRequest;
use App\Http\Requests\PaymentMethod\UpdatePaymentMethodRequest;
use App\Http\Resources\PaymentMethod\Client\ClientPaymentMethodCollection;
use App\Http\Resources\PaymentMethod\PaymentMethodCollection;
use App\Http\Resources\PaymentMethod\PaymentMethodResource;
use App\Repositories\Interfaces\PaymentMethod\PaymentMethodRepositoryInterface;
use App\Services\Interfaces\PaymentMethod\PaymentMethodServiceInterface;
use Illuminate\Http\JsonResponse;

class PaymentMethodController extends Controller
{
    protected $paymentMethodService;

    protected $paymentMethodRepository;

    public function __construct(
        PaymentMethodServiceInterface $paymentMethodService,
        PaymentMethodRepositoryInterface $paymentMethodRepository
    ) {
        $this->paymentMethodService = $paymentMethodService;
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    /**
     * Display a listing of the payment methods.
     */
    public function index(): JsonResponse
    {
        $this->authorizePermission('payment-methods-index');
        $paginator = $this->paymentMethodService->paginate();
        $data = new PaymentMethodCollection($paginator);

        return successResponse('', $data, true);
    }

    /**
     * Store a newly created payment method in storage.
     */
    public function store(StorePaymentMethodRequest $request): JsonResponse
    {
        $this->authorizePermission('payment-methods.index');
        $response = $this->paymentMethodService->create();

        return handleResponse($response, ResponseEnum::CREATED);
    }

    /**
     * Display the specified payment method.
     */
    public function show(string $id): JsonResponse
    {
        $this->authorizePermission('payment-methods.show');
        $paymentMethod = new PaymentMethodResource($this->paymentMethodRepository->findById($id));

        return successResponse('', $paymentMethod, true);
    }

    /**
     * Update the specified payment method in storage.
     */
    public function update(UpdatePaymentMethodRequest $request, string $id): JsonResponse
    {
        $this->authorizePermission('payment-methods.update');
        $response = $this->paymentMethodService->update($id);

        return handleResponse($response);
    }

    /**
     * Retrieve all payment methods for the client.
     */
    public function getAllPaymentMethod(): JsonResponse
    {
        $paymentMethods = $this->paymentMethodService->getAllPaymentMethod();

        $data = new ClientPaymentMethodCollection($paymentMethods);

        return successResponse('', $data, true);
    }
}
