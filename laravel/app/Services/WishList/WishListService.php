<?php

// Trong Laravel, Service Pattern thường được sử dụng để tạo các lớp service, giúp tách biệt logic của ứng dụng khỏi controller.

namespace App\Services\WishList;

use App\Events\WishListEvent;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use App\Repositories\Interfaces\WishList\WishListRepositoryInterface;
use App\Services\BaseService;
use App\Services\Interfaces\Cart\CartServiceInterface;
use App\Services\Interfaces\WishList\WishListServiceInterface;

class WishListService extends BaseService implements WishListServiceInterface
{
    public function __construct(
        protected WishListRepositoryInterface $wishListRepository,
        protected UserRepositoryInterface $userRepository,
        protected CartServiceInterface $cartService,
    ) {}

    public function paginate()
    {

        $select = ['id', 'user_id', 'product_variant_id'];

        $pageSize = request('pageSize');

        $withWhereHas = [
            'product_variant' => function ($q) {
                $q->where('stock', '>', 0)
                    ->whereHas('product', function ($query) {
                        $query->where('publish', 1);
                    });
            },
        ];

        $data = $this->wishListRepository->pagination(
            $select,
            [],
            $pageSize,
            [],
            [],
            ['product_variant'],
            [],
            $withWhereHas
        );

        return $data;
    }

    public function create()
    {
        return $this->executeInTransaction(function () {

            $payload = $this->preparePayload();

            if (empty($payload)) {
                return errorResponse(__('messages.wishlist.error.auth'));
            }

            $exists = $this->wishListRepository->findByWhere(
                [
                    'user_id'            => $payload['user_id'],
                    'product_variant_id' => $payload['product_variant_id'],
                ]
            );

            if (! empty($exists)) {
                return errorResponse(__('messages.wishlist.error.existed'));
            }

            $this->wishListRepository->create($payload);
            $wishlists = $this->getWishListByUserId();

            return $wishlists;
        }, __('messages.wishlist.error.create'));
    }

    private function preparePayload(): array
    {
        $payload = request()->except('_token', '_method');

        $payload['user_id'] = auth()->user()->id;

        return $payload;
    }

    public function destroy($id)
    {
        return $this->executeInTransaction(function () use ($id) {
            $this->wishListRepository->delete($id);

            $wishlists = $this->getWishListByUserId();

            return $wishlists;
        }, __('messages.wishlist.error.delete'));
    }

    public function getWishListByUserId()
    {
        $withWhereHas = [
            'product_variant' => function ($q) {
                $q->where('stock', '>', 0)
                    ->whereHas('product', function ($query) {
                        $query->where('publish', 1);
                    });
            },
        ];

        $conditions = [
            'where' => [
                'user_id' => auth()->user()->id,
            ]
        ];

        $wishLists = $this->wishListRepository->pagination(
            ['*'],
            $conditions,
            10,
            [],
            [],
            ['product_variant'],
            [],
            $withWhereHas
        );

        return $wishLists ?? collect();
    }

    public function destroyAll()
    {
        return $this->executeInTransaction(function () {

            $user = auth()->user();

            $user->wishList = $this->wishListRepository->findByWhere(
                ['user_id' => $user->id],
                ['*'],
                [],
                true
            );

            if (! $user->wishList) {
                return errorResponse(__('messages.wishlist.error.wishlist_not_found'));
            }
            foreach ($user->wishList as $item) {
                $item->delete();
            }

            return successResponse(__('messages.wishlist.success.clean'));
        }, __('messages.wishlist.error.delete'));
    }

    public function sendWishListMail()
    {
        return $this->executeInTransaction(function () {

            $userId = auth()->user()->id;

            $user = $this->userRepository->findById($userId);

            $data = $this->wishListRepository->findByWhere(
                ['user_id' => $userId],
                ['*'],
                ['product_variant'],
                true
            )->take(5);

            // event(new WishListEvent($user, $data));

            return successResponse(__('messages.wishlist.success.mail'));
        }, __('messages.wishlist.error.mail'));
    }

    public function addToCart($request)
    {
        return $this->executeInTransaction(function () use ($request) {

            $this->cartService->createOrUpdate($request);

            $conditions = [
                'user_id'            => auth()->user()->id,
                'product_variant_id' => $request->product_variant_id,
            ];

            $this->wishListRepository->deleteByWhere($conditions);
            $wishlists = $this->getWishListByUserId();

            return $wishlists;
        }, __('messages.cart.error.not_found'));
    }
}
