<?php

declare(strict_types=1);

namespace Modules\Inventories\MoonShine\Resources;

use Modules\Inventories\Models\Category;
use Modules\Moonlaunch\Traits\Properties;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Enums\Action;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\PageType;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use Sweet1s\MoonshineRBAC\Traits\WithRolePermissions;

#[Icon('s.tag')]
/**
 * @extends ModelResource<Category>
 */
class CategoryResource extends ModelResource
{
    use Properties, WithRolePermissions;

    protected string $model = Category::class;

    public function __construct()
    {
        $this->title(__('inventories::ui.resource.categories'))
            ->redirectAfterSave(PageType::INDEX)
            ->column('name')
            ->async(false)
            ->allInModal();
    }

    protected function activeActions(): ListOf
    {
        return parent::activeActions()
            ->except(Action::VIEW);
    }

    private function fields(): array
    {
        return [
            ID::make()->sortable(),
            Text::make('name')->translatable('inventories::ui.label'),
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ...$this->fields(),
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
            Box::make($this->fields()),
        ];
    }

    /**
     * @param  Category  $item
     * @return array<string, string[]|string>
     *
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [
            'name' => moonshineRequest()->isMethod('post')
                ? 'required|string|unique:categories,name'
                : 'required|string|unique:categories,name,'.$item->id,
        ];
    }
}
