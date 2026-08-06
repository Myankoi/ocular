<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            ['name' => 'Pemrograman Web', 'code' => 'PWEB'],
            ['name' => 'Basis Data', 'code' => 'BD'],
            ['name' => 'Pemrograman Berorientasi Objek', 'code' => 'PBO'],
            ['name' => 'Pemrograman Mobile', 'code' => 'PMOB'],
            ['name' => 'Dasar Pemrograman', 'code' => 'DPRG'],
            ['name' => 'Konsentrasi Keahlian RPL', 'code' => 'KKRPL'],
        ];

        foreach ($subjects as $subject) {
            Subject::updateOrCreate(
                ['code' => $subject['code']],
                ['name' => $subject['name']]
            );
        }
    }
}
