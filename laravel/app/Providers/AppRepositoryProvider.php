<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppRepositoryProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    protected $repositoryBindings = [
        // Base
        'App\Repositories\Interfaces\BaseRepositoryInterface' => 'App\Repositories\BaseRepository',
        // User
        'App\Repositories\Interfaces\User\UserRepositoryInterface' => 'App\Repositories\User\UserRepository',
        // UserCatalogue
        'App\Repositories\Interfaces\User\UserCatalogueRepositoryInterface' => 'App\Repositories\User\UserCatalogueRepository',
        // Permission
        'App\Repositories\Interfaces\Permission\PermissionRepositoryInterface' => 'App\Repositories\Permission\PermissionRepository',
        // Province
        'App\Repositories\Interfaces\Location\ProvinceRepositoryInterface' => 'App\Repositories\Location\ProvinceRepository',
        // District
        'App\Repositories\Interfaces\Location\DistrictRepositoryInterface' => 'App\Repositories\Location\DistrictRepository',
        // Ward
        'App\Repositories\Interfaces\Location\WardRepositoryInterface' => 'App\Repositories\Location\WardRepository',
        // ProductCatalogue
        'App\Repositories\Interfaces\Product\ProductCatalogueRepositoryInterface' => 'App\Repositories\Product\ProductCatalogueRepository',
        // Product
        'App\Repositories\Interfaces\Product\ProductRepositoryInterface' => 'App\Repositories\Product\ProductRepository',
        // ProductVariant
        'App\Repositories\Interfaces\Product\ProductVariantRepositoryInterface' => 'App\Repositories\Product\ProductVariantRepository',
        // AttributeValue
        'App\Repositories\Interfaces\Attribute\AttributeValueRepositoryInterface' => 'App\Repositories\Attribute\AttributeValueRepository',
        // Attribute
        'App\Repositories\Interfaces\Attribute\AttributeRepositoryInterface' => 'App\Repositories\Attribute\AttributeRepository',
        // Brand
        'App\Repositories\Interfaces\Brand\BrandRepositoryInterface' => 'App\Repositories\Brand\BrandRepository',
        // SystemConfig
        'App\Repositories\Interfaces\SystemConfig\SystemConfigRepositoryInterface' => 'App\Repositories\SystemConfig\SystemConfigRepository',
        // ShippingMethod
        'App\Repositories\Interfaces\ShippingMethod\ShippingMethodRepositoryInterface' => 'App\Repositories\ShippingMethod\ShippingMethodRepository',
        // PaymentMethod
        'App\Repositories\Interfaces\PaymentMethod\PaymentMethodRepositoryInterface' => 'App\Repositories\PaymentMethod\PaymentMethodRepository',
        // Cart
        'App\Repositories\Interfaces\Cart\CartRepositoryInterface' => 'App\Repositories\Cart\CartRepository',
        // Widget
        'App\Repositories\Interfaces\Widget\WidgetRepositoryInterface' => 'App\Repositories\Widget\WidgetRepository',
        // Voucher
        'App\Repositories\Interfaces\Voucher\VoucherRepositoryInterface' => 'App\Repositories\Voucher\VoucherRepository',
        // WishList
        'App\Repositories\Interfaces\WishList\WishListRepositoryInterface' => 'App\Repositories\WishList\WishListRepository',
        // Slider
        'App\Repositories\Interfaces\Slider\SliderRepositoryInterface' => 'App\Repositories\Slider\SliderRepository',
        // Order
        'App\Repositories\Interfaces\Order\OrderRepositoryInterface' => 'App\Repositories\Order\OrderRepository',
        // OrderItem
        'App\Repositories\Interfaces\Order\OrderItemRepositoryInterface' => 'App\Repositories\Order\OrderItemRepository',
        // Post
        'App\Repositories\Interfaces\Post\PostRepositoryInterface' => 'App\Repositories\Post\PostRepository',
        // UserAddress
        'App\Repositories\Interfaces\User\UserAddressRepositoryInterface' => 'App\Repositories\User\UserAddressRepository',
        // PRODUCT REVIEW
        'App\Repositories\Interfaces\Product\ProductReviewRepositoryInterface' => 'App\Repositories\Product\ProductReviewRepository',
        // NOTIFICATION
        'App\Repositories\Interfaces\Notification\NotificationRepositoryInterface' => 'App\Repositories\Notification\NotificationRepository',
        // CHAT
        'App\Repositories\Interfaces\Chat\ChatRepositoryInterface' => 'App\Repositories\Chat\ChatRepository',
        // FLASH SALE
        'App\Repositories\Interfaces\FlashSale\FlashSaleRepositoryInterface' => 'App\Repositories\FlashSale\FlashSaleRepository',

    ];

    public function register(): void
    {
        foreach ($this->repositoryBindings as $key => $value) {
            $this->app->bind($key, $value);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
