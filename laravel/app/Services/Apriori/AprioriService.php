<?php

namespace App\Services\Apriori;

use App\Models\Order;
use App\Models\ProductVariant;
use App\Repositories\Interfaces\Order\OrderRepositoryInterface;
use App\Services\Interfaces\Apriori\AprioriServiceInterface;
use Illuminate\Support\Facades\Redis;

class AprioriService implements AprioriServiceInterface
{

    public function __construct(
        protected OrderRepositoryInterface $orderRepository
    ) {}

    //     UPDATE orders
    // SET
    //     order_status = 'completed',
    //     payment_status = 'paid',

    public function suggestProducts($targetVariantProductId, $topN = 3)
    {
        // Lấy kết quả Apriori từ Redis
        $aprioriResults = $this->getAprioriResults($targetVariantProductId);

        // Lọc và sắp xếp các sản phẩm gợi ý theo confidence và lift
        usort($aprioriResults, function ($a, $b) {
            return ($b['confidence'] <=> $a['confidence']) ?: ($b['lift'] <=> $a['lift']);
        });

        // Chọn ra các sản phẩm gợi ý duy nhất
        $uniqueSuggestions = [];
        foreach ($aprioriResults as $suggestion) {
            $productId = $suggestion['product_variant_id'];
            if (!isset($uniqueSuggestions[$productId]) || $suggestion['confidence'] > $uniqueSuggestions[$productId]['confidence']) {
                $uniqueSuggestions[$productId] = $suggestion;
            }
        }

        $productVariantIds = array_slice(array_values($uniqueSuggestions), 0, $topN);

        if (empty($productVariantIds)) {
            return [];
        }

        $productVariantIds = collect($productVariantIds)->pluck('product_variant_id')->unique();

        $productVariants = ProductVariant::whereIn('id', $productVariantIds)->get();

        return $productVariants;
    }

    private function getAprioriResults($targetVariantProductId)
    {
        $frequentItemsets = Redis::lrange('apriori_frequent_itemsets', 0, -1);
        $associationRules = Redis::lrange('apriori_association_rules', 0, -1);

        $recommendedProducts = [];
        $result = [];

        foreach ($frequentItemsets as $json) {
            $itemset = json_decode($json, true);

            if (isset($itemset['items']) && in_array($targetVariantProductId, $itemset['items'])) {
                $recommendedProducts = array_merge($recommendedProducts, $itemset['items']);
            }
        }

        foreach ($associationRules as $json) {
            $rule = json_decode($json, true);

            if (isset($rule['antecedent']) && in_array($targetVariantProductId, $rule['antecedent'])) {
                foreach ($rule['consequent'] as $productId) {
                    $recommendedProducts[] = $productId;

                    $confidence = $rule['confidence'] ?? 0;
                    $lift = $rule['lift'] ?? 0;

                    $result[] = [
                        'product_variant_id' => $productId,
                        'confidence' => $confidence,
                        'lift' => $lift,
                    ];
                }
            }
        }

        // Loại bỏ các sản phẩm trùng lặp và sản phẩm đã chọn ban đầu
        $recommendedProducts = array_diff($recommendedProducts, [$targetVariantProductId]);
        $recommendedProducts = array_unique($recommendedProducts);

        // Lọc lại mảng kết quả để chỉ bao gồm các sản phẩm gợi ý
        $finalResult = [];
        foreach ($result as $item) {
            if (in_array($item['product_variant_id'], $recommendedProducts)) {
                $finalResult[] = $item;
            }
        }

        return $finalResult;
    }

}
