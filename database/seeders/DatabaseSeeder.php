<?php

namespace Database\Seeders;

use App\Models\Training;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application database.
     */
    public function run(): void
    {
        // Admin (full) and Mini Admin (sub-admin) accounts
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@deped.local',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
        ]);

        User::create([
            'name' => 'Sub-Administrator',
            'email' => 'subadmin@deped.local',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SUB_ADMIN,
            'employee_id' => 'SUBADMIN001',
            'designation' => 'Sub-Administrator',
            'department' => 'HRDD',
            'school' => 'DepEd Division Office',
        ]);

        $personnel = [
            User::create([
                'name' => 'Juan Dela Cruz',
                'email' => 'juan.delacruz@deped.local',
                'password' => Hash::make('password'),
                'role' => User::ROLE_PERSONNEL,
                'employee_id' => 'EMP001',
                'designation' => 'Teacher I',
                'department' => 'English',
                'school' => 'Sample Elementary School',
            ]),
            User::create([
                'name' => 'Maria Santos',
                'email' => 'maria.santos@deped.local',
                'password' => Hash::make('password'),
                'role' => User::ROLE_PERSONNEL,
                'employee_id' => 'EMP002',
                'designation' => 'Teacher II',
                'department' => 'Mathematics',
                'school' => 'Sample National High School',
            ]),
            User::create([
                'name' => 'Jake Leorenz D. Cartilla',
                'email' => 'jake.cartilla@deped.local',
                'password' => Hash::make('password'),
                'role' => User::ROLE_PERSONNEL,
                'employee_id' => 'EMP003',
                'designation' => 'Teacher I',
                'department' => 'Filipino',
                'school' => 'DepEd Division Office',
            ]),
            User::create([
                'name' => 'Juben B. Moring',
                'email' => 'juben.moring@deped.local',
                'password' => Hash::make('password'),
                'role' => User::ROLE_PERSONNEL,
                'employee_id' => 'EMP004',
                'designation' => 'Teacher II',
                'department' => 'Science',
                'school' => 'DepEd Division Office',
            ]),
            User::create([
                'name' => 'Jay-V P. Magallanes',
                'email' => 'jayv.magallanes@deped.local',
                'password' => Hash::make('password'),
                'role' => User::ROLE_PERSONNEL,
                'employee_id' => 'EMP005',
                'designation' => 'Teacher I',
                'department' => 'Mathematics',
                'school' => 'DepEd Division Office',
            ]),
        ];

        $trainings = [
            Training::create([
                'title' => 'K to 12 Curriculum Training',
                'type_of_ld' => 'Training',
                'provider' => 'DepEd Regional Office',
                'venue' => 'Regional Training Center',
                'start_date' => now()->subMonths(3),
                'end_date' => now()->subMonths(3)->addDays(2),
                'hours' => 24,
                'description' => 'Orientation on K to 12 curriculum updates.',
            ]),
            Training::create([
                'title' => 'ICT Integration in Teaching',
                'type_of_ld' => 'Seminar',
                'provider' => 'DepEd ICT Division',
                'venue' => 'Online',
                'start_date' => now()->subMonths(2),
                'end_date' => now()->subMonths(2)->addDay(),
                'hours' => 8,
                'description' => 'Using technology in the classroom.',
            ]),
            Training::create([
                'title' => 'Classroom Management Workshop',
                'type_of_ld' => 'Workshop',
                'provider' => 'Schools Division Office',
                'venue' => 'Division Conference Hall',
                'start_date' => now()->subMonth(),
                'end_date' => now()->subMonth()->addDays(1),
                'hours' => 16,
            ]),
            Training::create([
                'title' => 'Reading and Numeracy Program Seminar',
                'type_of_ld' => 'Seminar',
                'provider' => 'Bureau of Learning Delivery',
                'venue' => 'Division Office',
                'start_date' => now()->subMonths(4),
                'end_date' => now()->subMonths(4)->addDays(1),
                'hours' => 8,
                'description' => 'Strategies for improving reading and numeracy.',
            ]),
            Training::create([
                'title' => 'Disaster Risk Reduction and Management',
                'type_of_ld' => 'Training',
                'provider' => 'DepEd DRRM Unit',
                'venue' => 'Regional Training Center',
                'start_date' => now()->subMonths(5),
                'end_date' => now()->subMonths(5)->addDays(2),
                'hours' => 16,
                'description' => 'School-based DRRM orientation.',
            ]),
            Training::create([
                'title' => 'Gender and Development (GAD) Seminar',
                'type_of_ld' => 'Seminar',
                'provider' => 'DepEd GAD Focal',
                'venue' => 'Online',
                'start_date' => now()->subMonths(2)->addDays(5),
                'end_date' => now()->subMonths(2)->addDays(6),
                'hours' => 6,
                'description' => 'Gender-sensitive teaching and school operations.',
            ]),
            Training::create([
                'title' => 'Mental Health and Psychosocial Support',
                'type_of_ld' => 'Workshop',
                'provider' => 'DepEd Health and Nutrition',
                'venue' => 'Division Conference Hall',
                'start_date' => now()->subWeeks(3),
                'end_date' => now()->subWeeks(3)->addDay(),
                'hours' => 8,
                'description' => 'MHPSS for learners and personnel.',
            ]),
        ];

        // Juan Dela Cruz
        $personnel[0]->trainings()->attach([$trainings[0]->id, $trainings[1]->id], ['attended_date' => null, 'remarks' => null]);
        // Maria Santos
        $personnel[1]->trainings()->attach([$trainings[0]->id, $trainings[2]->id], ['attended_date' => null, 'remarks' => null]);

        // Jake Leorenz D. Cartilla – multiple seminars/trainings
        $personnel[2]->trainings()->attach([
            $trainings[0]->id, $trainings[1]->id, $trainings[3]->id, $trainings[4]->id, $trainings[5]->id, $trainings[6]->id,
        ], ['attended_date' => null, 'remarks' => null]);

        // Juben B. Moring
        $personnel[3]->trainings()->attach([
            $trainings[0]->id, $trainings[2]->id, $trainings[4]->id, $trainings[6]->id,
        ], ['attended_date' => null, 'remarks' => null]);

        // Jay-V P. Magallanes
        $personnel[4]->trainings()->attach([
            $trainings[1]->id, $trainings[3]->id, $trainings[5]->id, $trainings[6]->id,
        ], ['attended_date' => null, 'remarks' => null]);

        $this->command->info('--- Admin accounts (change password after first login) ---');
        $this->command->info('Admin:       admin@deped.local / password');
        $this->command->info('Mini Admin:  subadmin@deped.local / password');
        $this->command->info('--- Personnel (password: password) ---');
        $this->command->info('juan.delacruz@deped.local, maria.santos@deped.local, jake.cartilla@deped.local, juben.moring@deped.local, jayv.magallanes@deped.local');
    }
}
