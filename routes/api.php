<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\api\RoleController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Route នេះគឺ http://127.0.0.1:8000/api/role
Route::get("role", [RoleController::class, 'index']);
Route::post("role", [RoleController::class, 'store']);
Route::get("role/{id}", [RoleController::class, 'show']);
Route::put("role/{id}", [RoleController::class, 'update']);
Route::delete("role/{id}", [RoleController::class, 'destroy']);

// គេអាចធ្វើការសរសេរតែមួយ Line គឺស្គាល់ 5 Route ខាងលើ ប៉ុន្ដែលុះត្រាតែ Function Name ត្រូវដូចប្រាំខាងលើទើបប្រើកូដមួយបន្ទាត់ខាងក្រោមដើរ