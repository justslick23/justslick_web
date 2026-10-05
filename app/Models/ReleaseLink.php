<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReleaseLink extends Model
{
    protected $fillable = ['label', 'url', 'sort_order'];

    public function release(): BelongsTo
    {
        return $this->belongsTo(Release::class);
    }
}