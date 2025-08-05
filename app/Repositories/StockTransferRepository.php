<?php

namespace App\Repositories;

use App\Events\LowStockDetected;
use App\Models\Stock;
use App\Models\StockTransfer;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockTransferRepository
{
    public function transfer(array $data): StockTransfer
    {
        return DB::transaction(function () use ($data) {
            $sourceStock = $this->getOrCreateStock($data['inventory_item_id'], $data['from_warehouse_id']);

            if ($sourceStock->quantity < $data['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => ['Insufficient stock in source warehouse'],
                ]);
            }

            // Update source stock
            $sourceStock->decrement('quantity', $data['quantity']);
            
            // Check for low stock after transfer
            $threshold = config('inventory.low_stock_threshold');
            if ($sourceStock->quantity <= $threshold) {
                event(new LowStockDetected($sourceStock, $threshold));
            }

            // Update destination stock
            $destinationStock = $this->getOrCreateStock($data['inventory_item_id'], $data['to_warehouse_id']);
            $destinationStock->increment('quantity', $data['quantity']);

            // Record transfer
            return StockTransfer::create($data);
        });
    }

    private function getOrCreateStock(int $inventoryItemId, int $warehouseId): Stock
    {
        return Stock::firstOrCreate([
            'inventory_item_id' => $inventoryItemId,
            'warehouse_id' => $warehouseId,
        ], [
            'quantity' => 0,
        ]);
    }
}
