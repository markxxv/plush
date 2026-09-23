<?php

namespace App\Models;

use App\Models\Delivery;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Order extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'items' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            // Generate temporary order number, will be updated after save
            $order->order_number = 'temp_' . time();
        });

        static::created(function ($order) {
            $order->generateOrderNumber();
        });
    }

    public function generateOrderNumber()
    {
        $date = Carbon::now()->format('dmY'); // DDMMYYYY
        $orderNumber = $date . $this->id;
        $this->update(['order_number' => $orderNumber]);
    }

    public function delivery()
    {
        return $this->belongsTo(Delivery::class, 'delivery_country_code', 'iso_code');
    }

    public function hasFreeDelivery(): bool
    {
        return false;
       // return $this->subtotal >= 300; // 300 EUR in cents
    }

    public function getFinalDeliveryCost(): int
    {
        return $this->delivery_cost;
        //return $this->hasFreeDelivery() ? 0 : $this->delivery_cost;
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Payment pending',
            'paid' => 'Paid',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'return' => 'Return',
            'cancelled' => 'Cancelled',
            default => 'Unknown'
        };
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
