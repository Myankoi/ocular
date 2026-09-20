<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AcademicYear::updateOrCreate(
            ['name' => '2026/2027', 'semester' => 1],
            [
                'is_active' => true,
                'start_date' => '2026-07-13',
                'end_date' => '2026-12-18',
            ]
        );

        AcademicYear::updateOrCreate(
            ['name' => '2026/2027', 'semester' => 2],
            [
                'is_active' => false,
                'start_date' => '2027-01-04',
                'end_date' => '2027-06-18',
            ]
        );
    }
}
