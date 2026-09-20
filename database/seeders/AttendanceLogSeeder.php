<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\AttendanceLog;
use Illuminate\Database\Seeder;

class AttendanceLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Attendance::query()
            ->whereIn('status', ['hadir', 'izin', 'sakit'])
            ->with('session')
            ->get()
            ->each(function (Attendance $attendance) {
                AttendanceLog::updateOrCreate(
                    [
                        'attendance_id' => $attendance->id,
                        'new_status' => $attendance->status,
                    ],
                    [
                        'old_status' => 'alpha',
                        'changed_by' => $attendance->updated_by,
                        'changed_at' => $attendance->updated_at,
                    ]
                );
            });
    }
}
