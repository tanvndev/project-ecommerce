<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ProductVariant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Faker\Factory as Faker;

class ProductReviewsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create();

        $productIds = Product::pluck('id')->toArray();
        $userIds = User::pluck('id')->toArray();
        $orderIds = User::pluck('id')->toArray();

        $comments = [
            'Sản phẩm tuyệt vời, tôi rất thích!',
            'Màn hình đẹp, hiệu suất mạnh mẽ.',
            'Sử dụng rất mượt mà, tôi sẽ mua thêm.',
            'Chất lượng sản phẩm rất tốt, xứng đáng với giá tiền.',
            'Apple luôn không làm tôi thất vọng.',
            'Hàng chính hãng, sử dụng rất ổn định.',
            'Máy chạy rất nhanh và mượt mà, tôi rất hài lòng.',
            'Thiết kế đẹp, sử dụng cực kỳ thích.',
            'Mua xong mà không muốn dùng sản phẩm khác.',
            'Đánh giá cao về chất lượng và hiệu suất.',
            'Sản phẩm này thực sự tuyệt vời. Tôi đã sử dụng nó trong vài tháng và thấy hiệu suất vượt xa mong đợi. Màn hình hiển thị rất sắc nét, và khả năng chạy các ứng dụng nặng rất mượt mà. Tôi sẽ tiếp tục ủng hộ Apple.',
            'MacBook Air M1 của tôi đã làm tôi ngạc nhiên với khả năng xử lý mượt mà và tuổi thọ pin lâu dài. Tôi có thể làm việc cả ngày mà không cần sạc lại. Thiết kế cực kỳ mỏng nhẹ, rất phù hợp để mang đi công tác.',
            'Tôi đã mua iPhone 14 Pro và không thể tin được vào chất lượng của nó. Máy có thiết kế rất sang trọng và hiệu năng mạnh mẽ, đặc biệt là camera cực kỳ sắc nét. Tôi rất hài lòng và sẽ giới thiệu cho bạn bè.',
            'iMac 24-inch M1 thật sự tuyệt vời! Màn hình Retina sắc nét, màu sắc sống động và hiệu suất cực kỳ mạnh mẽ. Tôi dùng máy này để làm việc và thiết kế đồ họa, tất cả đều rất mượt mà và không bị giật lag.',
            'Apple Watch Series 8 là người bạn đồng hành tuyệt vời trong việc theo dõi sức khỏe. Nó có khả năng đo nhịp tim, oxy trong máu và cảnh báo khi có vấn đề về sức khỏe. Đây là một thiết bị không thể thiếu đối với tôi.',
            'Máy tính bảng iPad Air 5th gen của tôi sử dụng cực kỳ mượt mà và pin rất lâu. Nó giúp tôi học tập và làm việc hiệu quả hơn. Thiết kế đẹp và màn hình sắc nét, tôi không thể yêu cầu gì hơn.',
            'Tôi rất ấn tượng với hiệu suất của MacBook Pro M1. Máy chạy cực kỳ nhanh, các ứng dụng mở lên tức thì và việc chỉnh sửa video, hình ảnh trở nên dễ dàng hơn bao giờ hết. Đây là một trong những sản phẩm tốt nhất tôi từng sở hữu.',
            'Đây là lần đầu tiên tôi mua sản phẩm của Apple và tôi hoàn toàn bị chinh phục. Máy có cấu hình mạnh, thiết kế đẹp, và hệ điều hành iOS cực kỳ mượt mà. Chắc chắn tôi sẽ tiếp tục trung thành với Apple.',
            'iPhone của tôi có camera cực kỳ xuất sắc, giúp tôi ghi lại những khoảnh khắc tuyệt vời trong cuộc sống. Cảm giác sử dụng rất mượt mà và không có hiện tượng giật lag. Tôi rất vui khi chọn sản phẩm này.',
            'Sản phẩm tuyệt vời với thiết kế tinh tế, độ bền cao và đặc biệt là tính năng bảo mật. Tôi luôn cảm thấy an toàn khi sử dụng các sản phẩm của Apple. Không thể hài lòng hơn với sự đầu tư này.'
        ];

        try {
            for ($i = 0; $i < 500; $i++) { // Tạo 100 bản ghi
                $startDate = Carbon::createFromFormat('Y-m-d', '2023-01-01');
                $endDate = Carbon::createFromFormat('Y-m-d', '2024-12-31');

                $randomDate = $startDate->copy()->addDays(rand(0, $endDate->diffInDays($startDate)));
                $comment = $comments[array_rand($comments)];
                try {
                    DB::table('product_reviews')->insert([
                        'product_id' => $productIds[array_rand($productIds)],
                        'user_id' => $userIds[array_rand($userIds)],
                        'order_id' => $orderIds[array_rand($orderIds)],
                        'rating' => rand(1, 5),
                        'parent_id' => null,
                        'comment' => $comment,
                        'images' => "[\"http://127.0.0.1:8000/images/2024/10/imac-24-inch-2023-4-5k-m3-red-2-650x650jpg_671b8a5763661.webp\",\"http://127.0.0.1:8000/images/2024/10/imac-24-inch-2023-4-5k-m3-red-3-650x650jpg_671b8a574c5af.webp\",\"http://127.0.0.1:8000/images/2024/10/imac-24-inch-2023-4-5k-m3-red-4-650x650jpg_671b8a5731c39.webp\",\"http://127.0.0.1:8000/images/2024/10/imac-24-inch-2023-4-5k-m3-red-5-650x650jpg_671b8a5715f18.webp\",\"http://127.0.0.1:8000/images/2024/10/imac-m3-pink-thumb-650x650png_671b8a57774ab.webp\"]",
                        'publish' => rand(1, 2),
                        'created_at' => $randomDate,
                        'updated_at' => $randomDate,
                    ]);
                } catch (\Throwable $th) {
                }
            }
        } catch (\Throwable $th) {
        }
    }
}
