<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Schedule;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->string('q')->toString());
        $filters = fn ($query) => $query
            ->when($request->filled('academic_year_id'), fn ($query) => $query->where('academic_year_id', $request->integer('academic_year_id')))
            ->when($request->filled('class_id'), fn ($query) => $query->where('class_id', $request->integer('class_id')))
            ->when($request->filled('user_id'), fn ($query) => $query->where('user_id', $request->integer('user_id')))
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $query->whereHas('schoolClass', fn ($class) => $class->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('subject', fn ($subject) => $subject->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('teacher', fn ($teacher) => $teacher->where('name', 'like', "%{$search}%"));
            }));

        $gridSchedules = $filters(Schedule::query())
            ->with(['academicYear', 'teacher', 'subject', 'schoolClass'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $gridClasses = SchoolClass::query()
            ->with('academicYear')
            ->when($request->filled('academic_year_id'), fn ($query) => $query->where('academic_year_id', $request->integer('academic_year_id')))
            ->when($request->filled('class_id'), fn ($query) => $query->whereKey($request->integer('class_id')))
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        $grid = $this->buildGrid($gridSchedules);

        return view('admin.schedules.index', [
            'schedules' => $filters(Schedule::query())
                ->with(['academicYear', 'teacher', 'subject', 'schoolClass'])
                ->orderBy('day_of_week')
                ->orderBy('start_time')
                ->paginate(15)
                ->withQueryString(),
            'academicYears' => $this->academicYears(),
            'classes' => $this->classes(),
            'teachers' => $this->teachers(),
            'selectedAcademicYearId' => $request->input('academic_year_id'),
            'selectedClassId' => $request->input('class_id'),
            'selectedTeacherId' => $request->input('user_id'),
            'search' => $search,
            'days' => $this->days(),
            'selectedDay' => max(1, min(6, $request->integer('day', 1))),
            'gridSchedules' => $gridSchedules,
            'gridClasses' => $gridClasses,
            'grid' => $grid,
            'scheduleRows' => $this->scheduleRows(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.schedules.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        if ($this->hasConflict($data)) {
            return back()
                ->withErrors(['time' => 'Jadwal bentrok dengan kelas atau guru di jam tersebut.'])
                ->withInput();
        }

        Schedule::create($data);

        return redirect()
            ->route('admin.schedules.index')
            ->with('success', 'Jadwal berhasil dibuat.');
    }

    public function edit(Schedule $schedule): View
    {
        return view('admin.schedules.edit', [
            ...$this->formData(),
            'schedule' => $schedule,
        ]);
    }

    public function update(Request $request, Schedule $schedule): RedirectResponse
    {
        $data = $this->validatedData($request, $schedule);

        if ($this->hasConflict($data, $schedule)) {
            return back()
                ->withErrors(['time' => 'Jadwal bentrok dengan kelas atau guru di jam tersebut.'])
                ->withInput();
        }

        $schedule->update($data);

        return redirect()
            ->route('admin.schedules.index')
            ->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Schedule $schedule): RedirectResponse
    {
        if ($schedule->attendanceSessions()->exists()) {
            return back()->withErrors([
                'delete' => 'Jadwal tidak bisa dihapus karena sudah punya sesi absensi.',
            ]);
        }

        $schedule->delete();

        return redirect()
            ->route('admin.schedules.index')
            ->with('success', 'Jadwal berhasil dihapus.');
    }

    private function validatedData(Request $request, ?Schedule $schedule = null): array
    {
        $data = $request->validate([
            'academic_year_id' => ['required', 'integer', 'exists:academic_years,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'class_id' => ['required', 'integer', 'exists:classes,id'],
            'day_of_week' => ['required', 'integer', Rule::in([1, 2, 3, 4, 5, 6])],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        $teacher = User::query()->whereKey($data['user_id'])->where('role', 'guru')->where('is_active', true)->first();
        abort_unless($teacher, 422, 'Guru yang dipilih tidak aktif atau bukan akun guru.');

        abort_unless($teacher->subjects()->whereKey($data['subject_id'])->exists(), 422, 'Mata pelajaran belum di-assign ke guru tersebut.');

        abort_unless(SchoolClass::whereKey($data['class_id'])->where('academic_year_id', $data['academic_year_id'])->exists(), 422, 'Kelas tidak termasuk dalam tahun ajaran yang dipilih.');

        return $data;
    }

    private function hasConflict(array $data, ?Schedule $schedule = null): bool
    {
        return Schedule::query()
            ->where('academic_year_id', $data['academic_year_id'])
            ->where('day_of_week', $data['day_of_week'])
            ->where(function ($query) use ($data): void {
                $query->where('class_id', $data['class_id'])
                    ->orWhere('user_id', $data['user_id']);
            })
            ->where('start_time', '<', $data['end_time'])
            ->where('end_time', '>', $data['start_time'])
            ->when($schedule, fn ($query) => $query->whereKeyNot($schedule->id))
            ->exists();
    }

    private function formData(): array
    {
        return [
            'academicYears' => $this->academicYears(),
            'classes' => $this->classes(),
            'subjects' => Subject::query()->orderBy('name')->get(),
            'teachers' => $this->teachers(),
            'days' => $this->days(),
        ];
    }

    private function academicYears()
    {
        return AcademicYear::query()
            ->orderByDesc('is_active')
            ->orderByDesc('start_date')
            ->get();
    }

    private function classes()
    {
        return SchoolClass::query()
            ->with('academicYear')
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();
    }

    private function teachers()
    {
        return User::query()
            ->where('role', 'guru')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    private function days(): array
    {
        return [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];
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

            if ($overlappingSlots->isEmpty()) {
                continue;
            }

            $startSlot = $overlappingSlots->first();
            $endSlot = $overlappingSlots->last();

            foreach ($groups as [$groupStart, $groupEnd]) {
                $segmentStart = max($startSlot['jp'], $groupStart);
                $segmentEnd = min($endSlot['jp'], $groupEnd);

                if ($segmentStart > $segmentEnd) {
                    continue;
                }

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
