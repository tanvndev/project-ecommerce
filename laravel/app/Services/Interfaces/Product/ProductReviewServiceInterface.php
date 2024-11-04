<?php

namespace App\Services\Interfaces\Product;

interface ProductReviewServiceInterface
{
    public function getReviewByProductId(string $productId);

    public function getAllProductReviews();

    public function createReview(array $data);

    public function adminReply(array $data, string $parentId);

    public function adminUpdateReply(array $data, string $replyId);
}
