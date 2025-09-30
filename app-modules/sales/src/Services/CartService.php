<?php

namespace Modules\Sales\Services;

use Modules\Inventories\Models\Product;

class CartService
{
    public function getProducts(): array
    {
        return session('products', []);
    }

    public function putProducts(array $products): void
    {
        session()->put('products', $products);
    }

    public function incrementQuantity(string|int $id): void
    {
        $products = $this->getProducts();

        if (isset($products[$id])) {
            $products[$id]['quantity']++;
            $products[$id]['total'] = $products[$id]['quantity'] * $products[$id]['price'];
        }

        $this->putProducts($products);
    }

    public function removeProduct(string|int $id): void
    {
        $products = $this->getProducts();

        unset($products[$id]);

        $this->putProducts($products);
    }

    public function addProduct(array $data): void
    {
        if (empty($data['code']) && empty($data['productId'])) {
            throw new \DomainException(__('sales::ui.toast.enter-product'));
        }

        $product = Product::when($data['productId'] ?? null, fn ($q) => $q->where('id', $data['productId']))
            ->when($data['code'] ?? null, fn ($q) => $q->whereCode($data['code']))
            ->first();

        if (! $product) {
            throw new \DomainException(__('sales::ui.toast.product-not-found'));
        }

        if ($product->stock < $data['quantity']) {
            throw new \DomainException(__('sales::ui.toast.insufficient-stock'));
        }

        $products = $this->getProducts();

        $quantity = ($products[$product->id]['quantity'] ?? 0) + $data['quantity'];
        $products[$product->id] = [
            'id' => $product->id,
            'code' => $product->code,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => $quantity,
            'total' => $quantity * $product->price,
        ];

        $this->putProducts($products);
    }

    public function total(): float|int
    {
        return collect($this->getProducts())->sum(fn ($p) => $p['price'] * $p['quantity']);
    }
}
