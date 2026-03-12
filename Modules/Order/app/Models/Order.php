<?php

namespace Modules\Order\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'invoice_id',
        'buyer_id',
        'status',
        'has_coupon',
        'coupon_code',
        'coupon_discount_percent',
        'coupon_discount_amount',
        'payment_method',
        'payment_status',
        'payable_amount',
        'gateway_charge',
        'payable_with_charge',
        'paid_amount',
        'conversion_rate',
        'payable_currency',
        'payment_details',
        'transaction_id',
        'commission_rate',
        'order_type',
        'order_details',
    ];
    protected $casts = [
        'order_details' => 'array',
    ];
    public function getOrderDetailsAttribute($value): object | null {
        $rawValue = $value;

        if ($rawValue === null && array_key_exists('order_details', $this->attributes)) {
            $rawValue = $this->attributes['order_details'];
        }

        if ($rawValue === null) {
            return null;
        }

        if (is_object($rawValue)) {
            return $rawValue;
        }

        if (is_array($rawValue)) {
            return (object) $rawValue;
        }

        return json_decode((string) $rawValue);
    }

    public const ORDER_TYPE_BUNDLE = 'bundle';
    public function isBundleOrder(): bool {
        return $this->order_type == self::ORDER_TYPE_BUNDLE;
    }

    public const ORDER_TYPE_GIFT = 'gift';
    public function isGiftOrder(): bool {
        return $this->order_type == self::ORDER_TYPE_GIFT;
    }
    public function scopeGiftOrder($query) {
        return $query->where('order_type', self::ORDER_TYPE_GIFT);
    }

    public function user() {
        return $this->belongsTo(User::class, 'buyer_id', 'id')->select('id', 'name', 'email', 'phone', 'address', 'image');
    }

    public function orderItems() {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }
}
