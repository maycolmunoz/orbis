<?php

namespace Modules\Inventories\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Inventories\Models\Category;

class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
        ];
    }
}
