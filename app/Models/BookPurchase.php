<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookPurchase extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = [
        'copies',
    ];
   
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function scopePurchasedInPeriod(Builder $query, Carbon $start, Carbon $end): void
    {
        $query->whereBetween('created_at', [$start, $end]);
    }   
}
