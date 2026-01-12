<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'barcode',
        'name',
        'cost_price',
        'sell_price',
        'stock',
    ];

    public function stockEntries()
    {
        return $this->hasMany(StockEntry::class);
    }

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Get the last purchase price from stock entries (fallback to cost_price)
     */
    public function getLastPurchasePriceAttribute()
    {
        $price = $this->stockEntries()->orderBy('created_at', 'desc')->value('purchase_price');
        return $price ?? $this->cost_price ?? 0;
    }
}
