<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function() {
    Route::apiResource('courses', CourseController::class);
    Route::get('teacher-courses', [CourseController::class, 'authorCourses']);

    Route::post('sign-up', [UserController::class, 'register']);
    Route::post('sign-in', [UserController::class, 'login']);
    Route::post('user-find', [UserController::class, 'getUserByToken']);
    Route::post('set-roles', [UserController::class, 'setRole']);
});
