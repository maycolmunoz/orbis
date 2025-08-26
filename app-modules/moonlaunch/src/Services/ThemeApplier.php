<?php

namespace Modules\Moonlaunch\Services;

use MoonShine\Contracts\ColorManager\ColorManagerContract;

class ThemeApplier
{
    /**
     * Create a new class instance.
     */
    public function __construct(private ColorManagerContract $colorManager) {}

    public function theme1(): void
    {
        $this->applyTheme([
            'background' => '#121212',
            'content' => '#1e1e1e',
            'tableRow' => '#1a1a1a',
            'primary' => '#4aa3df',
            'secondary' => '#7f8c8d',
            'stateColor' => '#4a4a4a',
        ]);
    }

    private function applyTheme(array $colors): void
    {
        $this->colorManager
            ->background($colors['background'])
            ->content($colors['content'])
            ->tableRow($colors['tableRow'])
            ->primary($colors['primary'])
            ->secondary($colors['secondary'])
            ->buttons($colors['stateColor'])
            ->dividers($colors['stateColor'])
            ->borders($colors['stateColor']);

        $this->colorManager
            ->successBg('#198754')
            ->successText('#FFFFFF')
            ->warningBg('#FFC107')
            ->warningText('#FFFFFF')
            ->errorBg('#DC3545')
            ->errorText('#FFFFFF')
            ->infoBg('#0D6EFD')
            ->infoText('#FFFFFF')

            ->successBg('#198754', dark: true)
            ->successText('#FFFFFF', dark: true)
            ->warningBg('#FFC107', dark: true)
            ->warningText('#FFFFFF', dark: true)
            ->errorBg('#DC3545', dark: true)
            ->errorText('#FFFFFF', dark: true)
            ->infoBg('#0D6EFD', dark: true)
            ->infoText('#FFFFFF', dark: true);
    }
}
