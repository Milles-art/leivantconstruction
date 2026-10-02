<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = ['cart_id', 'product_id', 'quantity', 'purchase_type', 'rental_start_date', 'rental_end_date', 'rental_days'];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'rental_start_date' => 'date',
            'rental_end_date' => 'date',
            'rental_days' => 'integer',
        ];
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
