<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;
use Modules\Inventories\Database\Seeders\DatabaseSeeder as InventoriesSeeder;
use Modules\Sales\Database\Seeders\DatabaseSeeder as SalesSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            InventoriesSeeder::class,
            SalesSeeder::class,
        ]);

    }
}
