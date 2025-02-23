<?php
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\StudentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/employees', [EmployeeController::class, 'index']);
Route::get('/students', [StudentController::class, 'index']);

Route::get('/employees/search', [EmployeeController::class, 'search']);
Route::get('/students/search', [StudentController::class,'search']);

Route::post('/students', [StudentController::class, 'create']);
Route::post('/employees', [EmployeeController::class, 'create']);

Route::get('/students/{id}', [StudentController::class, 'show']);
Route::get('/employees/{id}', [EmployeeController::class, 'show']);

Route::put('/employees/{id}', [EmployeeController::class, 'update']);
Route::put('/students/{id}', [StudentController::class, 'update']);

Route::delete('/employees/{id}', [EmployeeController::class, 'delete']);
Route::delete('/students/{id}', [StudentController::class, 'delete']);