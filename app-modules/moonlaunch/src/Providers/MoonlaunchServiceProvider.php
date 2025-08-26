<?php

namespace Modules\Moonlaunch\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Moonlaunch\Services\Launch;
use Modules\Moonlaunch\Services\ThemeApplier;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;

class MoonlaunchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Launch::class);
    }

    public function boot(
        CoreContract $core,
        ColorManagerContract $colorManager,
        Launch $launch
    ): void {

        (new ThemeApplier($colorManager))->theme1();

        $core->resources($launch->getResources());
    }
}
