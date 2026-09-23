<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductSize extends Model
{
    protected $guarded = ['id'];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_size',
            'product_size_id',
            'product_id'
        );
    }
}
