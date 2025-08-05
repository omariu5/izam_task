<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Stock;
use App\Models\Warehouse;
use App\Models\InventoryItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create warehouses
        $warehouses = Warehouse::factory(5)->create();

        // Create inventory items
        $items = InventoryItem::factory(20)->create();

        // Create stock entries
        foreach ($warehouses as $warehouse) {
            // Randomly select between 5 and 15 items for each warehouse
            $randomItems = $items->random(fake()->numberBetween(5, 15));
            
            foreach ($randomItems as $item) {
                Stock::create([
                    'warehouse_id' => $warehouse->id,
                    'inventory_item_id' => $item->id,
                    'quantity' => fake()->numberBetween(10, 1000),
                ]);
            }
        }
    }
}
