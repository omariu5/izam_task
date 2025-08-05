<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Stock;
use App\Models\Warehouse;
use App\Models\InventoryItem;
use App\Events\LowStockDetected;
use App\Repositories\StockTransferRepository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LowStockDetectedTest extends TestCase
{
    use RefreshDatabase;

    private StockTransferRepository $repository;
    private InventoryItem $item;
    private Warehouse $sourceWarehouse;
    private Warehouse $destinationWarehouse;
    private Stock $sourceStock;
    private int $threshold = 10;

    protected function setUp(): void
    {
        parent::setUp();

        // Set fixed threshold for testing
        Config::set('inventory.low_stock_threshold', $this->threshold);
        
        $this->repository = new StockTransferRepository();

        // Create test data
        $this->item = InventoryItem::factory()->create();
        $this->sourceWarehouse = Warehouse::factory()->create();
        $this->destinationWarehouse = Warehouse::factory()->create();

        // Set initial stock to just above threshold
        $this->sourceStock = Stock::factory()->create([
            'inventory_item_id' => $this->item->id,
            'warehouse_id' => $this->sourceWarehouse->id,
            'quantity' => $this->threshold + 5
        ]);
    }

    /** @test */
    public function it_fires_low_stock_event_when_threshold_is_crossed()
    {
        Event::fake();

        // Initial stock is threshold + 5 (15)
        // Transfer 6 units to get below threshold (9)
        $this->repository->transfer([
            'inventory_item_id' => $this->item->id,
            'from_warehouse_id' => $this->sourceWarehouse->id,
            'to_warehouse_id' => $this->destinationWarehouse->id,
            'quantity' => 6
        ]);

        Event::assertDispatched(LowStockDetected::class, function ($event) {
            return $event->stock->id === $this->sourceStock->id
                && $event->stock->quantity === 9 // 15 - 6 = 9 (below threshold of 10)
                && $event->threshold === $this->threshold;
        });
    }

    /** @test */
    public function it_does_not_fire_low_stock_event_when_above_threshold()
    {
        Event::fake();

        // Initial stock is threshold + 5 (15)
        // Transfer 2 units to stay above threshold (13)
        $this->repository->transfer([
            'inventory_item_id' => $this->item->id,
            'from_warehouse_id' => $this->sourceWarehouse->id,
            'to_warehouse_id' => $this->destinationWarehouse->id,
            'quantity' => 2
        ]);

        // Stock should be 13, which is above threshold of 10
        $this->assertEquals(13, $this->sourceStock->fresh()->quantity);
        Event::assertNotDispatched(LowStockDetected::class);
    }

    /** @test */
    public function it_includes_correct_data_in_event()
    {
        Event::fake();

        // Initial stock is threshold + 5 (15)
        // Transfer 6 units to get to 9 (below threshold)
        $transferQuantity = 6;
        $expectedFinalQuantity = $this->sourceStock->quantity - $transferQuantity;

        $this->repository->transfer([
            'inventory_item_id' => $this->item->id,
            'from_warehouse_id' => $this->sourceWarehouse->id,
            'to_warehouse_id' => $this->destinationWarehouse->id,
            'quantity' => $transferQuantity
        ]);

        Event::assertDispatched(function (LowStockDetected $event) use ($expectedFinalQuantity) {
            $this->assertEquals($this->item->id, $event->stock->inventory_item_id);
            $this->assertEquals($this->sourceWarehouse->id, $event->stock->warehouse_id);
            $this->assertEquals($expectedFinalQuantity, $event->stock->quantity);
            $this->assertEquals($this->threshold, $event->threshold);
            return true;
        });
    }
}
