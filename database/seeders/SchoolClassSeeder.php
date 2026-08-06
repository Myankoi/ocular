<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use Illuminate\Database\Seeder;

class SchoolClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $academicYear = AcademicYear::where('name', '2026/2027')
            ->where('semester', 1)
            ->firstOrFail();

        $classes = [
            ['name' => 'X RPL 1', 'grade_level' => 10],
            ['name' => 'X RPL 2', 'grade_level' => 10],
            ['name' => 'XI RPL 1', 'grade_level' => 11],
            ['name' => 'XI RPL 2', 'grade_level' => 11],
            ['name' => 'XII RPL 1', 'grade_level' => 12],
            ['name' => 'XII RPL 2', 'grade_level' => 12],
        ];

        foreach ($classes as $class) {
            SchoolClass::updateOrCreate(
                [
                    'academic_year_id' => $academicYear->id,
                    'name' => $class['name'],
                ],
                ['grade_level' => $class['grade_level']]
            );
        }
    }
}
