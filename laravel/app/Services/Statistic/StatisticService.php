<?php

// Trong Laravel, Service Pattern thường được sử dụng để tạo các lớp service, giúp tách biệt logic của ứng dụng khỏi controller.

namespace App\Services\Statistic;

use App\Repositories\Interfaces\Order\OrderItemRepositoryInterface;
use App\Repositories\Interfaces\Order\OrderRepositoryInterface;
use App\Services\BaseService;
use App\Services\Interfaces\Statistic\StatisticServiceInterface;
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
            DB::raw('CAST(0 AS DECIMAL(15,2)) as total_profit'),// Lợi nhuận gộp
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
}
