<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Staff;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@university.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('Admin@123'),
                'phone' => '+260 211 000001',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('super-admin');

        // Registrar
        $registrar = User::firstOrCreate(
            ['email' => 'registrar@university.com'],
            [
                'name' => 'Jane Mwale',
                'password' => Hash::make('Admin@123'),
                'phone' => '+260 211 000002',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $registrar->assignRole('registrar');

        // Finance Officer
        $finance = User::firstOrCreate(
            ['email' => 'finance@university.com'],
            [
                'name' => 'Peter Banda',
                'password' => Hash::make('Admin@123'),
                'phone' => '+260 211 000003',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $finance->assignRole('finance-officer');

        // Dean
        $dean = User::firstOrCreate(
            ['email' => 'dean.science@university.com'],
            [
                'name' => 'Prof. David Phiri',
                'password' => Hash::make('Admin@123'),
                'phone' => '+260 211 000004',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $dean->assignRole('dean');

        // Lecturer
        $lecturer = User::firstOrCreate(
            ['email' => 'lecturer@university.com'],
            [
                'name' => 'Dr. Mary Tembo',
                'password' => Hash::make('Admin@123'),
                'phone' => '+260 211 000005',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $lecturer->assignRole('lecturer');

        // Librarian
        $librarian = User::firstOrCreate(
            ['email' => 'librarian@university.com'],
            [
                'name' => 'Sarah Zulu',
                'password' => Hash::make('Admin@123'),
                'phone' => '+260 211 000006',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $librarian->assignRole('librarian');

        // HR Officer
        $hr = User::firstOrCreate(
            ['email' => 'hr@university.com'],
            [
                'name' => 'James Mulenga',
                'password' => Hash::make('Admin@123'),
                'phone' => '+260 211 000007',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $hr->assignRole('hr-officer');

        // Hostel Manager
        $hostel = User::firstOrCreate(
            ['email' => 'hostel@university.com'],
            [
                'name' => 'Grace Kabwe',
                'password' => Hash::make('Admin@123'),
                'phone' => '+260 211 000008',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $hostel->assignRole('hostel-manager');

        // Demo Student
        $studentUser = User::firstOrCreate(
            ['email' => 'student@university.com'],
            [
                'name' => 'John Chilemba',
                'password' => Hash::make('Admin@123'),
                'phone' => '+260 977 000001',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $studentUser->assignRole('student');

        $this->command->info('Users seeded successfully.');
    }
}
