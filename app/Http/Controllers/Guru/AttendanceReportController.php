<?php

namespace App\Http\Controllers\Guru;

use App\Exports\AttendanceReportExport;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->filters($request);
        $userId = $request->user()->id;

        return view('guru.attendances.index', [
            'attendances' => $this->query($filters, $userId)->paginate(25)->withQueryString(),
            'filters' => $filters,
            'classes' => SchoolClass::query()->whereHas('schedules', fn ($query) => $query->where('user_id', $userId))->orderBy('name')->get(),
            'subjects' => Subject::query()->whereHas('schedules', fn ($query) => $query->where('user_id', $userId))->orderBy('name')->get(),
        ]);
    }

    public function export(Request $request)
    {
        $filters = $this->filters($request);
        $filters['user_id'] = $request->user()->id;

        return Excel::download(new AttendanceReportExport($filters), 'laporan-absensi-guru-'.now()->format('Y-m-d').'.xlsx');
    }

    private function query(array $filters, int $userId)
    {
        return Attendance::query()
            ->with(['student', 'session.schedule.schoolClass', 'session.schedule.subject', 'session.schedule.teacher'])
            ->whereHas('session.schedule', function ($schedule) use ($userId, $filters): void {
                $schedule->where('user_id', $userId)
                    ->when($filters['class_id'] ?? null, fn ($query, $value) => $query->where('class_id', $value))
                    ->when($filters['subject_id'] ?? null, fn ($query, $value) => $query->where('subject_id', $value));
            })
            ->when($filters['date_from'] ?? null, fn ($query, $value) => $query->whereHas('session', fn ($session) => $session->whereDate('date', '>=', $value)))
            ->when($filters['date_to'] ?? null, fn ($query, $value) => $query->whereHas('session', fn ($session) => $session->whereDate('date', '<=', $value)))
            ->latest('id');
    }

    private function filters(Request $request): array
    {
        return array_merge([
            'class_id' => null,
            'subject_id' => null,
            'date_from' => null,
            'date_to' => null,
        ], $request->validate([
            'class_id' => ['nullable', 'integer', 'exists:classes,id'],
            'subject_id' => ['nullable', 'integer', 'exists:subjects,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]));
    }
}
