<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StudentController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// get students model information
Route::get('students', [StudentController::class, 'index'])->name('index');
// create
Route::post('students', [StudentController::class, 'store'])->name('store');
// show id information
Route::get('students/{id}', [StudentController::class, 'show'])->name('show');
// update
Route::put('students/{id}/edit', [StudentController::class, 'update'])->name('update');
// delete
Route::delete('students/{id}/delete', [StudentController::class, 'delete'])->name('delete');