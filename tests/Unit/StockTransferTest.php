<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Stock;
use App\Models\Warehouse;
use App\Models\InventoryItem;
use App\Repositories\StockTransferRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

class StockTransferTest extends TestCase
{
    use RefreshDatabase;

    private StockTransferRepository $repository;
    private InventoryItem $item;
    private Warehouse $sourceWarehouse;
    private Warehouse $destinationWarehouse;
    private Stock $sourceStock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new StockTransferRepository();

        // Create test data
        $this->item = InventoryItem::factory()->create();
        $this->sourceWarehouse = Warehouse::factory()->create();
        $this->destinationWarehouse = Warehouse::factory()->create();

        // Create initial stock
        $this->sourceStock = Stock::factory()->create([
            'inventory_item_id' => $this->item->id,
            'warehouse_id' => $this->sourceWarehouse->id,
            'quantity' => 10
        ]);
    }

    /** @test */
    public function it_fails_when_transfer_quantity_exceeds_available_stock()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Insufficient stock in source warehouse');

        $this->repository->transfer([
            'inventory_item_id' => $this->item->id,
            'from_warehouse_id' => $this->sourceWarehouse->id,
            'to_warehouse_id' => $this->destinationWarehouse->id,
            'quantity' => 20 // Trying to transfer more than available
        ]);
    }

    /** @test */
    public function it_fails_when_source_stock_does_not_exist()
    {
        $this->expectException(ValidationException::class);

        $nonexistentItem = InventoryItem::factory()->create();

        $this->repository->transfer([
            'inventory_item_id' => $nonexistentItem->id,
            'from_warehouse_id' => $this->sourceWarehouse->id,
            'to_warehouse_id' => $this->destinationWarehouse->id,
            'quantity' => 5
        ]);
    }
}
