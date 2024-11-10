<?php

namespace App\Http\Controllers\Api\V1\Cart;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\CreateAndUpdateRequest;
use App\Http\Resources\Cart\CartCollection;
use App\Repositories\Interfaces\Cart\CartRepositoryInterface;
use App\Services\Interfaces\Cart\CartServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cartService;

    protected $cartdRepository;

    public function __construct(
        CartServiceInterface $cartService,
        CartRepositoryInterface $cartdRepository
    ) {
        $this->cartService = $cartService;
        $this->cartdRepository = $cartdRepository;
    }

    /**
     * Display the user's cart.
     */
    public function index(): JsonResponse
    {
        $response = $this->cartService->getCart();

        $data = new CartCollection($response);

        return successResponse('', $data, true);
    }

    /**
     * Create or update an item in the cart.
     */
    public function createOrUpdate(CreateAndUpdateRequest $request): JsonResponse
    {
        $response = $this->cartService->createOrUpdate($request);

        $data = new CartCollection($response);

        return successResponse(__('messages.cart.success.create'), $data, true);
    }

    /**
     * Buy now.
     */
    public function buyNow(Request $request): JsonResponse
    {
        $response = $this->cartService->buyNow($request);

        return handleResponse($response);
    }

    /**
     * Remove the specified item from the cart.
     */
    public function destroy(string $id): JsonResponse
    {
        $response = $this->cartService->deleteOneItem($id);

        $data = new CartCollection($response);

        return successResponse('', $data, true);
    }

    /**
     * Clean the entire cart.
     */
    public function forceDestroy(): JsonResponse
    {
        $response = $this->cartService->cleanCart();

        return handleResponse($response);
    }

    /**
     * Handle selected items in the cart.
     */
    public function handleSelected(Request $request): JsonResponse
    {
        $response = $this->cartService->handleSelected($request);

        $data = new CartCollection($response);

        return successResponse('', $data, true);
    }

    /**
     * Delete selected items from the cart.
     */
    public function deleteCartSelected(): JsonResponse
    {
        $response = $this->cartService->deleteCartSelected();

        return handleResponse($response);
    }

    /**
     * Add paid products to the cart.
     */
    public function addPaidProductsToCart(Request $request): JsonResponse
    {
        $response = $this->cartService->addPaidProductsToCart($request);

        if (is_array($response)) {
            return $response;
        }

        $data = new CartCollection($response);

        return successResponse(__('messages.cart.success.create'), $data, true);
    }
}
