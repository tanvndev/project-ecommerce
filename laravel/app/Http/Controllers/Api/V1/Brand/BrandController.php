<?php

namespace App\Http\Controllers\Api\V1\Brand;

use App\Enums\ResponseEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Brand\StoreBrandRequest;
use App\Http\Requests\Brand\UpdateBrandRequest;
use App\Http\Resources\Brand\BrandCollection;
use App\Http\Resources\Brand\BrandResource;
use App\Repositories\Interfaces\Brand\BrandRepositoryInterface;
use App\Services\Interfaces\Brand\BrandServiceInterface;
use Illuminate\Http\JsonResponse;

class BrandController extends Controller
{
    protected $brandService;

    protected $brandRepository;

    public function __construct(
        BrandServiceInterface $brandService,
        BrandRepositoryInterface $brandRepository
    ) {
        $this->brandService = $brandService;
        $this->brandRepository = $brandRepository;
    }

    /**
     * Display a listing of the brands.
     */
    public function index(): JsonResponse
    {
        $this->authorizePermission('brands.index');
        $paginator = $this->brandService->paginate();
        $data = new BrandCollection($paginator);

        return successResponse('', $data, true);
    }

    /**
     * Store a newly created brand in storage.
     */
    public function store(StoreBrandRequest $request): JsonResponse
    {
        $this->authorizePermission('brands.store');
        $response = $this->brandService->create();

        return handleResponse($response, ResponseEnum::CREATED);
    }

    /**
     * Display the specified brand.
     */
    public function show(string $id): JsonResponse
    {
        $this->authorizePermission('brands.show');
        $brand = new BrandResource($this->brandRepository->findById($id));

        return successResponse('', $brand, true);
    }

    /**
     * Update the specified brand in storage.
     */
    public function update(UpdateBrandRequest $request, string $id): JsonResponse
    {
        $this->authorizePermission('brands.update');
        $response = $this->brandService->update($id);

        return handleResponse($response);
    }

    /**
     * Remove the specified brand from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $this->authorizePermission('brands.destroy');
        $response = $this->brandService->destroy($id);

        return handleResponse($response);
    }
}
