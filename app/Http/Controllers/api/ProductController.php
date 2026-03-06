<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product; // កុំភ្លេចបង្កើត Model Product
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // ១. បង្ហាញ Product ទាំងអស់
    public function index(Request $request)
    {
        // ប្រើ with ដើម្បីទាញឈ្មោះ Category និង Brand មកជាមួយ (Eager Loading)
        $query = Product::with(['category', 'brand']);

        // ២. មុខងារ Search (តាម product_name ឬ ID)
        if ($request->filled("text_search")) {
            $searchText = $request->input("text_search");
            $query->where(function($q) use ($searchText) {
                $q->where("product_name", "LIKE", "%" . $searchText . "%")
                ->orWhere("id", "LIKE", "%" . $searchText . "%");
            });
        }

        // ៣. មុខងារ Filter តាម Status
        if ($request->filled("status")) {
            $query->where("status", $request->input("status"));
        }

        $products = $query->orderBy('id', 'desc')->get();

        // ៤. Map ដើម្បីប្តូរ Path រូបភាព
        $products->map(function ($product) {
            $product->image = $product->image
                ? asset('storage/' . $product->image)
                : asset('storage/no-image.jpg'); 
            return $product;
        });

        return response()->json([
            'status' => 'success',
            'data'   => $products
        ]);
    }

    // ២. បង្ហាញ Product តាម ID
    public function show($id)
    {
        $product = Product::with(['category', 'brand'])->find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product Not Found'
            ], 404);
        }

        $product->image = $product->image
            ? asset('storage/' . $product->image)
            : asset('storage/no-image.png');

        return response()->json([
            'status' => 'success',
            'data' => $product
        ]);
    }

    // ៣. បង្កើត Product
    public function store(Request $request)
    {
        $request->validate([
            'category_id'  => 'required|exists:categories,id',
            'brand_id'     => 'required|exists:brands,id',
            'product_name' => 'required|string|max:255',
            'quantity'     => 'required|integer|min:0',
            'price'        => 'required|numeric|min:0',
            'status'       => 'required|boolean',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'description'  => 'nullable|string',
        ]);

        $data = $request->only([
            'category_id', 'brand_id', 'product_name', 
            'description', 'quantity', 'price', 'status'
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        return response()->json([
            'message' => 'Product created successfully!',
            'status'  => 'success',
            'data'    => $product
        ], 201);
    }

    // ៤. Update Product
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product Not Found'
            ], 404);
        }

        $request->validate([
            'category_id'  => 'required|exists:categories,id',
            'brand_id'     => 'required|exists:brands,id',
            'product_name' => 'required|string|max:255',
            'quantity'     => 'required|integer|min:0',
            'price'        => 'required|numeric|min:0',
            'status'       => 'required|boolean',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'description'  => 'nullable|string',
        ]);

        $data = $request->only([
            'category_id', 'brand_id', 'product_name', 
            'description', 'quantity', 'price', 'status'
        ]);

        if ($request->hasFile('image')) {
            // លុបរូបចាស់
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Product updated successfully!',
            'data'    => $product
        ]);
    }

    // ៥. Delete Product
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product Not Found'
            ], 404);
        }

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Product deleted successfully'
        ]);
    }
}