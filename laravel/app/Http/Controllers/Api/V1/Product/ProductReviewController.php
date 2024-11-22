<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Enums\ResponseEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductReviewRequest;
use App\Http\Resources\Product\Client\ClientProductReviewCollection;
use App\Http\Resources\Product\ProductReviewCollection;
use App\Http\Resources\Product\ProductReviewResource;
use App\Repositories\Interfaces\Product\ProductReviewRepositoryInterface;
use App\Services\Interfaces\Product\ProductReviewServiceInterface;
use Illuminate\Http\JsonResponse;

class ProductReviewController extends Controller
{
    protected $productReviewService;

    protected $productReviewRepository;

    public function __construct(
        ProductReviewServiceInterface $productReviewService,
        ProductReviewRepositoryInterface $productReviewRepository
    ) {
        $this->productReviewService = $productReviewService;
        $this->productReviewRepository = $productReviewRepository;
    }

    /**
     * Get all reviews for a product by product id
     */
    public function getReviewByProductId(string $productId): JsonResponse
    {

        $productReviews = $this->productReviewService->getReviewByProductId($productId);

        $data = new ClientProductReviewCollection($productReviews);

        return successResponse('', $data, true);
    }

    public function adminGetReplies(string $id): JsonResponse
    {

        $replies = $this->productReviewService->getReplies($id);

        $data = new ProductReviewResource($replies);

        return successResponse('', $data, true);
    }

    /**
     * Get all product reviews
     */
    public function getAllProductReviews(): JsonResponse
    {
        $productReviews = $this->productReviewService->paginate();

        $data = new ProductReviewCollection($productReviews);

        return successResponse('', $data, true);
    }

    /**
     * Create a new product review.
     */
    public function store(StoreProductReviewRequest $request): JsonResponse
    {
        $response = $this->productReviewService->createReview($request->all());

        return handleResponse($response, ResponseEnum::CREATED);
    }

    /**
     * Admin reply a product review.
     */
    public function adminReply(StoreProductReviewRequest $request): JsonResponse
    {
        $response = $this->productReviewService->adminReply($request->all());

        // return handleResponse($response, ResponseEnum::CREATED);
        return handleResponse($response);
    }

    /**
     * Update a reply product review as admin.
     */
    public function adminUpdateReply(StoreProductReviewRequest $request, string $replyId): JsonResponse
    {
        $response = $this->productReviewService->adminUpdateReply($request->all(), $replyId);

        return handleResponse($response);
    }
}
