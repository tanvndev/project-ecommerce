<?php

namespace App\Repositories\ProductReview;

use App\Services\BaseService;
use App\Services\Interfaces\ProductReview\ProductReviewServiceInterface;
use Illuminate\Support\Facades\Storage;

class ProductReviewService extends BaseService implements ProductReviewServiceInterface
{
    protected $productReviewRepository;

    public function __construct(ProductReviewRepository $productReviewRepository)
    {
        $this->productReviewRepository = $productReviewRepository;
    }

    public function createReview(array $data)
    {
        return $this->executeInTransaction(function () use ($data) {

            if ( ! auth()->check()) {
                return;
            }

            $data['user_id'] = auth()->user()->id;

            $existing = $this->productReviewRepository->findByWhere([
                'product_id' => $data['product_id'],
            ])->first();

            if ($existing) {
                return errorResponse(__('messages.product_review.error.already_exists'));
            }

            if (isset($data['images'])) {
                $images = [];
                foreach ($data['images'] as $image) {
                    $path = Storage::put('uploads/product-reviews', $image);
                    $images[] = $path;
                }
                $data['images'] = json_encode($images);
            }

            $this->productReviewRepository->create($data);

            return successResponse(__('messages.create.success'));
        }, __('messages.create.error'));
    }

    public function adminReply(array $data) {}

    public function adminUpdateReply(array $data) {}
}
