<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales_product_sale', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sale_id')
                ->constrained('sales_sales')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('inventories_products')
                ->cascadeOnDelete();

            $table->unsignedInteger('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_product_sale');
    }
};
