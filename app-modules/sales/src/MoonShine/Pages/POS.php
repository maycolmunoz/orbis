<?php

declare(strict_types=1);

namespace Modules\Sales\MoonShine\Pages;

use Modules\Inventories\Models\Product;
use Modules\Inventories\MoonShine\Resources\ProductResource;
use Modules\Sales\Models\Sale;
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

#[Icon('s.arrow-down-on-square')]
class POS extends Page
{
    protected ?string $alias = 'pos';

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

    private function getProducts(): array
    {
        return session('products', []);
    }

    private function putProducts(array $products): void
    {
        session()->put('products', $products);
    }

    public function total()
    {
        return array_reduce($this->getProducts(), fn ($total, $p) => $total + ($p['price'] * $p['quantity']), 0);
    }

    private function events(): array
    {
        return [
            AlpineJs::event(JsEvent::TABLE_UPDATED, 'table_sale'),
            AlpineJs::event(JsEvent::FRAGMENT_UPDATED, 'alert'),
        ];
    }

    private function form()
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
                            ->default(1)->min(1)->buttons(),
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

    public function addProduct(MoonShineRequest $r)
    {
        $r->validate(['quantity' => 'required|int|min:1']);

        if (empty($r->code) && empty($r->productId)) {
            return MoonShineJsonResponse::make()
                ->toast(__('sales::ui.toast.enter-product'), ToastType::ERROR);
        }

        $product = Product::when($r->productId, fn ($q) => $q->where('id', $r->productId))
            ->when($r->code, fn ($q) => $q->whereCode($r->code))->first();

        if (empty($product)) {
            return MoonShineJsonResponse::make()
                ->toast(__('sales::ui.toast.product-not-found'), ToastType::ERROR);
        }

        if ($product->stock < $r->quantity) {
            return MoonShineJsonResponse::make()
                ->toast(__('sales::ui.toast.insufficient-stock'), ToastType::ERROR);
        }

        $products = $this->getProducts();

        if (isset($products[$product->id])) {
            $products[$product->id]['quantity'] += $r->quantity;
        } else {
            $products[$product->id] = [
                'id' => $product->id,
                'code' => $product->code,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $r->quantity,
            ];
        }
        $products[$product->id]['total'] = $products[$product->id]['quantity'] * $products[$product->id]['price'];

        $this->putProducts($products);

        return MoonShineJsonResponse::make()
            ->events([
                ...$this->events(),
                AlpineJs::event(JsEvent::FORM_RESET, 'form_sale'),
            ])
            ->toast(__('sales::ui.toast.added-product'), ToastType::INFO);
    }

    private function table()
    {
        return TableBuilder::make()
            ->name('table_sale')
            ->items(array_values($this->getProducts()))
            ->fields([
                Text::make('code')->translatable('sales::ui.label'),
                Text::make('name')->translatable('sales::ui.label'),
                Text::make('price')->translatable('sales::ui.label'),
                Text::make('quantity')->translatable('sales::ui.label'),
                Text::make('total')->translatable('sales::ui.label'),
            ])
            ->async()
            ->buttons([
                ActionButton::make('')
                    ->icon('s.x-mark')
                    ->error()
                    ->method('removeProduct', fn ($item) => $item),
            ]);
    }

    public function removeProduct(MoonShineRequest $r)
    {
        $products = $this->getProducts();

        unset($products[$r->id]);

        $this->putProducts($products);

        return MoonShineJsonResponse::make()
            ->events($this->events())
            ->toast(__('sales::ui.toast.removed-product'), ToastType::INFO);
    }

    public function cancelSale()
    {
        $this->putProducts([]);

        return MoonShineJsonResponse::make()
            ->events($this->events())
            ->toast(__('sales::ui.toast.cancelled-sale'), ToastType::INFO);
    }

    public function finishSale()
    {
        $products = $this->getProducts();

        if (empty($products)) {
            return MoonShineJsonResponse::make()
                ->toast(__('sales::ui.toast.empty-sale'), ToastType::ERROR);
        }

        $sale = Sale::create([
            'total_amount' => $this->total(),
        ]);

        foreach ($products as $product) {
            $sale->products()->attach($product['id'], ['quantity' => $product['quantity']]);
        }

        $this->putProducts([]);

        return MoonShineJsonResponse::make()
            ->events($this->events())
            ->toast(__('sales::ui.toast.completed-sale'), ToastType::SUCCESS);
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

            Divider::make(__('sales::ui.label.products')),

            Fragment::make([
                Alert::make('s.currency-dollar', 'primary')
                    ->content(fn () => $this->total()),
            ])->name('alert'),

            $this->table(),
        ];
    }
}
