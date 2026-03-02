<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\api\RoleController;
use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Route នេះគឺ http://127.0.0.1:8000/api/role
Route::controller(RoleController::class)->group(function () {
    Route::get('role', 'index');
    Route::post('role', 'store');
    Route::get('role/{id}', 'show');
    Route::post('role/{id}', 'update');
    Route::delete('role/{id}', 'destroy');
});

// Route របស់ Category សម្រាប់ CRUD
Route::controller(CategoryController::class)->group(function () {
    Route::get('categories', 'index');
    Route::post('categories', 'store');
    Route::get('categories/{id}', 'show');
    Route::post('categories/{id}', 'update');
    Route::delete('categories/{id}', 'destroy');
    // Route សម្រាប់ប្តូរ Status
    Route::put('categories/{id}/status', 'updateStatus'); 
});


// Auth
Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
});

// គេអាចធ្វើការសរសេរតែមួយ Line គឺស្គាល់ 5 Route ខាងលើ ប៉ុន្ដែលុះត្រាតែ Function Name ត្រូវដូចប្រាំខាងលើទើបប្រើកូដមួយបន្ទាត់ខាងក្រោមដើរ