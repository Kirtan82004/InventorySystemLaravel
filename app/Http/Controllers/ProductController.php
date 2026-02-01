<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // GET PRODUCTS (search + filter)
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->search) {
            $query->where('name', 'ILIKE', '%' . $request->search . '%');
        }

        return response()->json([
            'data' => $query->paginate(10)
        ]);
    }

    public function all()
{
    return response()->json(
        Product::with('category')->get()
    );
}

    // CREATE PRODUCT
    public function store(Request $request)
{
    $request->validate([
        'name' => 'required',
        'category_name' => 'required|string', // user sends category name
        'price' => 'required|numeric',
        'quantity' => 'required|numeric|min:0',
        'status' => 'required|boolean'
    ]);

    // Find category by name
    $category = Category::where('name', $request->category_name)->first();
    if (!$category) {
        return response()->json([
            'message' => 'Category not found'
        ], 404);
    }

    // Create product with resolved category_id
    $product = Product::create([
        'name' => $request->name,
        'category_id' => $category->id,
        'price' => $request->price,
        'quantity' => $request->quantity,
        'status' => $request->status
    ]);

    return response()->json([
        'message' => 'Product created',
        'data' => $product
    ], 201);
}

// GET PRODUCT DETAIL
public function show($id)
{
    $product = Product::with('category')->find($id);

    if (!$product) {
        return response()->json([
            'message' => 'Product not found'
        ], 404);
    }

    return response()->json([
        'data' => $product
    ]);
}

    // UPDATE PRODUCT
    public function update(Request $request, $id)
{
    $product = Product::findOrFail($id);

    // ✅ category_name ko category_id me convert karo
    if ($request->filled('category_name')) {
        $category = Category::where('name', $request->category_name)->first();

        if (!$category) {
            return response()->json([
                'message' => 'Invalid category name'
            ], 422);
        }

        $product->category_id = $category->id;
    }

    // ✅ baaki fields update karo
    $product->update([
        'name'     => $request->name,
        'price'    => $request->price,
        'quantity' => $request->quantity,
        'status'   => $request->status,
    ]);

    return response()->json([
        'message' => 'Product updated successfully',
        'data' => $product->load('category')
    ]);
}

    // DELETE PRODUCT
    public function destroy($id)
    {
        Product::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Product deleted'
        ]);
    }

    public function stockIn(Request $request, $id){
    $request->validate([
        'quantity' => 'required|integer|min:1'
    ]);

    $product = Product::findOrFail($id);
    $product->quantity += $request->quantity;
    $product->save();

    return response()->json(['message'=>'Stock added', 'data'=>$product]);
}

public function stockOut(Request $request, $id){
    $request->validate([
        'quantity' => 'required|integer|min:1'
    ]);

    $product = Product::findOrFail($id);
    if($request->quantity > $product->quantity){
        return response()->json(['message'=>'Not enough stock'], 400);
    }

    $product->quantity -= $request->quantity;
    $product->save();

    return response()->json(['message'=>'Stock reduced', 'data'=>$product]);
}

}


