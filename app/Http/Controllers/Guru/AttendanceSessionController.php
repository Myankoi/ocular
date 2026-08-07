<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AttendanceSessionController extends Controller
{
    public function store(Request $request, Schedule $schedule): RedirectResponse
    {
        abort_unless($schedule->user_id === $request->user()->id, 403);

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

        return view('guru.sessions.show', [
            'session' => $attendanceSession,
            'attendances' => $attendanceSession->attendances
                ->sortBy(fn (Attendance $attendance) => $attendance->student->name)
                ->values(),
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
}
