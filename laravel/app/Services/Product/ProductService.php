<?php

// Trong Laravel, Service Pattern thường được sử dụng để tạo các lớp service, giúp tách biệt logic của ứng dụng khỏi controller.

namespace App\Services\Product;

use Exception;
use Carbon\Carbon;
use App\Models\Order;
use Ramsey\Uuid\Uuid;
use App\Models\Product;
use App\Models\OrderItem;
use App\Services\BaseService;
use App\Models\ProductVariant;
use App\Models\ProductAttribute;
use Illuminate\Support\Facades\DB;
use App\Models\ProductRecommendation;
use Illuminate\Support\Facades\Cache;
use App\Models\ProductVariantAttributeValue;
use App\Services\Interfaces\Product\ProductServiceInterface;
use App\Repositories\Interfaces\Product\ProductRepositoryInterface;
use App\Repositories\Interfaces\Product\SearchHistoryRepositoryInterface;
use App\Repositories\Interfaces\Product\ProductVariantRepositoryInterface;
use App\Repositories\Interfaces\Attribute\AttributeValueRepositoryInterface;
use App\Repositories\Interfaces\ProhibitedWord\ProhibitedWordRepositoryInterface;

class ProductService extends BaseService implements ProductServiceInterface
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        protected ProductVariantRepositoryInterface $productVariantRepository,
        protected AttributeValueRepositoryInterface $attributeValueRepository,
        protected SearchHistoryRepositoryInterface $searchHistoryRepository,
        protected ProhibitedWordRepositoryInterface $prohibitedWordRepository
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

    // CLIENT API //

    public function getProduct(string $slug)
    {
        $slugParts = explode('-', $slug);
        $productId = array_pop($slugParts);  // Lấy phần tử cuối và loại bỏ nó
        $newSlug = implode('-', $slugParts); // Ghép lại phần còn lại thành slug mới

        // Tìm sản phẩm và biến thể
        $product = $this->productRepository->findById($productId);
    $productVariant = $product->variants()->where('slug', $newSlug)->first();

        // Theo dõi lượt xem sản phẩm
        if (auth()->check()) {
            $this->trackProductView($productVariant);
        }
        return $product;
    }


    private function trackProductView($productVariant)
    {
        $userId = auth()->user()->id;

        // Check if the user viewed this variant within the last hour
        $lastView = $productVariant->product_views()
            ->where('user_id', $userId)
            ->where('product_variant_id', $productVariant->id)
            ->latest('viewed_at')
            ->first();
        // Kiểm tra 1 tiếng mới thêm 1 lần
        if (!$lastView || Carbon::now()->diffInHours($lastView->viewed_at) >= 1) {
            $productVariant->product_views()->create([
                'product_variant_id' => $productVariant->id,
                'user_id'            => $userId,
                'viewed_at'          => Carbon::now(),
            ]);
        }
    }

    public function getRecommendedProducts()
    {
        $productVariantIds = auth()->user()
            ->product_recommendations
            ->pluck('product_variant_id')
            ->unique()
            ->values()
            ->toArray();

        return $this->productVariantRepository->findByWhereIn($productVariantIds);
    }

    public function filterProducts()
    {
        $payload = request()->all();
        $catalogues = $this->getCatalogues($payload);
        $priceRange = $this->getPriceRange($payload);

        $sort = $payload['sort'] ?? 'asc';
        $search = $payload['search'] ?? '';
        $values = $payload['values'] ?? '';
        $stars = $payload['stars'] ?? '';
        $pageSize = $payload['pageSize'] ?? 20;

        $productVariants = $this->getProductVariantsFilter($catalogues, $priceRange, $sort, $search, $values, $stars, $pageSize);
        $formattedAttributes = $this->getFormattedAttributes($productVariants);

        return [
            'product_variants' => $productVariants,
            'attributes' => $formattedAttributes,
        ];
    }

    // lấy ra mảnh danh mục
    protected function getCatalogues($data)
    {
        return array_filter(explode(',', $data['catalogues'] ?? ''));
    }

    // lấy ra giá request
    protected function getPriceRange($data)
    {
        return [
            'min' => isset($data['min_price']) ? (float)$data['min_price'] : null,
            'max' => isset($data['max_price']) ? (float)$data['max_price'] : null,
        ];
    }

    // gọi các hàm lọc
    protected function getProductVariantsFilter($catalogues, $priceRange, $sort, $search, $values, $stars, $pageSize)
    {
        $query = ProductVariant::query();

        $query = $this->getPublicProductVariants($query);

        $query = $this->filterByCatalogue($query, $catalogues);

        $query = $this->filterByPrice($query, $priceRange);

        $this->applySearch($query, $search);

        $this->applyValueFilter($query, $values);

        $this->filterByStars($query, $stars);

        $this->applySorting($query, $sort);

        return $query->paginate($pageSize);
    }

    protected function getPublicProductVariants($query)
    {
        $query->whereHas('product', function ($subQuery) {
            $subQuery->where('publish', 1);
        });

        return $query;
    }

    // protected function applySearch($query, $search)
    // {
    //     if (!empty($search)) {
    //         $prohibitedWords = Cache::remember('prohibited_words', 3600, function () {
    //             return file(storage_path('prohibited_words.txt'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    //         });

    //         $query->where('product_variants.name', 'like', '%' . $search . '%');

    //         $pattern = '/\b(' . implode('|', array_map('preg_quote', $prohibitedWords)) . ')\b/i';

    //         $containsProhibitedWord = preg_match($pattern, $search);
    //         if (!$containsProhibitedWord) {
    //             $existingKeyword  = $this->searchHistoryRepository->findByWhere(['keyword' => $search]);
    //             dd($existingKeyword);
    //             if ($existingKeyword) {
    //                 $existingKeyword->increment('count');
    //                 $existingKeyword->update(['updated_at' => now()]);
    //             } else {
    //                 $this->searchHistoryRepository->create(['keyword' => $search, 'count' => 1]);
    //             }
    //         }
    //     }
    // }

    protected function applySearch($query, $search)
{
    if (!empty($search)) {
        $prohibitedWords = Cache::remember('prohibited_words', 3600, function () {
            return $this->prohibitedWordRepository->pluck('keyword');
        });

        $query->where('product_variants.name', 'like', '%' . $search . '%');

        $pattern = '/\b(' . implode('|', array_map('preg_quote', $prohibitedWords)) . ')\b/i';
        $containsProhibitedWord = preg_match($pattern, $search);

        if (!$containsProhibitedWord) {
            $existingKeyword = $this->searchHistoryRepository->findByWhere(['keyword' => $search]);

            if ($existingKeyword) {
                $existingKeyword->increment('count');
                $existingKeyword->update(['updated_at' => now()]);
            } else {
                $this->searchHistoryRepository->create(['keyword' => $search, 'count' => 1]);
            }
        }
    }
}


    protected function applyValueFilter($query, $values)
    {
        if (!empty($values)) {
            $valueArray = explode(',', $values);
            $query->whereHas('attribute_values', function ($q) use ($valueArray) {
                $q->whereIn('attribute_values.id', $valueArray);
            });
        }
    }


    protected function applySorting($query, $sort)
    {
        $query->orderBy('price', $sort);
    }

    // lọc giá
    protected function filterByPrice($query, $priceRange)
    {
        if ($priceRange['min'] !== null || $priceRange['max'] !== null) {
            if ($priceRange['min'] !== null) {
                $query->having('price', '>=', $priceRange['min']);
            }
            if ($priceRange['max'] !== null) {
                $query->having('price', '<=', $priceRange['max']);
            }
        }

        return $query;
    }

    // lọc danh mục
    protected function filterByCatalogue($query, $catalogues)
    {
        if (!empty($catalogues)) {
            $query->where(function ($q) use ($catalogues) {
                foreach ($catalogues as $catalogue) {
                    $q->orWhereHas('product.catalogues', function ($subQuery) use ($catalogue) {
                        $subQuery->where('product_catalogue_id', $catalogue);
                    });
                }
            });
        }
        return $query;
    }

    protected function filterByStars($query, $stars)
    {
        if (!empty($stars)) {
            $star = (float) $stars;

            $productIds = DB::table('product_reviews')
                ->select('product_id')
                ->groupBy('product_id')
                ->havingRaw('ROUND(AVG(rating), 2) BETWEEN ? AND ?', [$star, $star + 0.91])
                ->pluck('product_id');

            $query->whereHas('product', function ($q) use ($productIds) {
                $q->whereIn('id', $productIds);
            });
        }

        return $query;
    }

    protected function getFormattedAttributes($productVariants)
    {
        $variantIds = $productVariants->pluck('id');

        $attributeValues = DB::table('product_variant_attribute_value')
            ->whereIn('product_variant_id', $variantIds)
            ->join('attribute_values', 'product_variant_attribute_value.attribute_value_id', '=', 'attribute_values.id')
            ->join('attributes', 'attribute_values.attribute_id', '=', 'attributes.id')
            ->select('attributes.id as attribute_id', 'attributes.name as attribute_name', 'attribute_values.id as value_id', 'attribute_values.name as value_name')
            ->distinct()
            ->get()
            ->groupBy('attribute_id');

        return $this->formatAttributes($attributeValues);
    }

    // format attributes
    protected function formatAttributes($attributeValues)
    {
        $formatted = [];

        foreach ($attributeValues as $attributeId => $values) {
            $formatted[$attributeId] = [
                'id' => $values[0]->attribute_id,
                'name' => $values[0]->attribute_name,
                'values' => []
            ];

            foreach ($values as $value) {
                $formatted[$attributeId]['values'][] = [
                    'id' => $value->value_id,
                    'name' => $value->value_name,
                ];
            }
        }

        return $formatted;
    }
}
