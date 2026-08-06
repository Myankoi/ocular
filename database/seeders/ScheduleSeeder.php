<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Schedule;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $academicYear = AcademicYear::where('name', '2026/2027')
            ->where('semester', 1)
            ->firstOrFail();

        $subjects = Subject::pluck('id', 'code');
        $classes = SchoolClass::where('academic_year_id', $academicYear->id)->pluck('id', 'name');
        $teachers = User::where('role', 'guru')->orderBy('id')->get();

        $scheduleRows = [
            ['teacher' => 0, 'subject' => 'DPRG', 'class' => 'X RPL 1', 'day' => 1, 'start' => '07:00', 'end' => '08:30'],
            ['teacher' => 1, 'subject' => 'DPRG', 'class' => 'X RPL 2', 'day' => 1, 'start' => '08:30', 'end' => '10:00'],
            ['teacher' => 2, 'subject' => 'PWEB', 'class' => 'XI RPL 1', 'day' => 1, 'start' => '10:15', 'end' => '11:45'],
            ['teacher' => 3, 'subject' => 'PWEB', 'class' => 'XI RPL 2', 'day' => 2, 'start' => '07:00', 'end' => '08:30'],
            ['teacher' => 4, 'subject' => 'BD', 'class' => 'XI RPL 1', 'day' => 2, 'start' => '08:30', 'end' => '10:00'],
            ['teacher' => 5, 'subject' => 'BD', 'class' => 'XI RPL 2', 'day' => 3, 'start' => '07:00', 'end' => '08:30'],
            ['teacher' => 6, 'subject' => 'PBO', 'class' => 'XII RPL 1', 'day' => 3, 'start' => '08:30', 'end' => '10:00'],
            ['teacher' => 7, 'subject' => 'PMOB', 'class' => 'XII RPL 2', 'day' => 4, 'start' => '07:00', 'end' => '08:30'],
            ['teacher' => 0, 'subject' => 'KKRPL', 'class' => 'XII RPL 1', 'day' => 5, 'start' => '10:15', 'end' => '11:45'],
            ['teacher' => 1, 'subject' => 'KKRPL', 'class' => 'XII RPL 2', 'day' => 5, 'start' => '13:00', 'end' => '14:30'],
        ];

        foreach ($scheduleRows as $row) {
            $teacher = $teachers[$row['teacher']];
            $subjectId = $subjects[$row['subject']];

            $teacher->subjects()->syncWithoutDetaching([$subjectId]);

            Schedule::updateOrCreate(
                [
                    'academic_year_id' => $academicYear->id,
                    'class_id' => $classes[$row['class']],
                    'day_of_week' => $row['day'],
                    'start_time' => $row['start'],
                ],
                [
                    'user_id' => $teacher->id,
                    'subject_id' => $subjectId,
                    'end_time' => $row['end'],
                ]
            );
        }
    }
}
