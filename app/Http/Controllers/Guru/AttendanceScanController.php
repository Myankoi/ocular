<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\AttendanceSession;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceScanController extends Controller
{
    public function __invoke(Request $request, AttendanceSession $attendanceSession): RedirectResponse
    {
        $attendanceSession->load('schedule.schoolClass');

        abort_unless($attendanceSession->schedule->user_id === $request->user()->id, 403);

        if ($attendanceSession->status !== 'open') {
            return back()->withErrors([
                'scan' => 'Sesi absensi sudah ditutup.',
            ]);
        }

        $data = $request->validate([
            'nisn' => ['required', 'string', 'max:255'],
        ]);

        $student = Student::query()
            ->where('nisn', $data['nisn'])
            ->where('is_active', true)
            ->first();

        if (! $student) {
            return back()->withErrors([
                'scan' => 'NISN tidak ditemukan atau siswa tidak aktif.',
            ]);
        }

        if ($student->class_id !== $attendanceSession->schedule->class_id) {
            return back()->withErrors([
                'scan' => 'Siswa ditemukan, tapi bukan bagian dari kelas pada sesi ini.',
            ]);
        }

        $attendance = Attendance::query()
            ->where('session_id', $attendanceSession->id)
            ->where('student_id', $student->id)
            ->first();

        if (! $attendance) {
            return back()->withErrors([
                'scan' => 'Siswa belum terdaftar di roster sesi ini. Tutup dan buka ulang sesi jika data kelas baru berubah.',
            ]);
        }

        if ($attendance->status === 'hadir') {
            return back()->withErrors([
                'scan' => $student->name . ' sudah tercatat hadir.',
            ]);
        }

        DB::transaction(function () use ($attendance, $request): void {
            $oldStatus = $attendance->status;

            $attendance->update([
                'status' => 'hadir',
                'scanned_at' => now(),
                'updated_by' => $request->user()->id,
                'notes' => null,
            ]);

            AttendanceLog::create([
                'attendance_id' => $attendance->id,
                'old_status' => $oldStatus,
                'new_status' => 'hadir',
                'changed_by' => $request->user()->id,
                'changed_at' => now(),
            ]);
        });

        return redirect()
            ->route('guru.sessions.show', $attendanceSession)
            ->with('success', $student->name . ' berhasil tercatat hadir.');
    }
}
