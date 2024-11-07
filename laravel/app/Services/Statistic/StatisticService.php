<?php

// Trong Laravel, Service Pattern thường được sử dụng để tạo các lớp service, giúp tách biệt logic của ứng dụng khỏi controller.

namespace App\Services\Statistic;


use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ProductView;
use App\Models\WishList;
use App\Repositories\Interfaces\Cart\CartItemRepositoryInterface;
use App\Repositories\Interfaces\Order\OrderItemRepositoryInterface;
use App\Repositories\Interfaces\Order\OrderRepositoryInterface;
use App\Repositories\Interfaces\Product\ProductVariantRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use App\Services\BaseService;
use App\Services\Interfaces\Statistic\StatisticServiceInterface;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StatisticService extends BaseService implements StatisticServiceInterface
{
    protected $orderRepository;
    protected $orderItemRepository;
    protected $productVariantRepository;
    protected $cartItemRepository;
    protected $userRepository;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderItemRepositoryInterface $orderItemRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        CartItemRepositoryInterface $cartItemRepository,
        UserRepositoryInterface $userRepository,
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->cartItemRepository = $cartItemRepository;
        $this->userRepository = $userRepository;
    }


    public function reportOverview(): mixed
    {

        $request = request();

        $columns = [
            DB::raw('COUNT(id) as total_orders'), // Số lượng đơn hàng
            DB::raw('SUM(total_price) as total_price'), // Tổng tiền hàng
            DB::raw('SUM(discount) as total_discount'), // Tổng tiền giảm giá
            DB::raw('CAST(0 AS DECIMAL(15,2)) as money_returned'), // Tổng tiền hàng trả lại
            DB::raw('CAST(0 AS DECIMAL(15,2)) as net_revenue'), // Tổng doanh thu thuần
            DB::raw('CAST(0 AS DECIMAL(15,2)) as total_profit'), // Lợi nhuận gộp
        ];

        $conditions = [
            'search' => addslashes($request->search),
            'publish' => $request->publish,
            'archive' => $request->boolean('archive'),
        ];

        $data = $this->orderRepository->pagination(
            $columns,
            $conditions,
            20,
            [],
            [],
            [],
            [],
            [],
            []
        );


        $moneyReturned = $this->orderRepository->pagination(
            [
                DB::raw('SUM(total_price) as money_returned'),
            ],
            [
                'where' => [
                    'order_status' => 'returned' // lấy ra những đơn hàng bị hoàn
                ]
            ],
            null,
            [],
            [],
            [],
            [],
            [],
            []
        );

        foreach ($data as $item) {
            $data = $item;
        }

        $data->net_revenue = number_format($data->total_price - $data->total_discount, 2, '.', '');

        foreach ($moneyReturned as $return) {
            $item->money_returned = number_format($return->money_returned, 2, '.', '');
            $item->net_revenue = number_format($item->net_revenue - $item->money_returned, 2, '.', '');
        }

        $orderItems = $this->orderItemRepository->pagination(
            [
                DB::raw('SUM(cost_price * quantity) AS total_cost'),
            ],
            [],
            null,
            [],
            [],
            [],
            [],
            [],
            []
        );




        // Tính lợi nhuận gộp
        foreach ($orderItems as $orderItem) {
            $data->total_profit = number_format($data->net_revenue - $orderItem->total_cost, 2, '.', '');
        }

        $totalValueOfStock = $this->productVariantRepository->pagination(
            [DB::raw('SUM(cost_price * stock) as total_value_of_stock')],
            [],
            null,
            [],
            [],
            [],
            [],
            [],
            []
        );

        //Giá trị tồn kho
        foreach ($totalValueOfStock as $item) {
            $data->total_value_of_stock = $item->total_value_of_stock;
        }

        // Xóa những trường không cần thiết nữa
        unset($data->total_price);
        unset($data->total_discount);
        unset($data->money_returned);

        return $data;
    }

    public function revenueByDate()
    {
        $request = request();

        $start_date = null;
        $end_date = null;

        //Lọc theo các active
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
                    $start_date = now()->subDays(6)->startOfDay()->format('Y-m-d H:i:s');
                    $end_date = now()->endOfDay()->format('Y-m-d H:i:s');
                    break;

                case 'last_30_days':
                    $start_date = now()->subDays(29)->startOfDay()->format('Y-m-d H:i:s');
                    $end_date = now()->endOfDay()->format('Y-m-d H:i:s');
                    break;

                case 'last_week':
                    $start_date = now()->subWeek()->startOfWeek()->format('Y-m-d H:i:s');
                    $end_date = now()->subWeek()->endOfWeek()->format('Y-m-d H:i:s');
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

                default:
                    // Trạng thái lọc không hợp lệ
                    return errorResponse(__('messages.statistic.error.active'));
            }
        }
        // Lọc theo ngày cố định
        elseif (!empty($request->start_date) && !empty($request->end_date)) {
            try {
                $start_date = Carbon::createFromFormat('d/m/Y', $request->start_date);
                $end_date = Carbon::createFromFormat('d/m/Y', $request->end_date);

                if ($start_date && $end_date) {
                    $start_date = $start_date->startOfDay()->format('Y-m-d H:i:s'); // Đặt giờ về đầu ngày
                    $end_date = $end_date->endOfDay()->format('Y-m-d H:i:s'); // Đặt giờ về cuối ngày
                }
            } catch (\Exception $e) {
                return errorResponse(__('messages.statistic.error.format'));
            }
        } else {
            return errorResponse(__('messages.statistic.error.request'));
        }

        $columns = [
            DB::raw('DATE(ordered_at) as order_date'), // Thống kê doanh thu theo ngày
            DB::raw('COUNT(id) as total_orders'), // Số lượng đơn hàng
            DB::raw('SUM(total_price) as total_price'), // Tổng tiền hàng
            DB::raw('SUM(discount) as total_discount'), // Tổng tiền hàng trả lại
            DB::raw('CAST(0 AS DECIMAL(15,2)) as money_returned'), // Tổng tiền hàng trả lại
            DB::raw('CAST(0 AS DECIMAL(15,2)) as net_revenue'), // Tổng doanh thu thuần
            DB::raw('SUM(shipping_fee) as total_shipping_fee'), // Tổng tiền ship
            DB::raw('SUM(final_price) as total_revenue'), // Tổng doanh thu
            DB::raw('CAST(0 AS DECIMAL(15,2)) as total_profit'), // Lợi nhuận gộp
        ];

        $conditions = [
            'search' => addslashes($request->search),
            'publish' => $request->publish,
            'archive' => $request->boolean('archive'),
        ];

        $orderBy = ['order_date' => 'ASC'];

        $groupBy = ['order_date'];

        $rawQuery = [
            'whereRaw' => [
                ['ordered_at  BETWEEN ? AND ?', [$start_date, $end_date]],
            ],
        ];

        $data = $this->orderRepository->pagination(
            $columns,
            $conditions,
            20,
            $orderBy,
            [],
            [],
            $groupBy,
            [],
            $rawQuery
        );

        $moneyReturned = $this->orderRepository->pagination(
            [
                DB::raw('DATE(ordered_at) as order_date'),
                DB::raw('SUM(total_price) as money_returned'),
            ],
            [
                'where' => [
                    'order_status' => 'returned' // lấy ra những đơn hàng bị hoàn
                ]
            ],
            null,
            $orderBy,
            [],
            [],
            $groupBy,
            [],
            $rawQuery
        );

        foreach ($data as $item) {

            $item->net_revenue = number_format($item->total_price - $item->total_discount, 2, '.', '');

            foreach ($moneyReturned as $return) {

                if ($item->order_date === $return->order_date) {

                    $item->money_returned = number_format($return->money_returned, 2, '.', '');

                    $item->net_revenue = number_format($item->net_revenue - $item->money_returned, 2, '.', '');

                    $item->total_revenue = number_format($item->total_revenue - $item->money_returned, 2, '.', '');
                }
            }
        }

        $rawQuery1 = [
            'whereRaw' => [
                ['created_at  BETWEEN ? AND ?', [$start_date, $end_date]],
            ],
        ];

        $orderItems = $this->orderItemRepository->pagination(
            [
                DB::raw('DATE(created_at) as order_date'),
                DB::raw('SUM(cost_price * quantity) AS total_cost'),
            ],
            [],
            null,
            $orderBy,
            [],
            [],
            $groupBy,
            [],
            $rawQuery1
        );

        // Tính lợi nhuận theo từng ngày
        foreach ($data as $item) {
            foreach ($orderItems as $orderItem) {
                if ($item->order_date === $orderItem->order_date) {
                    $item->order_date = Carbon::parse($item->order_date)->format('d/m/Y');
                    $item->total_profit = number_format($item->total_revenue - $orderItem->total_cost, 2, '.', '');
                }
            }
        }

        return $data;
    }


    private function preparePayload(): array
    {
        $payload = request()->except('_token', '_method');
        return $payload;
    }

    public function getProductReport()
    {


        $payload = $this->preparePayload();

        $startDate = Carbon::createFromFormat('d/m/Y', $payload['start_date']);
        $endDate = Carbon::createFromFormat('d/m/Y', $payload['end_date']);

        $startDateFormatted = $startDate->format('Y-m-d H:i:s');
        $endDateFormatted = $endDate->format('Y-m-d H:i:s');


        $condition = $payload['condition'] ?? "product_sell_best";

        switch ($condition) {
            case 'product_sell_best':
                $query = $this->getProductSellTop($startDateFormatted, $endDateFormatted);
                $result = $query->map(function ($item) {
                    return [
                        'product_variant_id'    => $item['product_variant_id'],
                        'product_variant_name'  => $item['name'] ?? "",
                        'total_quantity_sold'   => $item['total_quantity_sold'],
                        'revenue'               => $item['revenue'],
                        'discount'              => $item['discount'],
                        'net_revenue'           => $item['net_revenue'],
                        'total_revenue'         => $item['total_revenue'],
                    ];
                });
                break;
            case 'product_review_top':
                $query = $this->getProductReviewTop($start_date, $end_date);
                $result = $query
                    ->filter(function ($item) {
                        return $item->review_count > 0 && !is_null($item->avg_rating);
                    })
                    ->map(function ($item) {
                        return [
                            'product_id'                => $item->product_id,
                            'product_name'              => $item->product['name'],
                            'review_count'      => $item->review_count,
                            'average_rating'    => number_format($item->avg_rating, 1, '.', ','),
                        ];
                    });
                break;


            case 'product_wishlist_top':
                $query = $this->getProductWishlistTop($start_date, $end_date);
                $result = $query->get()
                    ->map(function ($item) {
                        return [
                            'product_variant_id'    => $item['product_variant_id'],
                            'name'                  => $item['product_variant']['name'],
                            'wishlist_count'        => $item['wishlist_count'],
                        ];
                    });
                break;
            case 'product_views_top':
                $query = $this->getProductTopView($start_date, $end_date);
                $result = $query->map(function ($item) {
                    if ($item->product_to_order != 0) {
                        $avg_product_purchase = $item->view_count /  $item->product_to_order;
                    } else {
                        $avg_product_purchase = null;
                    }

                    return [
                        'product_variant_id'    => $item->product_variant_id,
                        'product_variant_name'  => $item->product_variant['name'],
                        'view_count'            => $item->view_count,
                        'product_to_order'      => $item->product_to_order,
                        'avg_product_purchase'  => number_format($avg_product_purchase, 2, '.', ',')
                    ];
                });
                break;
        }
        return $result;
    }

    private function getAllDatesInRange($startDate, $endDate)
    {
        $start = Carbon::createFromFormat('Y-m-d H:i:s', $startDate);
        $end = Carbon::createFromFormat('Y-m-d H:i:s', $endDate);

        $dates = [];

        while ($start->lte($end)) {
            $dates[] = $start->format('d/m/Y');
            $start->addDay();
        }

        return $dates;
    }

    public function getSalesReportByDayIncludingEmpty($start_date, $end_date)
    {
        // Lấy danh sách các ngày trong phạm vi start_date và end_date
        $dates = $this->getAllDatesInRange($start_date, $end_date);

        $orders = Order::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$start_date, $end_date])
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();
        dd($orders->toArray());
        dd($query);
        return $query;
    }


    /** Top 20 Sản phẩm bán chạy nhất */
    protected function getProductSellTop($start_date, $end_date)
    {

        // $this->getSalesReportByDayIncludingEmpty($start_date, $end_date);
        $cacheKey = "top-selling-products:$start_date:$end_date";
        $orders = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($start_date, $end_date) {
            return OrderItem::query()
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->leftJoin('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
                ->whereBetween('orders.ordered_at', [$start_date, $end_date])
                ->where('orders.order_status', 'completed')
                ->selectRaw('
                    DATE(orders.ordered_at) as date,
                    SUM(order_items.quantity) as total_quantity_sold,
                    SUM(COALESCE(order_items.sale_price, order_items.price) * order_items.quantity) as revenue
                ')
                ->groupBy('date')
                ->orderBy('date', 'ASC')
                ->get();
        });
        dd($orders->toArray());

        return $query;
    }

    /** Top sản phẩm được đánh giá tốt nhất */
    protected function getProductReviewTop($start_date, $end_date)
    {
        $productReviews = ProductReview::with(['product', 'order'])
            ->whereHas('order', function ($query) {
                $query->where('order_status', 'completed');
            })
            ->whereBetween('created_at', [$start_date, $end_date])
            ->where('publish', 1)
            ->select(
                'product_reviews.product_id',
                DB::raw('COUNT(product_reviews.id) AS review_count'),
                DB::raw('AVG(product_reviews.rating) AS avg_rating')
            )
            ->groupBy('product_reviews.product_id')
            ->orderBy('avg_rating', 'DESC')
            ->limit(10)
            ->get();
        // dd($productReviews->toArray());
        return $productReviews;
    }

    /** Top sản phẩm được yêu thích nhiều nhất */
    protected function getProductWishlistTop($start_date, $end_date)
    {
        $topWishlistProducts = WishList::with(['product_variant'])
            ->select('product_variant_id')
            ->selectRaw('COUNT(product_variant_id) as wishlist_count')
            ->whereBetween('created_at', [$start_date, $end_date])
            ->groupBy('product_variant_id')
            ->orderByDesc('wishlist_count')
            ->limit(10);
        return $topWishlistProducts;
    }

    /** Top sản phẩm nhiều lượt xem nhất */
    protected function getProductTopView($start_date, $end_date)
    {
        $productViews = ProductView::with('product_variant')
            ->whereBetween('viewed_at', [$start_date, $end_date])
            ->select(
                'product_views.product_variant_id',
                DB::raw('COUNT(product_views.id) AS view_count'),
            )
            ->groupBy('product_views.product_variant_id')
            ->orderBy('view_count', 'DESC')
            ->limit(10)
            ->get();

        $productViewToOrder = OrderItem::with('order')
            ->whereHas('order', function ($query) use ($start_date, $end_date) {
                $query->where('order_status', 'completed')
                    ->whereBetween('created_at', [$start_date, $end_date]);
            })
            ->select(
                'order_items.product_variant_id',
                DB::raw('COUNT(order_items.id) AS product_order'),

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

        return $productViews;
    }

    public function seasonalSale()
    {
        // $request = request();


    }

    public function popularProducts()
    {
        $request = request();

        $start_date = null;
        $end_date = null;

        //Lọc theo các active
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
                    $start_date = now()->subDays(6)->startOfDay()->format('Y-m-d H:i:s');
                    $end_date = now()->endOfDay()->format('Y-m-d H:i:s');
                    break;

                case 'last_30_days':
                    $start_date = now()->subDays(29)->startOfDay()->format('Y-m-d H:i:s');
                    $end_date = now()->endOfDay()->format('Y-m-d H:i:s');
                    break;

                case 'last_week':
                    $start_date = now()->subWeek()->startOfWeek()->format('Y-m-d H:i:s');
                    $end_date = now()->subWeek()->endOfWeek()->format('Y-m-d H:i:s');
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

                default:
                    // Trạng thái lọc không hợp lệ
                    return errorResponse(__('messages.statistic.error.active'));
            }
        }
        // Lọc theo ngày cố định
        elseif (!empty($request->start_date) && !empty($request->end_date)) {
            try {
                $start_date = Carbon::createFromFormat('d/m/Y', $request->start_date);
                $end_date = Carbon::createFromFormat('d/m/Y', $request->end_date);

                if ($start_date && $end_date) {
                    $start_date = $start_date->startOfDay()->format('Y-m-d H:i:s'); // Đặt giờ về đầu ngày
                    $end_date = $end_date->endOfDay()->format('Y-m-d H:i:s'); // Đặt giờ về cuối ngày
                }
            } catch (\Exception $e) {
                return errorResponse(__('messages.statistic.error.format'));
            }
        } else {
            return errorResponse(__('messages.statistic.error.request'));
        }

        $columns = [
            'cart_items.product_variant_id',
            'product_variants.name',
            DB::raw('COUNT(cart_items.product_variant_id) AS frequency_of_appearance'),
        ];

        $conditions = [
            'search' => addslashes($request->search),
            'publish' => $request->publish,
            'archive' => $request->boolean('archive'),
        ];

        $orderBy = ['frequency_of_appearance' => 'DESC'];

        $groupBy = ['cart_items.product_variant_id'];

        $join = ['product_variants' => ['product_variants.id', 'cart_items.product_variant_id']];

        $rawQuery = [
            'whereRaw' => [
                ['cart_items.created_at  BETWEEN ? AND ?', [$start_date, $end_date]],
            ],
        ];

        $popularProducts = $this->cartItemRepository->pagination(
            $columns,
            $conditions,
            20,
            $orderBy,
            $join,
            [],
            $groupBy,
            [],
            $rawQuery
        );

        return $popularProducts;
    }

    public function loyalCustomers()
    {
        $request = request();

        $start_date = null;
        $end_date = null;

        //Lọc theo các active
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
                    $start_date = now()->subDays(6)->startOfDay()->format('Y-m-d H:i:s');
                    $end_date = now()->endOfDay()->format('Y-m-d H:i:s');
                    break;

                case 'last_30_days':
                    $start_date = now()->subDays(29)->startOfDay()->format('Y-m-d H:i:s');
                    $end_date = now()->endOfDay()->format('Y-m-d H:i:s');
                    break;

                case 'last_week':
                    $start_date = now()->subWeek()->startOfWeek()->format('Y-m-d H:i:s');
                    $end_date = now()->subWeek()->endOfWeek()->format('Y-m-d H:i:s');
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

                default:
                    // Trạng thái lọc không hợp lệ
                    return errorResponse(__('messages.statistic.error.active'));
            }
        }
        // Lọc theo ngày cố định
        elseif (!empty($request->start_date) && !empty($request->end_date)) {
            try {
                $start_date = Carbon::createFromFormat('d/m/Y', $request->start_date);
                $end_date = Carbon::createFromFormat('d/m/Y', $request->end_date);

                if ($start_date && $end_date) {
                    $start_date = $start_date->startOfDay()->format('Y-m-d H:i:s'); // Đặt giờ về đầu ngày
                    $end_date = $end_date->endOfDay()->format('Y-m-d H:i:s'); // Đặt giờ về cuối ngày
                }
            } catch (\Exception $e) {
                return errorResponse(__('messages.statistic.error.format'));
            }
        } else {
            return errorResponse(__('messages.statistic.error.request'));
        }

        // Khách hàng có 5 đơn hàng trở lên là khách hàng trung thành
        $columns = [
            DB::raw('users.id AS customer_id'),
            DB::raw('users.fullname AS customer_name'),
            DB::raw('COUNT(orders.id) AS total_orders'),
            DB::raw('SUM(orders.final_price) AS total_spent'),
            DB::raw('AVG(orders.final_price) AS average_spent')
        ];

        $conditions = [
            'search' => addslashes($request->search),
            'publish' => $request->publish,
            'archive' => $request->boolean('archive'),
        ];

        $orderBy = ['total_spent' => 'DESC'];

        $join = ['orders' => ['orders.user_id', 'users.id']];

        $rawQuery = [
            'whereRaw' => [
                ['(orders.ordered_at  BETWEEN ? AND ?) AND orders.order_status = ? GROUP BY users.id HAVING COUNT(orders.id) > ?', [$start_date, $end_date, 'completed', 5]],
            ],
        ];

        $loyalCustomers = $this->userRepository->pagination(
            $columns,
            $conditions,
            null,
            $orderBy,
            $join,
            [],
            [],
            [],
            $rawQuery
        );
        return $loyalCustomers;
    }
}
