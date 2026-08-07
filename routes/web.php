<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Guru\DashboardController as GuruDashboardController;
use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\SchoolClassController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\StudentQrCodeController;
use App\Http\Controllers\Guru\AttendanceSessionController;
use App\Http\Controllers\Guru\AttendanceScanController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    return auth()->user()->role === 'admin'
        ? redirect()->route('admin.dashboard')
        : redirect()->route('guru.dashboard');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
        Route::resource('/academic-years', AcademicYearController::class)->except('show');
        Route::resource('/classes', SchoolClassController::class)
            ->except('show')
            ->parameters(['classes' => 'schoolClass']);
        Route::resource('/subjects', SubjectController::class)->except('show');
        Route::resource('/teachers', TeacherController::class)->except('show');
        Route::resource('/students', StudentController::class)->except('show');

        Route::get('/students/qr-codes', [StudentQrCodeController::class, 'index'])->name('students.qr-codes.index');
        Route::get('/students/{student}/qr-code', [StudentQrCodeController::class, 'show'])->name('students.qr-codes.show');
        Route::get('/students/{student}/qr-code/download', [StudentQrCodeController::class, 'download'])->name('students.qr-codes.download');
        Route::get('/classes/{schoolClass}/qr-codes/download', [StudentQrCodeController::class, 'downloadClass'])->name('classes.qr-codes.download');

        Route::resource('/schedules', ScheduleController::class)->except('show');

    });

Route::middleware(['auth', 'role:guru'])
    ->prefix('guru')
    ->name('guru.')
    ->group(function (): void {
        Route::get('/dashboard', GuruDashboardController::class)->name('dashboard');
        Route::post('/schedules/{schedule}/sessions', [AttendanceSessionController::class, 'store'])->name('sessions.store');
        Route::get('/sessions/{attendanceSession}', [AttendanceSessionController::class, 'show'])->name('sessions.show');
        Route::patch('/sessions/{attendanceSession}/close', [AttendanceSessionController::class, 'close'])->name('sessions.close');
        Route::post('/sessions/{attendanceSession}/scan', AttendanceScanController::class)->name('sessions.scan');
    });
