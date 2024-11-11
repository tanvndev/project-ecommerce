<?php

// Trong Laravel, Service Pattern thường được sử dụng để tạo các lớp service, giúp tách biệt logic của ứng dụng khỏi controller.

namespace App\Services\Statistic;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductReview;
use App\Models\ProductVariant;
use App\Models\ProductView;
use App\Models\User;
use App\Models\WishList;
use App\Repositories\Interfaces\Order\OrderItemRepositoryInterface;
use App\Repositories\Interfaces\Order\OrderRepositoryInterface;
use App\Repositories\Interfaces\Product\ProductVariantRepositoryInterface;
use App\Services\BaseService;
use App\Services\Interfaces\Statistic\StatisticServiceInterface;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StatisticService extends BaseService implements StatisticServiceInterface
{
    protected $orderRepository;
    protected $orderItemRepository;
    protected $productVariantRepository;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderItemRepositoryInterface $orderItemRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->productVariantRepository = $productVariantRepository;
    }


    public function reportOverview(): mixed
    {
        $request = request();
        [$start_date, $end_date] = $this->getDateRangeByRequest($request);

        $cacheKey = "reportOverview_{$start_date}_{$end_date}";

        return Cache::remember($cacheKey, 30 * 60, function () use ($start_date, $end_date) {
            $query = Order::query()
                ->whereBetween('ordered_at', [$start_date, $end_date]);

            $totals = $query->selectRaw('
                COUNT(id) as total_orders,
                SUM(final_price) as total_price,
                SUM(discount) as total_discount,
                SUM(shipping_fee) as total_shipping_fee
            ')->first();

            $netRevenue = $totals->total_price - $totals->total_discount - $totals->total_shipping_fee;

            $totalCost = OrderItem::whereHas(
                'order',
                fn($q) =>
                $q->whereBetween('ordered_at', [$start_date, $end_date])
            )->sum(DB::raw('cost_price * quantity'));

            $totalProfit = $netRevenue - $totalCost;

            $totalValueOfStock = ProductVariant::sum(DB::raw('cost_price * stock'));

            return [
                'total_orders' => $totals->total_orders,
                'net_revenue' => number_format($netRevenue, 2, '.', ''),
                'total_profit' => number_format($totalProfit, 2, '.', ''),
                'total_value_of_stock' => number_format($totalValueOfStock, 2, '.', ''),
            ];
        });
    }

    private function getDateRangeByRequest($request)
    {
        $start_date = '';
        $end_date = '';

        if (!empty($request->date)) {
            switch ($request->date) {
                case 'today':
                    $start_date = now()->startOfDay()->format('Y-m-d H:i:s');
                    $end_date = now()->endOfDay()->format('Y-m-d H:i:s');
                    break;
                case 'yesterday':
                    $start_date = now()->subDay()->startOfDay()->format('Y-m-d H:i:s');
                    $end_date = now()->subDay()->endOfDay()->format('Y-m-d H:i:s');
                    break;
                case 'last_7_days':
                    $start_date = now()->subDays(7)->startOfDay()->format('Y-m-d H:i:s');
                    $end_date = now()->endOfDay()->format('Y-m-d H:i:s');
                    break;

                case 'last_week':
                    $start_date = now()->subWeek()->startOfWeek()->format('Y-m-d H:i:s');
                    $end_date = now()->subWeek()->endOfWeek()->format('Y-m-d H:i:s');
                    break;
                case 'last_30_days':
                    $start_date = now()->subDays(30)->startOfDay()->format('Y-m-d H:i:s');
                    $end_date = now()->endOfDay()->format('Y-m-d H:i:s');
                    break;

                case 'this_week':
                    $start_date = now()->startOfWeek()->format('Y-m-d H:i:s');
                    $end_date = now()->endOfWeek()->format('Y-m-d H:i:s');
                    break;

                case 'last_month':
                    $start_date = now()->subMonth()->startOfMonth()->format('Y-m-d H:i:s');
                    $end_date = now()->subMonth()->endOfMonth()->format('Y-m-d H:i:s');
                    break;

                case 'this_month':
                    $start_date = now()->startOfMonth()->format('Y-m-d H:i:s');
                    $end_date = now()->endOfMonth()->format('Y-m-d H:i:s');
                    break;

                case 'last_year':
                    $start_date = now()->subYear()->startOfYear()->format('Y-m-d H:i:s');
                    $end_date = now()->subYear()->endOfYear()->format('Y-m-d H:i:s');
                    break;

                case 'this_year':
                    $start_date = now()->startOfYear()->format('Y-m-d H:i:s');
                    $end_date = now()->endOfYear()->format('Y-m-d H:i:s');
                    break;
                case 'custom':

                    if (!$request->start_date || !$request->end_date) {
                        break;
                    }

                    $start_date = Carbon::createFromFormat('d/m/Y', $request->start_date ?? '');
                    $end_date = Carbon::createFromFormat('d/m/Y', $request->end_date ?? '');

                    if ($start_date && $end_date) {
                        $start_date = $start_date->startOfDay()->format('Y-m-d H:i:s');
                        $end_date = $end_date->endOfDay()->format('Y-m-d H:i:s');
                    }
                    break;

                default:
                    break;
            }
        }

        return [$start_date, $end_date];
    }

    public function revenueByDate()
    {
        try {
            $request = request();

            [$start_date, $end_date] = $this->getDateRangeByRequest($request);

            $ordersCacheKey = "orders_by_date_{$start_date}_{$end_date}";
            $profitCacheKey = "profit_by_date_{$start_date}_{$end_date}";
            $allDatesCacheKey = "all_dates_collection_{$start_date}_{$end_date}";

            $ordersByDate = Cache::remember($ordersCacheKey, 15, function () use ($start_date, $end_date) {
                return Order::when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                    $query->where('order_status', Order::ORDER_STATUS_COMPLETED);
                    $query->whereBetween('ordered_at', [$start_date, $end_date]);
                })
                    ->select(
                        DB::raw('DATE(ordered_at) as order_date'), // Ngay
                        DB::raw('COUNT(id) as total_orders'), // So don hang
                        DB::raw('SUM(final_price) as net_revenue'), // Doanh thu thuan
                        DB::raw('IF(COUNT(id) > 0,SUM(final_price)/COUNT(id),0) as avg_order_value'), // Gia tri don hang trung binh
                        DB::raw('SUM(shipping_fee) as total_shipping_fee'), // Ship
                        DB::raw('SUM(discount) as total_discount'), // Discount
                    )
                    ->groupBy('order_date')
                    ->orderBy('order_date', 'asc')
                    ->get()
                    ->keyBy('order_date');
            });

            $profitByDate = Cache::remember($profitCacheKey, 15, function () use ($start_date, $end_date) {
                return OrderItem::when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                    $query->whereHas('order', function ($q) use ($start_date, $end_date) {
                        $q->where('order_status', Order::ORDER_STATUS_COMPLETED)
                            ->whereBetween('ordered_at', [$start_date, $end_date]);
                    });
                })
                    ->select(
                        DB::raw('DATE(orders.ordered_at) as order_date'),
                        DB::raw('SUM((COALESCE(sale_price, price, 0) - COALESCE(cost_price, 0)) * COALESCE(quantity, 0) - COALESCE(orders.discount, 0) - COALESCE(orders.shipping_fee, 0)) as total_profit')
                    )
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->groupBy('order_date')
                    ->get()
                    ->keyBy('order_date');
            });

            $allDatesCollection = Cache::remember($allDatesCacheKey, 15, function () use ($start_date, $end_date, $ordersByDate, $profitByDate) {
                $allDates = [];
                for ($currentDate = Carbon::parse($start_date); $currentDate->lte($end_date); $currentDate->addDay()) {
                    $date = $currentDate->toDateString();
                    $allDates[] = [
                        'order_date' => $date,
                        'total_orders' => $ordersByDate[$date]['total_orders'] ?? 0,
                        'net_revenue' => $ordersByDate[$date]['net_revenue'] ?? 0,
                        'avg_order_value' => $ordersByDate[$date]['avg_order_value'] ?? 0,
                        'total_shipping_fee' => $ordersByDate[$date]['total_shipping_fee'] ?? 0,
                        'total_profit' => $profitByDate[$date]['total_profit'] ?? 0,
                        'total_discount' => $ordersByDate[$date]['total_discount'] ?? 0,
                    ];
                }

                return collect($allDates);
            });

            $chartColumn = $request->input('chartColumn', 'net_revenue');
            $chartData = $request->has('chart')
                ? Cache::remember("chart_data_{$start_date}_{$end_date}_{$chartColumn}", 15, function () use ($allDatesCollection, $chartColumn) {
                    return $this->getChart($allDatesCollection, $chartColumn);
                })
                : [];

            $pageSize = $request->input('pageSize', 20);
            $page = $request->input('page', 1);

            $paginatedData = new LengthAwarePaginator(
                $allDatesCollection->forPage($page, $pageSize),
                $allDatesCollection->count(),
                $pageSize,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            return [
                'chartData' => $chartData,
                'data' => $paginatedData,
            ];
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'Có lỗi xảy ra'], 500);
        }
    }

    private function getChart($allDatesCollection, $column)
    {
        $chartData = [];
        foreach ($allDatesCollection as $date => $data) {
            $chartData[] = $data[$column];
        }
        return $chartData;
    }

    public function getProductReport()
    {
        try {
            $request = request();

            [$start_date, $end_date] = $this->getDateRangeByRequest($request);

            $condition = $request->input('condition', 'product_sell_best');

            switch ($condition) {
                case 'product_sell_best':
                    $result = $this->getProductSellTop($start_date, $end_date, $request);
                    break;
                case 'product_review_top':
                    $result = $this->getProductReviewTop($start_date, $end_date, $request);
                    break;
                case 'product_wishlist_top':
                    $result = $this->getProductWishlistTop($start_date, $end_date, $request);
                    break;
                case 'product_views_top':
                    $result = $this->getProductTopView($start_date, $end_date, $request);
                    break;
            }
            return $result;
        } catch (\Exception $e) {
            getError($e);
            Log::error($e->getMessage());
            return response()->json(['error' => 'Có lỗi xảy ra'], 500);
        }
    }

    protected function getProductSellTop($start_date, $end_date, $request)
    {
        $pageSize = $request->input('pageSize', 20);
        $page = $request->input('page', 1);
        $cacheKey = "top-selling-products:$start_date:$end_date:$pageSize:$page";

        $topSellingData = Cache::remember($cacheKey, now()->addMinutes(15), function () use ($start_date, $end_date, $pageSize) {
            return OrderItem::when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                $query->whereHas('order', function ($q) use ($start_date, $end_date) {
                    $q->where('order_status', Order::ORDER_STATUS_COMPLETED)
                        ->whereBetween('ordered_at', [$start_date, $end_date]);
                });
            })
                ->select(
                    'product_variant_id',
                    'product_variant_name',
                    DB::raw('SUM(quantity) as total_quantity'), // Tổng số lượng bán ra
                    DB::raw('SUM(COALESCE(sale_price, price, 0) * quantity) as net_revenue'), // Tổng doanh thu
                    DB::raw('SUM(orders.shipping_fee) as total_shipping_fee'), // Ship
                    DB::raw('SUM(orders.discount) as total_discount'), // Discount
                    DB::raw('SUM((COALESCE(sale_price, price, 0) - COALESCE(cost_price, 0)) * quantity - COALESCE(orders.discount, 0) - COALESCE(orders.shipping_fee, 0)) as total_profit') // Tổng lợi nhuận
                )
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->groupBy('product_variant_id', 'product_variant_name')
                ->orderByDesc('total_quantity')
                ->paginate($pageSize);
        });

        return $topSellingData;
    }


    protected function getProductReviewTop($start_date, $end_date, $request)
    {
        $pageSize = $request->input('pageSize', 20);
        $page = $request->input('page', 1);
        $cacheKey = "top-reviewing-products:$start_date:$end_date:$pageSize:$page";

        $topReviewingData = Cache::remember($cacheKey, now()->addMinutes(15), function () use ($start_date, $end_date, $pageSize) {
            return ProductReview::when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                $query
                    ->whereHas('order', function ($q) {
                        $q->where('order_status', Order::ORDER_STATUS_COMPLETED);
                    })
                    ->whereBetween('created_at', [$start_date, $end_date])
                    ->where('publish', 1);
            })
                ->with(['product' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->select(
                    'product_reviews.product_id',
                    DB::raw('COUNT(product_reviews.id) AS review_count'),
                    DB::raw('AVG(product_reviews.rating) AS avg_rating')
                )
                ->groupBy('product_reviews.product_id')
                ->orderBy('avg_rating', 'DESC')
                ->paginate($pageSize);
        });


        $topReviewingData->map(function ($item) {
            $item->product_name = $item->product->name;
            $item->avg_rating_percent = starsToPercent($item->avg_rating);
            unset($item->product);
            return $item;
        });
        return $topReviewingData;
    }


    /** Top sản phẩm được yêu thích nhiều nhất */
    protected function getProductWishlistTop($start_date, $end_date, $request)
    {

        $pageSize = $request->input('pageSize', 20);
        $page = $request->input('page', 1);
        $cacheKey = "top-wishlist-product:$start_date:$end_date:$pageSize:$page";

        $topWishlistProduct =  Cache::remember($cacheKey, now()->addMinutes(15), function () use ($start_date, $end_date, $pageSize) {
            return WishList::when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                $query->whereBetween('created_at', [$start_date, $end_date]);
            })
                ->with(['product_variant' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->select(
                    'product_variant_id',
                    DB::raw('COUNT(product_variant_id) as wishlist_count'),
                )
                ->groupBy('product_variant_id')
                ->orderByDesc('wishlist_count')
                ->paginate($pageSize);;
        });
        $topWishlistProduct->map(function ($item) {
            $item->product_variant_name = $item->product_variant->name;
            unset($item->product_variant);
            return $item;
        });
        return $topWishlistProduct;
    }

    /** Top sản phẩm nhiều lượt xem nhất */
    protected function getProductTopView($start_date, $end_date, $request)
    {
        $pageSize = $request->input('pageSize', 20);
        $page = $request->input('page', 1);
        $cacheKey = "top-view-product:$start_date:$end_date:$pageSize:$page";

        $topViewProductData = Cache::remember($cacheKey, now()->addMinutes(15), function () use ($start_date, $end_date, $pageSize) {
            $productViews = ProductView::when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                $query->whereBetween('viewed_at', [$start_date, $end_date]);
            })
                ->with(['product_variant' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->select(
                    'product_views.product_variant_id',
                    DB::raw('COUNT(product_views.id) AS view_count'),
                )
                ->groupBy('product_views.product_variant_id')
                ->orderBy('view_count', 'DESC')
                ->paginate($pageSize);

            $productViews->map(function ($item) {
                $item->product_variant_name = $item->product_variant->name;
                unset($item->product_variant);
                return $item;
            });


            $productViewToOrder = OrderItem::when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                $query->whereHas('order', function ($q) use ($start_date, $end_date) {
                    $q->where('order_status', Order::ORDER_STATUS_COMPLETED)
                        ->whereBetween('created_at', [$start_date, $end_date]);
                });
            })
                ->select(
                    'order_items.product_variant_id',
                    DB::raw('COUNT(order_items.id) AS product_order')
                )
                ->groupBy('order_items.product_variant_id')
                ->get();


            foreach ($productViewToOrder as $item) {
                $data[$item->product_variant_id] = $item->product_order;
            }

            foreach ($productViews as $item) {
                $product_variant_id = $item['product_variant_id'];
                if (isset($data[$product_variant_id])) {
                    $item['product_to_order'] = $data[$product_variant_id];
                } else {
                    $item['product_to_order'] = 0;
                }
            }
            $productViews->map(function ($item) {
                if ($item->product_to_order != 0) {
                    $item->avg_product_purchase =  round($item->view_count /  $item->product_to_order);
                } else {
                    $item->avg_product_purchase = null;
                }
            });
            return $productViews;
        });

        return $topViewProductData;
    }

    public function seasonalSale()
    {
        // $request = request();


    }

    public function popularProducts()
    {
        $request = request();

        $start_date = '';
        $end_date = '';

        $dateRange = $this->getDateRangeByRequest($request);

        if (!empty($dateRange[0]) && !empty($dateRange[1])) {
            $start_date = $dateRange[0];
            $end_date = $dateRange[1];
        }

        $popularProductsCacheKey = "popularProducts_by_date_{$start_date}_{$end_date}";

        $popularProducts = Cache::remember($popularProductsCacheKey, 15, function () use ($start_date, $end_date) {
            return CartItem::join('product_variants', 'cart_items.product_variant_id', '=', 'product_variants.id')
                ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('cart_items.created_at', [$start_date, $end_date]);
                })
                ->select(
                    'cart_items.product_variant_id',
                    'product_variants.name',
                    DB::raw('COUNT(cart_items.product_variant_id) AS frequency_of_appearance'),
                )
                ->groupBy('cart_items.product_variant_id')
                ->orderBy('frequency_of_appearance', 'DESC')
                ->get();
        });

        return $popularProducts;
    }

    public function loyalCustomers()
    {
        $request = request();

        $start_date = '';
        $end_date = '';

        $dateRange = $this->getDateRangeByRequest($request);

        if (!empty($dateRange[0]) && !empty($dateRange[1])) {
            $start_date = $dateRange[0];
            $end_date = $dateRange[1];
        }

        $loyalCustomersCacheKey = "loyalCustomers_by_date_{$start_date}_{$end_date}";

        $loyalCustomers = Cache::remember($loyalCustomersCacheKey, 15, function () use ($start_date, $end_date) {
            return User::join('orders', 'orders.user_id', '=', 'users.id')
                ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                    $query->where('order_status', Order::ORDER_STATUS_COMPLETED);
                    $query->whereBetween('ordered_at', [$start_date, $end_date]);
                })
                ->select(
                    DB::raw('users.id AS customer_id'), // id khach hang
                    DB::raw('users.fullname AS customer_name'), // Ten khach hang
                    DB::raw('COUNT(orders.id) AS total_orders'), // So don hang
                    DB::raw('SUM(orders.final_price) AS total_spent'),  // Tong chi tieu cua khach hang
                    DB::raw('AVG(orders.final_price) AS average_spent') // Chi tieu trung binh
                )
                ->groupBy('users.id')
                ->havingRaw('COUNT(orders.id) > ?', [5]) // Khách hàng có 5 đơn hàng trở lên là khách hàng trung thành
                ->orderBy('total_spent', 'DESC')
                ->get();
        });

        return $loyalCustomers;
    }
}
