<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;

class CartService
{
    public function cartFor(User $user): Cart
    {
        return $user->cart()->firstOrCreate();
    }

    public function add(User $user, Product $product, int $quantity = 1): CartItem
    {
        $cart = $this->cartFor($user);

        $item = $cart->items()->firstOrNew(['product_id' => $product->id]);
        $item->quantity = (int) $item->quantity + max(1, $quantity);
        $item->save();

        return $item;
    }

    public function update(User $user, Product $product, int $quantity): CartItem
    {
        $cart = $this->cartFor($user);

        return $cart->items()->updateOrCreate(
            ['product_id' => $product->id],
            ['quantity' => max(1, $quantity)]
        );
    }

    public function remove(User $user, Product $product): void
    {
        $this->cartFor($user)->items()->where('product_id', $product->id)->delete();
    }

    public function clear(User $user): void
    {
        $this->cartFor($user)->items()->delete();
    }
}
