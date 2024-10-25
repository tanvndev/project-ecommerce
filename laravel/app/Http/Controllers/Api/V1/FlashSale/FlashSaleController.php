<?php

namespace App\Http\Controllers\Api\V1\FlashSale;

use App\Enums\ResponseEnum;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\FlashSale\FlashSaleCollection;
use App\Http\Requests\FlashSale\FlashSaleStoreRequest;
use App\Services\Interfaces\FlashSale\FlashSaleServiceInterface;
use App\Repositories\Interfaces\FlashSale\FlashSaleRepositoryInterface;

class FlashSaleController extends Controller
{
    protected $flashSaleService;

    protected $flashSaleRepository;
    function __construct(FlashSaleServiceInterface $flashSaleService,  FlashSaleRepositoryInterface $flashSaleRepository)
    {
        $this->flashSaleService = $flashSaleService;
        $this->flashSaleRepository = $flashSaleRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $paginator = $this->flashSaleService->paginate();
        $data = new FlashSaleCollection($paginator);

        return successResponse('', $data, true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FlashSaleStoreRequest $request)
    {
        $data = $this->flashSaleService->store($request->all());

        return handleResponse($data, ResponseEnum::CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $response = $this->flashSaleService->findById($id);

        $data = new FlashSaleCollection($response);

        return successResponse('', $data, true);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
       $data = $this->flashSaleService->update($id, $request->all());

        return successResponse('', $data, true);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function changeStatus(string $id){

        $data = $this->flashSaleService->changeStatus($id);

        return successResponse('', $data, true);
    }
}
