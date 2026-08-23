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
use App\Http\Controllers\Admin\AttendanceReportController;
use App\Http\Controllers\Guru\AttendanceSessionController;
use App\Http\Controllers\Guru\AttendanceScanController;
use App\Http\Controllers\Guru\AttendanceReportController as GuruAttendanceReportController;
use App\Http\Controllers\Guru\ClassController as GuruClassController;
use App\Http\Controllers\Guru\ScheduleController as GuruScheduleController;
use App\Http\Controllers\Guru\SessionIndexController as GuruSessionIndexController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\StudentPhotoController as AdminStudentPhotoController;
use App\Http\Controllers\Guru\StudentPhotoController;
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
        Route::delete('/academic-years/bulk-destroy', [AcademicYearController::class, 'bulkDestroy'])->name('academic-years.bulk-destroy');
        Route::resource('/academic-years', AcademicYearController::class)->except('show');
        Route::delete('/classes/bulk-destroy', [SchoolClassController::class, 'bulkDestroy'])->name('classes.bulk-destroy');
        Route::resource('/classes', SchoolClassController::class)
            ->except('show')
            ->parameters(['classes' => 'schoolClass']);
        Route::delete('/subjects/bulk-destroy', [SubjectController::class, 'bulkDestroy'])->name('subjects.bulk-destroy');
        Route::resource('/subjects', SubjectController::class)->except('show');
        Route::delete('/teachers/bulk-destroy', [TeacherController::class, 'bulkDestroy'])->name('teachers.bulk-destroy');
        Route::resource('/teachers', TeacherController::class)->except('show');
        Route::patch('/teachers/{teacher}/reset-password', [TeacherController::class, 'resetPassword'])->name('teachers.reset-password');
        Route::delete('/students/bulk-destroy', [StudentController::class, 'bulkDestroy'])->name('students.bulk-destroy');
        Route::resource('/students', StudentController::class)->except('show');
        Route::get('/students/{student}/photo', [AdminStudentPhotoController::class, 'show'])->name('students.photo');
        Route::post('/students/import', [StudentController::class, 'import'])->name('students.import');
        Route::get('/imports', [ImportController::class, 'create'])->name('imports.create');
        Route::post('/imports/preview', [ImportController::class, 'preview'])->name('imports.preview');
        Route::post('/imports/commit', [ImportController::class, 'commit'])->name('imports.commit');
        Route::get('/imports/template', [ImportController::class, 'template'])->name('imports.template');
        Route::get('/students/promotion', [StudentController::class, 'promotion'])->name('students.promotion');
        Route::post('/students/promotion', [StudentController::class, 'promote'])->name('students.promote');

        Route::get('/students/qr-codes', [StudentQrCodeController::class, 'index'])->name('students.qr-codes.index');
        Route::get('/students/{student}/qr-code', [StudentQrCodeController::class, 'show'])->name('students.qr-codes.show');
        Route::get('/students/{student}/qr-code/download', [StudentQrCodeController::class, 'download'])->name('students.qr-codes.download');
        Route::get('/classes/{schoolClass}/qr-codes/download', [StudentQrCodeController::class, 'downloadClass'])->name('classes.qr-codes.download');

        Route::delete('/schedules/bulk-destroy', [ScheduleController::class, 'bulkDestroy'])->name('schedules.bulk-destroy');
        Route::resource('/schedules', ScheduleController::class)->except('show');
        Route::get('/attendances', [AttendanceReportController::class, 'index'])->name('attendances.index');
        Route::get('/attendances/export', [AttendanceReportController::class, 'export'])->name('attendances.export');
        Route::patch('/attendances/{attendance}', [AttendanceReportController::class, 'update'])->name('attendances.update');

    });

Route::middleware(['auth', 'role:guru'])
    ->prefix('guru')
    ->name('guru.')
    ->group(function (): void {
        Route::get('/dashboard', GuruDashboardController::class)->name('dashboard');
        Route::get('/sessions', GuruSessionIndexController::class)->name('sessions.index');
        Route::get('/schedules', [GuruScheduleController::class, 'index'])->name('schedules.index');
        Route::get('/classes', GuruClassController::class)->name('classes.index');
        Route::get('/attendances', [GuruAttendanceReportController::class, 'index'])->name('attendances.index');
        Route::get('/attendances/export', [GuruAttendanceReportController::class, 'export'])->name('attendances.export');
        Route::post('/schedules/{schedule}/sessions', [AttendanceSessionController::class, 'store'])->name('sessions.store');
        Route::get('/sessions/{attendanceSession}', [AttendanceSessionController::class, 'show'])->name('sessions.show');
        Route::patch('/sessions/{attendanceSession}/close', [AttendanceSessionController::class, 'close'])->name('sessions.close');
        Route::patch('/sessions/{attendanceSession}/attendances', [AttendanceSessionController::class, 'bulkUpdateAttendances'])->name('sessions.attendances.bulk-update');
        Route::patch('/sessions/{attendanceSession}/attendances/{attendance}', [AttendanceSessionController::class, 'updateAttendance'])->name('sessions.attendances.update');
        Route::post('/sessions/{attendanceSession}/scan', AttendanceScanController::class)->name('sessions.scan');
        Route::get('/sessions/{attendanceSession}/students/{student}/photo', [StudentPhotoController::class, 'show'])->name('sessions.students.photo');
    });
