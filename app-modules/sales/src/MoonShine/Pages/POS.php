<?php

declare(strict_types=1);

namespace Modules\Sales\MoonShine\Pages;

use Modules\Inventories\MoonShine\Resources\ProductResource;
use Modules\Sales\Models\Sale;
use Modules\Sales\Services\CartService;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\Components\Fragment;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Http\Responses\MoonShineJsonResponse;
use MoonShine\Laravel\MoonShineRequest;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\Support\Enums\ToastType;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Alert;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Divider;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Text;

#[Icon('arrow-down-on-square')]
class POS extends Page
{
    protected ?string $alias = 'pos';

    public function __construct(protected CartService $cartService) {}

    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            '#' => $this->getTitle(),
        ];
    }

    protected function prepareBeforeRender(): void
    {
        parent::prepareBeforeRender();
        $user = moonshineRequest()->user();

        if (! $user->can('SaleResource.create')) {
            abort(403);
        }
    }

    public function getTitle(): string
    {
        return $this->title ?: 'POS';
    }

    private function events(): array
    {
        return [
            AlpineJs::event(JsEvent::TABLE_UPDATED, 'table_sale'),
            AlpineJs::event(JsEvent::FRAGMENT_UPDATED, 'alert'),
        ];
    }

    /**
     * toast
     *
     * @param  mixed  $message
     * @param  mixed  $type
     * @param  mixed  $events
     */
    private function toast(string $message, ToastType $type, ?array $events = []): MoonShineJsonResponse
    {
        return MoonShineJsonResponse::make()
            ->toast($message, $type)
            ->events([
                ...$this->events(),
                ...$events,
            ]);
    }

    /**
     * form
     */
    private function form(): FormBuilder
    {
        return FormBuilder::make()
            ->asyncMethod('addProduct')
            ->submit(__('sales::ui.label.add-product'))
            ->name('form_sale')
            ->fields([
                Grid::make([
                    Column::make([
                        Number::make('code')
                            ->translatable('sales::ui.label')
                            ->customAttributes(['autofocus' => 'true']),

                    ], 4),
                    Column::make([
                        Number::make('quantity')
                            ->translatable('sales::ui.label')
                            ->default(1)->min(1)->buttons()
                            ->required(),
                    ], 4),
                    Column::make([
                        BelongsTo::make('product', resource: ProductResource::class)
                            ->translatable('sales::ui.label')
                            ->customAttributes(['name' => 'productId'])
                            ->asyncSearch('name'),
                    ], 4),
                ]),
            ]);
    }

    /**
     * addProduct
     *
     * @param  mixed  $r
     */
    public function addProduct(MoonShineRequest $r): MoonShineJsonResponse
    {
        $data = $r->validate([
            'quantity' => ['required', 'int', 'min:1'],
            'code' => ['nullable', 'string'],
            'productId' => ['nullable', 'int'],
        ]);

        try {
            $this->cartService->addProduct($data);
        } catch (\DomainException $e) {
            return $this->toast($e->getMessage(), ToastType::ERROR);
        }

        return $this->toast(__('sales::ui.toast.added-product'),
            ToastType::INFO,
            [AlpineJs::event(JsEvent::FORM_RESET, 'form_sale')]);
    }

    /**
     * table
     */
    private function table(): TableBuilder
    {
        return TableBuilder::make()
            ->name('table_sale')
            ->items(array_values($this->cartService->getProducts()))
            ->fields([
                Text::make('code')->translatable('sales::ui.label'),
                Text::make('name')->translatable('sales::ui.label'),
                Text::make('price')->translatable('sales::ui.label'),
                Text::make('quantity')->translatable('sales::ui.label'),
                Text::make('total')->translatable('sales::ui.label')
                    ->sortable()
                    ->badge(),
            ])
            ->async()
            ->buttons([
                ActionButton::make('')
                    ->icon('s.x-mark')
                    ->error()
                    ->method('removeProduct', fn ($item) => $item),
            ]);
    }

    /**
     * removeProduct
     *
     * @param  mixed  $r
     */
    public function removeProduct(MoonShineRequest $r): MoonShineJsonResponse
    {
        $this->cartService->removeProduct($r->input('id'));

        return $this->toast(__('sales::ui.toast.removed-product'), ToastType::INFO);
    }

    /**
     * cancelSale
     */
    public function cancelSale(): MoonShineJsonResponse
    {
        $this->cartService->putProducts([]);

        return $this->toast(__('sales::ui.toast.cancelled-sale'), ToastType::INFO);
    }

    /**
     * finishSale
     */
    public function finishSale(): MoonShineJsonResponse
    {
        $products = $this->cartService->getProducts();

        if (empty($products)) {
            return $this->toast(__('sales::ui.toast.empty-sale'), ToastType::ERROR);
        }

        $sale = Sale::create([
            'total_amount' => $this->cartService->total(),
        ]);

        foreach ($products as $product) {
            $sale->products()->attach($product['id'], ['quantity' => $product['quantity']]);
        }

        $this->cartService->putProducts([]);

        return $this->toast(__('sales::ui.toast.completed-sale'), ToastType::SUCCESS);
    }

    /**
     * @return list<ComponentContract>
     */
    protected function components(): iterable
    {
        return [
            Flex::make([
                ActionButton::make(__('sales::ui.label.finish-sale'))
                    ->method('finishSale')
                    ->withConfirm()
                    ->primary(),

                ActionButton::make(__('sales::ui.label.cancel-sale'))
                    ->method('cancelSale')
                    ->withConfirm()
                    ->error(),
            ])->justifyAlign('between'),

            Divider::make(),

            $this->form(),

            Divider::make(__('sales::ui.label.products'))->centered(),

            Fragment::make([
                Alert::make('s.currency-dollar', 'primary')
                    ->content(fn () => $this->cartService->total()),
            ])->name('alert'),

            $this->table(),
        ];
    }
}
