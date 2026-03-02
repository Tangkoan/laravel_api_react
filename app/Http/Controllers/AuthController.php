<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\User;


class AuthController extends Controller
{
    public function register(Request $request){
        $request->validate([
            'name'=> 'required|string',
            'email'=> 'required|string|email|unique:users,email',
            'password'=> 'required|string|min:6|confirmed',​// យើងសរសេរពាក្យ confirmed គឺបានន័យថា ពេល Register ត្រូវមាន value password_confirmation
        ]);

        $user = User::create([
            'name' => $request->name,
            'email'=> $request->email,
            'password'=> Hash::make($request->password)
        ]);

        return response()->json([
            'message'=> "User Register Is successfullly!!!",
            'user'=> $user,
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

        return response()->json([
            'message'=> "Login successfully",
            'user'=> JWTAuth::user(),
            'access_token'=> $token
        ], 201);

    }
}
