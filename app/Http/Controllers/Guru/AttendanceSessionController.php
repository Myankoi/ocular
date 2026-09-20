<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\AttendanceLog;
use App\Models\Schedule;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AttendanceSessionController extends Controller
{
    public function store(Request $request, Schedule $schedule): RedirectResponse
    {
        $schedule->load('academicYear');
        abort_unless($schedule->user_id === $request->user()->id, 403);

        if ($schedule->archived_at) {
            return back()->withErrors(['session' => 'Jadwal ini sudah diarsipkan dan tidak dapat membuka sesi baru.']);
        }

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
        ]);

        abort_unless($attendanceSession->schedule->user_id === $request->user()->id, 403);

        $attendances = $attendanceSession->attendances()
            ->with('student')
            ->orderBy(Student::select('name')->whereColumn('students.id', 'attendances.student_id'))
            ->paginate(25, ['*'], 'student_page')
            ->withQueryString();
        $studentTotal = $attendanceSession->attendances()->count();
        $studentPresent = $attendanceSession->attendances()->where('status', 'hadir')->count();
        $studentExcused = $attendanceSession->attendances()->whereIn('status', ['izin', 'sakit'])->count();
        $studentAlpha = $attendanceSession->attendances()->where('status', 'alpha')->count();

        return view('guru.sessions.show', [
            'session' => $attendanceSession,
            'attendances' => $attendances,
            'studentTotal' => $studentTotal,
            'studentPresent' => $studentPresent,
            'studentExcused' => $studentExcused,
            'studentAlpha' => $studentAlpha,
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

    public function updateAttendance(Request $request, AttendanceSession $attendanceSession, Attendance $attendance): RedirectResponse|JsonResponse
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

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Status kehadiran berhasil diperbarui.',
                'attendance' => [
                    'id' => $attendance->id,
                    'student_id' => $attendance->student_id,
                    'status' => $attendance->status,
                    'scanned_at' => $attendance->scanned_at?->format('H:i'),
                ],
            ]);
        }

        return back()->with('success', 'Status kehadiran berhasil diperbarui.');
    }

    public function bulkUpdateAttendances(Request $request, AttendanceSession $attendanceSession): RedirectResponse|JsonResponse
    {
        $attendanceSession->load('schedule');

        abort_unless($attendanceSession->schedule->user_id === $request->user()->id, 403);

        if ($attendanceSession->date->lt(now()->subDays(3)->startOfDay())) {
            return back()->withErrors(['attendance' => 'Perubahan guru hanya dapat dilakukan sampai H+3 dari tanggal sesi.']);
        }

        $data = $request->validate([
            'attendance_ids' => ['required', 'array', 'min:1'],
            'attendance_ids.*' => ['integer'],
            'status' => ['required', 'in:hadir,sakit,izin,alpha'],
        ]);

        $attendances = Attendance::query()
            ->where('session_id', $attendanceSession->id)
            ->whereIn('id', $data['attendance_ids'])
            ->get();

        if ($attendances->isEmpty()) {
            return back()->withErrors(['attendance' => 'Pilih minimal satu siswa yang valid.']);
        }

        $updated = 0;

        DB::transaction(function () use ($attendances, $data, $request, &$updated): void {
            foreach ($attendances as $attendance) {
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
                    'scanned_at' => $data['status'] === 'hadir' ? ($attendance->scanned_at ?? now()) : null,
                    'updated_by' => $request->user()->id,
                ]);

                $updated++;
            }
        });

        if ($request->expectsJson()) {
            return response()->json([
                'message' => "{$updated} status siswa berhasil diperbarui.",
                'attendances' => $attendances->map(fn (Attendance $attendance): array => [
                    'id' => $attendance->id,
                    'student_id' => $attendance->student_id,
                    'status' => $attendance->status,
                    'scanned_at' => $attendance->scanned_at?->format('H:i'),
                ])->values(),
            ]);
        }

        return back()->with('success', "{$updated} status siswa berhasil diperbarui.");
    }
}
