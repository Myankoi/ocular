<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = SchoolClass::query()->orderBy('grade_level')->orderBy('name')->get();
        $sequence = 1;

        foreach ($classes as $class) {
            for ($i = 1; $i <= 10; $i++) {
                $number = str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);

                Student::updateOrCreate(
                    ['nisn' => '006789' . $number],
                    [
                        'nis' => '2627' . $number,
                        'name' => 'Siswa ' . $class->name . ' ' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                        'class_id' => $class->id,
                        'photo' => null,
                        'is_active' => true,
                    ]
                );

                $sequence++;
            }
        }
    }
}
