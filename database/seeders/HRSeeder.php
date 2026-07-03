<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LeaveType;
use App\Models\Employee;
use App\Models\User;
use App\Models\Department;

class HRSeeder extends Seeder
{
    public function run(): void
    {
        $leaveTypes = [
            ['name' => 'Annual Leave', 'days_allowed' => 21, 'is_paid' => true, 'description' => 'Annual paid leave entitlement'],
            ['name' => 'Sick Leave', 'days_allowed' => 14, 'is_paid' => true, 'description' => 'Medical leave with doctor\'s certificate'],
            ['name' => 'Maternity Leave', 'days_allowed' => 84, 'is_paid' => true, 'description' => 'Leave for new mothers'],
            ['name' => 'Paternity Leave', 'days_allowed' => 5, 'is_paid' => true, 'description' => 'Leave for new fathers'],
            ['name' => 'Study Leave', 'days_allowed' => 14, 'is_paid' => false, 'description' => 'Leave for examinations and study'],
            ['name' => 'Compassionate Leave', 'days_allowed' => 5, 'is_paid' => true, 'description' => 'Bereavement leave'],
            ['name' => 'Unpaid Leave', 'days_allowed' => 30, 'is_paid' => false, 'description' => 'Unpaid absence'],
        ];

        foreach ($leaveTypes as $lt) {
            LeaveType::firstOrCreate(['name' => $lt['name']], $lt);
        }

        // Create employees for existing users
        $hrUser = User::where('email', 'hr@university.com')->first();
        $dept = Department::where('code', 'BA')->first();
        if ($hrUser && $dept && !Employee::where('user_id', $hrUser->id)->exists()) {
            Employee::create([
                'user_id' => $hrUser->id,
                'employee_id' => 'EMP/2024/0001',
                'first_name' => 'James',
                'last_name' => 'Mulenga',
                'email' => $hrUser->email,
                'department_id' => $dept->id,
                'position' => 'HR Officer',
                'employment_type' => 'full_time',
                'basic_salary' => 8500.00,
                'hire_date' => '2022-01-10',
                'status' => 'active',
            ]);
        }

        $this->command->info('HR data seeded successfully.');
    }
}
