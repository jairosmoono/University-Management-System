<?php

if (!function_exists('formatCurrency')) {
    function formatCurrency($amount, $currency = 'ZMW') {
        return $currency . ' ' . number_format($amount, 2);
    }
}

if (!function_exists('gradeToPoints')) {
    function gradeToPoints($grade) {
        try {
            foreach (\App\Models\GradeScale::orderedForLookup() as $row) {
                if ($row['grade'] === $grade) return (float) $row['grade_points'];
            }
        } catch (\Throwable $e) { /* fallback below */ }
        // Fallback if DB unavailable
        $map = ['A+'=>4.0,'A'=>4.0,'A-'=>3.7,'B+'=>3.3,'B'=>3.0,'B-'=>2.7,'C+'=>2.3,'C'=>2.0,'C-'=>1.7,'D+'=>1.3,'D'=>1.0,'D-'=>0.7,'F'=>0.0];
        return $map[$grade] ?? 0.0;
    }
}

if (!function_exists('scoreToGrade')) {
    function scoreToGrade($score) {
        try {
            $row = \App\Models\GradeScale::fromScore((float) $score);
            return $row['grade'];
        } catch (\Throwable $e) { /* fallback below */ }
        // Fallback if DB unavailable
        if ($score >= 90) return 'A+';
        if ($score >= 85) return 'A';
        if ($score >= 80) return 'A-';
        if ($score >= 75) return 'B+';
        if ($score >= 70) return 'B';
        if ($score >= 65) return 'B-';
        if ($score >= 60) return 'C+';
        if ($score >= 55) return 'C';
        if ($score >= 50) return 'C-';
        if ($score >= 45) return 'D+';
        if ($score >= 40) return 'D';
        if ($score >= 35) return 'D-';
        return 'F';
    }
}

if (!function_exists('generateStudentId')) {
    function generateStudentId($year = null) {
        $year = $year ?? date('Y');
        $count = \App\Models\Student::whereYear('created_at', $year)->count() + 1;
        return 'STU/' . $year . '/' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('generateStaffId')) {
    function generateStaffId() {
        $count = \App\Models\Staff::count() + 1;
        return 'STF/' . date('Y') . '/' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('generateApplicationNumber')) {
    function generateApplicationNumber() {
        $count = \App\Models\Admission::count() + 1;
        return 'APP/' . date('Y') . '/' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('calculateAge')) {
    function calculateAge($dob) {
        return \Carbon\Carbon::parse($dob)->age;
    }
}

if (!function_exists('userHasRole')) {
    function userHasRole($role) {
        return auth()->check() && auth()->user()->hasRole($role);
    }
}

if (!function_exists('userCan')) {
    function userCan($permission) {
        return auth()->check() && auth()->user()->can($permission);
    }
}

if (!function_exists('statusBadge')) {
    function statusBadge($status) {
        $badges = [
            'active' => 'success',
            'inactive' => 'secondary',
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'enrolled' => 'primary',
            'graduated' => 'info',
            'suspended' => 'danger',
            'paid' => 'success',
            'unpaid' => 'danger',
            'partial' => 'warning',
            'open' => 'primary',
            'closed' => 'secondary',
            'resolved' => 'success',
            'issued' => 'warning',
            'returned' => 'success',
            'overdue' => 'danger',
        ];
        $class = $badges[strtolower($status)] ?? 'secondary';
        return '<span class="badge bg-' . $class . '">' . ucfirst($status) . '</span>';
    }
}

if (!function_exists('setting')) {
    function setting(string $key, $default = null) {
        static $cache = null;
        if ($cache === null) {
            $path = storage_path('app/settings.json');
            $cache = file_exists($path) ? (json_decode(file_get_contents($path), true) ?? []) : [];
        }
        return $cache[$key] ?? $default;
    }
}
