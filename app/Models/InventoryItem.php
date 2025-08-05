<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function stockTransfers(): HasMany
    {
        return $this->hasMany(StockTransfer::class);
    }
}
