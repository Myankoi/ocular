<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\AttendanceSession;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AttendanceSession::query()
            ->with(['schedule.schoolClass.students'])
            ->get()
            ->each(function (AttendanceSession $session) {
                $students = $session->schedule->schoolClass->students->values();

                foreach ($students as $index => $student) {
                    $status = match (true) {
                        $index === 8 => 'izin',
                        $index === 9 => 'sakit',
                        default => 'hadir',
                    };

                    Attendance::updateOrCreate(
                        [
                            'session_id' => $session->id,
                            'student_id' => $student->id,
                        ],
                        [
                            'status' => $status,
                            'scanned_at' => $status === 'hadir' ? $session->date->format('Y-m-d') . ' ' . $session->schedule->start_time : null,
                            'updated_by' => $session->opened_by,
                            'notes' => $status === 'hadir' ? null : 'Data contoh seeder.',
                        ]
                    );
                }
            });
    }
}
