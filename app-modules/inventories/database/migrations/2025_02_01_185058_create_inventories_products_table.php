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
        Schema::create('inventories_products', function (Blueprint $table) {
            $table->id();
            $table->string('code', 14)->unique();
            $table->string('name', 100);
            $table->json('images')->nullable();
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('stock');

            $table->foreignId('category_id')
                ->constrained('inventories_categories')
                ->cascadeOnDelete();

            $table->foreignId('supplier_id')
                ->nullable()
                ->constrained('inventories_suppliers')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories_products');
    }
};
