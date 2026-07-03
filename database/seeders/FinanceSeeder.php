<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FeeStructure;
use App\Models\AcademicYear;
use App\Models\Scholarship;
use App\Models\Hostel;
use App\Models\HostelRoom;

class FinanceSeeder extends Seeder
{
    public function run(): void
    {
        $currentYear = AcademicYear::where('is_current', true)->first();
        if (!$currentYear) return;

        // Fee Structures
        $fees = [
            ['name' => 'Tuition Fee (Science & Technology)', 'type' => 'tuition', 'amount' => 7500.00, 'applicable_to' => 'all', 'is_mandatory' => true],
            ['name' => 'Tuition Fee (Business)', 'type' => 'tuition', 'amount' => 6500.00, 'applicable_to' => 'all', 'is_mandatory' => true],
            ['name' => 'Registration Fee', 'type' => 'registration', 'amount' => 500.00, 'applicable_to' => 'all', 'is_mandatory' => true],
            ['name' => 'Library Fee', 'type' => 'library', 'amount' => 300.00, 'applicable_to' => 'all', 'is_mandatory' => true],
            ['name' => 'Student Activity Fee', 'type' => 'activity', 'amount' => 200.00, 'applicable_to' => 'all', 'is_mandatory' => true],
            ['name' => 'Examination Fee', 'type' => 'exam', 'amount' => 400.00, 'applicable_to' => 'all', 'is_mandatory' => true],
            ['name' => 'Medical Fee', 'type' => 'other', 'amount' => 150.00, 'applicable_to' => 'all', 'is_mandatory' => true],
            ['name' => 'ICT Levy', 'type' => 'lab', 'amount' => 250.00, 'applicable_to' => 'all', 'is_mandatory' => true],
        ];

        foreach ($fees as $fee) {
            FeeStructure::firstOrCreate(
                ['name' => $fee['name'], 'academic_year_id' => $currentYear->id],
                array_merge($fee, ['academic_year_id' => $currentYear->id, 'status' => 'active'])
            );
        }

        // Scholarships
        $scholarships = [
            ['name' => 'Academic Excellence Scholarship', 'type' => 'percentage', 'amount' => 0, 'percentage' => 50.00, 'criteria' => 'CGPA of 3.5 or above', 'max_recipients' => 20, 'status' => 'active'],
            ['name' => 'Government Bursary', 'type' => 'full', 'amount' => 8500.00, 'percentage' => 100.00, 'criteria' => 'Government sponsored students', 'max_recipients' => 100, 'status' => 'active'],
            ['name' => 'Needs-Based Scholarship', 'type' => 'partial', 'amount' => 3000.00, 'percentage' => 40.00, 'criteria' => 'Students from low-income families', 'max_recipients' => 50, 'status' => 'active'],
            ['name' => 'Sports Excellence Award', 'type' => 'partial', 'amount' => 1500.00, 'percentage' => 20.00, 'criteria' => 'Students who represent the university in sports', 'max_recipients' => 10, 'status' => 'active'],
        ];

        foreach ($scholarships as $sch) {
            Scholarship::firstOrCreate(['name' => $sch['name']], array_merge($sch, ['academic_year_id' => $currentYear->id]));
        }

        // Hostels
        $hostels = [
            ['name' => 'Sunrise Male Hostel', 'gender' => 'male', 'total_rooms' => 50, 'monthly_fee' => 800.00, 'location' => 'North Campus', 'status' => 'active'],
            ['name' => 'Blossom Female Hostel', 'gender' => 'female', 'total_rooms' => 50, 'monthly_fee' => 800.00, 'location' => 'South Campus', 'status' => 'active'],
            ['name' => 'Unity Mixed Hostel', 'gender' => 'mixed', 'total_rooms' => 30, 'monthly_fee' => 900.00, 'location' => 'East Campus', 'status' => 'active'],
        ];

        foreach ($hostels as $hostelData) {
            $hostel = Hostel::firstOrCreate(['name' => $hostelData['name']], $hostelData);

            // Create rooms
            for ($i = 1; $i <= min(10, $hostel->total_rooms); $i++) {
                HostelRoom::firstOrCreate(
                    ['hostel_id' => $hostel->id, 'room_number' => str_pad($i, 3, '0', STR_PAD_LEFT)],
                    ['capacity' => 2, 'occupied' => 0, 'type' => 'double', 'monthly_fee' => $hostelData['monthly_fee'], 'status' => 'available']
                );
            }
        }

        $this->command->info('Finance and hostel data seeded successfully.');
    }
}
