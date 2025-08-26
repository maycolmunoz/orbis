<?php

namespace Modules\Sales\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Inventories\Models\Product;
use Modules\Sales\Models\Sale;
use Modules\Sales\Models\SaleProduct;

class SaleProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SaleProduct::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'sale_id' => Sale::factory(),
            'product_id' => Product::factory(),
            'quantity' => fake()->numberBetween(-10000, 10000),
        ];
    }
}
