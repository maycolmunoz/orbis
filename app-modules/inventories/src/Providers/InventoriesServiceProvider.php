<?php

namespace Modules\Inventories\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Inventories\Services\InventoryModule;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;

class InventoriesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(InventoryModule::class);
    }

    public function boot(
        CoreContract $core,
        ColorManagerContract $colorManager,
        InventoryModule $inventories): void
    {

        $core->resources($inventories->getResources());
    }
}
