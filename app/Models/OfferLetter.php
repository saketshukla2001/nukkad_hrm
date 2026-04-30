<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfferLetter extends Model
{
    protected $fillable = [
        'title',
        'header',
        'content',
        'table_html',
        'footer',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(OfferLetterImage::class)->orderBy('sort_order')->orderBy('id');
    }
}
