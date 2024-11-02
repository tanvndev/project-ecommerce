<?php

// Trong Laravel, Service Pattern thường được sử dụng để tạo các lớp service, giúp tách biệt logic của ứng dụng khỏi controller.

namespace App\Services\Statistic;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\WishList;
use App\Repositories\Interfaces\Order\OrderItemRepositoryInterface;
use App\Repositories\Interfaces\Order\OrderRepositoryInterface;
use App\Services\BaseService;
use App\Services\Interfaces\Statistic\StatisticServiceInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatisticService extends BaseService implements StatisticServiceInterface
{
    protected $orderRepository;

    protected $orderItemRepository;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderItemRepositoryInterface $orderItemRepository,
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
    }

    public function paginate()
    {
        $request = request();

        $start_date = $request->start_date;
        $end_date = $request->end_date;


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
                    $item->total_profit = number_format($item->total_revenue - $orderItem->total_cost, 2, '.', '');
                }
            }
        }

        return $data;
    }

    private function preparePayload(): array
    {
        $payload = request()->except('_token', '_method');
        $payload = $this->createSEO($payload, 'name', 'excerpt');
        $payload['shipping_ids'] = array_map('intval', $payload['shipping_ids'] ?? []);

        return $payload;
    }

    public function getProductReport()
    {

        $payload = $this->preparePayload();


        $start_date = isset($payload['start_date']) ? $payload['start_date'] : Carbon::now()->startOfYear()->toDateString();
        $end_date = isset($payload['end_date']) ? $payload['end_date'] : Carbon::now()->endOfYear()->toDateString();

        $condition = $payload['condition'] ?? "product_sell_best";
        // product_sell_best: sản phẩm bán chạy nhất (đã xong)
        // product_review_top: sản phẩm được đánh giá cao (đã xong)
        // product_wishlist_top: sản phẩm được yêu thích nhất(đã xong)

        // product_sell_top: sản phẩm có doanh thu cao nhất
        // product_sell_best_type: sản phẩm bán chạy theo loại
        // product_return: sản phẩm có tỉ lệ hoàn trả cao nhất
        // product_inventory_lowest: sản phẩm có lượng tồn kho thấp nhất

        // Kiểm tra điều kiện và sắp xếp tương ứng
        switch ($condition) {
            case 'product_sell_best':
                $result = $this->getProductSellTop($start_date, $end_date);
                $result->map(function ($item) {

                    return [
                        'product_variant_id' => $item['product_variant_id'],
                        'product_variant_name' => $item['product_variant_name'] ?? "",
                        'total_quantity_sold' => $item['total_quantity_sold'],
                        'revenue' => $item['revenue'],
                        'discount' => $item['discount'],
                        'moneyReturned' => $item['moneyReturned'],
                        'net_revenue' => $item['net_revenue'],
                        'total_revenue' => $item['total_revenue'],
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

            case 'product_wishlist_top':
                $query = $this->getProductWishlistTop($start_date, $end_date);
                dd($query->get()->toArray());
                $result = $query->get()
                    ->map(function ($item) {
                        return [
                            'product_variant_id' => $item['product_variant_id'],
                            'name' => $item['product_variant']['name'],
                            'wishlist_count' => $item['wishlist_count'],
                        ];
                    });
                break;
        }



        return $result;
    }

    // Top sản phẩm bán chạy nhất
    private function getProductSellTop($start_date, $end_date)
    {

        $query = OrderItem::query()
            ->whereHas('order', function ($query) use ($start_date, $end_date) {
                $query
                    ->whereBetween('ordered_at', [$start_date, $end_date]);
            })
            ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id') // Join với product_variant
            ->selectRaw('order_items.product_variant_id,
                         product_variants.name,
                         SUM(order_items.quantity) as total_quantity_sold,
                         SUM( COALESCE(order_items.sale_price, order_items.price) * order_items.quantity) as revenue')
            ->groupBy('order_items.product_variant_id')
            ->orderBy('total_quantity_sold', 'DESC')
            ->get();
        $discounts = $this->get_discount_product_variant_in_order($start_date, $end_date);
        $moneyReturned = $this->get_moneyReturn_product_variant($start_date, $end_date);
        $shipping = $this->get_money_shipping($start_date, $end_date);


        $revenueTotal = 0;
        $discountTotal = 0;
        $moneyReturnedTotal = 0;
        $net_revenueTotal = 0;

        foreach ($query as &$item) {
            $productId = $item['product_variant_id'];

            // Kiểm tra nếu product_variant_id tồn tại trong mảng thứ hai
            if (isset($discounts[$productId])) {
                // Thêm trường 'discount' từ mảng $dataArray2 vào phần tử của $dataArray1
                $item['discount'] = $discounts[$productId];
            } else {
                // Nếu không có trong mảng thứ hai, có thể gán discount là 0 hoặc một giá trị mặc định
                $item['discount'] = 0;
            }

            if (isset($moneyReturned[$productId])) {
                $item['moneyReturned'] = $moneyReturned[$productId];
            } else {
                $item['moneyReturned'] = 0;
            }

            $item['net_revenue'] = $item['revenue'] - $item['discount'] - $item['moneyReturned'];
            $item['total_revenue'] = $item['revenue'] - $item['discount'] - $item['moneyReturned'];

            $revenueTotal +=  $item['revenue'];
            $discountTotal += $item['discount'];
            $moneyReturnedTotal += $item['moneyReturned'];
            $net_revenueTotal += $item['net_revenue'];
        }

        $query['shipping'] = [
            "product_variant_id" => "",
            "total_quantity_sold" => "",
            "product_variant_name" => "",
            "revenue" => "",
            "discount"  => "",
            "moneyReturned" => "",
            "net_revenue" => "",
            "total_revenue" =>  $shipping,
        ];

        $query['tong'] = [
            "product_variant_id" => "",
            "product_variant_name" => "",
            "total_quantity_sold" => "",
            "revenue" => $revenueTotal,
            "discount"  => $discountTotal,
            "moneyReturned" => $moneyReturnedTotal,
            "net_revenue" => $net_revenueTotal,
            "total_revenue" => ($net_revenueTotal + $shipping),
        ];

        return $query;
    }

    /** Lấy số tiền giảm giá theo từng sản phẩm */
    private function get_discount_product_variant_in_order($start_date, $end_date)
    {

        $query1 = Order::with('order_items')->where('discount', '>', '0')->whereBetween('ordered_at', [$start_date, $end_date])->get();

        foreach ($query1 as $order) {
            $discount       = $order->discount ?? 0;
            $voucher_type   = $order->additional_details['voucher']['value_type'];
            $voucher_value  = $order->additional_details['voucher']['value'];
            $total_discount = 0;
            foreach ($order['order_items'] as $item) {
                $price = $item['sale_price'] ?? $item['price'];
                $total_discount     += $item['quantity'] * $price * ($voucher_value / 100);
            }

            foreach ($order['order_items'] as $item) {
                $price = $item['sale_price'] ?? $item['price'];
                $product_variant_id = $item['product_variant_id'];
                $orderItemPrice     = $item['quantity'] * $price;


                if ($voucher_type  == "percentage") {
                    if ($total_discount > $discount) {
                        if (!isset($discounts[$product_variant_id])) {
                            $discounts[$product_variant_id] = $orderItemPrice * ($discount / $order['total_price']);
                        } else {
                            $discounts[$product_variant_id] += $orderItemPrice * ($discount / $order['total_price']);
                        }
                    } else {

                        if (!isset($discounts[$product_variant_id])) {
                            $discounts[$product_variant_id] = $orderItemPrice * ($voucher_value / 100);
                        } else {
                            $discounts[$product_variant_id] += $orderItemPrice * ($voucher_value / 100);
                        }
                    }
                } else if ($voucher_type  == "fixed") {
                    if (!isset($discounts[$product_variant_id])) {
                        $discounts[$product_variant_id] = $discount / $item['quantity'];
                    } else {
                        $discounts[$product_variant_id] += $discount / $item['quantity'];
                    }
                }
            }
        }

        return $discounts;
    }

    /** Lấy tổng tiền ship */
    private function get_money_shipping($start_date, $end_date)
    {
        $query = Order::with('order_items')->whereNotIn('order_status', ['returned', 'cancelled'])->whereBetween('ordered_at', [$start_date, $end_date])->get();
        $shipping = 0;
        foreach ($query as $key => $value) {
            $shipping += $value['shipping_fee'];
        }

        return $shipping;
    }

    /** Lấy tổng tiền trả lại theo từng sản phẩm */
    private function get_moneyReturn_product_variant($start_date, $end_date)
    {
        $query2 = Order::with('order_items')->whereIn('order_status', ['returned', 'cancelled'])->whereBetween('ordered_at', [$start_date, $end_date])->get();
        foreach ($query2 as $order) {
            if ($order->discount > 0) {

                $discount       = $order->discount;
                $voucher_type   = $order->additional_details['voucher']['value_type'];
                $voucher_value  = $order->additional_details['voucher']['value'];
                $total_discount = 0;

                foreach ($order['order_items'] as $item) {
                    $price =  $item['sale_price'] ?? $item['price'];
                    $total_discount     += $item['quantity'] * $price * ($voucher_value / 100);
                }


                foreach ($order['order_items'] as $item) {
                    $price =  $item['sale_price'] ?? $item['price'];
                    $product_variant_id = $item['product_variant_id'];
                    $orderItemPrice     = $item['quantity'] * $price;

                    // Tiền mã giảm giá
                    if ($voucher_type  == "percentage") {
                        if ($total_discount > $discount) {
                            if (!isset($discountReturn[$product_variant_id])) {
                                $discountReturn[$product_variant_id] = $orderItemPrice * ($discount / $order['total_price']);
                                // if (isset($discountReturn['408'])) {
                                //     dd("a");
                                // }
                            } else {
                                $discountReturn[$product_variant_id] += $orderItemPrice * ($discount / $order['total_price']);
                            }
                        } else {
                            if (!isset($discountReturn[$product_variant_id])) {
                                $discountReturn[$product_variant_id] = $orderItemPrice * ($voucher_value / 100);
                            } else {
                                $discountReturn[$product_variant_id] += $orderItemPrice * ($voucher_value / 100);
                            }
                        }
                    } else if ($voucher_type  == "fixed") {
                        if (!isset($discountReturn[$product_variant_id])) {
                            $discountReturn[$product_variant_id] = $discount / $item['quantity'];
                        } else {
                            $discountReturn[$product_variant_id] += $discount / $item['quantity'];
                        }
                    }
                    // Tiền trả hàng

                    if (!isset($moneyReturned[$product_variant_id])) {
                        $moneyReturned[$product_variant_id] = $orderItemPrice - ($discountReturn[$product_variant_id] ?? 0);
                    } else {
                        $moneyReturned[$product_variant_id] += $orderItemPrice - ($discountReturn[$product_variant_id] ?? 0);
                    }
                }
            } else {
                foreach ($order['order_items'] as $item) {
                    $price = $item['sale_price'] ?? $item['price'];
                    $product_variant_id = $item['product_variant_id'];
                    $orderItemPrice     = $item['quantity'] * $price;
                    if (!isset($moneyReturned[$product_variant_id])) {
                        $moneyReturned[$product_variant_id] = $orderItemPrice;
                    } else {
                        $moneyReturned[$product_variant_id] += $orderItemPrice;
                    }
                }
            }
        }
        return $moneyReturned;
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

    /** Top sản phẩm được yêu thích nhiều nhất */
    private function getProductWishlistTop($start_date, $end_date)
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
}
