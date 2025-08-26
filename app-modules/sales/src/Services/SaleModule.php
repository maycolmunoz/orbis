<?php

namespace Modules\Sales\Services;

use Modules\Sales\MoonShine\Pages\POS;
use Modules\Sales\MoonShine\Resources\SaleResource;
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\MenuItem;
use Sweet1s\MoonshineRBAC\Components\MenuRBAC;

class SaleModule
{
    public function getResources(): array
    {
        return [
            SaleResource::class,
        ];
    }

    public function getPages(): array
    {
        return [
            POS::class,
        ];
    }

    public function getMenu(): array
    {
        return MenuRBAC::menu(
            MenuGroup::make('sales', [
                MenuItem::make('POS', POS::class)
                    ->canSee(fn (): bool => moonshineRequest()->user()->can('SaleResource.create')),

                MenuItem::make('sales', SaleResource::class)
                    ->translatable('sales::ui.resource'),

            ], 's.computer-desktop')->translatable('sales::ui.resource'),

            // MenuGroup::make('CRM', [
            //     MenuItem::make('Clientes', CustomerResource::class),
            // ], 's.briefcase')

        );
    }
}
