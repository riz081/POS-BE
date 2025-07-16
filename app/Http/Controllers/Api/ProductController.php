<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    //add product
    public function addProduct(Request $request)
    {

        $request->validate([
            'name' => 'required|string',
            'category_id' => 'required|integer',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'cost' => 'required|numeric',
            // 'stock' => 'required|integer',
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
            'stock' => 0,
            'barcode' => $request->barcode,
            'sku' => $sku,
        ]);

        //if image is sent
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            try {
                $image = $request->file('image');
                $filename = 'product_'.$product->id.'_'.time().'.'.$image->getClientOriginalExtension();
                
                // Upload tanpa ACL
                $s3Path = Storage::disk('s3')->putFileAs(
                    'products',
                    $image,
                    $filename
                    // Hapus parameter visibility
                );
                
                // Dapatkan URL
                $product->image = Storage::url($s3Path);
                $product->save();
                
            } catch (\Exception $e) {
                Log::error('S3 Upload Error', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'error' => 'Image upload failed',
                    'details' => $e->getMessage()
                ], 500);
            }
        }

        //outlet by business
        $outlets = Outlet::where('business_id', $request->business_id)->get();

        foreach ($outlets as $outlet) {
            Stock::create([
                'product_id' => $product->id,
                'quantity' => 0,
                'outlet_id' => $outlet->id,
            ]);
        }

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


        ]);

        $product = Product::find($id);
        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->description = $request->description;
        $product->color = $request->color;
        $product->price = $request->price;
        $product->cost = $request->cost;

        $product->barcode = $request->barcode;
        $product->save();

        return response()->json([
            'message' => 'Product updated successfully',
            'data' => $product,
        ]);
    }

    //edit product
    public function updateProductWithImage(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'category_id' => 'required|integer',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'cost' => 'required|numeric',


        ]);

        $product = Product::find($id);
        $product->name = $request->name;
        $product->category_id = $request
            ->category_id;
        $product->description = $request->description;
        $product->color = $request->color;
        $product->price = $request->price;
        $product->cost = $request->cost;

        $product->barcode = $request->barcode;
        $product->save();

        //if image is sent
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            // Simpan file di storage dan dapatkan path
            // $path = $image->store('public/products');
            $path = Storage::disk('s3')->put('products', $image, 'public');

            // Simpan path relatif ke database
            $product->image = Storage::url($path);
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

        $products->load('category', 'stocks', 'stocks.outlet', 'stocks.product');

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
