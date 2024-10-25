<?php

// Trong Laravel, Service Pattern thường được sử dụng để tạo các lớp service, giúp tách biệt logic của ứng dụng khỏi controller.

namespace App\Services\Product;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductVariantAttributeValue;
use App\Repositories\Interfaces\Attribute\AttributeValueRepositoryInterface;
use App\Repositories\Interfaces\Product\ProductRepositoryInterface;
use App\Repositories\Interfaces\Product\ProductVariantRepositoryInterface;
use App\Services\BaseService;
use App\Services\Interfaces\Product\ProductServiceInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class ProductService extends BaseService implements ProductServiceInterface
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        protected ProductVariantRepositoryInterface $productVariantRepository,
        protected AttributeValueRepositoryInterface $attributeValueRepository
    ) {}

    public function paginate()
    {
        $request = request();

        $condition = [
            'search'  => addslashes($request->search),
            'publish' => $request->publish,
            'archive' => $request->boolean('archive'),
        ];

        $select = ['id', 'name', 'brand_id', 'publish', 'product_type', 'upsell_ids', 'canonical', 'meta_title', 'meta_description', 'shipping_ids'];
        $orderBy = ['id' => 'desc'];
        $relations = ['variants', 'catalogues', 'brand'];

        $cacheKey = 'products_' . md5(json_encode($condition)) . '_page_' . $request->page;

        $data = Cache::remember($cacheKey, 600, function () use ($select, $condition, $orderBy, $relations) {
            return $this->productRepository->pagination(
                $select,
                $condition,
                request()->pageSize,
                $orderBy,
                [],
                $relations
            );
        });

        return $data;
    }

    public function create()
    {
        return $this->executeInTransaction(function () {
            $payload = $this->preparePayload();
            $product = $this->productRepository->create($payload);
            // dd($payload);
            $this->syncCatalogue($product, $payload['product_catalogue_id']);
            $this->createProductAttribute($product, $payload);
            $this->createProductVariant($product, $payload);

            return successResponse(__('messages.create.success'));
        }, __('messages.create.error'));
    }

    public function syncCatalogue($product, array $catalogueIds): void
    {
        $product->catalogues()->sync($catalogueIds);
    }

    private function preparePayload(): array
    {
        $payload = request()->except('_token', '_method');
        $payload = $this->createSEO($payload, 'name', 'excerpt');
        $payload['shipping_ids'] = array_map('intval', $payload['shipping_ids'] ?? []);

        return $payload;
    }

    private function createProductVariant($product, array $payload)
    {
        $is_discount_time = filter_var($payload['is_discount_time'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $sale_price_start_at = $payload['sale_price_time'][0] ?? null;
        $sale_price_end_at = $payload['sale_price_time'][1] ?? null;
        $uuid = Uuid::uuid5(Uuid::NAMESPACE_DNS, $product->id . ', ' . 'default');

        $mainData = [
            'uuid'                => $uuid,
            'name'                => $payload['name'] ?? null,
            'image'               => $payload['image'] ?? null,
            'album'               => $payload['album'] ?? null,
            'price'               => $payload['price'] ?? null,
            'sale_price'          => $payload['sale_price'] ?? null,
            'cost_price'          => $payload['cost_price'] ?? null,
            'is_discount_time'    => $is_discount_time,
            'weight'              => $payload['weight'] ?? null,
            'length'              => $payload['length'] ?? null,
            'width'               => $payload['width'] ?? null,
            'height'              => $payload['height'] ?? null,
            'stock'               => $payload['stock'] ?? 0,
            'low_stock_amount'    => $payload['low_stock_amount'] ?? 0,
            'sku'                 => generateSKU($payload['name'], 3, ['default']),
            'sale_price_start_at' => $sale_price_start_at ? convertToYyyyMmDdHhMmSs($sale_price_start_at) : null,
            'sale_price_end_at'   => $sale_price_end_at ? convertToYyyyMmDdHhMmSs($sale_price_end_at) : null,
        ];

        if ($payload['product_type'] === Product::TYPE_SIMPLE) {
            return $product->variants()->create($mainData);
        }

        if ($payload['product_type'] === Product::TYPE_VARIABLE) {
            return $this->createProductVariants($product, $payload, $mainData);
        }
    }

    private function createProductVariants($product, array $payload, array $mainData)
    {
        $variables = $payload[Product::TYPE_VARIABLE] ?? [];
        $variants = json_decode($payload['variants'] ?? '[]', true);
        $variantTexts = $variants['variantTexts'];
        $variantIds = $variants['variantIds'];
        $attributes = removeEmptyValues(json_decode($payload['attributes'] ?? '[]', true));
        $attributeIds = $attributes['attrIds'];
        $attributeIdEnableVariation = $this->formatAttributeEnableVariation($attributeIds, $attributes['enable_variation'] ?? []);

        if (empty($attributeIdEnableVariation)) {
            return false;
        }

        $productVariantPayload = collect($variables ?? [])
            ->map(function ($variable, $key) use ($mainData, $variantTexts, $variantIds, $product) {

                $options = explode('-', $variantTexts[$key] ?? '');
                $sku = generateSKU($mainData['name'], 3, $options) . '-' . ($key + 1);
                $name = "{$mainData['name']} {$variantTexts[$key]}";
                $attribute_value_combine = sortString($variantIds[$key]);
                $uuid = Uuid::uuid5(Uuid::NAMESPACE_DNS, $product->id . ', ' . $attribute_value_combine);

                $variantData = [
                    'uuid'                    => $uuid,
                    'name'                    => $name,
                    'attribute_value_combine' => $attribute_value_combine,
                    'image'                   => $variable['image'] ?? $mainData['image'],
                    'album'                   => $variable['album'] ?? $mainData['album'],
                    'price'                   => $variable['price'] ?? $mainData['price'],
                    'sale_price'              => $variable['sale_price'] ?? null,
                    'cost_price'              => $variable['cost_price'] ?? $mainData['cost_price'],
                    'is_discount_time'        => filter_var($variable['is_discount_time'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'width'                   => $variable['width'] ?? $mainData['width'],
                    'height'                  => $variable['height'] ?? $mainData['height'],
                    'length'                  => $variable['length'] ?? $mainData['length'],
                    'weight'                  => $variable['weight'] ?? $mainData['weight'],
                    'sku'                     => $sku,
                    'stock'                   => $variable['stock'] ?? 0,
                    'low_stock_amount'        => $variable['low_stock_amount'] ?? 0,
                    'sale_price_start_at'     => isset($variable['sale_price_time'][0]) ? convertToYyyyMmDdHhMmSs($variable['sale_price_time'][0]) : null,
                    'sale_price_end_at'       => isset($variable['sale_price_time'][1]) ? convertToYyyyMmDdHhMmSs($variable['sale_price_time'][1]) : null,
                ];

                return $variantData;
            })
            ->values()
            ->toArray();

        $createdVariants = $product->variants()->createMany($productVariantPayload);
        $variantAttributeValuePayload = $this->combineVariantAttributeValue($createdVariants);

        DB::table('product_variant_attribute_value')->insert($variantAttributeValuePayload);
    }

    private function createProductAttribute($product, array $payload)
    {
        if (! isset($payload['attributes']) || empty($payload['attributes'])) {
            return false;
        }

        $attributes = removeEmptyValues(json_decode($payload['attributes'] ?? '[]', true));

        $attributePayload = [];

        foreach ($attributes['attrIds'] as $attrId => $attrValueIds) {
            $attributePayload[] = [
                'attribute_id'        => $attrId,
                'attribute_value_ids' => $attrValueIds,
                'enable_variation'    => $attributes['enable_variation'][$attrId] ?? false,
            ];
        }

        $product->attributes()->createMany($attributePayload);

        return true;
    }

    private function formatAttributeEnableVariation(array $attributeIds, array $enableVariantion, bool $enable = true): array
    {
        $attrIds = [];
        foreach ($enableVariantion as $key => $value) {
            if ($enable == true && $value == true) {
                $attrIds[$key] = $attributeIds[$key];
            } elseif ($enable == false && $value == true) {
                unset($attributeIds[$key]);
            }
        }

        return $enable ? $attrIds : $attributeIds;
    }

    private function combineVariantAttributeValue($productVariants)
    {
        if (! count($productVariants)) {
            return [];
        }

        $result = $productVariants->flatMap(function ($item) {
            $attributeValueIds = explode(',', $item['attribute_value_combine']);

            return collect($attributeValueIds)->map(function ($value) use ($item) {
                return [
                    'attribute_value_id' => $value,
                    'product_variant_id' => $item['id'],
                ];
            });
        });

        return $result->toArray();
    }

    public function update($id)
    {
        return $this->executeInTransaction(function () use ($id) {
            $payload = $this->preparePayload();

            $product = $this->productRepository->save($id, $payload);
            $this->syncCatalogue($product, $payload['product_catalogue_id']);
            $this->updateProductAttribute($product, $payload['attribute_value_ids'] ?? []);

            return successResponse(__('messages.update.success'));
        }, __('messages.update.error'));
    }

    private function updateProductAttribute($product, array $attributeValueIds)
    {
        if (empty($attributeValueIds)) {
            return;
        }

        $attributeValueIds = removeEmptyValues($attributeValueIds);
        $attributePayload = [];

        foreach ($attributeValueIds as $attrId => $attributeValueId) {
            $attributePayload[] = [
                'attribute_id'        => $attrId,
                'attribute_value_ids' => array_map('intval', $attributeValueId),
                'enable_variation'    => false,
            ];
        }

        $product->attributes()->where('enable_variation', false)->delete();
        $product->attributes()->createMany($attributePayload);
    }

    public function destroy($id) {}

    // VARIANT

    public function getProductVariants()
    {
        $request = request();
        $condition = [
            'search'  => addslashes($request->search),
            'archive' => $request->boolean('archive'),
        ];

        $withWhereHas = [
            'product' => function ($q) use ($request) {
                $q->where('publish', 1);

                if ($brandId = $request->input('brand_id')) {
                    $q->where('brand_id', $brandId);
                }

                if ($catalogues = json_decode($request->input('catalogues', '[]'), true)) {
                    if (! empty($catalogues)) {
                        $q->whereHas('catalogues', function ($q) use ($catalogues) {
                            $q->whereIn('product_catalogue_id', $catalogues);
                        });
                    }
                }
            },
        ];

        $select = ['id', 'name', 'product_id', 'price', 'cost_price', 'sale_price', 'image', 'attribute_value_combine'];

        $data = ($ids = request('ids'))
            ? $this->productVariantRepository->findByWhereIn(explode(',', $ids), 'id', $select, ['attribute_values'])
            : $this->productVariantRepository->pagination(
                $select,
                $condition,
                request('pageSize', 20),
                ['id' => 'desc'],
                [],
                ['attribute_values'],
                [],
                $withWhereHas
            );

        return $data;
    }

    public function updateVariant()
    {
        return $this->executeInTransaction(function () {
            $payload = $this->preparePayloadVariant();
            $this->productVariantRepository->lockForUpdate([
                'id' => ['=', $payload['id']],
            ], $payload);

            return successResponse(__('messages.update.success'));
        }, __('messages.update.error'));
    }

    private function preparePayloadVariant(): array
    {
        $payload = request()->except(['_token', '_method', 'variable_is_used']);

        // Transform keys by removing "variable_" prefix
        $payloadFormat = array_combine(
            array_map(fn($key) => str_replace('variable_', '', $key), array_keys($payload)),
            array_values($payload)
        );

        $is_discount_time = filter_var($payloadFormat['is_discount_time'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $payloadFormat['is_discount_time'] = $is_discount_time;

        if ($is_discount_time) {
            $payloadFormat['sale_price_start_at'] = $payloadFormat['sale_price_time'][0] ?? null;
            $payloadFormat['sale_price_end_at'] = $payloadFormat['sale_price_time'][1] ?? null;
        }

        return $payloadFormat;
    }

    public function deleteVariant($id)
    {
        return $this->executeInTransaction(function () use ($id) {
            $variant = $this->productVariantRepository->findByWhere([
                'id'      => ['=', $id],
                'is_used' => ['=', false],
            ]);

            if (! $variant) {
                throw new Exception('VARIANT_NOT_FOUND');
            }

            if (! $variant->delete()) {
                throw new Exception('FAILED_TO_DELETE_VARIANT');
            }

            $remainingAttributes = ProductVariantAttributeValue::query()
                ->whereHas('product_variant', fn($query) => $query->where('product_id', $variant->product_id))
                ->with('attribute_value:id,attribute_id')
                ->get(['attribute_value_id'])
                ->groupBy('attribute_value.attribute_id')
                ->map(fn($group) => [
                    'product_id'          => $variant->product_id,
                    'attribute_id'        => $group->first()->attribute_value->attribute_id,
                    'attribute_value_ids' => $group->pluck('attribute_value_id')->unique()->values()->toArray(),
                    'enable_variation'    => true,
                ])
                ->values();

            ProductAttribute::where('enable_variation', true)
                ->where('product_id', $variant->product_id)
                ->delete();

            $remainingAttributes->each(function ($attribute) {
                ProductAttribute::create($attribute);
            });

            return successResponse(__('messages.delete.success'));
        }, __('messages.delete.error'));
    }

    public function createAttribute()
    {
        return $this->executeInTransaction(function () {});
    }

    public function updateAttribute(string $productId)
    {
        return $this->executeInTransaction(function () use ($productId) {
            $payload = request()->input('attribute_attribute_value_ids');

            if (empty($payload)) {
                throw new Exception('PAYLOAD_NOT_FOUND');
            }

            $product = $this->productRepository->findById($productId, ['id', 'name', 'product_type'], ['variants']);

            if (empty($product)) {
                throw new Exception('PRODUCT_NOT_FOUND');
            }

            if ($product->product_type == Product::TYPE_SIMPLE) {
                $product->variants()->delete();
                $product->product_type = Product::TYPE_VARIABLE;
                $product->publish = 2;
                $product->save();
            }

            $attributeValueCombine = $this->generateCombinationAttributeIds($payload);
            $payloadVariantByAttribute = $this->payloadVariantByAttribute($product, $attributeValueCombine);

            if (empty($payloadVariantByAttribute)) {
                return errorResponse('Sản phẩm đã đầy đủ đủ phiên bản.');
            }

            $this->createProductAttributeFromUpdateAttribute($payload, $product);

            $createdVariants = $product->variants()->createMany($payloadVariantByAttribute);
            $variantAttributeValuePayload = $this->combineVariantAttributeValue($createdVariants);

            DB::table('product_variant_attribute_value')->insert($variantAttributeValuePayload);

            return successResponse(__('messages.update.success'));
        }, __('messages.update.error'));
    }

    private function createProductAttributeFromUpdateAttribute($payload, $product)
    {
        collect($payload)->each(function ($attrValueIds, $attrId) use ($product) {
            $product->attributes()->updateOrCreate(
                ['attribute_id' => $attrId, 'enable_variation' => true],
                ['attribute_value_ids' => array_map('intval', $attrValueIds)]
            );
        });
    }

    private function payloadVariantByAttribute($product, $attributeValueCombines)
    {
        $productVariants = $product->variants;
        if (empty($productVariants)) {
            return [];
        }

        $existingAttributeCombines = $productVariants->pluck('attribute_value_combine')->toArray();

        $productVariantPayload = $attributeValueCombines->map(function ($attributeValueCombine, $key) use ($existingAttributeCombines, $product) {
            if (! in_array($attributeValueCombine['attribute_value_combine'], $existingAttributeCombines)) {
                $productName = $product->name;
                $options = explode(' - ', $attributeValueCombine['attributeText'] ?? '');
                $sku = generateSKU($productName, 3, $options) . '-' . ($key + 1);
                $name = "{$productName} {$attributeValueCombine['attributeText']}";
                $attribute_value_combine = $attributeValueCombine['attribute_value_combine'];
                $uuid = Uuid::uuid5(Uuid::NAMESPACE_DNS, $product->id . ', ' . $attribute_value_combine);

                return [
                    'uuid'                    => $uuid,
                    'name'                    => $name,
                    'attribute_value_combine' => $attribute_value_combine,
                    'sku'                     => $sku,
                    'price'                   => 0,
                    'cost_price'              => 0,
                ];
            }
        })->filter()
            ->values()
            ->toArray();

        return $productVariantPayload;
    }

    private function generateCombinationAttributeIds($input)
    {
        $input = collect($input)->sortKeys();

        $keys = $input->keys()->toArray();
        $values = $input->values()->toArray();

        $result = $this->generateCombinationsRecursive($keys, $values);

        return $result->map(function ($combination) {
            ksort($combination);
            $attributeValue = $this->attributeValueRepository->findByWhereIn($combination);
            $data = [
                'attributeText'           => implode(' - ', $attributeValue->pluck('name')->toArray()),
                'attribute_value_combine' => implode(',', array_values($combination)),
            ];

            return $data;
        })->values();
    }

    private function generateCombinationsRecursive($keys, $values, $current = [], $index = 0)
    {
        if ($index >= count($keys)) {
            return collect([$current]);
        }

        $result = collect();

        foreach ($values[$index] as $value) {
            $newCurrent = $current;
            $newCurrent[$keys[$index]] = $value;
            $result = $result->concat($this->generateCombinationsRecursive($keys, $values, $newCurrent, $index + 1));
        }

        return $result;
    }


    public function getProductReport()
    {

        $payload = $this->preparePayload();


        $start_date = isset($payload['start_date']) ? $payload['start_date'] : Carbon::now()->startOfYear()->toDateString();
        $end_date = isset($payload['end_date']) ? $payload['end_date'] : Carbon::now()->endOfYear()->toDateString();

        $condition = $payload['condition'] ?? "product_sell_best";
        // product_sell_top: sản phẩm có doanh thu cao nhất
        // product_sell_best: sản phẩm bán chạy nhất (đã xong)
        // product_sell_best_type: sản phẩm bán chạy theo loại
        // product_return: sản phẩm có tỉ lệ hoàn trả cao nhất
        // product_inventory_lowest: sản phẩm có lượng tồn kho thấp nhất

        // Kiểm tra điều kiện và sắp xếp tương ứng
        switch ($condition) {
            case 'product_sell_best':
                $query = $this->getProductSellTop($start_date, $end_date);
                $result = $query->get()
                    ->map(function ($item) {
                        if (!$item->product_variant) {
                            return null;
                        }
                        return [
                            'product_variant_id' => $item->product_variant_id,
                            'product_id' => $item->product_variant['product_id'],
                            'product_name' => $item->product_variant['name'],
                            'product_image' => $item->product_variant['image'],
                            'product_price' => $item->product_variant['sale_price'] ?? $item->product_variant['price'],
                            'product_cost_price' => $item->product_variant['cost_price'],
                            'total_quantity_sold' => $item->total_quantity_sold,
                            'total_revenue' => $item->total_revenue,
                            'net_revenue' => (($item->product_variant['sale_price'] ?? $item->product_variant['price']) - $item->product_variant['cost_price']) * $item->total_quantity_sold,
                        ];
                    });
                break;
            case 'product_review_top':
                $query = $this->getProductReviewTop($start_date, $end_date);
                $result = $query->get()
                    ->filter(function ($item) {

                        return $item->review_count > 0 && !is_null($item->average_rating);
                    })
                    ->map(function ($item) {

                        return [
                            'id' => $item->id,
                            'name' => $item->name,
                            'review_count' => $item->review_count,
                            'average_rating' => $item->average_rating,
                            'reviews' => $item->reviews,
                        ];
                    });
                break;
        }



        return $result;
    }

    // Top sản phẩm bán chạy nhất
    private function getProductSellTop($start_date, $end_date)
    {
        $query = OrderItem::with(['order', 'product_variant'])
            ->whereHas('order', function ($query) use ($start_date, $end_date) {
                $query->where('order_status', 'completed')
                    ->whereBetween('ordered_at', [$start_date, $end_date]);
            })
            ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id') // Join với product_variant
            ->selectRaw('order_items.product_variant_id, SUM(order_items.quantity) as total_quantity_sold
        , SUM(COALESCE(order_items.sale_price, order_items.price) * order_items.quantity) as total_revenue')
            ->groupBy('order_items.product_variant_id')
            ->orderBy('total_quantity_sold', 'DESC');
        return $query;
    }

    // Top sản phẩm được đánh giá tốt nhất
    private function getProductReviewTop($start_date, $end_date)
    {
        $query = Product::with('reviews')
            ->select('id', 'name') // Chỉ lấy các trường id và name
            ->withCount([
                'reviews as review_count', // Đếm số lượng reviews
                'reviews as average_rating' => function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('created_at', [$start_date, $end_date])
                        ->select(DB::raw('AVG(rating)')); // Tính đánh giá trung bình
                }
            ])
            ->orderByDesc('average_rating') // Sắp xếp theo điểm đánh giá trung bình giảm dần
            ->orderByDesc('review_count');  // Sắp xếp theo số lượng đánh giá giảm dần

        return $query;
    }




    // CLIENT API //

    public function getProduct(string $slug)
    {
        $productId = last(explode('-', $slug));
        $product = $this->productRepository->findById($productId);

        return $product;
    }
}
