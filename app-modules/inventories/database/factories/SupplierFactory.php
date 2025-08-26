<?php

namespace Modules\Inventories\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Inventories\Models\Supplier;

class SupplierFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Supplier::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'contact_info' => '{}',
        ];
    }
}
