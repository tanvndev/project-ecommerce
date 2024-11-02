<?php

namespace App\Services\Product;

use App\Classes\Upload;
use App\Models\User;
use App\Repositories\Interfaces\Product\ProductReviewRepositoryInterface;
use App\Services\BaseService;
use App\Services\Interfaces\Product\ProductReviewServiceInterface;
use Illuminate\Support\Collection;

class ProductReviewService extends BaseService implements ProductReviewServiceInterface
{
    protected $productReviewRepository;

    public function __construct(ProductReviewRepositoryInterface $productReviewRepository)
    {
        $this->productReviewRepository = $productReviewRepository;
    }

    /**
     * Get all reviews for a product by product id
     *
     * @param  int  $productId
     */
    public function getReviewByProductId(string $productId): Collection
    {

        $productReviews = $this->productReviewRepository->findByWhere(
            [
                'product_id' => $productId,
                'parent_id'  => null,
            ],
            ['*'],
            ['replies', 'user'],
            true
        );

        return $productReviews ?? collect();
    }

    /**
     * Get all product reviews without replies
     */
    public function paginate()
    {

        $request = request();

        $condition = [
            'search'  => addslashes($request->search),
            'publish' => $request->publish,
            'archive' => $request->boolean('archive'),
            'where' => [
                'parent_id' => null,
            ]
        ];

        $pageSize = $request->pageSize;

        $data = $pageSize && $request->page
            ? $this->productReviewRepository->pagination(['*'], $condition, $pageSize, ['id' => 'desc'], [], ['replies', 'user', 'product', 'order'])
            : $this->productReviewRepository->findByWhere(['publish' => 1], ['*'], ['replies', 'user', 'product', 'order'], true);

        return $data;
    }

    /**
     * Create a product review.
     *
     * @return \Illuminate\Http\Response
     */
    public function createReview(array $data)
    {
        return $this->executeInTransaction(function () use ($data) {
            /**
             * @var User $user
             */
            $user = auth()->user();

            if ($user->user_catalogue->id === 1) {
                return errorResponse(__('messages.product_review.error.admin_not_allowed'));
            }

            $order = $user->orders()->where('id', $data['order_id'])->first();

            if (! $order) {
                return errorResponse(__('messages.product_review.error.order_not_found'));
            }

            $productIds = is_array($data['product_id'])
                ? $data['product_id']
                : explode(',', $data['product_id']);

            $data['user_id'] = $user->id;

            $uploadedImages = [];
            if (! empty($data['images']) && is_array($data['images'])) {
                $uploadResponse = $this->handleImageUploads($data['images']);

                if ($uploadResponse['status'] === 'error') {
                    return errorResponse($uploadResponse['message']);
                }

                $uploadedImages = $uploadResponse['data'];
            }

            foreach ($productIds as $productId) {
                $orderItems = $order->order_items()
                    ->where('order_id', $data['order_id'])
                    ->whereHas('product_variant', function ($query) use ($productId) {
                        $query->where('product_id', $productId);
                    })->exists();

                if (! $orderItems) {
                    return errorResponse(__('messages.product_review.error.order_item_not_found'));
                }

                $existing = $this->productReviewRepository->findByWhere([
                    'product_id' => $productId,
                    'user_id'    => $data['user_id'],
                    'order_id'   => $data['order_id'],
                ]);

                if ($existing) {
                    return errorResponse(__('messages.product_review.error.already_exists'));
                }

                $reviewData = array_merge($data, [
                    'product_id' => $productId,
                    'images'     => $uploadedImages,
                ]);

                $this->productReviewRepository->create($reviewData);
            }

            return successResponse(__('messages.product_review.success.create'));
        }, __('messages.create.error'));
    }

    /**
     * Admin reply a product review.
     *
     * @return \Illuminate\Http\Response
     */
    public function adminReply(array $data)
    {
        return $this->executeInTransaction(function () use ($data) {

            if (auth()->user()->user_catalogue_id !== User::ROLE_ADMIN) {

                return errorResponse(__('messages.product_review.error.not_admin'));
            }

            $parentReview = $this->productReviewRepository->findByWhere([
                'id' => $data['review_id'],
            ]);

            if (! $parentReview) {
                return errorResponse(__('messages.product_review.error.parent_not_found'));
            }

            $existingReply = $this->productReviewRepository->findByWhere([
                'parent_id' => $data['review_id'],
            ]);

            if ($existingReply) {
                return errorResponse(__('messages.product_review.error.admin_reply_already_exists'));
            }

            $uploadedImages = [];
            if (! empty($data['images']) && is_array($data['images'])) {
                $uploadResponse = $this->handleImageUploads($data['images']);

                if ($uploadResponse['status'] === 'error') {
                    return errorResponse($uploadResponse['message']);
                }

                $uploadedImages = $uploadResponse['data'];
            }

            $data['product_id'] = $parentReview->product_id;
            $data['order_id'] = $parentReview->order_id;
            $data['user_id'] = auth()->user()->id;
            $data['parent_id'] = $parentReview->id;
            $data['images'] = $uploadedImages;

            $this->productReviewRepository->create($data);

            return successResponse(__('messages.create.success'));
        }, __('messages.create.error'));
    }

    public function getReplies(string $id)
    {
        return $this->productReviewRepository->findById($id, ['*'], ['replies', 'user']);

    }

    /**
     * Update a reply product review as admin.
     *
     * @return \Illuminate\Http\Response
     */
    public function adminUpdateReply(array $data, string $replyId)
    {

        return $this->executeInTransaction(function () use ($data, $replyId) {

            if (auth()->user()->user_catalogue_id !== User::ROLE_ADMIN) {

                return errorResponse(__('messages.product_review.error.not_admin'));
            }

            $parentReview = $this->productReviewRepository->findByWhere([
                'parent_id' => $replyId,
            ]);

            if (! $parentReview) {
                return errorResponse(__('messages.product_review.error.parent_not_found'));
            }

            $this->productReviewRepository->update($parentReview->id, $data);

            return successResponse(__('messages.update.success'));
        }, __('messages.update.error'));
    }

    /**
     * Handles the image uploads for product reviews.
     */
    protected function handleImageUploads(array $images): array
    {
        $uploadedImages = [];

        foreach ($images as $image) {
            $uploadResponse = Upload::uploadImage($image);

            if (! $uploadResponse['status'] === 'success') {
                return [
                    'status'  => 'error',
                    'message' => $uploadResponse['message'],
                ];
            }

            $uploadedImages[] = $uploadResponse['data'];
        }

        return [
            'status'  => 'success',
            'data'    => $uploadedImages,
        ];
    }
}
