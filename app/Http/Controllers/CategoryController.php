<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // GET ALL CATEGORIES
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => Category::all()
        ]);
    }

    // CREATE CATEGORY
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'required|boolean'
        ]);

        $category = Category::create($request->all());

        return response()->json([
            'message' => 'Category created',
            'data' => $category
        ], 201);
    }
    // GET SINGLE CATEGORY
    public function show($id)
    {
        $category = Category::findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $category
        ]);
    }

    // UPDATE CATEGORY
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $category->update($request->all());

        return response()->json([
            'message' => 'Category updated',
            'data' => $category
        ]);
    }

    // DELETE CATEGORY
    public function destroy($id)
    {
        Category::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Category deleted'
        ]);
    }
}
