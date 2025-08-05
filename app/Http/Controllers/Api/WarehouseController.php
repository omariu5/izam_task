<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StockResource;
use App\Repositories\WarehouseRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WarehouseController extends Controller
{
    public function __construct(
        private readonly WarehouseRepository $repository
    ) {}

    public function inventory(int $id): AnonymousResourceCollection
    {
        $inventory = $this->repository->getInventory($id);
        return StockResource::collection($inventory);
    }
}
