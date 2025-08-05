<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryIndexRequest;
use App\Http\Resources\InventoryItemResource;
use App\Repositories\InventoryItemRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InventoryController extends Controller
{
    public function __construct(
        private readonly InventoryItemRepository $repository
    ) {}

    public function index(InventoryIndexRequest $request): AnonymousResourceCollection
    {
        $inventory = $this->repository->getPaginatedList(
            $request->validated(),
            $request->input('per_page', 15)
        );

        return InventoryItemResource::collection($inventory);
    }
}
