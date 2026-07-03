<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Program;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Course;
use App\Models\Student;
use App\Models\User;
use App\Models\Staff;
use App\Models\Announcement;
use Illuminate\Support\Facades\Hash;

class AcademicSeeder extends Seeder
{
    public function run(): void
    {
        // Academic Years
        $ay2024 = AcademicYear::firstOrCreate(
            ['name' => '2024/2025'],
            ['start_date' => '2024-08-01', 'end_date' => '2025-07-31', 'is_current' => true, 'status' => 'active']
        );
        $ay2023 = AcademicYear::firstOrCreate(
            ['name' => '2023/2024'],
            ['start_date' => '2023-08-01', 'end_date' => '2024-07-31', 'is_current' => false, 'status' => 'completed']
        );

        // Semesters
        $sem1 = Semester::firstOrCreate(
            ['name' => 'Semester 1 2024/2025', 'academic_year_id' => $ay2024->id],
            ['start_date' => '2024-08-01', 'end_date' => '2024-12-31', 'registration_start' => '2024-07-15', 'registration_end' => '2024-08-15', 'exam_start' => '2024-12-01', 'exam_end' => '2024-12-20', 'is_current' => true, 'status' => 'active']
        );
        $sem2 = Semester::firstOrCreate(
            ['name' => 'Semester 2 2024/2025', 'academic_year_id' => $ay2024->id],
            ['start_date' => '2025-01-15', 'end_date' => '2025-06-30', 'registration_start' => '2025-01-01', 'registration_end' => '2025-01-20', 'exam_start' => '2025-06-01', 'exam_end' => '2025-06-20', 'is_current' => false, 'status' => 'upcoming']
        );

        // Faculties
        $facScience = Faculty::firstOrCreate(
            ['code' => 'FST'],
            ['name' => 'Faculty of Science and Technology', 'description' => 'Science, Technology, Engineering and Mathematics', 'email' => 'science@university.com', 'status' => 'active']
        );
        $facBusiness = Faculty::firstOrCreate(
            ['code' => 'FBE'],
            ['name' => 'Faculty of Business and Economics', 'description' => 'Business Administration, Economics, Finance', 'email' => 'business@university.com', 'status' => 'active']
        );
        $facSocial = Faculty::firstOrCreate(
            ['code' => 'FSS'],
            ['name' => 'Faculty of Social Sciences', 'description' => 'Sociology, Political Science, Psychology', 'email' => 'social@university.com', 'status' => 'active']
        );
        $facLaw = Faculty::firstOrCreate(
            ['code' => 'FLW'],
            ['name' => 'Faculty of Law', 'description' => 'Law and Legal Studies', 'email' => 'law@university.com', 'status' => 'active']
        );
        $facEd = Faculty::firstOrCreate(
            ['code' => 'FED'],
            ['name' => 'Faculty of Education', 'description' => 'Teacher Training and Education', 'email' => 'education@university.com', 'status' => 'active']
        );
        $facHealth = Faculty::firstOrCreate(
            ['code' => 'FHS'],
            ['name' => 'Faculty of Health Sciences', 'description' => 'Nursing, Public Health, Medical Sciences', 'email' => 'health@university.com', 'status' => 'active']
        );

        // Departments
        $deptCS = Department::firstOrCreate(['code' => 'CS'], ['name' => 'Computer Science', 'faculty_id' => $facScience->id, 'status' => 'active']);
        $deptMath = Department::firstOrCreate(['code' => 'MTH'], ['name' => 'Mathematics', 'faculty_id' => $facScience->id, 'status' => 'active']);
        $deptEng = Department::firstOrCreate(['code' => 'ENG'], ['name' => 'Engineering', 'faculty_id' => $facScience->id, 'status' => 'active']);
        $deptBA = Department::firstOrCreate(['code' => 'BA'], ['name' => 'Business Administration', 'faculty_id' => $facBusiness->id, 'status' => 'active']);
        $deptAcc = Department::firstOrCreate(['code' => 'ACC'], ['name' => 'Accounting and Finance', 'faculty_id' => $facBusiness->id, 'status' => 'active']);
        $deptEco = Department::firstOrCreate(['code' => 'ECO'], ['name' => 'Economics', 'faculty_id' => $facBusiness->id, 'status' => 'active']);
        $deptSoc = Department::firstOrCreate(['code' => 'SOC'], ['name' => 'Sociology', 'faculty_id' => $facSocial->id, 'status' => 'active']);
        $deptLaw = Department::firstOrCreate(['code' => 'LAW'], ['name' => 'Law', 'faculty_id' => $facLaw->id, 'status' => 'active']);
        $deptEd = Department::firstOrCreate(['code' => 'ED'], ['name' => 'Education', 'faculty_id' => $facEd->id, 'status' => 'active']);
        $deptNur = Department::firstOrCreate(['code' => 'NUR'], ['name' => 'Nursing', 'faculty_id' => $facHealth->id, 'status' => 'active']);

        // Programs
        $programs = [
            ['name' => 'Bachelor of Science in Computer Science', 'code' => 'BSCS', 'department_id' => $deptCS->id, 'faculty_id' => $facScience->id, 'degree_type' => 'bachelor', 'duration_years' => 4, 'total_credit_hours' => 120],
            ['name' => 'Bachelor of Science in Information Technology', 'code' => 'BSIT', 'department_id' => $deptCS->id, 'faculty_id' => $facScience->id, 'degree_type' => 'bachelor', 'duration_years' => 4, 'total_credit_hours' => 120],
            ['name' => 'Bachelor of Science in Mathematics', 'code' => 'BSMT', 'department_id' => $deptMath->id, 'faculty_id' => $facScience->id, 'degree_type' => 'bachelor', 'duration_years' => 4, 'total_credit_hours' => 120],
            ['name' => 'Bachelor of Engineering in Civil Engineering', 'code' => 'BECE', 'department_id' => $deptEng->id, 'faculty_id' => $facScience->id, 'degree_type' => 'bachelor', 'duration_years' => 5, 'total_credit_hours' => 150],
            ['name' => 'Bachelor of Business Administration', 'code' => 'BBA', 'department_id' => $deptBA->id, 'faculty_id' => $facBusiness->id, 'degree_type' => 'bachelor', 'duration_years' => 4, 'total_credit_hours' => 120],
            ['name' => 'Bachelor of Accounting', 'code' => 'BACC', 'department_id' => $deptAcc->id, 'faculty_id' => $facBusiness->id, 'degree_type' => 'bachelor', 'duration_years' => 4, 'total_credit_hours' => 120],
            ['name' => 'Bachelor of Economics', 'code' => 'BECO', 'department_id' => $deptEco->id, 'faculty_id' => $facBusiness->id, 'degree_type' => 'bachelor', 'duration_years' => 4, 'total_credit_hours' => 120],
            ['name' => 'Bachelor of Laws', 'code' => 'LLB', 'department_id' => $deptLaw->id, 'faculty_id' => $facLaw->id, 'degree_type' => 'bachelor', 'duration_years' => 5, 'total_credit_hours' => 150],
            ['name' => 'Bachelor of Education', 'code' => 'BED', 'department_id' => $deptEd->id, 'faculty_id' => $facEd->id, 'degree_type' => 'bachelor', 'duration_years' => 4, 'total_credit_hours' => 120],
            ['name' => 'Bachelor of Science in Nursing', 'code' => 'BSN', 'department_id' => $deptNur->id, 'faculty_id' => $facHealth->id, 'degree_type' => 'bachelor', 'duration_years' => 4, 'total_credit_hours' => 130],
            ['name' => 'Master of Business Administration', 'code' => 'MBA', 'department_id' => $deptBA->id, 'faculty_id' => $facBusiness->id, 'degree_type' => 'masters', 'duration_years' => 2, 'total_credit_hours' => 60],
            ['name' => 'Master of Science in Computer Science', 'code' => 'MSCS', 'department_id' => $deptCS->id, 'faculty_id' => $facScience->id, 'degree_type' => 'masters', 'duration_years' => 2, 'total_credit_hours' => 60],
            ['name' => 'Diploma in Business Administration', 'code' => 'DBA', 'department_id' => $deptBA->id, 'faculty_id' => $facBusiness->id, 'degree_type' => 'diploma', 'duration_years' => 2, 'total_credit_hours' => 60],
        ];

        foreach ($programs as $prog) {
            Program::firstOrCreate(['code' => $prog['code']], array_merge($prog, ['status' => 'active']));
        }

        // Courses
        $courses = [
            // CS Courses
            ['name' => 'Introduction to Programming', 'code' => 'CS101', 'department_id' => $deptCS->id, 'credit_hours' => 3, 'level' => '100', 'type' => 'compulsory'],
            ['name' => 'Data Structures and Algorithms', 'code' => 'CS201', 'department_id' => $deptCS->id, 'credit_hours' => 3, 'level' => '200', 'type' => 'compulsory'],
            ['name' => 'Database Management Systems', 'code' => 'CS301', 'department_id' => $deptCS->id, 'credit_hours' => 3, 'level' => '300', 'type' => 'compulsory'],
            ['name' => 'Software Engineering', 'code' => 'CS302', 'department_id' => $deptCS->id, 'credit_hours' => 3, 'level' => '300', 'type' => 'compulsory'],
            ['name' => 'Computer Networks', 'code' => 'CS303', 'department_id' => $deptCS->id, 'credit_hours' => 3, 'level' => '300', 'type' => 'compulsory'],
            ['name' => 'Artificial Intelligence', 'code' => 'CS401', 'department_id' => $deptCS->id, 'credit_hours' => 3, 'level' => '400', 'type' => 'elective'],
            ['name' => 'Web Development', 'code' => 'CS304', 'department_id' => $deptCS->id, 'credit_hours' => 3, 'level' => '300', 'type' => 'elective'],
            // Math Courses
            ['name' => 'Calculus I', 'code' => 'MTH101', 'department_id' => $deptMath->id, 'credit_hours' => 3, 'level' => '100', 'type' => 'compulsory'],
            ['name' => 'Calculus II', 'code' => 'MTH102', 'department_id' => $deptMath->id, 'credit_hours' => 3, 'level' => '100', 'type' => 'compulsory'],
            ['name' => 'Linear Algebra', 'code' => 'MTH201', 'department_id' => $deptMath->id, 'credit_hours' => 3, 'level' => '200', 'type' => 'compulsory'],
            ['name' => 'Statistics and Probability', 'code' => 'MTH202', 'department_id' => $deptMath->id, 'credit_hours' => 3, 'level' => '200', 'type' => 'compulsory'],
            // Business Courses
            ['name' => 'Principles of Management', 'code' => 'BA101', 'department_id' => $deptBA->id, 'credit_hours' => 3, 'level' => '100', 'type' => 'compulsory'],
            ['name' => 'Business Communication', 'code' => 'BA102', 'department_id' => $deptBA->id, 'credit_hours' => 2, 'level' => '100', 'type' => 'compulsory'],
            ['name' => 'Marketing Management', 'code' => 'BA201', 'department_id' => $deptBA->id, 'credit_hours' => 3, 'level' => '200', 'type' => 'compulsory'],
            ['name' => 'Financial Accounting', 'code' => 'ACC101', 'department_id' => $deptAcc->id, 'credit_hours' => 3, 'level' => '100', 'type' => 'compulsory'],
            ['name' => 'Management Accounting', 'code' => 'ACC201', 'department_id' => $deptAcc->id, 'credit_hours' => 3, 'level' => '200', 'type' => 'compulsory'],
            // Law Courses
            ['name' => 'Introduction to Law', 'code' => 'LAW101', 'department_id' => $deptLaw->id, 'credit_hours' => 3, 'level' => '100', 'type' => 'compulsory'],
            ['name' => 'Constitutional Law', 'code' => 'LAW201', 'department_id' => $deptLaw->id, 'credit_hours' => 3, 'level' => '200', 'type' => 'compulsory'],
            // Nursing Courses
            ['name' => 'Anatomy and Physiology', 'code' => 'NUR101', 'department_id' => $deptNur->id, 'credit_hours' => 4, 'level' => '100', 'type' => 'compulsory'],
            ['name' => 'Fundamentals of Nursing', 'code' => 'NUR102', 'department_id' => $deptNur->id, 'credit_hours' => 3, 'level' => '100', 'type' => 'compulsory'],
        ];

        foreach ($courses as $course) {
            Course::firstOrCreate(['code' => $course['code']], array_merge($course, ['status' => 'active', 'semester_offered' => 'both']));
        }

        // Create Staff Records
        $deanUser = User::where('email', 'dean.science@university.com')->first();
        if ($deanUser && !Staff::where('user_id', $deanUser->id)->exists()) {
            $staff = Staff::create([
                'user_id' => $deanUser->id,
                'staff_id' => 'STF/2024/0001',
                'first_name' => 'David',
                'last_name' => 'Phiri',
                'faculty_id' => $facScience->id,
                'department_id' => $deptCS->id,
                'position' => 'Dean',
                'staff_type' => 'academic',
                'employment_type' => 'full_time',
                'hire_date' => '2020-01-15',
                'qualification' => 'PhD Computer Science',
                'status' => 'active',
            ]);
            $facScience->update(['dean_id' => $staff->id]);
        }

        $lecturerUser = User::where('email', 'lecturer@university.com')->first();
        if ($lecturerUser && !Staff::where('user_id', $lecturerUser->id)->exists()) {
            Staff::create([
                'user_id' => $lecturerUser->id,
                'staff_id' => 'STF/2024/0002',
                'first_name' => 'Mary',
                'last_name' => 'Tembo',
                'faculty_id' => $facScience->id,
                'department_id' => $deptCS->id,
                'position' => 'Senior Lecturer',
                'staff_type' => 'academic',
                'employment_type' => 'full_time',
                'hire_date' => '2022-08-01',
                'qualification' => 'MSc Computer Science',
                'status' => 'active',
            ]);
        }

        // Create demo student
        $studentUser = User::where('email', 'student@university.com')->first();
        $bscsProgram = Program::where('code', 'BSCS')->first();
        if ($studentUser && $bscsProgram && !Student::where('user_id', $studentUser->id)->exists()) {
            Student::create([
                'user_id' => $studentUser->id,
                'student_id' => 'STU/2024/0001',
                'first_name' => 'John',
                'last_name' => 'Chilemba',
                'dob' => '2002-03-15',
                'gender' => 'male',
                'nationality' => 'Zambian',
                'phone' => '+260 977 000001',
                'faculty_id' => $facScience->id,
                'department_id' => $deptCS->id,
                'program_id' => $bscsProgram->id,
                'academic_year_id' => $ay2024->id,
                'current_level' => 1,
                'admission_date' => '2024-08-01',
                'status' => 'active',
                'admission_type' => 'regular',
            ]);
        }

        // Announcements
        $adminUser = User::where('email', 'admin@university.com')->first();
        if ($adminUser) {
            $announcements = [
                ['title' => 'Welcome to 2024/2025 Academic Year', 'content' => 'We are delighted to welcome all students to the 2024/2025 academic year. Please ensure you complete your course registration before the deadline.', 'category' => 'general'],
                ['title' => 'Course Registration Now Open', 'content' => 'Course registration for Semester 1 2024/2025 is now open. Students are required to register for courses online through the student portal by August 15, 2024.', 'category' => 'academic'],
                ['title' => 'Fee Payment Deadline', 'content' => 'All students are reminded to clear their tuition fees before September 1, 2024. Students with outstanding balances may be deregistered from courses.', 'category' => 'finance'],
            ];
            foreach ($announcements as $ann) {
                Announcement::firstOrCreate(
                    ['title' => $ann['title']],
                    array_merge($ann, ['published_by' => $adminUser->id, 'is_published' => true, 'published_at' => now(), 'target_audience' => ['all']])
                );
            }
        }

        $this->command->info('Academic data seeded successfully.');
    }
}
