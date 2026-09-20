<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SessionIndexController extends Controller
{
    public function __invoke(Request $request): View
    {
        $activeYear = AcademicYear::query()->where('is_active', true)->first();
        $today = now()->dayOfWeekIso;
        $date = now()->toDateString();

        $schedules = Schedule::query()
            ->with([
                'subject',
                'schoolClass',
                'academicYear',
                'attendanceSessions' => fn ($query) => $query
                    ->whereDate('date', $date)
                    ->withCount([
                        'attendances as hadir_count' => fn ($query) => $query->where('status', 'hadir'),
                        'attendances as alpha_count' => fn ($query) => $query->where('status', 'alpha'),
                        'attendances',
                    ]),
            ])
            ->where('user_id', $request->user()->id)
            ->whereNull('archived_at')
            ->when($activeYear, fn ($query) => $query->where('academic_year_id', $activeYear->id))
            ->where('day_of_week', $today)
            ->orderBy('start_time')
            ->get();

        return view('guru.sessions.index', [
            'activeYear' => $activeYear,
            'date' => now(),
            'schedules' => $schedules,
        ]);
    }
}
