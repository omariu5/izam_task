<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockTransferRequest;
use App\Http\Resources\StockTransferResource;
use App\Repositories\StockTransferRepository;
use Illuminate\Http\JsonResponse;

class StockTransferController extends Controller
{
    public function __construct(
        private readonly StockTransferRepository $repository
    ) {}

    public function store(StockTransferRequest $request): JsonResponse
    {
        try {
            $transfer = $this->repository->transfer($request->validated());
            return response()->json([
                'message' => 'Stock transfer completed successfully',
                'data' => new StockTransferResource($transfer)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to process stock transfer',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
