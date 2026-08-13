<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\AttendanceLog;
use App\Models\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class AttendanceSessionController extends Controller
{
    public function store(Request $request, Schedule $schedule): RedirectResponse
    {
        $schedule->load('academicYear');
        abort_unless($schedule->user_id === $request->user()->id, 403);

        if (! $schedule->academicYear?->is_active || (int) $schedule->day_of_week !== now()->dayOfWeekIso) {
            return back()->withErrors(['session' => 'Sesi hanya dapat dibuka untuk jadwal aktif pada hari ini.']);
        }

        $today = now()->toDateString();

        $session = DB::transaction(function () use ($request, $schedule, $today): AttendanceSession {
            $session = AttendanceSession::firstOrCreate(
                [
                    'schedule_id' => $schedule->id,
                    'date' => $today,
                ],
                [
                    'status' => 'open',
                    'opened_by' => $request->user()->id,
                    'opened_at' => now(),
                    'closed_at' => null,
                ]
            );

            if ($session->status === 'closed') {
                return $session;
            }

            $studentIds = $schedule->schoolClass()
                ->firstOrFail()
                ->students()
                ->where('is_active', true)
                ->pluck('id');

            foreach ($studentIds as $studentId) {
                Attendance::firstOrCreate(
                    [
                        'session_id' => $session->id,
                        'student_id' => $studentId,
                    ],
                    [
                        'status' => 'alpha',
                        'scanned_at' => null,
                        'updated_by' => $request->user()->id,
                        'notes' => null,
                    ]
                );
            }

            return $session;
        });

        if ($session->status === 'closed') {
            return redirect()
                ->route('guru.sessions.show', $session)
                ->withErrors(['session' => 'Sesi absensi hari ini sudah ditutup.']);
        }

        return redirect()->route('guru.sessions.show', $session);
    }

    public function show(Request $request, AttendanceSession $attendanceSession): View
    {
        $attendanceSession->load([
            'schedule.subject',
            'schedule.schoolClass',
            'attendances.student',
        ]);

        abort_unless($attendanceSession->schedule->user_id === $request->user()->id, 403);

        $allAttendances = $attendanceSession->attendances
            ->sortBy(fn (Attendance $attendance) => $attendance->student->name)
            ->values();
        $perPage = 25;
        $page = max(1, (int) $request->integer('student_page', 1));
        $attendances = new LengthAwarePaginator(
            $allAttendances->forPage($page, $perPage)->values(),
            $allAttendances->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
                'pageName' => 'student_page',
            ],
        );

        return view('guru.sessions.show', [
            'session' => $attendanceSession,
            'attendances' => $attendances,
            'studentTotal' => $allAttendances->count(),
            'studentPresent' => $allAttendances->where('status', 'hadir')->count(),
            'studentExcused' => $allAttendances->whereIn('status', ['izin', 'sakit'])->count(),
            'studentAlpha' => $allAttendances->where('status', 'alpha')->count(),
        ]);
    }

    public function close(Request $request, AttendanceSession $attendanceSession): RedirectResponse
    {
        $attendanceSession->load('schedule');

        abort_unless($attendanceSession->schedule->user_id === $request->user()->id, 403);

        if ($attendanceSession->status === 'closed') {
            return back()->withErrors(['session' => 'Sesi ini sudah ditutup.']);
        }

        $attendanceSession->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        return redirect()
            ->route('guru.sessions.show', $attendanceSession)
            ->with('success', 'Sesi absensi berhasil ditutup.');
    }

    public function updateAttendance(Request $request, AttendanceSession $attendanceSession, Attendance $attendance): RedirectResponse
    {
        $attendanceSession->load('schedule');

        abort_unless($attendanceSession->schedule->user_id === $request->user()->id, 403);
        abort_unless($attendance->session_id === $attendanceSession->id, 404);

        if ($attendanceSession->date->lt(now()->subDays(3)->startOfDay())) {
            return back()->withErrors(['attendance' => 'Perubahan guru hanya dapat dilakukan sampai H+3 dari tanggal sesi.']);
        }

        $data = $request->validate([
            'status' => ['required', 'in:hadir,sakit,izin,alpha'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($attendance->status !== $data['status']) {
            AttendanceLog::create([
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

        return back()->with('success', 'Status kehadiran berhasil diperbarui.');
    }
}
