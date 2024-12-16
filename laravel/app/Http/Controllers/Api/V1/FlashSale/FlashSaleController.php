<?php

namespace App\Http\Controllers\Api\V1\FlashSale;

use App\Enums\ResponseEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\FlashSale\FlashSaleStoreRequest;
use App\Http\Requests\FlashSale\FlashSaleUpdateRequest;
use App\Http\Resources\FlashSale\Client\ClientFlashSaleResource;
use App\Http\Resources\FlashSale\FlashSaleCollection;
use App\Http\Resources\FlashSale\FlashSaleResource;
use App\Repositories\Interfaces\FlashSale\FlashSaleRepositoryInterface;
use App\Services\Interfaces\FlashSale\FlashSaleServiceInterface;
use Illuminate\Http\JsonResponse;

class FlashSaleController extends Controller
{
    protected $flashSaleService;

    protected $flashSaleRepository;

    public function __construct(
        FlashSaleServiceInterface $flashSaleService,
        FlashSaleRepositoryInterface $flashSaleRepository
    ) {
        $this->flashSaleService = $flashSaleService;
        $this->flashSaleRepository = $flashSaleRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $this->authorizePermission('flash-sales.index');
        $paginator = $this->flashSaleService->paginate();
        $data = new FlashSaleCollection($paginator);

        return successResponse('', $data, true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FlashSaleStoreRequest $request)
    {
        $this->authorizePermission('flash-sales.store');
        $data = $this->flashSaleService->store($request->all());

        return handleResponse($data, ResponseEnum::CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->authorizePermission('flash-sales.show');
        $response = $this->flashSaleService->findById($id);

        $data = new FlashSaleResource($response);

        return successResponse('', $data, true);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FlashSaleUpdateRequest $request, string $id)
    {
        $this->authorizePermission('flash-sales.update');
        $data = $this->flashSaleService->update($id, $request->all());

        return handleResponse($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {}

    public function getFlashSale()
    {
        $response = $this->flashSaleService->getFlashSale();
        $data = new ClientFlashSaleResource($response);

        return successResponse('', $data);
    }
}
