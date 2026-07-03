<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.university.name', 'University Management System') }}</title>
    @php
        $brandingSettings = file_exists(storage_path('app/settings.json'))
            ? (json_decode(file_get_contents(storage_path('app/settings.json')), true) ?? [])
            : [];
    @endphp
    @if(!empty($brandingSettings['favicon_path']))
    <link rel="icon" href="{{ asset('storage/' . $brandingSettings['favicon_path']) }}" type="image/x-icon">
    @endif

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary: #0B1F3A;
            --primary-light: #1a3a6b;
            --secondary: #8B0000;
            --secondary-light: #a01010;
            --sidebar-width: 260px;
            --topbar-height: 60px;
        }
        [data-bs-theme="light"] {
            --bg-sidebar: #0B1F3A;
            --bg-topbar: #ffffff;
            --bg-main: #f0f4f8;
            --text-sidebar: rgba(255,255,255,0.85);
            --text-sidebar-hover: #ffffff;
            --card-bg: #ffffff;
            --border-color: #dee2e6;
        }
        [data-bs-theme="dark"] {
            --bg-sidebar: #060e1a;
            --bg-topbar: #1a1d23;
            --bg-main: #111318;
            --text-sidebar: rgba(255,255,255,0.75);
            --text-sidebar-hover: #ffffff;
            --card-bg: #1e2130;
            --border-color: #333;
        }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: var(--bg-main); }
        /* === SIDEBAR === */
        #sidebar {
            position: fixed; top: 0; left: 0; height: 100vh;
            width: var(--sidebar-width); background: var(--bg-sidebar);
            z-index: 1040; transition: all 0.3s ease; overflow-y: auto; overflow-x: hidden;
            scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.1) transparent;
        }
        #sidebar::-webkit-scrollbar { width: 4px; }
        #sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 4px; }
        .sidebar-brand {
            padding: 1rem 1.25rem; display: flex; align-items: center; gap: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            background: rgba(0,0,0,0.2);
        }
        .sidebar-brand-logo {
            width: 38px; height: 38px; background: var(--secondary);
            border-radius: 8px; display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 1.1rem; color: white; flex-shrink: 0;
        }
        .sidebar-brand-text { color: white; font-weight: 700; font-size: 0.9rem; line-height: 1.2; }
        .sidebar-brand-text small { font-weight: 400; opacity: 0.7; font-size: 0.75rem; display: block; }
        .sidebar-section { padding: 0.5rem 0; }
        .sidebar-label {
            font-size: 0.65rem; font-weight: 700; letter-spacing: 0.1em;
            text-transform: uppercase; color: rgba(255,255,255,0.4);
            padding: 0.75rem 1.25rem 0.25rem;
        }
        .sidebar-item { list-style: none; padding: 0; margin: 0; }
        .sidebar-link {
            display: flex; align-items: center; gap: 10px; padding: 0.6rem 1.25rem;
            color: var(--text-sidebar); text-decoration: none; font-size: 0.875rem;
            border-left: 3px solid transparent; transition: all 0.2s;
            white-space: nowrap;
        }
        .sidebar-link:hover { color: var(--text-sidebar-hover); background: rgba(255,255,255,0.07); border-left-color: rgba(255,255,255,0.3); }
        .sidebar-link.active { color: white; background: rgba(139,0,0,0.3); border-left-color: var(--secondary); }
        .sidebar-link .bi { font-size: 1rem; flex-shrink: 0; width: 18px; }
        .sidebar-link .badge { margin-left: auto; font-size: 0.65rem; }
        /* Submenu */
        .sidebar-submenu { list-style: none; padding: 0; background: rgba(0,0,0,0.15); display: none; }
        .sidebar-submenu.show { display: block; }
        .sidebar-submenu .sidebar-link { padding-left: 3rem; font-size: 0.8rem; }
        .sidebar-toggle-arrow { margin-left: auto; transition: transform 0.2s; font-size: 0.75rem; }
        .sidebar-link[aria-expanded="true"] .sidebar-toggle-arrow { transform: rotate(90deg); }
        /* === TOPBAR === */
        #topbar {
            position: fixed; top: 0; left: var(--sidebar-width); right: 0;
            height: var(--topbar-height); background: var(--bg-topbar);
            border-bottom: 1px solid var(--border-color); z-index: 1030;
            display: flex; align-items: center; padding: 0 1.5rem;
            gap: 1rem; transition: left 0.3s;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        }
        #topbar .topbar-title { font-weight: 600; font-size: 1rem; flex: 1; }
        /* === MAIN CONTENT === */
        #main-content {
            margin-left: var(--sidebar-width); margin-top: var(--topbar-height);
            padding: 1.5rem; min-height: calc(100vh - var(--topbar-height));
            transition: margin-left 0.3s;
        }
        /* === SIDEBAR COLLAPSED === */
        body.sidebar-collapsed #sidebar { width: 0; overflow: hidden; }
        body.sidebar-collapsed #topbar { left: 0; }
        body.sidebar-collapsed #main-content { margin-left: 0; }
        /* === CARDS === */
        .card { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
        .stat-card { border-radius: 12px; overflow: hidden; transition: transform 0.2s, box-shadow 0.2s; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.12); }
        .stat-card .stat-icon { width: 52px; height: 52px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .stat-card .stat-value { font-size: 1.75rem; font-weight: 700; line-height: 1; }
        .stat-card .stat-label { font-size: 0.8rem; opacity: 0.75; }
        .stat-card .stat-change { font-size: 0.75rem; }
        /* === BREADCRUMB === */
        .page-header { margin-bottom: 1.5rem; }
        .page-header h1 { font-size: 1.4rem; font-weight: 700; margin: 0; }
        /* === TABLES === */
        .table thead th { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; border-top: none; }
        /* === ALERTS === */
        .alert { border-radius: 10px; border: none; }
        /* === AVATAR === */
        .avatar { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; }
        .avatar-sm { width: 28px; height: 28px; }
        .avatar-lg { width: 64px; height: 64px; }
        /* === BADGE === */
        .status-badge { font-size: 0.7rem; padding: 0.25em 0.6em; border-radius: 20px; font-weight: 600; }
        /* === BUTTONS === */
        .btn-primary { background-color: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background-color: var(--primary-light); border-color: var(--primary-light); }
        .btn-danger { background-color: var(--secondary); border-color: var(--secondary); }
        /* === SIDEBAR OVERLAY (mobile) === */
        #sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1039; }
        @media (max-width: 991.98px) {
            #sidebar { transform: translateX(-100%); width: var(--sidebar-width) !important; }
            #topbar { left: 0 !important; }
            #main-content { margin-left: 0 !important; }
            body.mobile-sidebar-open #sidebar { transform: translateX(0); }
            body.mobile-sidebar-open #sidebar-overlay { display: block; }
        }
        /* === NOTIFICATION DROPDOWN === */
        .notification-item { padding: 0.75rem 1rem; border-bottom: 1px solid var(--border-color); transition: background 0.15s; cursor: pointer; }
        .notification-item:hover { background: rgba(0,0,0,0.04); }
        .notification-item.unread { background: rgba(11,31,58,0.05); }
        .notification-dot { width: 8px; height: 8px; background: var(--secondary); border-radius: 50%; flex-shrink: 0; }
        /* === DARK MODE TRANSITION === */
        *, *::before, *::after { transition: background-color 0.2s, border-color 0.2s, color 0.2s; }
    </style>
    @stack('styles')
</head>
<body>
<!-- Sidebar Overlay (mobile) -->
<div id="sidebar-overlay" onclick="closeMobileSidebar()"></div>

<!-- ===== SIDEBAR ===== -->
<nav id="sidebar">
    <div class="sidebar-brand">
        @if(!empty($brandingSettings['logo_path']))
            <img src="{{ asset('storage/' . $brandingSettings['logo_path']) }}" alt="Logo" style="height:42px;max-width:42px;object-fit:contain;flex-shrink:0;border-radius:6px;background:#fff;padding:3px;">
        @else
            <div class="sidebar-brand-logo">{{ strtoupper(substr($brandingSettings['university_short_name'] ?? config('app.university.short_name', 'U'), 0, 2)) }}</div>
        @endif
        <div class="sidebar-brand-text">
            {{ $brandingSettings['university_short_name'] ?? config('app.university.short_name', 'UMS') }}
            <small>Management System</small>
        </div>
    </div>

    <div class="sidebar-section">
        <ul class="sidebar-item">
            <li>
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
        </ul>
    </div>

    {{-- ── STUDENT: My Academics ────────────────────────────────────────────── --}}
    @hasrole('student')
    <div class="sidebar-section">
        <div class="sidebar-label">My Academics</div>
        <ul class="sidebar-item">
            <li>
                <a href="{{ route('academic.registrations.index') }}" class="sidebar-link {{ request()->routeIs('academic.registrations.*') ? 'active' : '' }}">
                    <i class="bi bi-pencil-square"></i> My Registrations
                </a>
            </li>
            <li>
                <a href="{{ route('academic.assignments.my') }}" class="sidebar-link {{ request()->routeIs('academic.assignments.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-check"></i> Assignments
                </a>
            </li>
            <li>
                <a href="{{ route('academic.timetable.index') }}" class="sidebar-link {{ request()->routeIs('academic.timetable.*') ? 'active' : '' }}">
                    <i class="bi bi-clock"></i> My Timetable
                </a>
            </li>
            <li>
                <a href="{{ route('academic.attendance.index') }}" class="sidebar-link {{ request()->routeIs('academic.attendance.*') ? 'active' : '' }}">
                    <i class="bi bi-person-check"></i> My Attendance
                </a>
            </li>
            <li>
                <a href="{{ route('academic.examinations.index') }}" class="sidebar-link {{ request()->routeIs('academic.examinations.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i> Examinations
                </a>
            </li>
            <li>
                <a href="{{ route('academic.results.index') }}" class="sidebar-link {{ request()->routeIs('academic.results.*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart"></i> My Results
                </a>
            </li>
            @php $pendingAppeals = \App\Models\GradeAppeal::where('status', 'pending')->count(); @endphp
            <li>
                <a href="{{ route('academic.grade-appeals.index') }}" class="sidebar-link {{ request()->routeIs('academic.grade-appeals.*') ? 'active' : '' }}">
                    <i class="bi bi-flag"></i> Grade Appeals
                    @if($pendingAppeals > 0)
                    <span class="badge bg-danger ms-auto" style="font-size:0.65rem">{{ $pendingAppeals > 99 ? '99+' : $pendingAppeals }}</span>
                    @endif
                </a>
            </li>
            <li>
                <a href="{{ route('academic.courses.index') }}" class="sidebar-link {{ request()->routeIs('academic.courses.*') ? 'active' : '' }}">
                    <i class="bi bi-book"></i> Course Catalogue
                </a>
            </li>
            <li>
                <a href="{{ route('elearning.index') }}" class="sidebar-link {{ request()->routeIs('elearning.*') ? 'active' : '' }}">
                    <i class="bi bi-laptop"></i> E-Learning
                </a>
            </li>
        </ul>
    </div>
    <div class="sidebar-section">
        <div class="sidebar-label">My Finance</div>
        <ul class="sidebar-item">
            <li>
                <a href="{{ route('finance.billing.index') }}" class="sidebar-link {{ request()->routeIs('finance.billing.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i> My Bills
                </a>
            </li>
            <li>
                <a href="{{ route('finance.payments.index') }}" class="sidebar-link {{ request()->routeIs('finance.payments.*') ? 'active' : '' }}">
                    <i class="bi bi-credit-card"></i> Payment History
                </a>
            </li>
        </ul>
    </div>
    <div class="sidebar-section">
        <div class="sidebar-label">Graduation</div>
        <ul class="sidebar-item">
            <li>
                <a href="{{ route('graduation.index') }}" class="sidebar-link {{ request()->routeIs('graduation.*') ? 'active' : '' }}">
                    <i class="bi bi-mortarboard"></i> My Graduation
                </a>
            </li>
        </ul>
    </div>
    @endhasrole

    @hasrole('super-admin|registrar')
    <div class="sidebar-section">
        <div class="sidebar-label">Students</div>
        <ul class="sidebar-item">
            <li>
                <a href="{{ route('students.index') }}" class="sidebar-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge"></i> Students
                </a>
            </li>
            <li>
                <a href="{{ route('academic.student-holds.index') }}" class="sidebar-link {{ request()->routeIs('academic.student-holds.*') ? 'active' : '' }}">
                    <i class="bi bi-slash-circle"></i> Student Holds
                </a>
            </li>
            <li>
                <a href="{{ route('admissions.index') }}" class="sidebar-link {{ request()->routeIs('admissions.*') ? 'active' : '' }}">
                    <i class="bi bi-person-plus"></i> Admissions
                    @if(!empty($pendingAdmissions) && $pendingAdmissions > 0)
                    <span class="badge bg-warning text-dark rounded-pill ms-auto">{{ $pendingAdmissions > 99 ? '99+' : $pendingAdmissions }}</span>
                    @endif
                </a>
            </li>
            <li>
                <a href="{{ route('alumni.index') }}" class="sidebar-link {{ request()->routeIs('alumni.*') ? 'active' : '' }}">
                    <i class="bi bi-person-lines-fill"></i> Alumni
                </a>
            </li>
            <li>
                <a href="{{ route('graduation.index') }}" class="sidebar-link {{ request()->routeIs('graduation.*') ? 'active' : '' }}">
                    <i class="bi bi-mortarboard"></i> Graduation
                </a>
            </li>
        </ul>
    </div>
    @endhasrole

    {{-- ── LECTURER: My Teaching ─────────────────────────────────────────── --}}
    @hasrole('lecturer')
    <div class="sidebar-section">
        <div class="sidebar-label">My Teaching</div>
        <ul class="sidebar-item">
            <li>
                <a href="{{ route('academic.course-offerings.index') }}" class="sidebar-link {{ request()->routeIs('academic.course-offerings.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-bookmark-fill"></i> My Courses
                </a>
            </li>
            <li>
                <a href="{{ route('academic.assignments.index') }}" class="sidebar-link {{ request()->routeIs('academic.assignments.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-check"></i> Assignments
                </a>
            </li>
            <li>
                <a href="{{ route('academic.timetable.index') }}" class="sidebar-link {{ request()->routeIs('academic.timetable.*') ? 'active' : '' }}">
                    <i class="bi bi-clock"></i> My Timetable
                </a>
            </li>
            <li>
                <a href="{{ route('academic.registrations.index') }}" class="sidebar-link {{ request()->routeIs('academic.registrations.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> My Students
                </a>
            </li>
            <li>
                <a href="{{ route('academic.attendance.index') }}" class="sidebar-link {{ request()->routeIs('academic.attendance.*') ? 'active' : '' }}">
                    <i class="bi bi-person-check"></i> Attendance
                </a>
            </li>
            <li>
                <a href="{{ route('academic.examinations.index') }}" class="sidebar-link {{ request()->routeIs('academic.examinations.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i> Examinations
                </a>
            </li>
            <li>
                <a href="{{ route('academic.grades.index') }}" class="sidebar-link {{ request()->routeIs('academic.grades.*') ? 'active' : '' }}">
                    <i class="bi bi-star-half"></i> Grades
                </a>
            </li>
            <li>
                <a href="{{ route('academic.results.index') }}" class="sidebar-link {{ request()->routeIs('academic.results.*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart"></i> Results
                </a>
            </li>
            <li>
                <a href="{{ route('academic.courses.index') }}" class="sidebar-link {{ request()->routeIs('academic.courses.*') ? 'active' : '' }}">
                    <i class="bi bi-book"></i> Course Catalogue
                </a>
            </li>
            <li>
                <a href="{{ route('elearning.index') }}" class="sidebar-link {{ request()->routeIs('elearning.*') ? 'active' : '' }}">
                    <i class="bi bi-laptop"></i> E-Learning
                </a>
            </li>
        </ul>
    </div>
    @endhasrole

    {{-- ── ADMIN / REGISTRAR / DEAN / HOD: Academic ──────────────────────── --}}
    @hasrole('super-admin|registrar|dean|head-of-department')
    <div class="sidebar-section">
        <div class="sidebar-label">Academic</div>
        <ul class="sidebar-item">
            @hasrole('super-admin|dean')
            <li>
                <a href="{{ route('academic.faculties.index') }}" class="sidebar-link {{ request()->routeIs('academic.faculties.*') ? 'active' : '' }}">
                    <i class="bi bi-building"></i> Faculties
                </a>
            </li>
            @endhasrole
            @hasrole('super-admin|dean|head-of-department')
            <li>
                <a href="{{ route('academic.departments.index') }}" class="sidebar-link {{ request()->routeIs('academic.departments.*') ? 'active' : '' }}">
                    <i class="bi bi-diagram-3"></i> Departments
                </a>
            </li>
            <li>
                <a href="{{ route('academic.programs.index') }}" class="sidebar-link {{ request()->routeIs('academic.programs.*') ? 'active' : '' }}">
                    <i class="bi bi-mortarboard"></i> Programs
                </a>
            </li>
            @endhasrole
            <li>
                <a href="{{ route('academic.courses.index') }}" class="sidebar-link {{ request()->routeIs('academic.courses.*') ? 'active' : '' }}">
                    <i class="bi bi-book"></i> Courses
                </a>
            </li>
            @hasrole('super-admin|registrar')
            <li>
                <a href="{{ route('academic.academic-years.index') }}" class="sidebar-link {{ request()->routeIs('academic.academic-years.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar3"></i> Academic Years
                </a>
            </li>
            <li>
                <a href="{{ route('academic.semesters.index') }}" class="sidebar-link {{ request()->routeIs('academic.semesters.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-week"></i> Semesters/Terms
                </a>
            </li>
            <li>
                <a href="{{ route('academic.course-offerings.index') }}" class="sidebar-link {{ request()->routeIs('academic.course-offerings.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-bookmark"></i> Course Offerings
                </a>
            </li>
            <li>
                <a href="{{ route('academic.registrations.index') }}" class="sidebar-link {{ request()->routeIs('academic.registrations.*') ? 'active' : '' }}">
                    <i class="bi bi-pencil-square"></i> Registrations
                </a>
            </li>
            <li>
                <a href="{{ route('academic.assignments.index') }}" class="sidebar-link {{ request()->routeIs('academic.assignments.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-check"></i> Assignments
                </a>
            </li>
            @endhasrole
            <li>
                <a href="{{ route('academic.timetable.index') }}" class="sidebar-link {{ request()->routeIs('academic.timetable.*') ? 'active' : '' }}">
                    <i class="bi bi-clock"></i> Timetable
                </a>
            </li>
            <li>
                <a href="{{ route('academic.attendance.index') }}" class="sidebar-link {{ request()->routeIs('academic.attendance.*') ? 'active' : '' }}">
                    <i class="bi bi-person-check"></i> Attendance
                </a>
            </li>
            <li>
                <a href="{{ route('academic.examinations.index') }}" class="sidebar-link {{ request()->routeIs('academic.examinations.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i> Examinations
                </a>
            </li>
            <li>
                <a href="{{ route('academic.grades.index') }}" class="sidebar-link {{ request()->routeIs('academic.grades.*') ? 'active' : '' }}">
                    <i class="bi bi-star-half"></i> Grades
                </a>
            </li>
            <li>
                <a href="{{ route('academic.results.index') }}" class="sidebar-link {{ request()->routeIs('academic.results.*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart"></i> Results
                </a>
            </li>
            @hasrole('super-admin|registrar')
            @php $pendingAppeals ??= \App\Models\GradeAppeal::where('status', 'pending')->count(); @endphp
            <li>
                <a href="{{ route('academic.grade-appeals.index') }}" class="sidebar-link {{ request()->routeIs('academic.grade-appeals.*') ? 'active' : '' }}">
                    <i class="bi bi-flag"></i> Grade Appeals
                    @if($pendingAppeals > 0)
                    <span class="badge bg-danger ms-auto" style="font-size:0.65rem">{{ $pendingAppeals > 99 ? '99+' : $pendingAppeals }}</span>
                    @endif
                </a>
            </li>
            <li>
                <a href="{{ route('elearning.index') }}" class="sidebar-link {{ request()->routeIs('elearning.*') ? 'active' : '' }}">
                    <i class="bi bi-laptop"></i> E-Learning
                </a>
            </li>
            @endhasrole
            @hasrole('super-admin')
            <li>
                <a href="{{ route('admin.academic-settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.academic-settings.*') ? 'active' : '' }}">
                    <i class="bi bi-sliders"></i> Academic Settings
                </a>
            </li>
            @endhasrole
        </ul>
    </div>
    @endhasrole

    @hasrole('super-admin|finance-officer')
    <div class="sidebar-section">
        <div class="sidebar-label">Finance</div>
        <ul class="sidebar-item">
            <li>
                <a href="{{ route('finance.fee-structures.index') }}" class="sidebar-link {{ request()->routeIs('finance.fee-structures.*') ? 'active' : '' }}">
                    <i class="bi bi-cash-stack"></i> Fee Structures
                </a>
            </li>
            <li>
                <a href="{{ route('finance.billing.index') }}" class="sidebar-link {{ request()->routeIs('finance.billing.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i> Billing
                </a>
            </li>
            <li>
                <a href="{{ route('finance.payments.index') }}" class="sidebar-link {{ request()->routeIs('finance.payments.*') ? 'active' : '' }}">
                    <i class="bi bi-credit-card"></i> Payments
                </a>
            </li>
            <li>
                <a href="{{ route('finance.scholarships.index') }}" class="sidebar-link {{ request()->routeIs('finance.scholarships.*') ? 'active' : '' }}">
                    <i class="bi bi-award"></i> Scholarships
                </a>
            </li>
            <li>
                <a href="{{ route('academic.budgets.index') }}" class="sidebar-link {{ request()->routeIs('academic.budgets.*') ? 'active' : '' }}">
                    <i class="bi bi-wallet2"></i> Dept. Budgets
                </a>
            </li>
        </ul>
    </div>
    @endhasrole

    @hasrole('super-admin|hostel-manager')
    <div class="sidebar-section">
        <div class="sidebar-label">Hostel</div>
        <ul class="sidebar-item">
            <li>
                <a href="{{ route('hostel.hostels.index') }}" class="sidebar-link {{ request()->routeIs('hostel.hostels.*') ? 'active' : '' }}">
                    <i class="bi bi-house-door"></i> Hostels
                </a>
            </li>
            <li>
                <a href="{{ route('hostel.rooms.index') }}" class="sidebar-link {{ request()->routeIs('hostel.rooms.*') ? 'active' : '' }}">
                    <i class="bi bi-door-open"></i> Rooms
                </a>
            </li>
            <li>
                <a href="{{ route('hostel.allocations.index') }}" class="sidebar-link {{ request()->routeIs('hostel.allocations.*') ? 'active' : '' }}">
                    <i class="bi bi-person-fill-up"></i> Allocations
                </a>
            </li>
            <li>
                <a href="{{ route('reports.hostel') }}" class="sidebar-link {{ request()->routeIs('reports.hostel') ? 'active' : '' }}">
                    <i class="bi bi-graph-up"></i> Reports
                </a>
            </li>
        </ul>
    </div>
    @endhasrole

    @hasrole('super-admin|librarian')
    <div class="sidebar-section">
        <div class="sidebar-label">Library</div>
        <ul class="sidebar-item">
            <li>
                <a href="{{ route('library.books.index') }}" class="sidebar-link {{ request()->routeIs('library.books.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-bookmark-fill"></i> Books
                </a>
            </li>
            <li>
                <a href="{{ route('library.borrowings.index') }}" class="sidebar-link {{ request()->routeIs('library.borrowings.index') ? 'active' : '' }}">
                    <i class="bi bi-arrow-left-right"></i> Borrowings
                </a>
            </li>
            <li>
                <a href="{{ route('library.borrowings.fines') }}" class="sidebar-link {{ request()->routeIs('library.borrowings.fines') ? 'active' : '' }}">
                    <i class="bi bi-cash-coin"></i> Manage Fines
                    @php $unpaidFinesCount = \App\Models\BookBorrowing::where('fine_amount', '>', 0)->where('fine_paid', false)->where('fine_waived', false)->count(); @endphp
                    @if($unpaidFinesCount > 0)
                    <span class="badge bg-warning text-dark rounded-pill ms-auto">{{ $unpaidFinesCount > 99 ? '99+' : $unpaidFinesCount }}</span>
                    @endif
                </a>
            </li>
        </ul>
    </div>
    @endhasrole

    @hasrole('super-admin|hr-officer')
    <div class="sidebar-section">
        <div class="sidebar-label">Human Resources</div>
        <ul class="sidebar-item">
            <li>
                <a href="{{ route('hr.employees.index') }}" class="sidebar-link {{ request()->routeIs('hr.employees.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Employees
                </a>
            </li>
            <li>
                <a href="{{ route('hr.leave.index') }}" class="sidebar-link {{ request()->routeIs('hr.leave.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-x"></i> Leave
                </a>
            </li>
            <li>
                <a href="{{ route('hr.leave-types.index') }}" class="sidebar-link {{ request()->routeIs('hr.leave-types.*') ? 'active' : '' }}">
                    <i class="bi bi-tags"></i> Leave Types
                </a>
            </li>
            <li>
                <a href="{{ route('hr.employment-listings.index') }}" class="sidebar-link {{ request()->routeIs('hr.employment-listings.*') ? 'active' : '' }}">
                    <i class="bi bi-briefcase"></i> Employment Listings
                </a>
            </li>
            <li>
                <a href="{{ route('hr.salary-advances.index') }}" class="sidebar-link {{ request()->routeIs('hr.salary-advances.*') ? 'active' : '' }}">
                    <i class="bi bi-cash-coin"></i> Salary Advances
                </a>
            </li>
            <li>
                <a href="{{ route('hr.employee-appointments.index') }}" class="sidebar-link {{ request()->routeIs('hr.employee-appointments.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-person"></i> Appointments
                </a>
            </li>
            <li>
                <a href="{{ route('hr.payroll.index') }}" class="sidebar-link {{ request()->routeIs('hr.payroll.index') || request()->routeIs('hr.payroll.generate') || request()->routeIs('hr.payroll.process') || request()->routeIs('hr.payroll.slip') ? 'active' : '' }}">
                    <i class="bi bi-currency-dollar"></i> Payroll
                </a>
            </li>
            <li>
                <a href="{{ route('hr.payroll.config') }}" class="sidebar-link {{ request()->routeIs('hr.payroll.config*') ? 'active' : '' }}">
                    <i class="bi bi-sliders"></i> Payroll Config
                </a>
            </li>
        </ul>
    </div>
    @endhasrole

    <div class="sidebar-section">
        <div class="sidebar-label">Other</div>
        <ul class="sidebar-item">
            @hasrole('super-admin|registrar')
            <li>
                <a href="{{ route('assets.index') }}" class="sidebar-link {{ request()->routeIs('assets.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i> Assets
                </a>
            </li>
            <li>
                <a href="{{ route('research.index') }}" class="sidebar-link {{ request()->routeIs('research.index') || request()->routeIs('research.show') || request()->routeIs('research.create') || request()->routeIs('research.edit') ? 'active' : '' }}">
                    <i class="bi bi-lightbulb"></i> Research
                </a>
            </li>
            <li>
                <a href="{{ route('research.papers.index') }}" class="sidebar-link {{ request()->routeIs('research.papers.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-pdf"></i> Research Papers
                </a>
            </li>
            @endhasrole
            <li>
                <a href="{{ route('announcements.index') }}" class="sidebar-link {{ request()->routeIs('announcements.*') ? 'active' : '' }}">
                    <i class="bi bi-megaphone"></i> Announcements
                </a>
            </li>
            <li>
                <a href="{{ route('messages.index') }}" class="sidebar-link {{ request()->routeIs('messages.*') ? 'active' : '' }}">
                    <i class="bi bi-envelope"></i> Messages
                    @if(isset($unreadMessages) && $unreadMessages > 0)
                        <span class="badge bg-danger">{{ $unreadMessages }}</span>
                    @endif
                </a>
            </li>
            @hasrole('super-admin')
            <li>
                <a href="{{ route('admin.email-notifications.index') }}" class="sidebar-link {{ request()->routeIs('admin.email-notifications.*') ? 'active' : '' }}">
                    <i class="bi bi-envelope-paper"></i> Email Notifications
                </a>
            </li>
            <li>
                <a href="{{ route('admin.sms-notifications.index') }}" class="sidebar-link {{ request()->routeIs('admin.sms-notifications.*') ? 'active' : '' }}">
                    <i class="bi bi-phone"></i> SMS Notifications
                </a>
            </li>
            @endhasrole
            <li>
                <a href="{{ route('documents.index') }}" class="sidebar-link {{ request()->routeIs('documents.*') ? 'active' : '' }}">
                    <i class="bi bi-folder2-open"></i> Documents
                </a>
            </li>
            <li>
                <a href="{{ route('support.index') }}" class="sidebar-link {{ request()->routeIs('support.*') ? 'active' : '' }}">
                    <i class="bi bi-headset"></i> Support
                    @if(!empty($openSupportTickets) && $openSupportTickets > 0)
                    <span class="badge bg-danger rounded-pill ms-auto">{{ $openSupportTickets > 99 ? '99+' : $openSupportTickets }}</span>
                    @endif
                </a>
            </li>
            @hasrole('super-admin|registrar|finance-officer')
            <li>
                <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="bi bi-graph-up"></i> Reports
                </a>
            </li>
            @endhasrole
            @hasrole('super-admin')
            <li>
                <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-person-gear"></i> Users
                </a>
            </li>
            <li>
                <a href="{{ route('admin.roles.index') }}" class="sidebar-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                    <i class="bi bi-shield-check"></i> Roles
                </a>
            </li>
            <li>
                <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i> Settings
                </a>
            </li>
            <li>
                <a href="{{ route('admin.audit-logs') }}" class="sidebar-link {{ request()->routeIs('admin.audit-logs') ? 'active' : '' }}">
                    <i class="bi bi-journal-text"></i> Audit Logs
                </a>
            </li>
            @endhasrole
        </ul>
    </div>

    <div class="pb-4"></div>
</nav>

<!-- ===== TOPBAR ===== -->
<header id="topbar">
    <button class="btn btn-sm btn-outline-secondary border-0 d-flex align-items-center" id="sidebar-toggle" onclick="toggleSidebar()">
        <i class="bi bi-list fs-5"></i>
    </button>

    <div class="topbar-title d-none d-md-block">
        @yield('page-title', 'Dashboard')
    </div>

    <div class="ms-auto d-flex align-items-center gap-2">
        <!-- Dark Mode Toggle -->
        <button class="btn btn-sm btn-outline-secondary border-0" onclick="toggleDarkMode()" title="Toggle Dark Mode">
            <i class="bi bi-moon-fill" id="dark-icon"></i>
        </button>

        <!-- Notifications -->
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary border-0 position-relative" data-bs-toggle="dropdown">
                <i class="bi bi-bell fs-5"></i>
                @php $notifCount = auth()->user()?->unreadNotificationsCount ?? 0; @endphp
                @if($notifCount > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.6rem">{{ $notifCount > 9 ? '9+' : $notifCount }}</span>
                @endif
            </button>
            <div class="dropdown-menu dropdown-menu-end p-0" style="width:360px; max-height:480px; overflow-y:auto; border-radius:12px;">
                <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                    <strong>Notifications</strong>
                    @if($notifCount > 0)
                    <form action="{{ route('notifications.read-all') }}" method="POST">@csrf
                        <button type="submit" class="btn btn-link btn-sm p-0">Mark all read</button>
                    </form>
                    @endif
                </div>
                @forelse(auth()->check() ? auth()->user()->notifications()->latest()->take(8)->get() : collect() as $notif)
                <form action="{{ route('notifications.read', $notif->id) }}" method="POST" class="m-0 p-0">
                    @csrf
                    <button type="submit" class="notification-item {{ $notif->is_read ? '' : 'unread' }} d-flex gap-2 w-100 text-start border-0 bg-transparent p-0" style="cursor:pointer">
                        <div class="mt-1">
                            @if(!$notif->is_read)<div class="notification-dot mt-1"></div>@else<div style="width:8px"></div>@endif
                        </div>
                        <div class="flex-1">
                            <div class="fw-semibold" style="font-size:0.82rem">{{ $notif->title }}</div>
                            <div class="text-muted" style="font-size:0.78rem">{{ Str::limit($notif->message, 60) }}</div>
                            <div class="text-muted" style="font-size:0.72rem">{{ $notif->created_at->diffForHumans() }}</div>
                        </div>
                    </button>
                </form>
                @empty
                <div class="text-center text-muted p-4"><i class="bi bi-bell-slash fs-3 d-block mb-2"></i>No notifications</div>
                @endforelse
                <div class="border-top text-center p-2">
                    <a href="{{ route('notifications.index') }}" class="btn btn-link btn-sm">View all notifications</a>
                </div>
            </div>
        </div>

        <!-- Messages -->
        <a href="{{ route('messages.index') }}" class="btn btn-sm btn-outline-secondary border-0 position-relative">
            <i class="bi bi-envelope fs-5"></i>
            @if(isset($unreadMessages) && $unreadMessages > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.6rem">{{ $unreadMessages > 9 ? '9+' : $unreadMessages }}</span>
            @endif
        </a>

        <!-- User Profile -->
        @auth
        <div class="dropdown">
            <button class="btn btn-sm p-0 border-0 d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                <img src="{{ auth()->user()->avatar_url }}" class="avatar" alt="{{ auth()->user()->name }}">
                <div class="d-none d-md-block text-start" style="line-height:1.2">
                    <div style="font-size:0.82rem; font-weight:600">{{ Str::limit(auth()->user()->name, 18) }}</div>
                    <div style="font-size:0.7rem; opacity:0.6">{{ ucwords(str_replace('-', ' ', auth()->user()->full_role)) }}</div>
                </div>
                <i class="bi bi-chevron-down d-none d-md-block" style="font-size:0.7rem; opacity:0.5"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" style="border-radius:12px; min-width:200px">
                <li class="px-3 py-2 border-bottom">
                    <div class="fw-semibold">{{ auth()->user()->name }}</div>
                    <div class="text-muted" style="font-size:0.78rem">{{ auth()->user()->email }}</div>
                </li>
                <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="bi bi-person me-2"></i>My Profile</a></li>
                <li><a class="dropdown-item" href="{{ route('notifications.index') }}"><i class="bi bi-bell me-2"></i>Notifications</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</button>
                    </form>
                </li>
            </ul>
        </div>
        @endauth
    </div>
</header>

<!-- ===== MAIN CONTENT ===== -->
<main id="main-content">
    <!-- Flash Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
        <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @yield('content')
</main>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
// Dark Mode
function toggleDarkMode() {
    const html = document.documentElement;
    const isDark = html.getAttribute('data-bs-theme') === 'dark';
    html.setAttribute('data-bs-theme', isDark ? 'light' : 'dark');
    document.getElementById('dark-icon').className = isDark ? 'bi bi-moon-fill' : 'bi bi-sun-fill';
    localStorage.setItem('theme', isDark ? 'light' : 'dark');
}
// Apply saved theme
(function() {
    const saved = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-bs-theme', saved);
    if (saved === 'dark') document.getElementById('dark-icon').className = 'bi bi-sun-fill';
})();

// Sidebar Toggle (desktop)
function toggleSidebar() {
    if (window.innerWidth < 992) {
        document.body.classList.toggle('mobile-sidebar-open');
    } else {
        document.body.classList.toggle('sidebar-collapsed');
        localStorage.setItem('sidebar-collapsed', document.body.classList.contains('sidebar-collapsed'));
    }
}
function closeMobileSidebar() {
    document.body.classList.remove('mobile-sidebar-open');
}
// Restore sidebar state
(function() {
    if (localStorage.getItem('sidebar-collapsed') === 'true' && window.innerWidth >= 992) {
        document.body.classList.add('sidebar-collapsed');
    }
})();

// Auto-dismiss alerts
setTimeout(() => {
    document.querySelectorAll('.alert-dismissible').forEach(el => {
        const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
        bsAlert.close();
    });
}, 5000);

// DataTables Init
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('table.datatable').forEach(table => {
        new DataTable(table, { responsive: true, pageLength: 15 });
    });
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el);
    });
});

// CSRF for AJAX
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
</script>
@stack('scripts')
</body>
</html>
