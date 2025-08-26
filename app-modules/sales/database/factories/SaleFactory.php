<?php

namespace Modules\Sales\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Sales\Models\Sale;

class SaleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Sale::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'customer_id' => User::factory(),
            'total_amount' => fake()->randomFloat(2, 0, 9999999999999.99),
        ];
    }
}
