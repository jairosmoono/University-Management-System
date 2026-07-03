<?php
namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\ELearningCourse;
use App\Models\ELearningLesson;
use App\Models\ELearningLessonItem;
use App\Models\ELearningLessonCompletion;
use App\Models\ELearningQuiz;
use App\Models\ELearningQuestion;
use App\Models\ELearningQuestionOption;
use App\Models\ELearningQuizAttempt;
use App\Models\CourseOffering;
use App\Models\CourseRegistration;
use App\Models\Staff;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ELearningController extends Controller
{
    // ─── Shared index ───────────────────────────────────────────────────────────

    public function index()
    {
        if (auth()->user()->hasRole('student')) {
            return $this->studentIndex();
        }
        if (auth()->user()->hasRole('super-admin|registrar')) {
            return $this->adminIndex();
        }
        return $this->lecturerIndex();
    }

    private function adminIndex()
    {
        $courses = ELearningCourse::with([
            'courseOffering.course.department.faculty',
            'courseOffering.semester',
            'courseOffering.lecturer.user',
        ])
        ->withCount(['lessons', 'quizzes'])
        ->latest()
        ->get();

        $courses->each(function ($c) {
            $c->enrolled_count    = CourseRegistration::where('course_offering_id', $c->course_offering_id)->count();
            $c->completions_count = ELearningLessonCompletion::whereHas('lesson', fn($q) => $q->where('elearning_course_id', $c->id))->count();
            $c->attempts_count    = ELearningQuizAttempt::whereHas('quiz', fn($q) => $q->where('elearning_course_id', $c->id))->count();
            $c->passed_count      = ELearningQuizAttempt::whereHas('quiz', fn($q) => $q->where('elearning_course_id', $c->id))->where('passed', true)->count();
        });

        $totalAttempts = ELearningQuizAttempt::count();
        $stats = [
            'total_courses'      => $courses->count(),
            'published'          => $courses->where('is_published', true)->count(),
            'draft'              => $courses->where('is_published', false)->count(),
            'total_lessons'      => ELearningLesson::count(),
            'total_quizzes'      => ELearningQuiz::count(),
            'total_completions'  => ELearningLessonCompletion::count(),
            'total_attempts'     => $totalAttempts,
            'pass_rate'          => $totalAttempts > 0
                ? round(ELearningQuizAttempt::where('passed', true)->count() / $totalAttempts * 100, 1)
                : 0,
        ];

        return view('elearning.admin.index', compact('courses', 'stats'));
    }

    private function lecturerIndex()
    {
        $staff = Staff::where('user_id', auth()->id())->first();

        $offerings = CourseOffering::with(['course', 'semester', 'elearningCourse'])
            ->where('lecturer_id', $staff?->id)
            ->latest()
            ->get();

        $stats = [
            'total_courses'    => $offerings->count(),
            'with_elearning'   => $offerings->filter(fn($o) => $o->elearningCourse !== null)->count(),
            'published'        => $offerings->filter(fn($o) => $o->elearningCourse?->is_published)->count(),
            'total_lessons'    => ELearningLesson::whereHas('course.courseOffering', fn($q) => $q->where('lecturer_id', $staff?->id))->count(),
        ];

        return view('elearning.index', compact('offerings', 'stats'));
    }

    private function studentIndex()
    {
        $student = Student::where('user_id', auth()->id())->firstOrFail();

        $registrations = CourseRegistration::with(['courseOffering.course', 'courseOffering.elearningCourse'])
            ->where('student_id', $student->id)
            ->get();

        $courses = $registrations
            ->filter(fn($r) => $r->courseOffering?->elearningCourse !== null)
            ->map(function ($r) use ($student) {
                $elCourse = $r->courseOffering->elearningCourse;
                return [
                    'course'   => $elCourse,
                    'offering' => $r->courseOffering,
                    'progress' => $elCourse->getProgressForStudent($student->id),
                ];
            })
            ->values();

        return view('elearning.index', compact('courses'));
    }

    // ─── Lecturer: Create / Manage Course ───────────────────────────────────────

    public function create()
    {
        $staff = Staff::where('user_id', auth()->id())->first();

        $existingIds = ELearningCourse::pluck('course_offering_id');
        $offerings   = CourseOffering::with(['course', 'semester'])
            ->where('lecturer_id', $staff?->id)
            ->whereNotIn('id', $existingIds)
            ->get();

        return view('elearning.create', compact('offerings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_offering_id' => 'required|exists:course_offerings,id|unique:elearning_courses',
            'description'        => 'nullable|string',
            'is_published'       => 'nullable|boolean',
        ]);

        $this->assertLecturerOwnsOffering($validated['course_offering_id']);

        $course = ELearningCourse::create([
            ...$validated,
            'is_published' => $request->boolean('is_published'),
            'created_by'   => auth()->id(),
        ]);

        return redirect()->route('elearning.show', $course)->with('success', 'E-Learning course created.');
    }

    public function show(ELearningCourse $eLearningCourse)
    {
        if (auth()->user()->hasRole('student')) {
            return $this->studentCourse($eLearningCourse);
        }
        $isAdmin = auth()->user()->hasRole('super-admin|registrar');
        return $this->lecturerCourse($eLearningCourse, $isAdmin);
    }

    private function lecturerCourse(ELearningCourse $course, bool $isAdmin = false)
    {
        if (!$isAdmin) $this->assertLecturerOwnsOffering($course->course_offering_id);

        $course->load([
            'courseOffering.course',
            'courseOffering.semester',
            'lessons.items',
            'quizzes.questions.options',
        ]);

        $enrolledCount = CourseRegistration::where('course_offering_id', $course->course_offering_id)->count();
        $attemptsCount = ELearningQuizAttempt::whereHas('quiz', fn($q) => $q->where('elearning_course_id', $course->id))->count();
        $completionsCount = ELearningLessonCompletion::whereHas('lesson', fn($q) => $q->where('elearning_course_id', $course->id))->count();

        return view('elearning.show', compact('course', 'enrolledCount', 'attemptsCount', 'completionsCount', 'isAdmin'));
    }

    private function studentCourse(ELearningCourse $course)
    {
        $student = Student::where('user_id', auth()->id())->firstOrFail();

        abort_unless(
            CourseRegistration::where('student_id', $student->id)
                ->where('course_offering_id', $course->course_offering_id)
                ->exists(),
            403, 'You are not enrolled in this course.'
        );

        $course->load(['courseOffering.course', 'courseOffering.semester', 'lessons.items', 'quizzes']);

        $completedLessonIds = ELearningLessonCompletion::where('student_id', $student->id)
            ->whereHas('lesson', fn($q) => $q->where('elearning_course_id', $course->id))
            ->pluck('lesson_id')
            ->toArray();

        $quizAttempts = ELearningQuizAttempt::where('student_id', $student->id)
            ->whereHas('quiz', fn($q) => $q->where('elearning_course_id', $course->id))
            ->orderByDesc('attempt_number')
            ->get()
            ->keyBy('quiz_id');

        $progress = $course->getProgressForStudent($student->id);

        return view('elearning.show', compact('course', 'student', 'completedLessonIds', 'quizAttempts', 'progress'));
    }

    public function update(Request $request, ELearningCourse $eLearningCourse)
    {
        $this->assertLecturerOwnsOffering($eLearningCourse->course_offering_id);

        $validated = $request->validate([
            'description'  => 'nullable|string',
            'is_published' => 'nullable|boolean',
        ]);

        $eLearningCourse->update([
            'description'  => $validated['description'] ?? $eLearningCourse->description,
            'is_published' => $request->boolean('is_published'),
        ]);

        return back()->with('success', 'Course updated.');
    }

    public function destroy(ELearningCourse $eLearningCourse)
    {
        $this->assertLecturerOwnsOffering($eLearningCourse->course_offering_id);
        $eLearningCourse->delete();
        return redirect()->route('elearning.index')->with('success', 'E-Learning course deleted.');
    }

    // ─── Lessons ────────────────────────────────────────────────────────────────

    public function storeLesson(Request $request, ELearningCourse $eLearningCourse)
    {
        $this->assertLecturerOwnsOffering($eLearningCourse->course_offering_id);

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'is_published' => 'nullable|boolean',
        ]);

        $order = $eLearningCourse->lessons()->max('sort_order') + 1;

        $eLearningCourse->lessons()->create([
            ...$validated,
            'is_published' => $request->boolean('is_published', true),
            'sort_order'   => $order,
        ]);

        return back()->with('success', 'Lesson added.');
    }

    public function updateLesson(Request $request, ELearningLesson $eLearningLesson)
    {
        $this->assertLecturerOwnsOffering($eLearningLesson->course->course_offering_id);

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'is_published' => 'nullable|boolean',
        ]);

        $eLearningLesson->update([
            ...$validated,
            'is_published' => $request->boolean('is_published', true),
        ]);

        return back()->with('success', 'Lesson updated.');
    }

    public function destroyLesson(ELearningLesson $eLearningLesson)
    {
        $this->assertLecturerOwnsOffering($eLearningLesson->course->course_offering_id);

        // Delete any uploaded PDFs
        foreach ($eLearningLesson->items()->where('content_type', 'pdf_upload')->get() as $item) {
            Storage::disk('local')->delete($item->content);
        }

        $eLearningLesson->delete();
        return back()->with('success', 'Lesson deleted.');
    }

    // ─── Lesson Items ────────────────────────────────────────────────────────────

    public function storeItem(Request $request, ELearningLesson $eLearningLesson)
    {
        $this->assertLecturerOwnsOffering($eLearningLesson->course->course_offering_id);

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'content_type' => 'required|in:video_url,pdf_upload,text_html,external_link',
            'content_url'  => 'required_if:content_type,video_url|nullable|string|max:1000',
            'content_link' => 'required_if:content_type,external_link|nullable|string|max:1000',
            'content_text' => 'required_if:content_type,text_html|nullable|string',
            'pdf_file'     => 'required_if:content_type,pdf_upload|file|mimes:pdf|max:20480',
        ]);

        $type = $validated['content_type'];

        $content = match($type) {
            'video_url'     => trim($validated['content_url'] ?? ''),
            'external_link' => trim($validated['content_link'] ?? ''),
            'text_html'     => $validated['content_text'] ?? '',
            default         => '',
        };

        if ($type === 'pdf_upload' && $request->hasFile('pdf_file')) {
            $content = $request->file('pdf_file')->store('elearning_pdfs', 'local');
        }

        $order = $eLearningLesson->items()->max('sort_order') + 1;

        $eLearningLesson->items()->create([
            'title'        => $validated['title'],
            'content_type' => $validated['content_type'],
            'content'      => $content,
            'sort_order'   => $order,
        ]);

        return back()->with('success', 'Content item added.');
    }

    public function serveFile(ELearningLessonItem $eLearningLessonItem)
    {
        if ($eLearningLessonItem->content_type !== 'pdf_upload') abort(404);

        if (auth()->user()->hasRole('student')) {
            $student = Student::where('user_id', auth()->id())->firstOrFail();
            abort_unless(
                CourseRegistration::where('student_id', $student->id)
                    ->where('course_offering_id', $eLearningLessonItem->lesson->course->course_offering_id)
                    ->exists(),
                403
            );
        }

        abort_unless(Storage::disk('local')->exists($eLearningLessonItem->content), 404);
        return Storage::disk('local')->response($eLearningLessonItem->content);
    }

    public function destroyItem(ELearningLessonItem $eLearningLessonItem)
    {
        $this->assertLecturerOwnsOffering($eLearningLessonItem->lesson->course->course_offering_id);

        if ($eLearningLessonItem->content_type === 'pdf_upload') {
            Storage::disk('local')->delete($eLearningLessonItem->content);
        }

        $eLearningLessonItem->delete();
        return back()->with('success', 'Item removed.');
    }

    // ─── Quizzes (Lecturer) ──────────────────────────────────────────────────────

    public function createQuiz(ELearningCourse $eLearningCourse)
    {
        $this->assertLecturerOwnsOffering($eLearningCourse->course_offering_id);
        $eLearningCourse->load('courseOffering.course');
        return view('elearning.quiz.create', compact('eLearningCourse'));
    }

    public function storeQuiz(Request $request, ELearningCourse $eLearningCourse)
    {
        $this->assertLecturerOwnsOffering($eLearningCourse->course_offering_id);

        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'time_limit_minutes'  => 'nullable|integer|min:1|max:300',
            'passing_score'       => 'required|integer|min:1|max:100',
            'max_attempts'        => 'required|integer|min:1|max:10',
            'is_published'        => 'nullable|boolean',
        ]);

        $quiz = $eLearningCourse->quizzes()->create([
            ...$validated,
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()->route('elearning.quizzes.show', $quiz)->with('success', 'Quiz created. Now add questions.');
    }

    public function showQuiz(ELearningQuiz $eLearningQuiz)
    {
        if (auth()->user()->hasRole('student')) {
            return redirect()->route('elearning.quizzes.take', $eLearningQuiz);
        }

        $this->assertLecturerOwnsOffering($eLearningQuiz->course->course_offering_id);
        $eLearningQuiz->load(['course.courseOffering.course', 'questions.options']);

        $attemptsCount   = $eLearningQuiz->attempts()->count();
        $passedCount     = $eLearningQuiz->attempts()->where('passed', true)->count();
        $avgScore        = $eLearningQuiz->attempts()->avg('score');
        $recentAttempts  = $eLearningQuiz->attempts()->with('student.user')->latest()->take(10)->get();

        return view('elearning.quiz.show', compact('eLearningQuiz', 'attemptsCount', 'passedCount', 'avgScore', 'recentAttempts'));
    }

    public function storeQuestion(Request $request, ELearningQuiz $eLearningQuiz)
    {
        $this->assertLecturerOwnsOffering($eLearningQuiz->course->course_offering_id);

        $validated = $request->validate([
            'question_text'  => 'required|string',
            'question_type'  => 'required|in:single_choice,true_false',
            'marks'          => 'required|integer|min:1|max:10',
            'options'        => 'required|array|min:2',
            'options.*'      => 'required|string|max:255',
            'correct_option' => 'required|integer',
        ]);

        $order    = $eLearningQuiz->questions()->max('sort_order') + 1;
        $question = $eLearningQuiz->questions()->create([
            'question_text' => $validated['question_text'],
            'question_type' => $validated['question_type'],
            'marks'         => $validated['marks'],
            'sort_order'    => $order,
        ]);

        foreach ($validated['options'] as $idx => $optionText) {
            if (trim($optionText) === '') continue;
            $question->options()->create([
                'option_text' => $optionText,
                'is_correct'  => $idx === (int) $validated['correct_option'],
                'sort_order'  => $idx,
            ]);
        }

        return back()->with('success', 'Question added.');
    }

    public function destroyQuestion(ELearningQuestion $eLearningQuestion)
    {
        $this->assertLecturerOwnsOffering($eLearningQuestion->quiz->course->course_offering_id);
        $eLearningQuestion->delete();
        return back()->with('success', 'Question deleted.');
    }

    public function quizResults(ELearningQuiz $eLearningQuiz)
    {
        $this->assertLecturerOwnsOffering($eLearningQuiz->course->course_offering_id);

        $eLearningQuiz->load(['course.courseOffering.course', 'questions.options']);

        $attempts = $eLearningQuiz->attempts()
            ->with('student.user')
            ->orderByDesc('attempt_number')
            ->get()
            ->groupBy('student_id')
            ->map(fn($group) => $group->sortByDesc('score')->first());

        $passRate   = $attempts->count() > 0
            ? round($attempts->where('passed', true)->count() / $attempts->count() * 100)
            : 0;

        return view('elearning.quiz.results', compact('eLearningQuiz', 'attempts', 'passRate'));
    }

    // ─── Student: Progress & Quizzes ────────────────────────────────────────────

    public function completeLesson(ELearningLesson $eLearningLesson)
    {
        $student = Student::where('user_id', auth()->id())->firstOrFail();

        abort_unless(
            CourseRegistration::where('student_id', $student->id)
                ->where('course_offering_id', $eLearningLesson->course->course_offering_id)
                ->exists(),
            403
        );

        ELearningLessonCompletion::firstOrCreate([
            'student_id' => $student->id,
            'lesson_id'  => $eLearningLesson->id,
        ], ['completed_at' => now()]);

        return back()->with('success', 'Lesson marked as complete!');
    }

    public function takeQuiz(ELearningQuiz $eLearningQuiz)
    {
        $student = Student::where('user_id', auth()->id())->firstOrFail();

        abort_unless(
            CourseRegistration::where('student_id', $student->id)
                ->where('course_offering_id', $eLearningQuiz->course->course_offering_id)
                ->exists(),
            403
        );

        abort_unless($eLearningQuiz->is_published, 404, 'This quiz is not available yet.');
        abort_unless($eLearningQuiz->canStudentAttempt($student->id), 403, 'You have used all your attempts for this quiz.');

        $eLearningQuiz->load(['questions.options', 'course.courseOffering.course']);
        $pastAttempts = $eLearningQuiz->getAttemptsForStudent($student->id);

        return view('elearning.quiz.take', compact('eLearningQuiz', 'student', 'pastAttempts'));
    }

    public function submitQuiz(Request $request, ELearningQuiz $eLearningQuiz)
    {
        $student = Student::where('user_id', auth()->id())->firstOrFail();

        abort_unless(
            CourseRegistration::where('student_id', $student->id)
                ->where('course_offering_id', $eLearningQuiz->course->course_offering_id)
                ->exists(),
            403
        );
        abort_unless($eLearningQuiz->canStudentAttempt($student->id), 403, 'No more attempts allowed.');

        $eLearningQuiz->load('questions.options');

        $answers      = $request->input('answers', []);
        $totalMarks   = 0;
        $earnedMarks  = 0;

        foreach ($eLearningQuiz->questions as $question) {
            $totalMarks += $question->marks;
            $selected = $answers[$question->id] ?? null;
            if ($selected) {
                $correctOption = $question->options()->where('is_correct', true)->first();
                if ($correctOption && (int) $selected === $correctOption->id) {
                    $earnedMarks += $question->marks;
                }
            }
        }

        $score  = $totalMarks > 0 ? round($earnedMarks / $totalMarks * 100, 2) : 0;
        $passed = $score >= $eLearningQuiz->passing_score;

        $attemptNo = $eLearningQuiz->attempts()->where('student_id', $student->id)->count() + 1;

        $attempt = ELearningQuizAttempt::create([
            'student_id'     => $student->id,
            'quiz_id'        => $eLearningQuiz->id,
            'attempt_number' => $attemptNo,
            'score'          => $score,
            'passed'         => $passed,
            'answers'        => $answers,
            'started_at'     => now()->subMinutes(1),
            'submitted_at'   => now(),
        ]);

        return redirect()->route('elearning.quizzes.result', $attempt);
    }

    public function quizAttemptResult(ELearningQuizAttempt $eLearningQuizAttempt)
    {
        $student = Student::where('user_id', auth()->id())->firstOrFail();

        if (!auth()->user()->hasRole('super-admin|registrar')) {
            abort_unless($eLearningQuizAttempt->student_id === $student->id, 403);
        }

        $eLearningQuizAttempt->load(['quiz.questions.options', 'quiz.course.courseOffering.course', 'student.user']);

        return view('elearning.quiz.result', compact('eLearningQuizAttempt'));
    }

    // ─── Admin Actions ───────────────────────────────────────────────────────────

    public function togglePublish(ELearningCourse $eLearningCourse)
    {
        abort_unless(auth()->user()->hasRole('super-admin|registrar'), 403);
        $eLearningCourse->update(['is_published' => !$eLearningCourse->is_published]);
        $label = $eLearningCourse->is_published ? 'published' : 'unpublished';
        return back()->with('success', "Course {$label} successfully.");
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────────

    private function assertLecturerOwnsOffering(int $offeringId): void
    {
        if (auth()->user()->hasRole('super-admin|registrar')) return;

        if (auth()->user()->hasRole('lecturer')) {
            $staff = Staff::where('user_id', auth()->id())->first();
            $offering = CourseOffering::findOrFail($offeringId);
            abort_if($offering->lecturer_id !== $staff?->id, 403, 'You are not assigned to this course.');
        }
    }
}
