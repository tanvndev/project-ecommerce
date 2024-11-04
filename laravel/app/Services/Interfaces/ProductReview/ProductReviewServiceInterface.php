<?php

namespace App\Services\Interfaces\ProductReview;

interface ProductReviewServiceInterface
{
    public function createReview(array $data);

    public function adminReply(array $data);

    public function adminUpdateReply(array $data);
}
