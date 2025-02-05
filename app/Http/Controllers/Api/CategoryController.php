<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    //add category
    public function addCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'business_id' => 'required',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'business_id' => $request->business_id,
        ]);

        return response()->json([
            'message' => 'Category added successfully',
            'data' => $category,
        ], 201);
    }

    //get categories for business
    public function getCategories(Request $request)
    {
        $categories = Category::where('business_id', $request->user()->business_id)->get();

        return response()->json([
            'data' => $categories,
        ]);
    }
}
