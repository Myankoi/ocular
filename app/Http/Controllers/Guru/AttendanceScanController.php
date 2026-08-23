<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\AttendanceSession;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceScanController extends Controller
{
    public function __invoke(Request $request, AttendanceSession $attendanceSession): RedirectResponse|JsonResponse
    {
        $attendanceSession->load('schedule.schoolClass');

        abort_unless($attendanceSession->schedule->user_id === $request->user()->id, 403);

        if ($attendanceSession->status !== 'open') {
            return $this->scanError($request, 'Sesi absensi sudah ditutup.');
        }

        $data = $request->validate([
            'nisn' => ['required', 'string', 'max:255'],
        ]);

        $student = Student::query()
            ->where('nisn', $data['nisn'])
            ->where('is_active', true)
            ->first();

        if (! $student) {
            return $this->scanError($request, 'NISN tidak ditemukan atau siswa tidak aktif.');
        }

        if ($student->class_id !== $attendanceSession->schedule->class_id) {
            return $this->scanError($request, 'Siswa ditemukan, tapi bukan bagian dari kelas pada sesi ini.');
        }

        $attendance = Attendance::query()
            ->where('session_id', $attendanceSession->id)
            ->where('student_id', $student->id)
            ->first();

        if (! $attendance) {
            return $this->scanError($request, 'Siswa belum terdaftar pada daftar kehadiran sesi ini. Tutup dan buka ulang sesi jika data kelas baru berubah.');
        }

        $result = DB::transaction(function () use ($attendance, $request, $student): array {
            $lockedAttendance = Attendance::query()->whereKey($attendance->id)->lockForUpdate()->firstOrFail();

            if ($lockedAttendance->status === 'hadir') {
                return ['duplicate' => true, 'attendance' => $lockedAttendance];
            }

            $oldStatus = $lockedAttendance->status;

            $lockedAttendance->update([
                'status' => 'hadir',
                'scanned_at' => now(),
                'updated_by' => $request->user()->id,
                'notes' => null,
            ]);

            AttendanceLog::create([
                'attendance_id' => $lockedAttendance->id,
                'old_status' => $oldStatus,
                'new_status' => 'hadir',
                'changed_by' => $request->user()->id,
                'changed_at' => now(),
            ]);

            return ['duplicate' => false, 'attendance' => $lockedAttendance];
        });

        if ($result['duplicate']) {
            return $this->scanError($request, $student->name . ' sudah tercatat hadir.');
        }

        $attendance = $result['attendance'];

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $student->name . ' berhasil tercatat hadir.',
                'attendance' => [
                    'id' => $attendance->id,
                    'student_id' => $attendance->student_id,
                    'status' => $attendance->status,
                    'scanned_at' => $attendance->scanned_at?->format('H:i'),
                ],
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'nisn' => $student->nisn,
                    'nis' => $student->nis,
                    'class' => $attendanceSession->schedule->schoolClass->name,
                    'photo_url' => $student->photo
                        ? route('guru.sessions.students.photo', [$attendanceSession, $student])
                        : null,
                ],
            ]);
        }

        return redirect()
            ->route('guru.sessions.show', $attendanceSession)
            ->with('success', $student->name . ' berhasil tercatat hadir.');
    }

    private function scanError(Request $request, string $message): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'errors' => [
                    'scan' => [$message],
                ],
            ], 422);
        }

        return back()->withErrors(['scan' => $message]);
    }
}
