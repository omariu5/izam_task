<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->foreignId('to_warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->foreignId('inventory_item_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('quantity');
            $table->timestamp('transferred_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfers');
    }
};
