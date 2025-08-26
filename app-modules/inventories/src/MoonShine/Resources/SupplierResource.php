<?php

declare(strict_types=1);

namespace Modules\Inventories\MoonShine\Resources;

use Modules\Inventories\Models\Supplier;
use Modules\Moonlaunch\Traits\Properties;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Enums\Action;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\Email;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Phone;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Url;
use Sweet1s\MoonshineRBAC\Traits\WithRolePermissions;

#[Icon('s.truck')]
/**
 * @extends ModelResource<Supplier>
 */
class SupplierResource extends ModelResource
{
    use Properties, WithRolePermissions;

    protected string $model = Supplier::class;

    public function __construct()
    {
        $this->title(__('inventories::ui.resource.suppliers'))
            ->async(false)
            ->errorsAbove(false)
            ->column('name');
    }

    protected function activeActions(): ListOf
    {
        return parent::activeActions()
            ->except(Action::VIEW);
    }

    protected function search(): array
    {
        return ['id', 'name'];
    }

    private function fields($vertical = false): array
    {
        return [
            ID::make()->sortable(),

            Text::make('name')->translatable('inventories::ui.label'),

            Json::make('contact_info')->translatable('inventories::ui.label')
                ->fields([
                    Email::make('email')->translatable('inventories::ui.label'),
                    Phone::make('phone')->translatable('inventories::ui.label'),
                    Text::make('address')->translatable('inventories::ui.label'),
                    Url::make('website')->translatable('inventories::ui.label'),
                ])
                ->creatable(limit: 2)
                ->vertical($vertical),
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ...$this->fields(true),
            HasMany::make('products', resource: ProductResource::class)
                ->translatable('inventories::ui.label')
                ->relatedLink(),
        ];
    }

    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function formFields(): iterable
    {
        return [
            Box::make([
                ...$this->fields(),

                HasMany::make('products', resource: ProductResource::class)
                    ->translatable('inventories::ui.label')
                    ->creatable(),
            ]),
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function detailFields(): iterable
    {
        return $this->fields();
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
            'name' => moonshineRequest()->isMethod('post')
                ? 'required|string|unique:suppliers,name'
                : 'required|string|unique:suppliers,name,'.$item->id,
        ];
    }
}
