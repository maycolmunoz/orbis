<?php

namespace Modules\Inventories\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Inventories\Models\Category;
use Modules\Inventories\Models\Image;
use Modules\Inventories\Models\Product;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::factory(4)
            ->has(
                Product::factory()
                    ->has(Image::factory()->count(3))
                    ->count(6)
            )
            ->create();
    }
}
