<?php

namespace Modules\Inventories\Services;

use Modules\Inventories\MoonShine\Resources\CategoryResource;
use Modules\Inventories\MoonShine\Resources\ImageResource;
use Modules\Inventories\MoonShine\Resources\ProductResource;
use Modules\Inventories\MoonShine\Resources\SupplierResource;
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\MenuItem;
use Sweet1s\MoonshineRBAC\Components\MenuRBAC;

class InventoryModule
{
    public function getResources(): array
    {
        return [
            CategoryResource::class,
            ProductResource::class,
            SupplierResource::class,
            ImageResource::class,
        ];
    }

    public function getMenu(): array
    {
        return MenuRBAC::menu(
            MenuGroup::make('inventory', [
                MenuItem::make('categories', CategoryResource::class)
                    ->translatable('inventories::ui.resource'),

                MenuItem::make('suppliers', SupplierResource::class)
                    ->translatable('inventories::ui.resource'),

                MenuItem::make('products', ProductResource::class)
                    ->translatable('inventories::ui.resource'),
            ], 's.archive-box')->translatable('inventories::ui.resource'),

        );
    }
}
