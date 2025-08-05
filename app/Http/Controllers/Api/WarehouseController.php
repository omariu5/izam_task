<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function inventory(Warehouse $warehouse)
    {
        return $warehouse->stocks()
            ->with('inventoryItem')
            ->paginate(15);
    }
}
