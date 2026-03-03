<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    // ១. បង្ហាញ Brand ទាំងអស់
    public function index()
    {
        $brands = Brand::latest()->get()->map(function ($brand) {
            $brand->image = $brand->image
                ? asset('storage/' . $brand->image)
                : asset('storage/no-image.jpg');

            return $brand;
        });

        return response()->json([
            'status' => 'success',
            'data' => $brands
        ]);
    }

    // ២. បង្ហាញ Brand តាម ID
    public function show($id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return response()->json([
                'status' => 'error',
                'message' => 'Brand Not Found'
            ], 404);
        }

        $brand->image = $brand->image
            ? asset('storage/' . $brand->image)
            : asset('storage/no-image.png');

        return response()->json([
            'status' => 'success',
            'data' => $brand
        ]);
    }

    // ៣. បង្កើត Brand
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:brands,code',
            'from_country' => 'required|string|max:255',
            'status' => 'required|in:active,disble',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only([
            'name',
            'code',
            'from_country',
            'status'
        ]);

        // Upload image
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('brands', 'public');
        }

        $brand = Brand::create($data);

        $brand->image = $brand->image
            ? asset('storage/' . $brand->image)
            : asset('storage/no-image.png');

        return response()->json([
            'status' => 'success',
            'data' => $brand
        ], 201);
    }

    // ៤. Update Brand
    public function update(Request $request, $id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return response()->json([
                'status' => 'error',
                'message' => 'Brand Not Found'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:brands,code,' . $id,
            'from_country' => 'required|string|max:255',
            'status' => 'required|in:active,disble',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only([
            'name',
            'code',
            'from_country',
            'status'
        ]);

        // បើមាន upload រូបថ្មី
        if ($request->hasFile('image')) {

            // លុបរូបចាស់
            if ($brand->image && Storage::disk('public')->exists($brand->image)) {
                Storage::disk('public')->delete($brand->image);
            }

            // Save រូបថ្មី
            $data['image'] = $request->file('image')->store('brands', 'public');
        }

        $brand->update($data);

        $brand->image = $brand->image
            ? asset('storage/' . $brand->image)
            : asset('storage/no-image.png');

        return response()->json([
            'status' => 'success',
            'data' => $brand
        ]);
    }

    // ៥. Delete Brand
    public function destroy($id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return response()->json([
                'status' => 'error',
                'message' => 'Brand Not Found'
            ], 404);
        }

        if ($brand->image && Storage::disk('public')->exists($brand->image)) {
            Storage::disk('public')->delete($brand->image);
        }

        $brand->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Brand deleted successfully'
        ]);
    }
}