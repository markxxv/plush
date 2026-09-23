<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $guarded = ['id'];

    public function getTitleAttribute()
    {
        return $this->{'title_' . app()->getLocale()} ?? $this->title_en;
    }

    public function getBodyAttribute()
    {
        return $this->{'body_' . app()->getLocale()} ?? $this->body_en;
    }

    public function getUrlAttribute()
    {
        return $this->{'url_' . app()->getLocale()} ?? $this->url_en;
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
