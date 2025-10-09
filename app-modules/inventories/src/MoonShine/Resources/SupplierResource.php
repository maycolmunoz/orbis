<?php

declare(strict_types=1);

namespace Modules\Inventories\MoonShine\Resources;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Modules\Inventories\Models\Supplier;
use Modules\MoonLaunch\Traits\WithProperties;
use Modules\MoonLaunch\Traits\WithSoftDeletes;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\PageType;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\Email;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Phone;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Url;
use Sweet1s\MoonshineRBAC\Traits\WithRolePermissions;

#[Icon('truck')]
/**
 * @extends ModelResource<Supplier>
 */
class SupplierResource extends ModelResource
{
    use WithProperties, WithRolePermissions, WithSoftDeletes;

    protected string $model = Supplier::class;

    public function __construct()
    {
        $this->title(__('inventories::ui.resource.suppliers'))
            ->redirectAfterSave(PageType::INDEX)
            ->errorsAbove(false)
            ->allInModal()
            ->async(false)
            ->column('name');
    }

    protected function modifyQueryBuilder(Builder $builder): Builder
    {
        return $builder->withCount('products');
    }

    protected function search(): array
    {
        return ['name', 'contact_info->[*]->email'];
    }

    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            Text::make('name')->translatable('inventories::ui.label')
                ->sortable(),
            Text::make('products', 'products_count')
                ->translatable('inventories::ui.label')
                ->sortable(),
        ];
    }

    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function formFields(): iterable
    {
        return [
            Box::make([
                Text::make('name')->translatable('inventories::ui.label')
                    ->required(),
                Json::make('contact_info')->translatable('inventories::ui.label')
                    ->creatable(limit: 2)
                    ->removable()
                    ->fields([
                        Email::make('email')->translatable('inventories::ui.label'),
                        Phone::make('phone')->translatable('inventories::ui.label'),
                        Text::make('address')->translatable('inventories::ui.label'),
                        Url::make('website')->translatable('inventories::ui.label'),
                    ]),
            ]),
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function detailFields(): iterable
    {
        return [
            Text::make('name')->translatable('inventories::ui.label'),
            Json::make('contact_info')->translatable('inventories::ui.label')
                ->creatable(limit: 2)
                ->fields([
                    Email::make('email')->translatable('inventories::ui.label'),
                    Phone::make('phone')->translatable('inventories::ui.label'),
                    Text::make('address')->translatable('inventories::ui.label'),
                    Url::make('website')->translatable('inventories::ui.label')
                        ->blank(),
                ]),
        ];
    }

    /**
     * @param  Supplier  $item
     * @return array<string, string[]|string>
     *
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [
            'name' => [
                'required',
                'string',
                Rule::unique('suppliers', 'name')->ignore($item?->id),
            ],
        ];
    }
}
