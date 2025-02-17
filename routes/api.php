<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\StudentsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::apiResource('employees', EmployeeController::class);
Route::apiResource('students', StudentsController::class);
