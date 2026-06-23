<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $items = CartItem::with('product.category')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $items,
            'total' => $items->sum(fn (CartItem $item) => (float) $item->product->price * $item->quantity),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $quantity = min($validated['quantity'] ?? 1, max($product->qty, 1));

        $item = CartItem::firstOrNew([
            'user_id' => $request->user()->id,
            'product_id' => $product->id,
        ]);

        $item->quantity = min(($item->exists ? $item->quantity : 0) + $quantity, max($product->qty, 1));
        $item->save();
        $item->load('product.category');

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart.',
            'data' => $item,
        ], 201);
    }

    public function update(Request $request, CartItem $cartItem)
    {
        abort_unless($cartItem->user_id === $request->user()->id, 404);

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cartItem->load('product');
        $cartItem->update([
            'quantity' => min($validated['quantity'], max($cartItem->product->qty, 1)),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cart item updated.',
            'data' => $cartItem->fresh('product.category'),
        ]);
    }

    public function destroy(Request $request, CartItem $cartItem)
    {
        abort_unless($cartItem->user_id === $request->user()->id, 404);

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart item removed.',
        ]);
    }
}
