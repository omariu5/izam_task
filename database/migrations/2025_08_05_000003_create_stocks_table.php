<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('quantity');
            $table->timestamps();

            $table->unique(['inventory_item_id', 'warehouse_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
