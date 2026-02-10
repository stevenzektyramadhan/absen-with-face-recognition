<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AttendanceController;

Route::post('/attendance/auto', [AttendanceController::class, 'autoAttendance']);
