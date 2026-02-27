<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * ១. បង្ហាញបញ្ជីដែលសកម្ម (Status = 1)
     */
    public function index(): JsonResponse
    {
        $categories = Category::where('status', 1)->orderBy('id', 'desc')->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $categories
        ], 200);
    }

    /**
     * ២. បង្កើតថ្មី (Validation ក្នុងនេះ)
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:categories,name',
            'parent_id' => 'nullable|integer',
        ], [
            'name.required' => 'សូមបញ្ចូលឈ្មោះប្រភេទ!',
            'name.unique' => 'ឈ្មោះនេះមានរួចហើយ!',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'parent_id' => $request->parent_id,
            'status' => $request->status ?? 1,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Created successfully',
            'data' => $category
        ], 201);
    }

    /**
     * ៣. បង្ហាញទិន្នន័យមួយ
     */
    public function show($id): JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['status' => 'error', 'message' => 'Not Found'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $category], 200);
    }

    /**
     * ៤. កែសម្រួលទិន្នន័យ (Update)
     */
    public function update(Request $request, $id): JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['status' => 'error', 'message' => 'Not Found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:categories,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        $category->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Updated successfully',
            'data' => $category
        ], 200);
    }

    /**
     * ៥. ប្ដូរតែ Status មួយមុខ (មុខងារបន្ថែម)
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['status' => 'error', 'message' => 'Not Found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:0,1', // ទទួលយកតែលេខ 0 ឬ 1
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Status must be 1 or 0'], 422);
        }

        $category->update(['status' => $request->status]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status updated successfully',
            'data' => $category
        ], 200);
    }

    /**
     * ៦. លុបទិន្នន័យ
     */
    public function destroy($id): JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['status' => 'error', 'message' => 'Not Found'], 404);
        }

        $category->delete();

        return response()->json([
            'status' => 'success',
            'data'=> $category,
            'message' => 'Deleted successfully'
        ], 200);
    }
}