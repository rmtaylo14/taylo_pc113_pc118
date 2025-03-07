<?php
use App\Http\Controllers\DashboardController; ////////
use App\Http\Controllers\UserController; ///////
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\StudentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json([
        'message' => 'Authenticated User',
        'user' => $request->user()
    ]);
});

Route::post('/login', [UserController::class, 'login']); 


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

//Admin
Route::middleware(['auth:sanctum','role:admin'])->group(function(){
    Route::get('/user', [UserController::class, 'index']);
});
//User
Route::middleware(['auth:sanctum','role:user'])->group(function(){
    Route::get('/userdashboard', [DashboardController::class, 'index']);
});


