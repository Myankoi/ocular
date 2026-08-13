<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AttendanceReportExport;
use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->filters($request);

        return view('admin.attendances.index', [
            'attendances' => Attendance::query()
                ->with(['student', 'session.schedule.schoolClass', 'session.schedule.subject', 'session.schedule.teacher'])
                ->when($filters['academic_year_id'] ?? null, fn ($query, $value) => $query->whereHas('session.schedule', fn ($schedule) => $schedule->where('academic_year_id', $value)))
                ->when($filters['class_id'] ?? null, fn ($query, $value) => $query->whereHas('session.schedule', fn ($schedule) => $schedule->where('class_id', $value)))
                ->when($filters['subject_id'] ?? null, fn ($query, $value) => $query->whereHas('session.schedule', fn ($schedule) => $schedule->where('subject_id', $value)))
                ->when($filters['user_id'] ?? null, fn ($query, $value) => $query->whereHas('session.schedule', fn ($schedule) => $schedule->where('user_id', $value)))
                ->when($filters['date_from'] ?? null, fn ($query, $value) => $query->whereHas('session', fn ($session) => $session->whereDate('date', '>=', $value)))
                ->when($filters['date_to'] ?? null, fn ($query, $value) => $query->whereHas('session', fn ($session) => $session->whereDate('date', '<=', $value)))
                ->latest('id')
                ->paginate(25)
                ->withQueryString(),
            'filters' => $filters,
            'academicYears' => AcademicYear::orderByDesc('start_date')->get(),
            'classes' => SchoolClass::orderBy('name')->get(),
            'subjects' => Subject::orderBy('name')->get(),
            'teachers' => User::where('role', 'guru')->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function export(Request $request)
    {
        return Excel::download(new AttendanceReportExport($this->filters($request)), 'laporan-absensi-'.now()->format('Y-m-d').'.xlsx');
    }

    public function update(Request $request, \App\Models\Attendance $attendance): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:hadir,sakit,izin,alpha'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($attendance->status !== $data['status']) {
            \App\Models\AttendanceLog::create([
                'attendance_id' => $attendance->id,
                'old_status' => $attendance->status,
                'new_status' => $data['status'],
                'changed_by' => $request->user()->id,
                'changed_at' => now(),
            ]);
        }

        $attendance->update([
            'status' => $data['status'],
            'notes' => $data['notes'] ?? null,
            'scanned_at' => $data['status'] === 'hadir' ? ($attendance->scanned_at ?? now()) : null,
            'updated_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Status absensi berhasil di-override oleh admin.');
    }

    private function filters(Request $request): array
    {
        return array_merge([
            'academic_year_id' => null,
            'class_id' => null,
            'subject_id' => null,
            'user_id' => null,
            'date_from' => null,
            'date_to' => null,
        ], $request->validate([
            'academic_year_id' => ['nullable', 'integer', 'exists:academic_years,id'],
            'class_id' => ['nullable', 'integer', 'exists:classes,id'],
            'subject_id' => ['nullable', 'integer', 'exists:subjects,id'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]));
    }
}
