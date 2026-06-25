<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'shipping_address' => ['required', 'string', 'max:1000'],
            'items' => ['sometimes', 'array', 'min:1'],
            'items.*.product_id' => ['required_with:items', 'exists:products,id'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
        ]);

        $user = $request->user();
        $cartItems = collect();

        if ($request->filled('items')) {
            $products = Product::whereIn('id', collect($validated['items'])->pluck('product_id'))
                ->get()
                ->keyBy('id');

            $cartItems = collect($validated['items'])->map(function (array $item) use ($products) {
                $product = $products->get($item['product_id']);

                return (object) [
                    'product_id' => $product->id,
                    'product' => $product,
                    'quantity' => min($item['quantity'], max($product->qty, 1)),
                ];
            });
        } else {
            $cartItems = CartItem::with('product')
                ->where('user_id', $user->id)
                ->get();
        }

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty.',
            ], 422);
        }

        $order = DB::transaction(function () use ($cartItems, $user, $validated) {
            $total = $cartItems->sum(fn ($item) => (float) $item->product->price * $item->quantity);

            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'pending',
                'total' => $total,
                ...$validated,
            ]);

            foreach ($cartItems as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'subtotal' => (float) $item->product->price * $item->quantity,
                ]);
            }

            if (!isset($validated['items'])) {
                CartItem::where('user_id', $user->id)->delete();
            }

            return $order->load('items.product');
        });

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully.',
            'data' => $order,
        ], 201);
    }
}
