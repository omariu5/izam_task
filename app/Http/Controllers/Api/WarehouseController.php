<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StockResource;
use App\Repositories\WarehouseRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class WarehouseController extends Controller
{
    public function __construct(
        private readonly WarehouseRepository $repository
    ) {}

    public function inventory(int $id): AnonymousResourceCollection
    {
        $cacheKey = "warehouse-inventory-{$id}";
        
        $inventory = Cache::remember($cacheKey, 60, function () use ($id) {
            return $this->repository->getInventory($id);
        });
        
        return StockResource::collection($inventory);
    }
}
