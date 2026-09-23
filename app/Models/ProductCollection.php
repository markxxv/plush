<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ProductCollection extends Model implements HasMedia
{
    protected $guarded = ['id'];
    use InteractsWithMedia;

    protected $casts = [
        'path_en' => 'array',
        'path_fr' => 'array',
    ];

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('cover')
            ->useDisk('public')
            ->singleFile();
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'collection_product',
            'product_collection_id',
            'product_id'
        );
    }

    protected function cover(): Attribute
    {
        return Attribute::get(
            fn (): ?string => $this->getFirstMediaUrl('cover') ?: null,
        );
    }

    public function getTitleAttribute()
    {
        return $this->{'name_' . app()->getLocale()} ?? $this->name_en;
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
