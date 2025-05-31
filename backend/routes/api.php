<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\AccessMiddleware;
use App\Http\Controllers\TokenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserExportController;
use App\Http\Controllers\UserImportController;

// Authentication
Route::post('/login', [AuthController::class, 'login']);

// Get authenticated user info
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json([
        'message' => 'Authenticated User',
        'user' => $request->user()
    ]);
});

// Profile Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/profile', [UserController::class, 'user']);
    Route::post('/user/profile/update', [UserController::class, 'update']); // Changed from PUT to POST
});

// User CRUD Routes (admin only)
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('users')->group(function () {
    Route::get('/index', [UserController::class, 'index']);
    Route::post('/store/user', [UserController::class, 'store']);
    Route::post('/find', [UserController::class, 'findUser']);
    Route::post('/update', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
});

// Dashboard (user role)
Route::middleware(['auth:sanctum', 'role:user'])->get('/userdashboard', [DashboardController::class, 'index']);

// Public user registration
Route::post('/register', [UserController::class, 'register']);


// Employee Routes
Route::prefix('employees')->group(function () {
    Route::get('/', [EmployeeController::class, 'index']);
    Route::get('/{id}', [EmployeeController::class, 'show']);
    Route::post('/', [EmployeeController::class, 'create']);
    Route::put('/{id}', [EmployeeController::class, 'update']);
    Route::delete('/{id}', [EmployeeController::class, 'delete']);
});

// Student Routes
Route::prefix('students')->group(function () {
    Route::get('/', [StudentController::class, 'index']);
    Route::post('/', [StudentController::class, 'create']);
    Route::get('/{id}', [StudentController::class, 'show']);
    Route::post('/{id}', [StudentController::class, 'update']);
    Route::delete('/{id}', [StudentController::class, 'delete']);
});


//menu routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/index/menu', [MenuItemController::class, 'index']);             // Load all
    Route::get('/update/{id}', [MenuItemController::class, 'show']);        // Fetch single
    Route::post('/menu', [MenuItemController::class, 'store']);             // Create
    Route::post('/menu/{id}', [MenuItemController::class, 'update']);       // Update via POST + X-HTTP-Method-Override
    Route::delete('/menu/{id}', [MenuItemController::class, 'destroy']);    // Delete
});



//order routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::put('/orders/{order}', [OrderController::class, 'update']);
    Route::delete('/orders/{order}', [OrderController::class, 'destroy']);
});

// Custom order grouping for delivery display
Route::get('/delivery-grouped', [OrderController::class, 'deliveriesGroupedByUser']);

Route::middleware('auth:sanctum')->get('/user/orders', [OrderController::class, 'userOrders']);

Route::middleware('auth:sanctum')->put('/orders/{id}/status', [OrderController::class, 'updateStatus']);

Route::get('/deliveries', [OrderController::class, 'deliveries']);

Route::get('/users/export', [UserExportController::class, 'export']);
Route::post('/users/import', [UserImportController::class, 'import']);


// Token-based access control
Route::middleware('access')->group(function(){
    Route::get('/test', [TokenController::class, 'index']);
});
