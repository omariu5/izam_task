<?php

namespace App\Observers;

use App\Models\Stock;
use Illuminate\Support\Facades\Cache;

class StockObserver
{
    /**
     * Handle the Stock "created" event.
     */
    public function created(Stock $stock): void
    {
        $this->invalidateCache($stock);
    }

    /**
     * Handle the Stock "updated" event.
     */
    public function updated(Stock $stock): void
    {
        $this->invalidateCache($stock);
    }

    /**
     * Handle the Stock "deleted" event.
     */
    public function deleted(Stock $stock): void
    {
        $this->invalidateCache($stock);
    }

    /**
     * Invalidate the warehouse inventory cache.
     */
    private function invalidateCache(Stock $stock): void
    {
        Cache::forget("warehouse-inventory-{$stock->warehouse_id}");
    }
}
