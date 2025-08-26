<?php

declare(strict_types=1);

namespace Modules\Inventories\MoonShine\Resources;

use Modules\Inventories\Models\Image;
use Modules\Moonlaunch\Traits\Properties;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Enums\Action;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image as Img;

/**
 * @extends ModelResource<Image>
 */
class ImageResource extends ModelResource
{
    use Properties;

    protected string $model = Image::class;

    public function __construct()
    {
        $this->title('Images')
            ->column('name');
    }

    protected function activeActions(): ListOf
    {
        return parent::activeActions()
            ->only(Action::DELETE);
    }

    private function fields(): array
    {
        return [
            Img::make('Imagen', 'name')
                ->disk('products'),
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return $this->fields();
    }

    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function formFields(): iterable
    {
        return [
            Box::make([
                ID::make(),
            ]),
        ];
    }

    /**
     * @param  Image  $item
     * @return array<string, string[]|string>
     *
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [
            'name' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }
}
