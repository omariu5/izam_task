<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\StockTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockTransferController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            return DB::transaction(function () use ($validated) {
                // Check if source warehouse has enough stock
                $sourceStock = Stock::where([
                    'warehouse_id' => $validated['from_warehouse_id'],
                    'inventory_item_id' => $validated['inventory_item_id'],
                ])->firstOrFail();

                if ($sourceStock->quantity < $validated['quantity']) {
                    throw ValidationException::withMessages([
                        'quantity' => ['Insufficient stock in source warehouse'],
                    ]);
                }

                // Decrease source warehouse stock
                $sourceStock->decrement('quantity', $validated['quantity']);

                // Increase or create destination warehouse stock
                Stock::updateOrCreate(
                    [
                        'warehouse_id' => $validated['to_warehouse_id'],
                        'inventory_item_id' => $validated['inventory_item_id'],
                    ],
                    [
                        'quantity' => DB::raw('quantity + ' . $validated['quantity']),
                    ]
                );

                // Record the transfer
                return StockTransfer::create($validated);
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to process stock transfer',
            ], 500);
        }
    }
}
