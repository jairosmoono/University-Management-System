<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\AcademicSettingController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\Academic\FacultyController;
use App\Http\Controllers\Academic\DepartmentController;
use App\Http\Controllers\Academic\ProgramController;
use App\Http\Controllers\Academic\CourseController;
use App\Http\Controllers\Academic\GradeAppealController;
use App\Http\Controllers\StudentHoldController;
use App\Http\Controllers\DepartmentBudgetController;
use App\Http\Controllers\Academic\AcademicYearController;
use App\Http\Controllers\Academic\SemesterController;
use App\Http\Controllers\Academic\CourseOfferingController;
use App\Http\Controllers\Academic\CourseRegistrationController;
use App\Http\Controllers\Academic\TimetableController;
use App\Http\Controllers\Academic\AttendanceController;
use App\Http\Controllers\Academic\ExaminationController;
use App\Http\Controllers\Academic\ResultController;
use App\Http\Controllers\Academic\GradeController;
use App\Http\Controllers\Academic\GraduationController;
use App\Http\Controllers\Academic\ELearningController;
use App\Http\Controllers\Academic\AssignmentController;
use App\Http\Controllers\Finance\FeeStructureController;
use App\Http\Controllers\Finance\BillingController;
use App\Http\Controllers\Finance\PaymentController;
use App\Http\Controllers\Finance\ScholarshipController;
use App\Http\Controllers\Finance\FinanceReportController;
use App\Http\Controllers\Hostel\HostelController;
use App\Http\Controllers\Hostel\RoomController;
use App\Http\Controllers\Hostel\AllocationController;
use App\Http\Controllers\Library\BookController;
use App\Http\Controllers\Library\BorrowingController;
use App\Http\Controllers\HR\EmployeeController;
use App\Http\Controllers\HR\LeaveController;
use App\Http\Controllers\HR\LeaveTypeController;
use App\Http\Controllers\HR\EmploymentListingController;
use App\Http\Controllers\HR\SalaryAdvanceController;
use App\Http\Controllers\HR\EmployeeAppointmentController;
use App\Http\Controllers\HR\PayrollController;
use App\Http\Controllers\HR\PayrollConfigController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\ResearchController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\EmailNotificationController;
use App\Http\Controllers\Admin\SmsNotificationController;

// Public Routes
use App\Http\Controllers\LandingController;
Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/programs', [LandingController::class, 'programs'])->name('programs');
Route::get('/news', [LandingController::class, 'announcements'])->name('news.public');
Route::get('/jobs', [LandingController::class, 'jobs'])->name('jobs.public');
Route::get('/apply',         [AdmissionController::class, 'publicApply'])->name('apply');
Route::post('/apply',        [AdmissionController::class, 'publicStore'])->name('apply.submit');
Route::get('/apply/success', [AdmissionController::class, 'publicSuccess'])->name('apply.success');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::get('/notifications/preferences', [NotificationController::class, 'preferences'])->name('notifications.preferences');
    Route::post('/notifications/preferences', [NotificationController::class, 'updatePreferences'])->name('notifications.preferences.update');

    // Admin Routes
    Route::middleware(['role:super-admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::resource('roles', RoleController::class);
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('settings/branding', [SettingController::class, 'uploadBranding'])->name('settings.upload-branding');
        Route::post('settings/hero-images', [SettingController::class, 'uploadHeroImages'])->name('settings.hero-images.upload');
        Route::delete('settings/hero-images', [SettingController::class, 'deleteHeroImage'])->name('settings.hero-images.delete');
        Route::post('settings/course-types', [SettingController::class, 'storeCourseType'])->name('settings.course-types.store');
        Route::delete('settings/course-types/{type}', [SettingController::class, 'destroyCourseType'])->name('settings.course-types.destroy');
        Route::get('audit-logs', [SettingController::class, 'auditLogs'])->name('audit-logs');
        Route::get('backup', [BackupController::class, 'index'])->name('backup.index');
        Route::post('backup/create', [BackupController::class, 'create'])->name('backup.create');
        Route::get('backup/download/{file}', [BackupController::class, 'download'])->name('backup.download');

        // Email Notifications
        Route::get('email-notifications', [EmailNotificationController::class, 'index'])->name('email-notifications.index');
        Route::post('email-notifications/send', [EmailNotificationController::class, 'send'])->name('email-notifications.send');
        Route::post('email-notifications/{log}/resend', [EmailNotificationController::class, 'resend'])->name('email-notifications.resend');
        Route::delete('email-notifications/{log}', [EmailNotificationController::class, 'destroy'])->name('email-notifications.destroy');

        // SMS Notifications
        Route::get('sms-notifications', [SmsNotificationController::class, 'index'])->name('sms-notifications.index');
        Route::post('sms-notifications/send', [SmsNotificationController::class, 'send'])->name('sms-notifications.send');

        // Academic Settings
        Route::get('academic-settings', [AcademicSettingController::class, 'index'])->name('academic-settings.index');
        Route::post('academic-settings/grade', [AcademicSettingController::class, 'storeGrade'])->name('academic-settings.grade.store');
        Route::put('academic-settings/grade/{gradeScale}', [AcademicSettingController::class, 'updateGrade'])->name('academic-settings.grade.update');
        Route::delete('academic-settings/grade/{gradeScale}', [AcademicSettingController::class, 'destroyGrade'])->name('academic-settings.grade.destroy');
        Route::post('academic-settings/type', [AcademicSettingController::class, 'storeType'])->name('academic-settings.type.store');
        Route::put('academic-settings/type/{examType}', [AcademicSettingController::class, 'updateType'])->name('academic-settings.type.update');
        Route::delete('academic-settings/type/{examType}', [AcademicSettingController::class, 'destroyType'])->name('academic-settings.type.destroy');
    });

    // Student Management
    Route::resource('students', StudentController::class);
    Route::patch('students/{student}/status', [StudentController::class, 'updateStatus'])->name('students.status');
    Route::get('students/{student}/card', [StudentController::class, 'printCard'])->name('students.card');
    Route::get('students/{student}/transcript', [StudentController::class, 'transcript'])->name('students.transcript');
    Route::get('students/{student}/result-slip', [StudentController::class, 'resultSlip'])->name('students.result-slip');
    Route::post('students/{student}/results', [StudentController::class, 'storeResult'])->name('students.results.store');
    Route::put('students/{student}/results/{result}', [StudentController::class, 'updateResult'])->name('students.results.update');
    Route::post('students/{student}/results/{result}/approve', [StudentController::class, 'approveResult'])->name('students.results.approve');
    Route::post('students/{student}/guardians', [StudentController::class, 'storeGuardian'])->name('students.guardians.store');
    Route::put('students/{student}/guardians/{guardian}', [StudentController::class, 'updateGuardian'])->name('students.guardians.update');
    Route::delete('students/{student}/guardians/{guardian}', [StudentController::class, 'destroyGuardian'])->name('students.guardians.destroy');
    Route::get('students-export', [StudentController::class, 'exportReport'])->name('students.export');
    Route::get('students-import-template', [StudentController::class, 'importTemplate'])->name('students.import-template');
    Route::post('students-bulk-import', [StudentController::class, 'bulkImport'])->name('students.bulk-import');
    Route::post('students-bulk-cards', [StudentController::class, 'bulkPrintCards'])->name('students.bulk-cards');

    // Admissions
    Route::resource('admissions', AdmissionController::class);
    Route::post('admissions/{admission}/approve', [AdmissionController::class, 'approve'])->name('admissions.approve');
    Route::post('admissions/{admission}/reject', [AdmissionController::class, 'reject'])->name('admissions.reject');
    Route::get('admissions/{admission}/letter', [AdmissionController::class, 'admissionLetter'])->name('admissions.letter');

    // Academic Management
    Route::prefix('academic')->name('academic.')->group(function () {
        Route::resource('faculties', FacultyController::class);
        Route::resource('departments', DepartmentController::class);
        Route::resource('programs', ProgramController::class);
        Route::resource('courses', CourseController::class);
        Route::post('courses/{course}/prerequisites', [CourseController::class, 'addPrerequisite'])->name('courses.prerequisites.store');
        Route::delete('courses/{course}/prerequisites/{prerequisite}', [CourseController::class, 'removePrerequisite'])->name('courses.prerequisites.destroy');
        Route::resource('academic-years', AcademicYearController::class);
        Route::post('academic-years/{academicYear}/set-current', [AcademicYearController::class, 'setCurrent'])->name('academic-years.set-current');
        Route::resource('semesters', SemesterController::class);
        Route::post('semesters/{semester}/set-current', [SemesterController::class, 'setCurrent'])->name('semesters.set-current');
        Route::resource('course-offerings', CourseOfferingController::class)
            ->middleware('redirect.students');

        // Course Registration
        Route::get('registrations', [CourseRegistrationController::class, 'index'])->name('registrations.index');
        Route::post('registrations/register', [CourseRegistrationController::class, 'register'])->name('registrations.register');
        Route::delete('registrations/{registration}/drop', [CourseRegistrationController::class, 'drop'])->name('registrations.drop');
        Route::get('registrations/my-courses', [CourseRegistrationController::class, 'myCourses'])->name('registrations.my-courses');
        Route::post('registrations/waitlist/{waitlist}/confirm', [CourseRegistrationController::class, 'confirmWaitlist'])->name('registrations.waitlist.confirm');
        Route::get('registrations/students/{student}/subjects', [CourseRegistrationController::class, 'studentSubjects'])->name('registrations.student.subjects');
        Route::patch('registrations/{registration}/status', [CourseRegistrationController::class, 'updateStatus'])->name('registrations.update-status');

        // Assignments — static routes BEFORE resource to avoid {assignment} swallowing 'my'
        Route::get('assignments/my', [AssignmentController::class, 'myAssignments'])->name('assignments.my');
        Route::get('assignments/{assignment}/submit', [AssignmentController::class, 'submitForm'])->name('assignments.submit-form');
        Route::post('assignments/{assignment}/submit', [AssignmentController::class, 'submit'])->name('assignments.submit');
        Route::post('assignments/{assignment}/publish', [AssignmentController::class, 'publish'])->name('assignments.publish');
        Route::post('assignments/{assignment}/close', [AssignmentController::class, 'close'])->name('assignments.close');
        Route::post('assignments/{assignment}/grade/{submission}', [AssignmentController::class, 'grade'])->name('assignments.grade');
        Route::get('assignments/{assignment}/performance-sheet', [AssignmentController::class, 'performanceSheet'])->name('assignments.performance-sheet');
        Route::resource('assignments', AssignmentController::class);

        // Grade Appeals
        Route::resource('grade-appeals', GradeAppealController::class)->only(['index','create','store','show']);
        Route::post('grade-appeals/{gradeAppeal}/review', [GradeAppealController::class, 'review'])->name('grade-appeals.review');

        // Student Holds
        Route::get('student-holds', [StudentHoldController::class, 'index'])->name('student-holds.index');
        Route::post('student-holds', [StudentHoldController::class, 'store'])->name('student-holds.store');
        Route::post('student-holds/{studentHold}/release', [StudentHoldController::class, 'release'])->name('student-holds.release');
        Route::get('students/{student}/holds', [StudentHoldController::class, 'studentHolds'])->name('student-holds.student');

        // Department Budgets
        Route::resource('budgets', DepartmentBudgetController::class)->only(['index','create','store','show','destroy']);
        Route::post('budgets/{budget}/transaction', [DepartmentBudgetController::class, 'addTransaction'])->name('budgets.transaction');
        Route::post('budgets/{budget}/approve', [DepartmentBudgetController::class, 'approve'])->name('budgets.approve');

        // Timetable
        Route::resource('timetable', TimetableController::class);
        Route::get('timetable/view/{semester}', [TimetableController::class, 'viewSemester'])->name('timetable.view-semester');
        Route::get('timetable/print', [TimetableController::class, 'print'])->name('timetable.print');

        // Attendance
        Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('attendance/take/{offering}', [AttendanceController::class, 'take'])->name('attendance.take');
        Route::get('attendance/by-program/{program}/{offering}', [AttendanceController::class, 'takeByProgram'])->name('attendance.by-program');
        Route::post('attendance/save', [AttendanceController::class, 'save'])->name('attendance.save');
        Route::get('attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');
        Route::get('attendance/student/{student}', [AttendanceController::class, 'studentReport'])->name('attendance.student');

        // Examinations
        Route::resource('examinations', ExaminationController::class);
        Route::get('examinations/{examination}/seating', [ExaminationController::class, 'seatingPlan'])->name('examinations.seating');

        // Grades
        Route::get('grades', [GradeController::class, 'index'])->name('grades.index');
        Route::get('grades/{examination}/entry', [GradeController::class, 'entry'])->name('grades.entry');
        Route::post('grades/save', [GradeController::class, 'save'])->name('grades.save');
        Route::put('grades/{result}', [GradeController::class, 'update'])->name('grades.update');

        // Results
        Route::get('results', [ResultController::class, 'index'])->name('results.index');
        Route::post('results', [ResultController::class, 'store'])->name('results.store');
        Route::get('results/entry/{offering}', [ResultController::class, 'entry'])->name('results.entry');
        Route::post('results/save', [ResultController::class, 'save'])->name('results.save');
        Route::post('results/approve/{result}', [ResultController::class, 'approve'])->name('results.approve');
        Route::post('results/generate-from-grades', [ResultController::class, 'generateFromGrades'])->name('results.generate-from-grades');
        Route::post('results/calculate-gpa', [ResultController::class, 'calculateGpa'])->name('results.calculate-gpa');
        Route::post('results/recalculate-student/{student}/{semester}', [ResultController::class, 'recalculateStudentGpa'])->name('results.recalculate-student');
        Route::put('results/{result}', [ResultController::class, 'update'])->name('results.update');
        Route::get('results/student/{student}', [ResultController::class, 'studentResults'])->name('results.student');
        Route::get('results/gpa/{student}', [ResultController::class, 'gpaReport'])->name('results.gpa');
    });

    // Graduation Management
    Route::prefix('graduation')->name('graduation.')->group(function () {
        Route::get('/', [GraduationController::class, 'index'])->name('index');
        Route::get('/eligible', [GraduationController::class, 'eligible'])->name('eligible');
        Route::get('/apply', [GraduationController::class, 'apply'])->name('apply');
        Route::post('/apply', [GraduationController::class, 'store'])->name('store');
        Route::get('/applications/{application}', [GraduationController::class, 'show'])->name('show');
        Route::post('/applications/{application}/clearance', [GraduationController::class, 'updateClearance'])->name('clearance');
        Route::post('/applications/{application}/review', [GraduationController::class, 'review'])->name('review');
        Route::post('/applications/{application}/approve', [GraduationController::class, 'approve'])->name('approve');
        Route::post('/applications/{application}/reject', [GraduationController::class, 'reject'])->name('reject');
        Route::post('/applications/{application}/graduate', [GraduationController::class, 'markGraduated'])->name('graduate');
        Route::get('/certificate/sample-preview', [GraduationController::class, 'certificateSamplePreview'])->name('certificate.sample');
        Route::get('/applications/{application}/certificate/preview', [GraduationController::class, 'certificatePreview'])->name('certificate.preview');
        Route::get('/applications/{application}/certificate', [GraduationController::class, 'certificate'])->name('certificate');
        Route::delete('/applications/{application}', [GraduationController::class, 'destroy'])->name('destroy');
        Route::get('/ceremonies', [GraduationController::class, 'ceremonies'])->name('ceremonies.index');
        Route::get('/ceremonies/create', [GraduationController::class, 'createCeremony'])->name('ceremonies.create');
        Route::post('/ceremonies', [GraduationController::class, 'storeCeremony'])->name('ceremonies.store');
        Route::get('/ceremonies/{ceremony}', [GraduationController::class, 'showCeremony'])->name('ceremonies.show');
        Route::delete('/ceremonies/{ceremony}', [GraduationController::class, 'destroyCeremony'])->name('ceremonies.destroy');
    });

    // E-Learning
    Route::prefix('elearning')->name('elearning.')->group(function () {
        // Static sub-paths defined BEFORE {eLearningCourse} wildcard to prevent conflicts
        Route::get('/create', [ELearningController::class, 'create'])->name('create');

        Route::put('/lesson/{eLearningLesson}', [ELearningController::class, 'updateLesson'])->name('lessons.update');
        Route::delete('/lesson/{eLearningLesson}', [ELearningController::class, 'destroyLesson'])->name('lessons.destroy');
        Route::post('/lesson/{eLearningLesson}/items', [ELearningController::class, 'storeItem'])->name('items.store');
        Route::post('/lesson/{eLearningLesson}/complete', [ELearningController::class, 'completeLesson'])->name('lessons.complete');

        Route::get('/item/{eLearningLessonItem}/file', [ELearningController::class, 'serveFile'])->name('items.file');
        Route::delete('/item/{eLearningLessonItem}', [ELearningController::class, 'destroyItem'])->name('items.destroy');

        Route::post('/quiz/{eLearningQuiz}/questions', [ELearningController::class, 'storeQuestion'])->name('questions.store');
        Route::delete('/question/{eLearningQuestion}', [ELearningController::class, 'destroyQuestion'])->name('questions.destroy');
        Route::get('/quiz/{eLearningQuiz}/take', [ELearningController::class, 'takeQuiz'])->name('quizzes.take');
        Route::post('/quiz/{eLearningQuiz}/submit', [ELearningController::class, 'submitQuiz'])->name('quizzes.submit');
        Route::get('/quiz/{eLearningQuiz}/results', [ELearningController::class, 'quizResults'])->name('quizzes.results');
        Route::get('/quiz/{eLearningQuiz}', [ELearningController::class, 'showQuiz'])->name('quizzes.show');
        Route::get('/attempt/{eLearningQuizAttempt}', [ELearningController::class, 'quizAttemptResult'])->name('quizzes.result');

        Route::get('/quiz-create/{eLearningCourse}', [ELearningController::class, 'createQuiz'])->name('quizzes.create');
        Route::post('/quiz-create/{eLearningCourse}', [ELearningController::class, 'storeQuiz'])->name('quizzes.store');
        Route::post('/{eLearningCourse}/lessons', [ELearningController::class, 'storeLesson'])->name('lessons.store');
        Route::post('/{eLearningCourse}/toggle-publish', [ELearningController::class, 'togglePublish'])->name('toggle-publish');
        Route::put('/{eLearningCourse}', [ELearningController::class, 'update'])->name('update');
        Route::delete('/{eLearningCourse}', [ELearningController::class, 'destroy'])->name('destroy');
        Route::get('/{eLearningCourse}', [ELearningController::class, 'show'])->name('show');

        Route::get('/', [ELearningController::class, 'index'])->name('index');
        Route::post('/', [ELearningController::class, 'store'])->name('store');
    });

    // Finance Management
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::resource('fee-structures', FeeStructureController::class);
        Route::post('fee-structures/{feeStructure}/clone', [FeeStructureController::class, 'clone'])->name('fee-structures.clone');
        Route::get('fee-structures/{feeStructure}/json', [FeeStructureController::class, 'json'])->name('fee-structures.json');
        Route::get('billing', [BillingController::class, 'index'])->name('billing.index');
        Route::get('billing/student/{student}', [BillingController::class, 'studentBill'])->name('billing.student');
        Route::post('billing/generate', [BillingController::class, 'generate'])->name('billing.generate');
        Route::get('billing/{bill}', [BillingController::class, 'show'])->name('billing.show');
        Route::get('billing/{bill}/invoice', [BillingController::class, 'invoice'])->name('billing.invoice');
        Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('payments/create/{bill?}', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');
        Route::post('payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
        Route::post('payments/{payment}/reverse', [PaymentController::class, 'reverse'])->name('payments.reverse');
        Route::resource('scholarships', ScholarshipController::class);
        Route::post('scholarships/award', [ScholarshipController::class, 'award'])->name('scholarships.award');
        Route::post('scholarships/awards/{award}/revoke', [ScholarshipController::class, 'revoke'])->name('scholarships.revoke');
        Route::delete('scholarships/awards/{award}', [ScholarshipController::class, 'destroyAward'])->name('scholarships.awards.destroy');
        Route::get('reports', [FinanceReportController::class, 'index'])->name('reports.index');
        Route::get('reports/revenue', [FinanceReportController::class, 'collectionReport'])->name('reports.revenue');
        Route::get('reports/collection', [FinanceReportController::class, 'collectionReport'])->name('reports.collection');
        Route::get('reports/outstanding', [FinanceReportController::class, 'outstandingReport'])->name('reports.outstanding');
    });

    // Hostel Management
    Route::prefix('hostel')->name('hostel.')->group(function () {
        Route::resource('hostels', HostelController::class);
        Route::resource('rooms', RoomController::class);
        Route::get('allocations', [AllocationController::class, 'index'])->name('allocations.index');
        Route::post('allocations/assign', [AllocationController::class, 'assign'])->name('allocations.assign');
        Route::post('allocations/{allocation}/checkout', [AllocationController::class, 'checkout'])->name('allocations.checkout');
        Route::get('allocations/occupancy', [AllocationController::class, 'occupancy'])->name('allocations.occupancy');
        Route::get('allocations/occupancy/export', [AllocationController::class, 'occupancyExport'])->name('allocations.occupancy.export');
    });

    // Library Management
    Route::prefix('library')->name('library.')->group(function () {
        Route::resource('books', BookController::class);
        Route::get('borrowings', [BorrowingController::class, 'index'])->name('borrowings.index');
        Route::post('borrowings/issue', [BorrowingController::class, 'issue'])->name('borrowings.issue');
        Route::post('borrowings/{borrowing}/return', [BorrowingController::class, 'return'])->name('borrowings.return');
        Route::post('borrowings/{borrowing}/renew', [BorrowingController::class, 'renew'])->name('borrowings.renew');
        Route::get('borrowings/overdue', [BorrowingController::class, 'overdue'])->name('borrowings.overdue');
        Route::get('borrowings/fines', [BorrowingController::class, 'fines'])->name('borrowings.fines');
        Route::post('borrowings/{borrowing}/collect-fine', [BorrowingController::class, 'collectFine'])->name('borrowings.collect-fine');
        Route::post('borrowings/{borrowing}/waive-fine', [BorrowingController::class, 'waiveFine'])->name('borrowings.waive-fine');
        Route::post('borrowings/{borrowing}/adjust-fine', [BorrowingController::class, 'adjustFine'])->name('borrowings.adjust-fine');
    });

    // HR Management
    Route::prefix('hr')->name('hr.')->group(function () {
        Route::get('employees/bulk-upload', [EmployeeController::class, 'bulkUploadForm'])->name('employees.bulk-upload');
        Route::post('employees/bulk-upload', [EmployeeController::class, 'bulkUpload'])->name('employees.bulk-upload.store');
        Route::get('employees/bulk-upload/template', [EmployeeController::class, 'downloadTemplate'])->name('employees.bulk-upload.template');
        Route::post('employees/{employee}/documents', [EmployeeController::class, 'uploadDocument'])->name('employees.documents.upload');
        Route::delete('employees/documents/{document}', [EmployeeController::class, 'destroyDocument'])->name('employees.documents.destroy');
        Route::resource('employees', EmployeeController::class);
        Route::get('leave', [LeaveController::class, 'index'])->name('leave.index');
        Route::post('leave/apply', [LeaveController::class, 'apply'])->name('leave.apply');
        Route::post('leave/{leave}/approve', [LeaveController::class, 'approve'])->name('leave.approve');
        Route::post('leave/{leave}/reject', [LeaveController::class, 'reject'])->name('leave.reject');
        Route::get('payroll', [PayrollController::class, 'index'])->name('payroll.index');
        Route::get('payroll/report', [PayrollController::class, 'report'])->name('payroll.report');
        Route::get('payroll/salary-schedule', [PayrollController::class, 'salarySchedule'])->name('payroll.salary-schedule');
        Route::post('payroll/generate', [PayrollController::class, 'generate'])->name('payroll.generate');
        Route::post('payroll/{payroll}/process', [PayrollController::class, 'process'])->name('payroll.process');
        Route::get('payroll/{payroll}/slip', [PayrollController::class, 'slip'])->name('payroll.slip');

        // Payroll Config
        Route::get('payroll/config', [PayrollConfigController::class, 'index'])->name('payroll.config');
        Route::post('payroll/config/global', [PayrollConfigController::class, 'updateGlobal'])->name('payroll.config.global');
        Route::post('payroll/config/allowances', [PayrollConfigController::class, 'storeAllowance'])->name('payroll.config.allowances.store');
        Route::put('payroll/config/allowances/{employeeAllowance}', [PayrollConfigController::class, 'updateAllowance'])->name('payroll.config.allowances.update');
        Route::delete('payroll/config/allowances/{employeeAllowance}', [PayrollConfigController::class, 'destroyAllowance'])->name('payroll.config.allowances.destroy');
        Route::post('payroll/config/deductions', [PayrollConfigController::class, 'storeDeduction'])->name('payroll.config.deductions.store');
        Route::put('payroll/config/deductions/{employeeDeduction}', [PayrollConfigController::class, 'updateDeduction'])->name('payroll.config.deductions.update');
        Route::delete('payroll/config/deductions/{employeeDeduction}', [PayrollConfigController::class, 'destroyDeduction'])->name('payroll.config.deductions.destroy');
        Route::post('payroll/config/item-types', [PayrollConfigController::class, 'storeItemType'])->name('payroll.config.item-types.store');
        Route::put('payroll/config/item-types/{payrollItemType}', [PayrollConfigController::class, 'updateItemType'])->name('payroll.config.item-types.update');
        Route::delete('payroll/config/item-types/{payrollItemType}', [PayrollConfigController::class, 'destroyItemType'])->name('payroll.config.item-types.destroy');
        Route::put('payroll/config/bank-accounts/{employee}', [PayrollConfigController::class, 'updateBankAccount'])->name('payroll.config.bank-accounts.update');

        // Leave Types
        Route::get('leave-types', [LeaveTypeController::class, 'index'])->name('leave-types.index');
        Route::post('leave-types', [LeaveTypeController::class, 'store'])->name('leave-types.store');
        Route::put('leave-types/{leaveType}', [LeaveTypeController::class, 'update'])->name('leave-types.update');
        Route::delete('leave-types/{leaveType}', [LeaveTypeController::class, 'destroy'])->name('leave-types.destroy');

        // Employment Listings
        Route::get('employment-listings', [EmploymentListingController::class, 'index'])->name('employment-listings.index');
        Route::post('employment-listings', [EmploymentListingController::class, 'store'])->name('employment-listings.store');
        Route::put('employment-listings/{employmentListing}', [EmploymentListingController::class, 'update'])->name('employment-listings.update');
        Route::delete('employment-listings/{employmentListing}', [EmploymentListingController::class, 'destroy'])->name('employment-listings.destroy');

        // Salary Advances
        Route::get('salary-advances', [SalaryAdvanceController::class, 'index'])->name('salary-advances.index');
        Route::post('salary-advances', [SalaryAdvanceController::class, 'store'])->name('salary-advances.store');
        Route::post('salary-advances/{salaryAdvance}/approve', [SalaryAdvanceController::class, 'approve'])->name('salary-advances.approve');
        Route::post('salary-advances/{salaryAdvance}/reject', [SalaryAdvanceController::class, 'reject'])->name('salary-advances.reject');
        Route::post('salary-advances/{salaryAdvance}/mark-paid', [SalaryAdvanceController::class, 'markPaid'])->name('salary-advances.mark-paid');
        Route::delete('salary-advances/{salaryAdvance}', [SalaryAdvanceController::class, 'destroy'])->name('salary-advances.destroy');

        // Employee Appointments
        Route::get('employee-appointments', [EmployeeAppointmentController::class, 'index'])->name('employee-appointments.index');
        Route::post('employee-appointments', [EmployeeAppointmentController::class, 'store'])->name('employee-appointments.store');
        Route::put('employee-appointments/{employeeAppointment}', [EmployeeAppointmentController::class, 'update'])->name('employee-appointments.update');
        Route::delete('employee-appointments/{employeeAppointment}', [EmployeeAppointmentController::class, 'destroy'])->name('employee-appointments.destroy');
    });

    // Asset Management
    Route::resource('assets', AssetController::class);
    Route::post('assets/{asset}/assign', [AssetController::class, 'assign'])->name('assets.assign');
    Route::post('assets/{asset}/depreciate', [AssetController::class, 'recordDepreciation'])->name('assets.depreciate');
    Route::get('assets-depreciation-report', [AssetController::class, 'depreciationReport'])->name('assets.depreciation-report');

    // Research Management
    Route::resource('research', ResearchController::class);
    Route::post('research/{research}/publications', [ResearchController::class, 'storePublication'])->name('research.publications.store');

    // Research Papers (upload / download)
    Route::get('research-papers',                      [ResearchController::class, 'papers'])->name('research.papers.index');
    Route::get('research-papers/upload',               [ResearchController::class, 'uploadPaper'])->name('research.papers.upload');
    Route::post('research-papers',                     [ResearchController::class, 'storePaper'])->name('research.papers.store');
    Route::get('research-papers/{paper}/download',     [ResearchController::class, 'downloadPaper'])->name('research.papers.download');
    Route::delete('research-papers/{paper}',           [ResearchController::class, 'destroyPaper'])->name('research.papers.destroy');

    // Announcements
    Route::get('announcements/{announcement}/views', [AnnouncementController::class, 'views'])->name('announcements.views');
    Route::post('announcements/{announcement}/publish', [AnnouncementController::class, 'publish'])->name('announcements.publish');
    Route::resource('announcements', AnnouncementController::class);

    // Messages
    Route::get('messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('messages/compose', [MessageController::class, 'compose'])->name('messages.compose');
    Route::post('messages', [MessageController::class, 'send'])->name('messages.send');
    Route::post('messages/{message}/reply', [MessageController::class, 'reply'])->name('messages.reply');
    Route::get('messages/{message}', [MessageController::class, 'show'])->name('messages.show');
    Route::delete('messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');

    // Documents
    Route::resource('documents', DocumentController::class);
    Route::get('documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');

    // Support Tickets
    Route::resource('support', SupportTicketController::class)->parameters(['support' => 'ticket']);
    Route::post('support/{ticket}/respond', [SupportTicketController::class, 'respond'])->name('support.respond');
    Route::post('support/{ticket}/close', [SupportTicketController::class, 'close'])->name('support.close');
    Route::post('support/{ticket}/assign', [SupportTicketController::class, 'assign'])->name('support.assign');
    Route::post('support/{ticket}/update-status', [SupportTicketController::class, 'updateStatus'])->name('support.update-status');

    // Alumni
    Route::resource('alumni', AlumniController::class);

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/students', [ReportController::class, 'students'])->name('reports.students');
    Route::get('reports/academic', [ReportController::class, 'academic'])->name('reports.academic');
    Route::get('reports/finance', [ReportController::class, 'finance'])->name('reports.finance');
    Route::get('reports/attendance', [ReportController::class, 'attendance'])->name('reports.attendance');
    Route::get('reports/hostel', [ReportController::class, 'hostel'])->name('reports.hostel');
    Route::get('reports/admissions', [ReportController::class, 'admissions'])->name('reports.admissions');
    Route::get('reports/scholarships', [ReportController::class, 'scholarships'])->name('reports.scholarships');
    Route::get('reports/export/{type}', [ReportController::class, 'export'])->name('reports.export');
    Route::get('reports/login-activity', [ReportController::class, 'loginActivity'])->name('reports.login-activity');
});

// API Routes for AJAX
Route::middleware(['auth'])->prefix('ajax')->name('ajax.')->group(function () {
    Route::get('students', [StudentController::class, 'ajaxSearch'])->name('students');
    Route::get('students/search', [StudentController::class, 'search'])->name('students.search');
    Route::get('student-bills', [\App\Http\Controllers\Finance\BillingController::class, 'ajaxStudentBill'])->name('student-bills');
    Route::get('courses/by-program/{program}', [CourseController::class, 'byProgram'])->name('courses.by-program');
    Route::get('offerings/by-program/{program}', [AttendanceController::class, 'offeringsByProgram'])->name('offerings.by-program');
    Route::get('course-offerings', [\App\Http\Controllers\Academic\CourseOfferingController::class, 'ajaxBySemester'])->name('course-offerings.ajax');
    Route::get('departments/by-faculty/{faculty}', [DepartmentController::class, 'byFaculty'])->name('departments.by-faculty');
    Route::get('programs/by-department/{department}', [ProgramController::class, 'byDepartment'])->name('programs.by-department');
    Route::get('programs', [ProgramController::class, 'byFaculty'])->name('programs.by-faculty');
    Route::get('rooms/available/{hostel}', [RoomController::class, 'available'])->name('rooms.available');
    Route::get('dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');
    Route::get('notifications/count', [NotificationController::class, 'count'])->name('notifications.count');
});
