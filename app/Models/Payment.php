<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{

    protected $fillable = [
        'order_id',
        'user_id',
        'amount',
        'currency',
        'provider',
        'provider_payment_id',
        'provider_charge_id',
        'status',
        'payment_method_type',
        'payload',
        'failure_reason',
        'paid_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'paid_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function order()
    {
        return $this->belongsTo(ShopOrder::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors / Helpers
    |--------------------------------------------------------------------------
    */

    public function getIsSuccessfulAttribute(): bool
    {
        return $this->status === 'succeeded';
    }

    public function getIsPendingAttribute(): bool
    {
        return in_array($this->status, ['pending', 'requires_action']);
    }

    public function getIsFailedAttribute(): bool
    {
        return in_array($this->status, ['failed', 'canceled']);
    }

    public function markAsSucceeded(array $payload = []): void
    {
        $this->update([
            'status'  => 'succeeded',
            'payload' => $payload ?: $this->payload,
            'paid_at' => now(),
        ]);
    }

    public function markAsFailed(?string $reason = null, array $payload = []): void
    {
        $this->update([
            'status'         => 'failed',
            'failure_reason' => $reason,
            'payload'        => $payload ?: $this->payload,
        ]);
    }

    public function markAsRefunded(): void
    {
        $this->update([
            'status'  => 'refunded',
            'paid_at' => null,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeSucceeded($query)
    {
        return $query->where('status', 'succeeded');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

}
