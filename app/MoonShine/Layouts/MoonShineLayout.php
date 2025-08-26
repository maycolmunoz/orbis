<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;

use Modules\Inventories\Services\InventoryModule;
use Modules\Moonlaunch\Services\Launch;
use Modules\Sales\Services\SaleModule;
use MoonShine\ColorManager\ColorManager;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\UI\Components\Layout\Favicon;
use MoonShine\UI\Components\Layout\Footer;
use MoonShine\UI\Components\Layout\Layout;

final class MoonShineLayout extends AppLayout
{
    protected function assets(): array
    {
        return [
            ...parent::assets(),
        ];
    }

    protected function getFaviconComponent(): Favicon
    {
        return parent::getFaviconComponent()->customAssets([
            'apple-touch' => '/icon.svg',
            '32' => '/icon.svg',
            '16' => '/icon.svg',
            'safari-pinned-tab' => '/icon.svg',
            'web-manifest' => '/icon.svg',
        ]);
    }

    protected function getFooterComponent(): Footer
    {
        return Footer::make()
            ->copyright(
                fn (): string => 'ORBIS'
            )->menu($this->getFooterMenu());
    }

    protected function menu(): array
    {
        return [
            ...app(Launch::class)->getMenu(),
            ...app(InventoryModule::class)->getMenu(),
            ...app(SaleModule::class)->getMenu(),
        ];
    }

    /**
     * @param  ColorManager  $colorManager
     */
    protected function colors(ColorManagerContract $colorManager): void
    {
        // parent::colors($colorManager);
    }

    public function build(): Layout
    {
        return parent::build();
    }
}
