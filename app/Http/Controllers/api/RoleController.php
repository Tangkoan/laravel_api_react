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
    // public function index(Request $request): JsonResponse
    // {
    //     // $data = Role::where('status', 1)->orderBy('id', 'desc')->get();
    //     // $data = Role::orderBy('id', 'desc')->get();
    //     $data = Role::query();

    //     // កូដដែលត្រូវ search
    //         if($request->has("text_search")){
    //             // $data->where("name","=", $request->input("text_search"));
    //             $data->where("name", "LIKE", "%" . $request->input("text_search") . "%");
    //         }
    //         $list = $data->get();
    //     // End

    //     return response()->json([
    //         'status' => 'success',
    //         'data'   => $list
    //     ]);
    // }

   public function index(Request $request): JsonResponse
{
    $data = Role::querys();

    // ១. ប្រើ LIKE ដើម្បី Search រកពាក្យខ្លះៗ (ឧទាហរណ៍៖ វាយ "a" ឃើញទាំង "Admin" និង "Agent")
    if ($request->filled("text_search")) {
        $searchText = $request->input("text_search");
        $data->where(function($q) use ($searchText) {
            $q->where("name", "LIKE", "%" . $searchText . "%")
              ->orWhere("id", "LIKE", "%" . $searchText . "%"); // បើចង់ Search តាម ID ដែរ
        });
    }

    // ២. ឆែក Status (ត្រូវប្រាកដថាផ្ញើមកពី Frontend ត្រឹមត្រូវ)
    if ($request->filled("status")) {
        $data->where("status", $request->input("status"));
    }

    $list = $data->orderBy('id', 'desc')->get();

    return response()->json([
        'status' => 'success',
        'data'   => $list
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