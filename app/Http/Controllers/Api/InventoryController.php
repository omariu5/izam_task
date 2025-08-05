<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Stock::query()
            ->with(['inventoryItem', 'warehouse'])
            ->select('stocks.*');

        // Filter by name
        if ($request->has('name')) {
            $query->whereHas('inventoryItem', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->name . '%');
            });
        }

        // Filter by SKU
        if ($request->has('sku')) {
            $query->whereHas('inventoryItem', function ($q) use ($request) {
                $q->where('sku', $request->sku);
            });
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->whereHas('inventoryItem', function ($q) use ($request) {
                $q->where('price', '>=', $request->min_price);
            });
        }

        if ($request->has('max_price')) {
            $query->whereHas('inventoryItem', function ($q) use ($request) {
                $q->where('price', '<=', $request->max_price);
            });
        }

        // Filter by warehouse
        if ($request->has('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        return $query->paginate(15);
    }
}
