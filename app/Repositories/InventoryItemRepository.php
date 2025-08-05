<?php

namespace App\Repositories;

use App\Models\InventoryItem;
use Illuminate\Pagination\LengthAwarePaginator;

class InventoryItemRepository
{
    public function getPaginatedList(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = InventoryItem::query()
            ->with(['stocks.warehouse']);

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['sku'])) {
            $query->where('sku', $filters['sku']);
        }

        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (!empty($filters['warehouse_id'])) {
            $query->whereHas('stocks', function ($query) use ($filters) {
                $query->where('warehouse_id', $filters['warehouse_id']);
            });
        }

        return $query->paginate($perPage);
    }
}
