<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@rpl.sch.id'],
            [
                'name' => 'Admin RPL',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'nip' => null,
                'is_active' => true,
            ]
        );

        $teachers = [
            ['name' => 'Budi Santoso', 'email' => 'budi@rpl.sch.id', 'nip' => '198001012010011001'],
            ['name' => 'Siti Aminah', 'email' => 'siti@rpl.sch.id', 'nip' => '198203052011012002'],
            ['name' => 'Agus Pratama', 'email' => 'agus@rpl.sch.id', 'nip' => '198504122012011003'],
            ['name' => 'Dewi Lestari', 'email' => 'dewi@rpl.sch.id', 'nip' => '198709182013012004'],
            ['name' => 'Rizky Maulana', 'email' => 'rizky@rpl.sch.id', 'nip' => '199001232014011005'],
            ['name' => 'Nina Kartika', 'email' => 'nina@rpl.sch.id', 'nip' => '199205102015012006'],
            ['name' => 'Fajar Nugroho', 'email' => 'fajar@rpl.sch.id', 'nip' => '199311302016011007'],
            ['name' => 'Maya Putri', 'email' => 'maya@rpl.sch.id', 'nip' => '199407142017012008'],
        ];

        foreach ($teachers as $teacher) {
            User::updateOrCreate(
                ['email' => $teacher['email']],
                [
                    'name' => $teacher['name'],
                    'password' => Hash::make('password'),
                    'role' => 'guru',
                    'nip' => $teacher['nip'],
                    'is_active' => true,
                ]
            );
        }

        $this->call([
            AcademicYearSeeder::class,
            SubjectSeeder::class,
            SchoolClassSeeder::class,
            StudentSeeder::class,
            ScheduleSeeder::class,
            AttendanceSessionSeeder::class,
            AttendanceSeeder::class,
            AttendanceLogSeeder::class,
        ]);
    }
}
