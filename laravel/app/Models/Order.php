<?php

namespace App\Models;

use App\Traits\QueryScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory, QueryScopes;

    const ORDER_STATUS = [
        'pending'    => 'Chờ xác nhận',
        'processing' => 'Đang xử lý',
        'delivering' => 'Đang giao',
        'completed'  => 'Hoàn thành',
        'canceled'   => 'Đã huỷ',
        'returned'   => 'Trả hàng',
    ];

    const PAYMENT_STATUS = [
        'paid'   => 'Đã thanh toán',
        'unpaid' => 'Chưa thanh toán',
    ];

    const ORDER_STATUS_PENDING = 'pending';

    const ORDER_STATUS_PROCESSING = 'processing';

    const ORDER_STATUS_DELIVERING = 'delivering';

    const ORDER_STATUS_COMPLETED = 'completed';

    const ORDER_STATUS_CANCELED = 'canceled';

    const ORDER_STATUS_RETURNED = 'returned';

    const STATUS_ORDER = [
        self::ORDER_STATUS_PENDING => 1,
        self::ORDER_STATUS_PROCESSING => 2,
        self::ORDER_STATUS_DELIVERING => 3,
        self::ORDER_STATUS_COMPLETED => 4,
        self::ORDER_STATUS_CANCELED => 5,
        self::ORDER_STATUS_RETURNED => 6
    ];

    const PAYMENT_STATUS_PAID = 'paid';

    const PAYMENT_STATUS_UNPAID = 'unpaid';

    const PAYMENT_STATUS_ORDER = [
        self::PAYMENT_STATUS_PAID => 1,
        self::PAYMENT_STATUS_UNPAID => 2,
    ];

    protected $fillable = [
        'code',
        'customer_name',
        'customer_email',
        'customer_phone',
        'province_id',
        'district_id',
        'ward_id',
        'shipping_address',
        'note',
        'shipping_method_id',
        'payment_method_id',
        'user_id',
        'voucher_id',
        'order_status',
        'payment_status',
        'total_price',
        'shipping_fee',
        'discount',
        'final_price',
        'ordered_at',
        'paid_at',
        'additional_details',
    ];

    protected $casts = [
        'additional_details' => 'array',
    ];

    public function isForwardStatus($newStatus)
    {
        return self::STATUS_ORDER[$newStatus] > self::STATUS_ORDER[$this->order_status];
    }

    public function isForwardPaymentStatus($newStatus)
    {
        return self::PAYMENT_STATUS_ORDER[$newStatus] > self::PAYMENT_STATUS_ORDER[$this->payment_order_status];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function shipping_method()
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'code');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'code');
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_id', 'code');
    }

    public function order_paymentable()
    {
        return $this->hasOne(OrderPaymentable::class);
    }

    public function order_items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function product_reviews()
    {
        return $this->hasMany(ProductReview::class);
    }
}
