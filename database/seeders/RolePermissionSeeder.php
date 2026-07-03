<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\LaravelPermission\Models\Role;
use Spatie\LaravelPermission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\LaravelPermission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Users
            ['name' => 'view users', 'module' => 'admin'],
            ['name' => 'create users', 'module' => 'admin'],
            ['name' => 'edit users', 'module' => 'admin'],
            ['name' => 'delete users', 'module' => 'admin'],
            ['name' => 'manage roles', 'module' => 'admin'],
            ['name' => 'manage settings', 'module' => 'admin'],
            ['name' => 'view audit logs', 'module' => 'admin'],
            ['name' => 'manage backups', 'module' => 'admin'],
            // Students
            ['name' => 'view students', 'module' => 'students'],
            ['name' => 'create students', 'module' => 'students'],
            ['name' => 'edit students', 'module' => 'students'],
            ['name' => 'delete students', 'module' => 'students'],
            ['name' => 'view own profile', 'module' => 'students'],
            // Admissions
            ['name' => 'view admissions', 'module' => 'admissions'],
            ['name' => 'create admissions', 'module' => 'admissions'],
            ['name' => 'approve admissions', 'module' => 'admissions'],
            // Academic
            ['name' => 'manage faculties', 'module' => 'academic'],
            ['name' => 'manage departments', 'module' => 'academic'],
            ['name' => 'manage programs', 'module' => 'academic'],
            ['name' => 'manage courses', 'module' => 'academic'],
            ['name' => 'manage academic years', 'module' => 'academic'],
            ['name' => 'manage semesters', 'module' => 'academic'],
            ['name' => 'manage course offerings', 'module' => 'academic'],
            ['name' => 'manage registrations', 'module' => 'academic'],
            ['name' => 'register courses', 'module' => 'academic'],
            ['name' => 'manage timetable', 'module' => 'academic'],
            ['name' => 'take attendance', 'module' => 'academic'],
            ['name' => 'view attendance', 'module' => 'academic'],
            ['name' => 'manage examinations', 'module' => 'academic'],
            ['name' => 'enter results', 'module' => 'academic'],
            ['name' => 'approve results', 'module' => 'academic'],
            ['name' => 'view results', 'module' => 'academic'],
            ['name' => 'publish results', 'module' => 'academic'],
            // Finance
            ['name' => 'manage fee structures', 'module' => 'finance'],
            ['name' => 'manage billing', 'module' => 'finance'],
            ['name' => 'record payments', 'module' => 'finance'],
            ['name' => 'view payments', 'module' => 'finance'],
            ['name' => 'manage scholarships', 'module' => 'finance'],
            ['name' => 'view financial reports', 'module' => 'finance'],
            ['name' => 'view own fees', 'module' => 'finance'],
            // Hostel
            ['name' => 'manage hostels', 'module' => 'hostel'],
            ['name' => 'manage rooms', 'module' => 'hostel'],
            ['name' => 'manage allocations', 'module' => 'hostel'],
            // Library
            ['name' => 'manage books', 'module' => 'library'],
            ['name' => 'issue books', 'module' => 'library'],
            ['name' => 'return books', 'module' => 'library'],
            ['name' => 'view library', 'module' => 'library'],
            // HR
            ['name' => 'manage employees', 'module' => 'hr'],
            ['name' => 'manage leave', 'module' => 'hr'],
            ['name' => 'manage payroll', 'module' => 'hr'],
            ['name' => 'apply leave', 'module' => 'hr'],
            // Assets
            ['name' => 'manage assets', 'module' => 'assets'],
            // Reports
            ['name' => 'view reports', 'module' => 'reports'],
            // Communication
            ['name' => 'manage announcements', 'module' => 'communication'],
            ['name' => 'send messages', 'module' => 'communication'],
            ['name' => 'manage documents', 'module' => 'communication'],
            ['name' => 'manage support tickets', 'module' => 'support'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm['name'], 'guard_name' => 'web'],
                ['module' => $perm['module'], 'description' => ucwords($perm['name'])]
            );
        }

        // Define roles
        $roles = [
            'super-admin' => [
                'description' => 'Full system access', 'color' => 'danger',
                'permissions' => Permission::all()->pluck('name')->toArray()
            ],
            'registrar' => [
                'description' => 'Student and admission management', 'color' => 'primary',
                'permissions' => ['view students', 'create students', 'edit students', 'view admissions', 'create admissions', 'approve admissions', 'manage academic years', 'manage semesters', 'manage course offerings', 'manage registrations', 'view results', 'publish results', 'view reports', 'manage announcements', 'send messages', 'manage documents', 'manage support tickets']
            ],
            'finance-officer' => [
                'description' => 'Finance and billing management', 'color' => 'success',
                'permissions' => ['manage fee structures', 'manage billing', 'record payments', 'view payments', 'manage scholarships', 'view financial reports', 'view students', 'view reports', 'send messages', 'manage announcements']
            ],
            'dean' => [
                'description' => 'Faculty management and oversight', 'color' => 'info',
                'permissions' => ['manage faculties', 'manage departments', 'manage programs', 'manage courses', 'view students', 'view attendance', 'approve results', 'view results', 'view reports', 'send messages', 'manage announcements']
            ],
            'head-of-department' => [
                'description' => 'Department management', 'color' => 'info',
                'permissions' => ['manage departments', 'manage courses', 'manage course offerings', 'view students', 'view attendance', 'approve results', 'view results', 'send messages']
            ],
            'lecturer' => [
                'description' => 'Course delivery and assessment', 'color' => 'warning',
                'permissions' => ['take attendance', 'view attendance', 'manage examinations', 'enter results', 'view results', 'view students', 'send messages', 'manage documents']
            ],
            'student' => [
                'description' => 'Student portal access', 'color' => 'secondary',
                'permissions' => ['view own profile', 'register courses', 'view results', 'view own fees', 'view library', 'send messages', 'view attendance']
            ],
            'parent-guardian' => [
                'description' => 'Parent portal access', 'color' => 'secondary',
                'permissions' => ['view results', 'view own fees', 'view attendance', 'send messages']
            ],
            'librarian' => [
                'description' => 'Library management', 'color' => 'warning',
                'permissions' => ['manage books', 'issue books', 'return books', 'view library', 'send messages']
            ],
            'hostel-manager' => [
                'description' => 'Hostel management', 'color' => 'warning',
                'permissions' => ['manage hostels', 'manage rooms', 'manage allocations', 'view students', 'send messages']
            ],
            'hr-officer' => [
                'description' => 'Human resource management', 'color' => 'info',
                'permissions' => ['manage employees', 'manage leave', 'manage payroll', 'view reports', 'send messages']
            ],
        ];

        foreach ($roles as $roleName => $roleData) {
            $role = Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => 'web'],
                ['description' => $roleData['description'], 'color' => $roleData['color']]
            );
            $role->syncPermissions($roleData['permissions']);
        }

        $this->command->info('Roles and permissions seeded successfully.');
    }
}
