<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'summary',
        'rating',
        'price',
        'date_published',
    ];

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(BookPurchase::class);
    }
}
