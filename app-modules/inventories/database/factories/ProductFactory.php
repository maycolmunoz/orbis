<?php

namespace Modules\Inventories\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Inventories\Models\Category;
use Modules\Inventories\Models\Product;
use Modules\Inventories\Models\Supplier;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->ean13(),
            'name' => fake()->unique()->sentence(1),
            'description' => fake()->text(),
            'price' => fake()->randomFloat(2, 11111.11, 99999.99),
            'stock' => fake()->numberBetween(0, 1000),
            'category_id' => Category::factory(),
            'supplier_id' => Supplier::factory(),
        ];
    }
}
