<?php

declare(strict_types=1);

namespace Modules\Sales\MoonShine\Resources;

use Modules\Inventories\MoonShine\Resources\ProductResource;
use Modules\Moonlaunch\Traits\Properties;
use Modules\Sales\Models\Sale;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Enums\Action;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\ListOf;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Text;
use Sweet1s\MoonshineRBAC\Traits\WithRolePermissions;

#[Icon('s.clipboard-document-list')]
/**
 * @extends ModelResource<Sale>
 */
class SaleResource extends ModelResource
{
    use Properties, WithRolePermissions;

    protected string $model = Sale::class;

    public function __construct()
    {
        $this->title(__('sales::ui.resource.sales'))
            ->itemsPerPage(20);
    }

    protected function activeActions(): ListOf
    {
        return parent::activeActions()
            ->only(Action::VIEW);
    }

    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),

            BelongsToMany::make('products', resource: ProductResource::class)
                ->translatable('sales::ui.label')
                ->onlyCount()
                ->sortable(),

            Number::make('total_amount')->translatable('sales::ui.label')
                ->sortable(),

            Date::make('created_at')->translatable('sales::ui.label')
                ->format('d/m/Y')
                ->sortable(),
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function detailFields(): iterable
    {
        return [
            Date::make('created_at')->translatable('sales::ui.label')
                ->format('d/m/Y'),

            Number::make('total_amount')->translatable('sales::ui.label'),

            BelongsToMany::make('products', resource: ProductResource::class)
                ->translatable('sales::ui.label')
                ->fields([
                    Text::make('quantity')->translatable('sales::ui.label'),
                ]),
        ];
    }

    /**
     * @param  Sale  $item
     * @return array<string, string[]|string>
     *
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [];
    }
}
