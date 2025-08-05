<?php

namespace App\Repositories;

use App\Models\Warehouse;
use Illuminate\Pagination\LengthAwarePaginator;

class WarehouseRepository
{
    public function getInventory(int $warehouseId, int $perPage = 15): LengthAwarePaginator
    {
        return Warehouse::findOrFail($warehouseId)
            ->stocks()
            ->with(['inventoryItem'])
            ->paginate($perPage);
    }
}
