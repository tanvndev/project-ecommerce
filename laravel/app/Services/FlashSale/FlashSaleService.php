<?php

namespace App\Services\FlashSale;

use App\Repositories\Interfaces\FlashSale\FlashSaleRepositoryInterface;
use App\Repositories\Interfaces\Product\ProductVariantRepositoryInterface;
use App\Services\BaseService;
use App\Services\Interfaces\FlashSale\FlashSaleServiceInterface;

class FlashSaleService extends BaseService implements FlashSaleServiceInterface
{
    protected FlashSaleRepositoryInterface $flashSaleRepository;

    protected ProductVariantRepositoryInterface $productVariantRepository;

    public function __construct(
        FlashSaleRepositoryInterface $flashSaleRepository,
        ProductVariantRepositoryInterface $productVariantRepository
    ) {
        $this->flashSaleRepository = $flashSaleRepository;
        $this->productVariantRepository = $productVariantRepository;
    }

    public function paginate()
    {
        $request = request();

        $condition = [
            'search'  => addslashes($request->search),
            'publish' => $request->publish,
            'archive' => $request->boolean('archive'),
        ];

        $select = ['id', 'name', 'start_date', 'end_date', 'publish'];

        $pageSize = $request->pageSize;

        $data = $this->flashSaleRepository->pagination($select, $condition, $pageSize, [], [], ['product_variants']);

        return $data;
    }

    public function findById($id)
    {
        $data = $this->flashSaleRepository->findById($id, ['*'], ['product_variants']);

        return $data;
    }

    public function store(array $data)
    {
        return $this->executeInTransaction(function () use ($data) {
            $payload = $this->preparePayload($data);

            // TAI CUNG 1 THOI DIEM CHI TON TAI 1 FLASH SALE
            if ($this->checkFlashSaleDateExists($payload)) {
                return errorResponse(__('messages.flash_sale.error.time_exist'));
            }

            $flashSale = $this->flashSaleRepository->create($payload);

            foreach ($data['max_quantities'] as $productVariantId => $quantity) {
                $productVariant = $this->productVariantRepository->findById($productVariantId, ['*'], [], true);

                if (! $productVariant) {
                    return errorResponse(__('messages.flash_sale.error.not_found'));
                }

                $flashSale->product_variants()->attach($productVariantId, [
                    'max_quantity' => $quantity,
                ]);

                $this->updateProductVariant($productVariant, $productVariantId, $data, $payload);
            }

            return successResponse(__('messages.create.success'));
        }, __('messages.create.error'));
    }

    private function updateProductVariant($productVariant, $productVariantId, $payload)
    {
        $productVariant->update([
            'sale_price'          => $payload['sale_prices'][$productVariantId],
            'is_discount_time'    => true,
            'sale_price_start_at' => $payload['start_date'],
            'sale_price_end_at'   => $payload['end_date'],
        ]);
    }

    public function update($flashSaleId, $data)
    {
        return $this->executeInTransaction(function () use ($flashSaleId, $data) {
            $payload = $this->preparePayload($data);

            // TAI CUNG 1 THOI DIEM CHI TON TAI 1 FLASH SALE
            if ($this->checkFlashSaleDateExists($payload, $flashSaleId)) {
                return errorResponse(__('messages.flash_sale.error.time_exist'));
            }

            $flashSale = $this->flashSaleRepository->findByWhere([
                'id' => $flashSaleId,
            ]);

            if (! $flashSale) {
                return errorResponse(__('messages.flash_sale.error.not_found'));
            }

            $flashSale->update($payload);

            foreach ($data['max_quantities'] as $productVariantId => $quantity) {
                $productVariant = $this->productVariantRepository->findById($productVariantId, ['*'], [], true);

                $flashSale->product_variants()->updateExistingPivot($productVariantId, [
                    'max_quantity' => $quantity,
                ]);

                $this->updateProductVariant($productVariant, $productVariantId, $data, $payload);
            }

            return successResponse(__('messages.update.success'));
        }, __('messages.update.error'));
    }

    private function checkFlashSaleDateExists($payload, $id = null)
    {
        $condition = [
            'start_date' => ['<=', $payload['start_date']],
            'end_date'   => ['>=', $payload['end_date']],

        ];
        if ($id) {
            $condition['id'] = ['!=', $id];
        }
        $flashSale = $this->flashSaleRepository->findByWhere($condition);

        return ! empty($flashSale);
    }

    private function preparePayload($data)
    {
        $startDate = convertToYyyyMmDdHhMmSs($data['start_date']);
        $endDate = convertToYyyyMmDdHhMmSs($data['end_date']);

        $payload = array_merge($data, [
            'start_date' => $startDate ?? null,
            'end_date'   => $endDate ?? null,
        ]);

        return $payload;
    }

    public function handlePurchase($productVariantId, $quantity)
    {
        return $this->executeInTransaction(function () use ($productVariantId, $quantity) {
            // Tìm flash sale hiện tại cho sản phẩm biến thể
            $flashSale = $this->findActiveFlashSaleForVariant($productVariantId);

            // Kiểm tra xem có flash sale nào không
            if ($flashSale) {
                // Lấy thông tin biến thể sản phẩm từ flash sale
                $productVariant = $flashSale->product_variants()->where('id', $productVariantId)->first();

                // Kiểm tra xem biến thể sản phẩm có trong flash sale không
                if ($productVariant) {
                    // Kiểm tra số lượng tối đa có thể mua
                    if ($productVariant->pivot->max_quantity >= $quantity) {
                        // Giảm số lượng tối đa trong flash sale
                        $newMaxQuantity = $productVariant->pivot->max_quantity - $quantity;
                        $flashSale->product_variants()->updateExistingPivot($productVariantId, ['max_quantity' => $newMaxQuantity]);

                        // Nếu số lượng còn lại bằng 0, reset giá khuyến mãi
                        if ($newMaxQuantity <= 0) {
                            $productVariant->update([
                                'sale_price_start_at' => null,
                                'sale_price_end_at'   => null,
                                'is_discount_time'    => false,
                            ]);
                        }

                        return true;
                    } else {
                        return errorResponse(__('messages.flash_sale.error.out_of_stock'));
                    }
                } else {
                    return errorResponse(__('messages.flash_sale.error.not_found'));
                }
            } else {
                return errorResponse(__('messages.flash_sale.error.not_found'));
            }
        });
    }

    private function findActiveFlashSaleForVariant($productVariantId)
    {
        return $this->flashSaleRepository->findByWhereHas([
            'publish' => true,
        ], ['*'], ['product_variants'], '', false)
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now())
            ->whereHas('product_variants', function ($query) use ($productVariantId) {
                $query->where('id', $productVariantId);
            });
    }

    public function getFlashSale()
    {
        $now = now();
        $condition = [
            'publish'    => 1,
            'start_date' => ['<=', $now],
            'end_date'   => ['>=', $now],
        ];

        $data = $this->flashSaleRepository->findByWhere($condition, ['*'], ['product_variants']);

        return $data;
    }

    public function deleteMultiple()
    {
        return $this->executeInTransaction(function () {
            $request = request();

            $flashSale = $this->flashSaleRepository->findByWhereIn($request->modelIds, 'id', ['*'], ['product_variants']);
            $productVariantIds = $flashSale->map(function ($item) {
                return $item->product_variants->pluck('id')->toArray();
            })->flatten()->unique()->toArray();

            foreach ($productVariantIds as $productVariantId) {
                $payload = [
                    'sale_price'          => null,
                    'is_discount_time'    => false,
                    'sale_price_start_at' => null,
                    'sale_price_end_at'   => null,
                ];
                $this->productVariantRepository->update($productVariantId, $payload);
            }

            $forceDelete = ($request->has('forceDelete') && $request->forceDelete == '1')
                ? 'forceDeleteByWhereIn'
                : 'deleteByWhereIn';

            $this->flashSaleRepository->{$forceDelete}('id', $request->modelIds);

            return successResponse(__('messages.action.success'));
        }, __('messages.action.error'));
    }
}
