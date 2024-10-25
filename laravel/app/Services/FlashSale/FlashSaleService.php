<?php

namespace App\Services\FlashSale;

use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Services\Interfaces\FlashSale\FlashSaleServiceInterface;
use App\Repositories\Interfaces\FlashSale\FlashSaleRepositoryInterface;
use App\Repositories\Interfaces\Product\ProductVariantRepositoryInterface;

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


        $data = $pageSize && $request->page
            ? $this->flashSaleRepository->pagination($select, $condition, $pageSize)
            : $this->flashSaleRepository->findByWhere(['publish' => 1], $select, [], true);

        return $data;
    }

    public function getAll() {}

    public function findById($id)
    {

        $data = $this->flashSaleRepository->findByWhere(['id' => $id], ['*'], ['productVariants'], true);

        return $data;
    }

    public function store(array $data)
    {
        return $this->executeInTransaction(function () use ($data) {

            $startDate = $this->formatDateForMySQL($data['start_date']);
            $endDate = $this->formatDateForMySQL($data['end_date']);

            $flashSale = $this->flashSaleRepository->create(array_merge($data, [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]));

            foreach ($data['max_quantities'] as $key => $quantity) {
                $productVariant = $this->productVariantRepository->findByWhere(['id' => $key]);

                if (!$productVariant) {
                    return errorResponse(__('messages.flash_sale.error.not_found'));
                }

                if ($this->isVariantInConflictingFlashSale($key, null, $startDate, $endDate)) {
                    continue;
                }

                $flashSale->productVariants()->attach($key, [
                    'max_quantity' => $quantity,
                    'sale_price' => $data['sale_prices'][$key]
                ]);

                $productVariant->update([
                    'is_discount_time' => true,
                    'sale_price_start_at' => $startDate,
                    'sale_price_end_at' => $endDate,
                ]);
            }

            return successResponse(__('messages.create.success'));
        }, __('messages.create.error'));
    }

    // Helper function to format date
    private function formatDateForMySQL($dateString)
    {
        $dateTime = \DateTime::createFromFormat(DATE_RFC2822, $dateString);
        return $dateTime ? $dateTime->format('Y-m-d H:i:s') : null;
    }




    public function update($flashSaleId, $data)
    {
        return $this->executeInTransaction(function () use ($flashSaleId, $data) {

            $flashSale = $this->flashSaleRepository->findByWhere([
                'id' => $flashSaleId
            ]);

            if (! $flashSale) {
                return errorResponse(__('messages.flash_sale.error.not_found'));
            }

            $flashSale->update([
                'name' => $data['name'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
            ]);

            foreach ($data['max_quantities'] as $key => $quantity) {
                $productVariant = $this->productVariantRepository->findById($key);

                if (
                    $productVariant->is_discount_time &&
                    !$this->isVariantInConflictingFlashSale($key, $flashSaleId, $data['start_date'], $data['end_date'])
                ) {
                    // return errorResponse(__('messages.flash_sale.error.already_on_sale'));
                    continue;
                }

                $flashSale->productVariants()->updateExistingPivot($key, [
                    'max_quantity' => $quantity,
                    'sale_price' => $data['sale_prices'][$key]
                ]);

                $productVariant->update([
                    'is_discount_time' => true,
                    'sale_price_start_at' => $data['start_date'],
                    'sale_price_end_at' => $data['end_date'],
                ]);
            }

            return successResponse(__('messages.update.success'));
        }, __('messages.update.error'));
    }



    protected function isVariantInConflictingFlashSale($productVariantId, $flashSaleId, $startDate, $endDate)
    {
        return DB::table('flash_sale_product_variants')
            ->join('flash_sales', 'flash_sale_product_variants.flash_sale_id', '=', 'flash_sales.id')
            ->where('flash_sale_product_variants.product_variant_id', $productVariantId)
            ->where('flash_sales.id', '!=', $flashSaleId)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('flash_sales.start_date', [$startDate, $endDate])
                    ->orWhereBetween('flash_sales.end_date', [$startDate, $endDate]);
            })
            ->where('flash_sales.publish', true)
            ->exists();
    }


    public function delete($id) {}
    public function changeStatus($id)
    {
        return $this->executeInTransaction(function () use ($id) {
            $flashSale = $this->flashSaleRepository->findById($id);

            if (! $flashSale) {
                return errorResponse(__('messages.flash_sale.error.not_found'));
            }

            $flashSale->update([
                'publish' => !$flashSale->publish
            ]);

            return successResponse(__('messages.update.success'));
        });
    }

    public function handlePurchase($productVariantId, $quantity)
    {
        return $this->executeInTransaction(function () use ($productVariantId, $quantity) {
            // Tìm flash sale hiện tại cho sản phẩm biến thể
            $flashSale = $this->findActiveFlashSaleForVariant($productVariantId);

            // Kiểm tra xem có flash sale nào không
            if ($flashSale) {
                // Lấy thông tin biến thể sản phẩm từ flash sale
                $productVariant = $flashSale->productVariants()->where('id', $productVariantId)->first();

                // Kiểm tra xem biến thể sản phẩm có trong flash sale không
                if ($productVariant) {
                    // Kiểm tra số lượng tối đa có thể mua
                    if ($productVariant->pivot->max_quantity >= $quantity) {
                        // Giảm số lượng tối đa trong flash sale
                        $newMaxQuantity = $productVariant->pivot->max_quantity - $quantity;
                        $flashSale->productVariants()->updateExistingPivot($productVariantId, ['max_quantity' => $newMaxQuantity]);

                        // Nếu số lượng còn lại bằng 0, reset giá khuyến mãi
                        if ($newMaxQuantity <= 0) {
                            $productVariant->update([
                                'sale_price_start_at' => null,
                                'sale_price_end_at' => null,
                                'is_discount_time' => false,
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
        ], ['*'], ['productVariants'], '', false)
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now())
            ->whereHas('productVariants', function ($query) use ($productVariantId) {
                $query->where('id', $productVariantId);
            });
    }
}
