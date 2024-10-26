<?php

return [
    'create' => [
        'success' => 'Tạo mới bản ghi thành công.',
        'error'   => 'Tạo mới bản ghi thất bại.',
    ],
    'update' => [
        'success' => 'Cập nhật bản ghi thành công.',
        'error'   => 'Cập nhật bản ghi thất bại.',
    ],
    'show' => [
        'success' => 'Hiển thị bản ghi thành công.',
        'error'   => 'Không thể hiển thị bản ghi.',
    ],
    'delete' => [
        'success' => 'Xóa bản ghi thành công.',
        'error'   => 'Xóa bản ghi thất bại.',
    ],
    'publish' => [
        'success' => 'Cập nhập trạng thái thành công.',
        'error'   => 'Cập nhập trạng thái thất bại.',
    ],
    'action' => [
        'success' => 'Thao tác thành công.',
        'error'   => 'Thao tác thất bại.',
    ],
    'upload' => [
        'create' => [
            'success' => 'Tải lên tệp mới thành công.',
            'error'   => 'Tải lên tệp mới thất bại.',
        ],
        'delete' => [
            'success' => 'Xóa tệp thành công.',
            'error'   => 'Xóa tệp thất bại.',
        ],
    ],
    'cart' => [
        'success' => [
            'create'  => 'Thêm giỏ hàng thành công.',
            'update'  => 'Cập nhật giỏ hàng thành công.',
            'delete'  => 'Xóa thành công.',
            'clean'   => 'Xóa thành công giỏ hàng.',
            'publish' => 'Cập nhật trạng thái thành công.',
            'buy_now' => 'Thao tác thành công.',

        ],
        'error' => [
            'not_found'      => 'Sản phẩm không tồn tại trên hệ thống.',
            'max'            => 'Số lượng trong kho không đủ.',
            'min'            => 'Số lượng trong giỏ hàng không đủ.',
            'delete'         => 'Xóa thất bại.',
            'item_not_found' => 'Sản phẩm này không có trong giỏ hàng.',
            'cart_not_found' => 'Người dùng chưa có giỏ hàng.',
        ],
    ],

    'product_review' => [
        'success' => [
            'create'  => 'Cảm ơn bạn đã đánh giá sản phẩm.',
        ],
        'error' => [
            'already_exists'             => 'Sản phẩm trong đơn hàng đã được đánh giá.',
            'order_not_found'            => 'Đơn hàng không tồn tại trên hệ thống.',
            'order_item_not_found'       => 'Sản phẩm này không có trong đơn hàng.',
            'parent_not_found'           => 'Không tìm thấy đánh giá gốc.',
            'admin_reply_already_exists' => 'Đánh giá đã được hồi rồi.',
            'admin_reply_not_found'      => 'Không tìm thấy đánh giá của bạn.',
            'not_admin'                  => 'Bạn không có thẩm quyền thực hiện chức năng này.',
            'admin_not_allowed'          => 'Quản trị viên không được phép đánh giá sản phẩm.',
        ],

    ],

    'voucher' => [
        'error' => [
            'create' => 'Mã giảm giá chỉ nhận một trong hai là giá trị đơn hàng hoặc số lượng tối thiểu',
            'apply'  => 'Đơn hàng chưa đủ điều kiện áp dụng mã giảm giá.',
        ],
        'success' => [
            'apply' => 'Áp dụng giảm giá thành công.',
        ],
    ],
    'order' => [
        'error' => [
            'create'  => 'Tạo đơn hàng thất bại vui lòng thử lại.',
            'payment' => 'Thanh toán đơn hàng thất bại vui lòng thử lại.',
        ],
        'success' => [
            'create'   => 'Tạo đơn hàng thành công vui lòng kiểm tra email.',
            'payment'  => 'Thanh toán đơn hàng thành công.',
            'create'   => 'Tạo đơn hàng thất bại vui lòng thử lại.',
            'payment'  => 'Thanh toán đơn hàng thất bại vui lòng thử lại.',
            'status'   => 'Cập nhập trạng thái đơn hàng thất bại.',
            'invalid'  => 'Bạn không thể hoàn tất đơn hàng khi các trạng thái khác chưa hoàn thành.',
        ],
        'success' => [
            'create'   => 'Tạo đơn hàng thành công vui lòng kiểm tra email.',
            'payment'  => 'Thanh toán đơn hàng thành công.',
            'status'   => 'Cập nhập trạng thái đơn hàng thành công.',
        ],
    ],
    'auth' => [
        'register' => [
            'success'        => 'Người dùng đã đăng ký thành công. Vui lòng kiểm tra email của bạn để xác nhận đăng ký.',
            'error'          => 'Người dùng đã đăng ký thành công vui lòng kiểm tra lại.',
            'email_verified' => 'Email đã xác nhận đăng ký vui lòng đăng nhập.',
        ],
        'reset_password' => [
            'success'    => 'Chúng tôi đã gửi mật khẩu mới vào email của bạn vui lòng kiểm tra.',
            'error'      => 'Đặt lại mật khẩu thất bại.',
        ],
        'update_profile' => [
            'success' => 'Cập nhật hồ sơ thành công.',
            'error'   => 'Cập nhật hồ sơ thất bại.',
        ],
        'invalid_code' => [
            'error' => 'Mã xác nhận không hợp lệ hoặc đã hết hạn.',
        ],
    ],
    'wishlist' => [
        'success' => [
            'create' => 'Thêm sản phẩm ưa thích thành công',
            'delete' => 'Xóa sản phẩm ưa thích thành công',
            'clean'  => 'Xóa tất cả sản phẩm khỏi ưa thích thành công',
            'mail'   => 'Gửi email thành công',
        ],
        'error' => [
            'existed'            => 'Sản phẩm đã tồn tại trong danh mục ưa thích.',
            'create'             => 'Thêm sản phẩm ưa thích không thành công',
            'delete'             => 'Xóa sản phẩm ưa thích thành công',
            'wishlist_not_found' => 'Bạn chưa có sản phẩm ưa thích',
            'auth'               => 'Vui lòng đăng nhập để yêu thích sản phẩm',
            'mail'               => 'Gửi email không thành công',
        ],
    ],
    'userAddress' => [
        'success' => [
            'create'             => 'Thêm địa chỉ thành công.',
            'update'             => 'Cập nhật địa chỉ thành công.',
            'delete'             => 'Xóa địa chỉ thành công.',
        ],
        'error' => [
            'create'             => 'Thêm địa chỉ không thành công.',
            'delete'             => 'Xóa địa chỉ không thành công.',
        ],
    ],

    'flash_sale' => [
        'success' => [
            'create'             => 'Tạo flash sale thành công.',
            'update'             => 'Cập nhật flash sale thành công.',
            'delete'             => 'Xóa flash sale thành công.',
            'publish'            => 'Cập nhật trạng thái flash sale thành công.',
        ],
        'error' => [
            'create'             => 'Tạo flash sale không thành công!',
            'update'             => 'Cập nhật flash sale không thành công!',
            'delete'             => 'Xóa flash sale không thành công!',
            'publish'            => 'Cập nhật trạng thái flash sale không thành công!',
            'not_found'          => 'Flash sale không tồn tại!',
            'out_of_stock'       => 'Không tìm thấy mẫu mã sản phẩm trong đợt giảm giá này!',
            'already_on_sale'    => 'Có biến thể đã tham giá flash sale khác!',
        ],
    ],
];
