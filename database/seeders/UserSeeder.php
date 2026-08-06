<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
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
    }
}
