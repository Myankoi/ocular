<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $today = now()->dayOfWeekIso;
        $date = now()->toDateString();
        $activeYear = AcademicYear::query()->where('is_active', true)->first();

        $todaySchedules = Schedule::query()
                ->with([
                    'subject',
                    'schoolClass',
                    'attendanceSessions' => fn ($query) => $query->whereDate('date', $date)->with('attendances'),
                ])
            ->where('user_id', $request->user()->id)
            ->whereNull('archived_at')
                ->when($activeYear, fn ($query) => $query->where('academic_year_id', $activeYear->id))
                ->where('day_of_week', $today)
                ->orderBy('start_time')
                ->get();

        $todayAttendances = $todaySchedules
            ->flatMap(fn ($schedule) => $schedule->attendanceSessions)
            ->flatMap(fn ($session) => $session->attendances);

        return view('guru.dashboard', [
            'todaySchedules' => $todaySchedules,
            'todayTotal' => $todayAttendances->count(),
            'todayPresent' => $todayAttendances->where('status', 'hadir')->count(),
            'todayExcused' => $todayAttendances->whereIn('status', ['sakit', 'izin'])->count(),
            'todayAlpha' => $todayAttendances->where('status', 'alpha')->count(),
            'todayRate' => $todayAttendances->count() > 0
                ? round($todayAttendances->where('status', 'hadir')->count() / $todayAttendances->count() * 100, 1)
                : 0,
            'activeYear' => $activeYear,
        ]);
    }
}
