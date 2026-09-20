<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Schedule;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $activeYear = AcademicYear::query()->where('is_active', true)->first();
        $search = trim((string) $request->input('search', $request->input('q', '')));
        $scope = $request->input('scope') === 'all' ? 'all' : 'mine';
        $viewMode = $request->input('view') === 'list' ? 'list' : 'map';
        $selectedDay = max(1, min(6, $request->integer('day', now()->dayOfWeekIso)));
        $days = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];

        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'scope' => ['nullable', 'in:mine,all'],
            'view' => ['nullable', 'in:map,list'],
            'day' => ['nullable', 'integer', 'between:1,6'],
        ]);

        $scheduleQuery = Schedule::query()
            ->with(['academicYear', 'subject', 'schoolClass', 'teacher'])
            ->when($scope === 'mine', fn ($query) => $query->where('user_id', $request->user()->id))
            ->whereNull('archived_at')
            ->when($activeYear, fn ($query) => $query->where('academic_year_id', $activeYear->id))
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $query->whereHas('schoolClass', fn ($class) => $class->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('subject', fn ($subject) => $subject->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('teacher', fn ($teacher) => $teacher->where('name', 'like', "%{$search}%"));
            }));
        $gridSchedules = (clone $scheduleQuery)
            ->orderBy('day_of_week')
            ->orderBy('class_id')
            ->orderBy('start_time')
            ->get();
        $gridClasses = SchoolClass::query()
            ->with('academicYear')
            ->when($activeYear, fn ($query) => $query->where('academic_year_id', $activeYear->id))
            ->whereIn('id', $gridSchedules->pluck('class_id')->unique())
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        return view('guru.schedules.index', [
            'activeYear' => $activeYear,
            'days' => $days,
            'selectedDay' => $selectedDay,
            'today' => now()->dayOfWeekIso,
            'scope' => $scope,
            'viewMode' => $viewMode,
            'gridClasses' => $gridClasses,
            'gridSchedules' => $gridSchedules,
            'grid' => $this->buildGrid($gridSchedules),
            'scheduleRows' => $this->scheduleRows(),
            'schedules' => (clone $scheduleQuery)
                ->orderBy('day_of_week')
                ->orderBy('class_id')
                ->orderBy('start_time')
                ->paginate(15)
                ->withQueryString(),
            'search' => $search,
        ]);
    }

    private function scheduleRows(): array
    {
        return [
            ['type' => 'jp', 'jp' => 1, 'start' => '06:30', 'end' => '07:15'],
            ['type' => 'jp', 'jp' => 2, 'start' => '07:15', 'end' => '08:00'],
            ['type' => 'jp', 'jp' => 3, 'start' => '08:00', 'end' => '08:45'],
            ['type' => 'jp', 'jp' => 4, 'start' => '08:45', 'end' => '09:30'],
            ['type' => 'break', 'label' => 'Istirahat 1', 'start' => '09:30', 'end' => '09:40'],
            ['type' => 'jp', 'jp' => 5, 'start' => '09:40', 'end' => '10:25'],
            ['type' => 'jp', 'jp' => 6, 'start' => '10:25', 'end' => '11:10'],
            ['type' => 'jp', 'jp' => 7, 'start' => '11:10', 'end' => '11:55'],
            ['type' => 'break', 'label' => 'Istirahat 2', 'start' => '11:55', 'end' => '12:45'],
            ['type' => 'jp', 'jp' => 8, 'start' => '12:45', 'end' => '13:30'],
            ['type' => 'jp', 'jp' => 9, 'start' => '13:30', 'end' => '14:15'],
            ['type' => 'jp', 'jp' => 10, 'start' => '14:15', 'end' => '15:00'],
        ];
    }

    private function buildGrid($schedules): array
    {
        $slots = collect($this->scheduleRows())->where('type', 'jp')->values();
        $groups = [[1, 4], [5, 7], [8, 10]];
        $grid = [];

        foreach ($schedules as $schedule) {
            $start = substr($schedule->start_time, 0, 5);
            $end = substr($schedule->end_time, 0, 5);
            $overlappingSlots = $slots->filter(fn (array $slot): bool => $slot['end'] > $start && $slot['start'] < $end);
            if ($overlappingSlots->isEmpty()) continue;
            $startSlot = $overlappingSlots->first();
            $endSlot = $overlappingSlots->last();

            foreach ($groups as [$groupStart, $groupEnd]) {
                $segmentStart = max($startSlot['jp'], $groupStart);
                $segmentEnd = min($endSlot['jp'], $groupEnd);
                if ($segmentStart > $segmentEnd) continue;

                $grid[$schedule->day_of_week][$schedule->class_id][$segmentStart] = [
                    'schedule' => $schedule,
                    'span' => $segmentEnd - $segmentStart + 1,
                ];
                for ($jp = $segmentStart + 1; $jp <= $segmentEnd; $jp++) {
                    $grid[$schedule->day_of_week][$schedule->class_id][$jp] = ['continuation' => true];
                }
            }
        }

        return $grid;
    }
}
