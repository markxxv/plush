<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Enums\CropPosition;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    protected $guarded = ['id'];
    use InteractsWithMedia;

    protected $casts = [
        'price' => 'decimal:2',
        'availability' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeForCard(Builder $query): Builder
    {
        return $query
            ->select([
                'products.id',
                'products.title_en',
                'products.title_fr',
                'products.slug_en',
                'products.slug_fr',
                'products.price',
            ])
            ->with([
                'media' => fn ($query) => $query
                    ->select([
                        'media.id',
                        'media.model_type',
                        'media.model_id',
                        'media.collection_name',
                        'media.name',
                        'media.file_name',
                        'media.disk',
                        'media.conversions_disk',
                        'media.manipulations',
                        'media.order_column',
                    ])
                    ->where('collection_name', 'gallery')
                    ->orderBy('order_column')
                    ->limit(2),
            ]);
    }

    public function sizes(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductSize::class,
            'product_size',
            'product_id',
            'product_size_id'
        );
    }

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductCollection::class,
            'collection_product',
            'product_id',
            'product_collection_id'
        );
    }

    public function reviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\ProductReview::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 400, 400)
            ->format('webp')
            ->quality(82)
            ->nonQueued();

        $this->addMediaConversion('small')
            ->fit(Fit::Crop, 800, 1199)
            ->format('webp')
            ->quality(82)
            ->nonQueued();


        $this->addMediaConversion('large')
            ->fit(Fit::Crop, 1200, 1799)
            ->format('webp')
            ->quality(82)
            ->nonQueued();

        $this->addMediaConversion('smalla')
            ->fit(Fit::Crop, 800, 1199)
            ->format('avif')
            ->quality(82)
            ->nonQueued();

        $this->addMediaConversion('largea')
            ->fit(Fit::Crop, 1200, 1799)
            ->format('avif')
            ->quality(82)
            ->nonQueued();
    }


    public function getTitleAttribute()
    {
        return $this->{'title_' . app()->getLocale()} ?? $this->title_en;
    }

    public function getDescriptionAttribute()
    {
        return $this->{'description_' . app()->getLocale()} ?? $this->description_en;
    }

    public function getUrlAttribute()
    {
        return $this->{'slug_' . app()->getLocale()} ?? $this->slug_en;
    }

    public function getMetaTitleAttribute()
    {
        return $this->{'meta_title_' . app()->getLocale()} ?? $this->meta_title_en;
    }

    public function getMetaDescriptionAttribute()
    {
        return $this->{'meta_description_' . app()->getLocale()} ?? $this->meta_description_en;
    }


}
