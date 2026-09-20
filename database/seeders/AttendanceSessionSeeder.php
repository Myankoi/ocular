<?php

namespace Database\Seeders;

use App\Models\AttendanceSession;
use App\Models\Schedule;
use Illuminate\Database\Seeder;

class AttendanceSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datesByDay = [
            1 => '2026-08-10',
            2 => '2026-08-11',
            3 => '2026-08-12',
            4 => '2026-08-13',
            5 => '2026-08-14',
            6 => '2026-08-15',
        ];

        Schedule::query()->with('teacher')->get()->each(function (Schedule $schedule) use ($datesByDay) {
            AttendanceSession::updateOrCreate(
                [
                    'schedule_id' => $schedule->id,
                    'date' => $datesByDay[$schedule->day_of_week],
                ],
                [
                    'status' => 'closed',
                    'opened_by' => $schedule->user_id,
                    'opened_at' => $datesByDay[$schedule->day_of_week] . ' ' . $schedule->start_time,
                    'closed_at' => $datesByDay[$schedule->day_of_week] . ' ' . $schedule->end_time,
                ]
            );
        });
    }
}
