<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Stock;

class ProductController extends Controller
{
    //add product
    public function addProduct(Request $request)
    {
        // $table->id();
        //     $table->string('name');
        //     $table->foreignId('category_id')->constrained('categories');
        //     //business_id is a foreign key that references the id column on the businesses table
        //     $table->foreignId('business_id')->constrained('businesses');
        //     $table->string('description');
        //     $table->string('image')->nullable();
        //     //color is a string that can be null
        //     $table->string('color')->nullable();
        //     $table->decimal('price', 8, 2);
        //     $table->decimal('cost', 8, 2);
        //     $table->integer('stock');
        //     $table->string('barcode');
        //     $table->string('sku');
        //     $table->timestamps();

        $request->validate([
            'name' => 'required|string',
            'category_id' => 'required|integer',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'cost' => 'required|numeric',
            'stock' => 'required|integer',
            'barcode' => 'required|string',
            'business_id' => 'required|integer',
        ]);
        //random time
        $sku = time();
        $product = Product::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'business_id' => $request->business_id,
            'description' => $request->description,

            'color' => $request->color,
            'price' => $request->price,
            'cost' => $request->cost,
            'stock' => $request->stock,
            'barcode' => $request->barcode,
            'sku' => $sku,
        ]);

        //if image is sent
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/products'), $imageName);

            $product->image = $imageName;
            $product->save();
        }


        $stock = Stock::create([
            'product_id' => $product->id,
            'quantity' => $request->stock,
            'outlet_id' => $request->outlet_id,
        ]);

        return response()->json([
            'message' => 'Product added successfully',
            'data' => $product,
        ], 201);
    }

    //update product
    public function updateProduct(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'category_id' => 'required|integer',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'cost' => 'required|numeric',
            'stock' => 'required|integer',
            'barcode' => 'required|string',
        ]);

        $product = Product::find($id);
        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->description = $request->description;
        $product->color = $request->color;
        $product->price = $request->price;
        $product->cost = $request->cost;
        $product->stock = $request->stock;
        $product->barcode = $request->barcode;
        $product->sku = $request->sku;
        $product->save();

        //if image is sent
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/products'), $imageName);

            $product->image = $imageName;
            $product->save();
        }

        return response()->json([
            'message' => 'Product updated successfully',
            'data' => $product,
        ]);
    }

    //get products for business
    public function getProducts(Request $request)
    {
        $products = Product::where('business_id', $request->user()->business_id)->orderBy('id', 'desc')->get();

        $products->load('category');

        return response()->json([
            'data' => $products,
        ]);
    }

    //get product by id
    public function getProduct($id)
    {
        $product = Product::find($id);

        $product->load('category');

        return response()->json([
            'data' => $product,
        ]);
    }

    //delete product
    public function deleteProduct($id)
    {
        $product = Product::find($id);
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully',
        ]);
    }
}
