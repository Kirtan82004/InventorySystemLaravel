<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
   public function index()
{
    $totalProducts = Product::count();
    $totalCategories = Category::count();

    $lowStock = Product::with('category:id,name')
        ->where('quantity', '<=', 10)
        ->get()
        ->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $product->quantity,
                'status' => $product->status,
                'category_name' => $product->category?->name,
            ];
        });

    return response()->json([
        'total_products' => $totalProducts,
        'total_categories' => $totalCategories,
        'low_stock' => $lowStock
    ]);
}
}

    
