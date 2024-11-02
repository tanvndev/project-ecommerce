<?php

namespace App\Services\Interfaces\Product;

interface ProductReviewServiceInterface
{
    public function getReviewByProductId(string $productId);

    public function paginate();

    public function getReplies(string $id);

    public function createReview(array $data);

    public function adminReply(array $data);

    public function adminUpdateReply(array $data, string $replyId);
}
