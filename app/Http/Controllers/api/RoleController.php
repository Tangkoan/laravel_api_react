<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Http\Requests\StoreRoleRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * បង្ហាញទិន្នន័យទាំងអស់ (យកតែ status = 1)
     */
    public function index(): JsonResponse
    {
        $data = Role::where('status', 1)->orderBy('id', 'desc')->get();
        return response()->json([
            'status' => 'success',
            'data'   => $data
        ]);
    }

    /**
     * បង្កើតទិន្នន័យថ្មី
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        // រៀបចំទិន្នន័យ រួមទាំងកំណត់ Default status បើគ្មានការបញ្ចូល
        $input = $request->all();
        if (!$request->has('status')) {
            $input['status'] = 1;
        }

        $role = Role::create($input);

        return response()->json([
            'status' => 'success',
            'message' => 'Role created successfully',
            'data'   => $role
        ], 201);
    }

    /**
     * បង្ហាញទិន្នន័យមួយតាម ID
     */
    public function show($id): JsonResponse
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['status' => 'error', 'message' => 'Not Found'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $role]);
    }

    /**
     * កែសម្រួលទិន្នន័យ
     */
    public function update(StoreRoleRequest $request, $id): JsonResponse
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['status' => 'error', 'message' => 'Not Found'], 404);
        }

        $role->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Role updated successfully',
            'data'   => $role
        ]);
    }

    /**
     * លុបទិន្នន័យ
     */
    public function destroy($id): JsonResponse
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['status' => 'error', 'message' => 'Not Found'], 404);
        }

        $role->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Role deleted successfully'
        ]);
    }
}