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
        return view('admin.schedules.index', [
            'schedules' => Schedule::query()
                ->with(['academicYear', 'teacher', 'subject', 'schoolClass'])
                ->when($request->filled('academic_year_id'), fn ($query) => $query->where('academic_year_id', $request->integer('academic_year_id')))
                ->when($request->filled('class_id'), fn ($query) => $query->where('class_id', $request->integer('class_id')))
                ->when($request->filled('user_id'), fn ($query) => $query->where('user_id', $request->integer('user_id')))
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
            'days' => $this->days(),
        ]);
    }

    public function create(): View
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
        return $request->validate([
            'academic_year_id' => ['required', 'integer', 'exists:academic_years,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'class_id' => ['required', 'integer', 'exists:classes,id'],
            'day_of_week' => ['required', 'integer', Rule::in([1, 2, 3, 4, 5, 6])],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);
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
}
