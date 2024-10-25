<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Enums\ResponseEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductAttributeRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Requests\Product\UpdateProductVariantRequest;
use App\Http\Resources\Product\Client\ClientProductResource;
use App\Http\Resources\Product\Client\ClientProductVariantCollection;
use App\Http\Resources\Product\ProductCollection;
use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\Product\ProductVariantCollection;
use App\Repositories\Interfaces\Product\ProductRepositoryInterface;
use App\Services\Interfaces\Apriori\AprioriServiceInterface;
use App\Services\Interfaces\Product\ProductServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function __construct(
        protected ProductServiceInterface $productService,
        protected ProductRepositoryInterface $productRepository,
        protected AprioriServiceInterface $aprioriService
    ) {}

    /**
     * Display a listing of the products.
     */
    public function index(): JsonResponse
    {
        $paginator = $this->productService->paginate();
        $data = new ProductCollection($paginator);

        return successResponse('', $data, true);
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $response = $this->productService->create();

        return handleResponse($response, ResponseEnum::CREATED);
    }

    /**
     * Display the specified product.
     */
    public function show(string $id): JsonResponse
    {
        $response = new ProductResource(
            $this->productRepository->findById($id)
        );

        return successResponse('', $response, true);
    }

    /**
     * Update the specified product in storage.
     */
    public function update(UpdateProductRequest $request, string $id): JsonResponse
    {
        $response = $this->productService->update($id);

        return handleResponse($response);
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $response = $this->productService->destroy($id);

        return handleResponse($response);
    }

    /**
     * Get all product variants.
     */
    public function getProductVariants(): JsonResponse
    {
        $paginator = $this->productService->getProductVariants();
        $data = new ProductVariantCollection($paginator);

        return successResponse('', $data, true);
    }

    /**
     * Update a specific product variant.
     */
    public function updateVariant(UpdateProductVariantRequest $request): JsonResponse
    {
        $response = $this->productService->updateVariant();

        return handleResponse($response);
    }

    /**
     * Delete a specific product variant.
     */
    public function deleteVariant(string $id): JsonResponse
    {
        $response = $this->productService->deleteVariant($id);

        return handleResponse($response);
    }

    /**
     * Update attributes for a specific product.
     */
    public function updateAttribute(UpdateProductAttributeRequest $request, string $productId): JsonResponse
    {
        $response = $this->productService->updateAttribute($productId);

        return handleResponse($response);
    }

    public function getProductReport()
    {
        $response = $this->productService->getProductReport();

        return successResponse('', $response, true);
    }

    // CLIENT API //

    /**
     * Get a specific product by its slug.
     */
    public function getProduct(string $slug): JsonResponse
    {
        $response = new ClientProductResource(
            $this->productService->getProduct($slug)
        );

        return successResponse('', $response, true);
    }

    public function getSuggestedProduct(string $productVariantId): JsonResponse
    {
        $response = $this->aprioriService->suggestProducts($productVariantId);
        $data = new ClientProductVariantCollection($response);

        return successResponse('', $data, true);
    }
}
