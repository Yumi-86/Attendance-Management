<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\RequestController as AdminRequestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'create'])->name('attendance.create');

    Route::get('/attendance/list', [AttendanceController::class, 'index'])->name('attendance.index');

    Route::get('/attendance/detail/{id}', [AttendanceController::class, 'show'])->name('attendance.show');

    Route::get('/stamp_correction_request/list', [RequestController::class, 'index'])->name('request.index');
});

Route::prefix('admin')->name('admin.')->group(function () {

    // Route::get('/login', function () {
    //     return view('admin.auth.login');
    // })->name('login');

    Route::middleware(['auth', 'admin'])->group(function () {

        Route::get('/attendances', [AdminAttendanceController::class, 'index'])->name('attendances.index');

        Route::get('/attendances/{id}', [AdminAttendanceController::class, 'show'])->name('attendances.show');

        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');

        Route::get('/users/{user}/attendances', [AdminAttendanceController::class, 'userIndex'])->name('users.attendances');

        Route::get('/requests', [AdminRequestController::class, 'index'])->name('requests.index');

        Route::get('/requests/{id}', [AdminRequestController::class, 'show'])->name('requests.show');
    });
});