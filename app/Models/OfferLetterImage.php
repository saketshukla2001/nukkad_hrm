<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferLetterImage extends Model
{
    protected $fillable = [
        'offer_letter_id',
        'name',
        'path',
        'sort_order',
    ];

    public function offerLetter(): BelongsTo
    {
        return $this->belongsTo(OfferLetter::class);
    }
}

