<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = Carbon::today();
        $activeAcademicYear = AcademicYear::query()->where('is_active', true)->first();
        $todayAttendances = Attendance::query()
            ->whereHas('session', fn ($query) => $query->whereDate('date', $today))
            ->get(['status']);

        $todayTotal = $todayAttendances->count();
        $todayPresent = $todayAttendances->where('status', 'hadir')->count();
        $todayAbsent = $todayAttendances->whereIn('status', ['sakit', 'izin', 'alpha'])->count();

        $classAttendance = SchoolClass::query()
            ->withCount(['students as active_students_count' => fn ($query) => $query->where('is_active', true)])
            ->with(['schedules.attendanceSessions' => fn ($query) => $query->whereDate('date', $today)->with('attendances')])
            ->when($activeAcademicYear, fn ($query) => $query->where('academic_year_id', $activeAcademicYear->id))
            ->orderBy('name')
            ->get()
            ->map(function (SchoolClass $schoolClass): array {
                $attendances = $schoolClass->schedules
                    ->flatMap(fn ($schedule) => $schedule->attendanceSessions)
                    ->flatMap(fn ($session) => $session->attendances);

                $total = $attendances->count();
                $present = $attendances->where('status', 'hadir')->count();

                return [
                    'name' => $schoolClass->name,
                    'students' => $schoolClass->active_students_count,
                    'present' => $present,
                    'total' => $total,
                    'percentage' => $total > 0 ? round(($present / $total) * 100, 1) : null,
                ];
            });

        return view('admin.dashboard', [
            'totalStudents' => Student::where('is_active', true)->count(),
            'totalTeachers' => User::where('role', 'guru')->where('is_active', true)->count(),
            'totalClasses' => SchoolClass::when($activeAcademicYear, fn ($query) => $query->where('academic_year_id', $activeAcademicYear->id))->count(),
            'totalAttendances' => Attendance::count(),
            'activeAcademicYear' => $activeAcademicYear,
            'todayTotal' => $todayTotal,
            'todayPresent' => $todayPresent,
            'todayAbsent' => $todayAbsent,
            'todayPercentage' => $todayTotal > 0 ? round(($todayPresent / $todayTotal) * 100, 1) : 0,
            'classAttendance' => $classAttendance,
        ]);
    }
}
