<?php

namespace App\Models;

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

    /**
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
   
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
