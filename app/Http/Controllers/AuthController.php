<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\User;

use Illuminate\Support\Facades\DB;



class AuthController extends Controller
{
    public function register(Request $request){
        $request->validate([
            'name'=> 'required|string',
            'email'=> 'required|string|email|unique:users,email',
            'password'=> 'required|string|min:6|confirmed', // ​យើងសរសេរពាក្យ confirmed គឺបានន័យថា ពេល Register ត្រូវមាន value password_confirmation

            // column បួនក្រោមនេះជា Field របស់ Table profile
            'phone'=> 'nullable',
            'address'=> 'nullable',
            'type'=> 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        

        $user = User::create([
            'name' => $request->name,
            'email'=> $request->email,
            'password'=> Hash::make($request->password)
        ]);

        // Handle the image upload if exists
        $imagePath = null;
        if($request->hasFile('image')){
            $imagePath = $request->file('image')->store('profiles', 'public');
        }
        // create the profile table
        $user->profile()->create([
            'phone'=> $request->phone,
            'address'=> $request->address,
            'type'=> $request->type,
            'image'=> $imagePath,
        ]);

        return response()->json([
            'message'=> "User Register Is successfullly!!!",
            'user'=> $user->load('profile'),
            ], 201);
    }

    public function login(Request $request){

        $request->validate([
            'name'=> 'required|string',
            'password'=> 'required|string',
        ]);

        if(!$token = JWTAuth::attempt($request->only('name', 'password'))){
            return response()->json([
                'error'=> "Unauthorized"
            ], 401);
        }

        $user = JWTAuth::user()->load('profile');
        // បើមាន profile image
        if ($user->profile && $user->profile->image) {
            $user->profile->image = asset('storage/' . $user->profile->image);
        }

        // ប្រើប្រាស់ Raw SQL ដើម្បីទាញយក Permissions របស់ User នេះ
        $permission = DB::select('
            SELECT p.*
            FROM permissions p
            INNER JOIN user_permission_roles pr ON p.id = pr.permission_id
            INNER JOIN roles r ON pr.role_id = r.id
            INNER JOIN user_roles ur ON r.id = ur.role_id
            WHERE ur.user_id = ?
        ', [$user->id]);

       

        return response()->json([
            'message'=> "Login successfully",
            
            'access_token'=> $token,

            // 'user'=> JWTAuth::user(),
            'user'=> $user,
            'permission'=> $permission,
        ], 201);

    }
}
