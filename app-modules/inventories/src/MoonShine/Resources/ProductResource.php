<?php

declare(strict_types=1);

namespace Modules\Inventories\MoonShine\Resources;

use Modules\Inventories\Models\Product;
use Modules\Moonlaunch\Traits\Properties;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\ImportExport\Contracts\HasImportExportContract;
use MoonShine\ImportExport\Traits\ImportExportConcern;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\RelationRepeater;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Attributes\Icon;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image as IMG;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use Sweet1s\MoonshineRBAC\Traits\WithRolePermissions;

#[Icon('s.square2x2')]
/**
 * @extends ModelResource<Product>
 */
class ProductResource extends ModelResource implements HasImportExportContract
{
    use ImportExportConcern, Properties, WithRolePermissions;

    protected string $model = Product::class;

    public function __construct()
    {
        $this->title(__('inventories::ui.resource.products'))
            ->errorsAbove(false)
            ->columnSelection()
            ->itemsPerPage(15)
            ->with(['image'])
            ->column('name')
            ->async(false);
    }

    private function exportAndImport(): iterable
    {
        return [
            ID::make(),

            Number::make('code')->translatable('inventories::ui.label'),

            Text::make('name')->translatable('inventories::ui.label'),

            Text::make('description')->translatable('inventories::ui.label'),

            Text::make('price')->translatable('inventories::ui.label'),

            Number::make('stock')->translatable('inventories::ui.label'),

            BelongsTo::make('category', resource: CategoryResource::class)
                ->translatable('inventories::ui.label'),

            BelongsTo::make('supplier', resource: SupplierResource::class)
                ->translatable('inventories::ui.label'),

            Date::make('created_at')->translatable('inventories::ui.label')
                ->format('d/M/Y'),
        ];
    }

    protected function exportFields(): iterable
    {
        return $this->exportAndImport();
    }

    protected function importFields(): iterable
    {
        return $this->exportAndImport();
    }

    protected function search(): array
    {
        return ['id', 'code', 'name'];
    }

    protected function filters(): iterable
    {
        return [
            BelongsTo::make('category', resource: CategoryResource::class)
                ->translatable('inventories::ui.label'),

            BelongsTo::make('supplier', resource: SupplierResource::class)
                ->translatable('inventories::ui.label')
                ->nullable(),
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),

            Img::make('image', 'image.name')->translatable('inventories::ui.label')
                ->disk('products'),

            Number::make('code')->translatable('inventories::ui.label'),

            Text::make('name')->translatable('inventories::ui.label'),

            Text::make('price')->translatable('inventories::ui.label')
                ->sortable(),

            Number::make('stock')->translatable('inventories::ui.label')
                ->badge(fn ($value) => $value > 10 ? 'green' : 'red')
                ->sortable(),

            BelongsTo::make('category', resource: CategoryResource::class)
                ->translatable('inventories::ui.label'),

            BelongsTo::make('supplier', resource: SupplierResource::class)
                ->translatable('inventories::ui.label'),

            Date::make('created_at')->translatable('inventories::ui.label')
                ->format('d/M/Y'),
        ];
    }

    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function formFields(): iterable
    {
        return [
            Box::make([
                Grid::make([
                    Column::make([
                        Text::make('name')->translatable('inventories::ui.label'),

                        Number::make('code')->translatable('inventories::ui.label'),

                        Flex::make([
                            Text::make('price')->translatable('inventories::ui.label'),

                            Number::make('stock')->translatable('inventories::ui.label')
                                ->buttons(),
                        ]),

                        BelongsTo::make('category', resource: CategoryResource::class)
                            ->translatable('inventories::ui.label'),

                        BelongsTo::make('supplier', resource: SupplierResource::class)
                            ->translatable('inventories::ui.label')
                            ->nullable(),

                    ], 6),

                    Column::make([
                        Textarea::make('description')->translatable('inventories::ui.label'),

                        RelationRepeater::make('images', resource: ImageResource::class)
                            ->translatable('inventories::ui.label')
                            ->fields([
                                Img::make('', 'name')->disk('products'),
                            ])
                            ->creatable(limit: 3)
                            ->removable(),
                    ], 6),
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
            ID::make()->sortable(),

            Number::make('code')->translatable('inventories::ui.label'),

            Text::make('name')->translatable('inventories::ui.label'),

            Text::make('price')->translatable('inventories::ui.label'),

            Number::make('stock')->translatable('inventories::ui.label')
                ->badge(fn ($value) => $value > 10 ? 'green' : 'red'),

            BelongsTo::make('category', resource: CategoryResource::class)
                ->translatable('inventories::ui.label'),

            BelongsTo::make('supplier', resource: SupplierResource::class)
                ->translatable('inventories::ui.label'),

            Date::make('created_at')->translatable('inventories::ui.label')
                ->format('d/M/Y'),

            Textarea::make('description')->translatable('inventories::ui.label'),

            RelationRepeater::make('images', resource: ImageResource::class)
                ->translatable('inventories::ui.label')
                ->fields([
                    Img::make('', 'name')->disk('products'),
                ]),

        ];
    }

    /**
     * @param  Product  $item
     * @return array<string, string[]|string>
     *
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [
            'code' => 'required|string|max:14|unique:products,code'.
                moonshineRequest()->isMethod('post') ? '' : $item->id,
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:1000',
            'price' => 'required|numeric|min:0|max:99999999.99',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ];
    }
}
