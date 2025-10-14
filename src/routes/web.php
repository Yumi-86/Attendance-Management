<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\general\AttendanceController;
use App\Http\Controllers\general\AttendanceApplicationController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ApplicationController as AdminApplicationController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Models\Attendance;

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

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('web')->name('login');

    Route::get('/admin/login', fn() => view('admin.auth.login'))->middleware('web')->name('admin.login');
    Route::post('/admin/login', [AuthenticatedSessionController::class, 'store'])->name('admin.login.store');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'create'])->name('attendance.create');

    Route::post('/attendance/clockIn', [AttendanceController::class, 'clockIn'])->name('attendance.clockIn');
    Route::post('/attendance/startBreak', [AttendanceController::class, 'startBreak'])->name('attendance.startBreak');
    Route::post('/attendance/endBreak', [AttendanceController::class, 'endBreak'])->name('attendance.endBreak');
    Route::post('/attendance/clockOut', [AttendanceController::class, 'clockOut'])->name('attendance.clockOut');

    Route::get('/attendance/list', [AttendanceController::class, 'index'])->name('attendance.index');

    Route::get('/attendance/detail/{attendance}', [AttendanceController::class, 'show'])->name('attendance.show');

    Route::post('/request/{attendance}', [AttendanceApplicationController::class, 'store'])->name('attendance_request.store');

    Route::get('/stamp_correction_request/list', [AttendanceApplicationController::class, 'index'])->name('attendance_request.index');
});

Route::prefix('admin')->name('admin.')->group(function () {

    Route::middleware(['auth', 'admin'])->group(function () {

        Route::get('/attendances', [AdminAttendanceController::class, 'index'])->name('attendances.index');

        Route::get('/attendances/{attendance}', [AdminAttendanceController::class, 'show'])->name('attendances.show');

        Route::patch('/attendances/{attendance}', [AdminAttendanceController::class, 'update'])->name('attendances.update');

        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');

        Route::get('/users/{user}/attendances', [AdminAttendanceController::class, 'userIndex'])->name('users.attendances');

        Route::get('/requests', [AdminApplicationController::class, 'index'])->name('requests.index');

        Route::get('/requests/{id}', [AdminApplicationController::class, 'show'])->name('requests.show');
    });
});