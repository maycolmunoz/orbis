<?php

namespace Modules\Sales\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Sales\Services\SaleModule;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;

class SalesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SaleModule::class);
    }

    public function boot(CoreContract $core, SaleModule $sale): void
    {
        $core->resources($sale->getResources())
            ->pages($sale->getPages());
    }
}
