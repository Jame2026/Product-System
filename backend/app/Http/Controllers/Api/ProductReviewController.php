<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    public function index(Product $product)
    {
        $reviews = $product->reviews()
            ->with('user:id,name')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reviews,
        ]);
    }

    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $review = ProductReview::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'product_id' => $product->id,
            ],
            $validated,
        );

        return response()->json([
            'success' => true,
            'message' => 'Review saved successfully.',
            'data' => $review->load('user:id,name'),
        ], 201);
    }
}
