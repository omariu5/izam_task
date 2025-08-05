<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Stock;
use App\Models\Warehouse;
use App\Models\InventoryItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class TransferStockFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private InventoryItem $item;
    private Warehouse $sourceWarehouse;
    private Warehouse $destinationWarehouse;
    private Stock $sourceStock;

    protected function setUp(): void
    {
        parent::setUp();

        // Create and authenticate user
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);

        // Create test data
        $this->item = InventoryItem::factory()->create();
        $this->sourceWarehouse = Warehouse::factory()->create();
        $this->destinationWarehouse = Warehouse::factory()->create();

        // Create initial stock
        $this->sourceStock = Stock::factory()->create([
            'inventory_item_id' => $this->item->id,
            'warehouse_id' => $this->sourceWarehouse->id,
            'quantity' => 100
        ]);
    }

    /** @test */
    public function authenticated_user_can_transfer_stock()
    {
        // Verify user is authenticated
        $this->assertTrue(auth()->check());

        $response = $this->postJson('/api/stock-transfers', [
            'inventory_item_id' => $this->item->id,
            'from_warehouse_id' => $this->sourceWarehouse->id,
            'to_warehouse_id' => $this->destinationWarehouse->id,
            'quantity' => 30
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'quantity',
                    'transferred_at'
                ]
            ]);

        // Verify source stock was decreased
        $this->assertDatabaseHas('stocks', [
            'inventory_item_id' => $this->item->id,
            'warehouse_id' => $this->sourceWarehouse->id,
            'quantity' => 70 // Original 100 - 30 transferred
        ]);

        // Verify destination stock was created
        $this->assertDatabaseHas('stocks', [
            'inventory_item_id' => $this->item->id,
            'warehouse_id' => $this->destinationWarehouse->id,
            'quantity' => 30
        ]);

        // Verify transfer record was created
        $this->assertDatabaseHas('stock_transfers', [
            'inventory_item_id' => $this->item->id,
            'from_warehouse_id' => $this->sourceWarehouse->id,
            'to_warehouse_id' => $this->destinationWarehouse->id,
            'quantity' => 30
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_transfer_stock()
    {
        // Create a fresh test instance without authentication
        $this->app->make('auth')->forgetGuards();

        $response = $this->postJson('/api/stock-transfers', [
            'inventory_item_id' => $this->item->id,
            'from_warehouse_id' => $this->sourceWarehouse->id,
            'to_warehouse_id' => $this->destinationWarehouse->id,
            'quantity' => 30
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->postJson('/api/stock-transfers', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'inventory_item_id',
                'from_warehouse_id',
                'to_warehouse_id',
                'quantity'
            ]);
    }
}
