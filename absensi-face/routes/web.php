<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\IzinController;
use App\Http\Controllers\FaceController;
use App\Http\Controllers\Admin\AdminUserController;

/*
|--------------------------------------------------------------------------
| ROOT REDIRECT (Role-Based)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| ROUTE WAJIB LOGIN (KARYAWAN)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // DASHBOARD KARYAWAN
    Route::get('/dashboard', [AttendanceController::class, 'dashboard'])
        ->name('dashboard');

    // ABSENSI (Legacy — redirects to scan)
    Route::post('/absen-masuk', [AttendanceController::class, 'absenMasuk'])
        ->name('absen.masuk');

    Route::post('/absen-keluar', [AttendanceController::class, 'absenKeluar'])
        ->name('absen.keluar');

    // PROFILE
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    Route::get('/profile/password', [PasswordController::class, 'edit'])
        ->name('password.edit');

    Route::patch('/profile/password', [PasswordController::class, 'update'])
        ->name('password.update');

    // PENGAJUAN KETIDAKHADIRAN
    Route::post('/izin', [IzinController::class, 'store'])
        ->name('izin.store');

    // FACE REGISTRATION
    Route::get('/face/register', [FaceController::class, 'index'])
        ->name('face.register');

    Route::post('/face/register', [FaceController::class, 'store'])
        ->name('face.register.store');

    // ATTENDANCE SCAN (Face Recognition)
    Route::get('/attendance/scan', [AttendanceController::class, 'scan'])
        ->name('attendance.scan');

    Route::post('/attendance/store', [AttendanceController::class, 'store'])
        ->name('attendance.store');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (Role Guard)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // Admin Dashboard
    Route::get('/dashboard', function () {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
        return view('admin.dashboard');
    })->name('dashboard');

    // Admin User Management (CRUD)
    Route::resource('users', AdminUserController::class)
        ->only(['index', 'create', 'store', 'destroy']);
});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES (LOGIN, LOGOUT — Registration disabled)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
