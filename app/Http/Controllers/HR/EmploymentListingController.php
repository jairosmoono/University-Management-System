<?php
namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\EmploymentListing;
use Illuminate\Http\Request;

class EmploymentListingController extends Controller
{
    public function index(Request $request)
    {
        $query = EmploymentListing::with('department');
        if ($request->status)        $query->where('status', $request->status);
        if ($request->department_id) $query->where('department_id', $request->department_id);

        $listings    = $query->latest()->paginate(20);
        $departments = Department::orderBy('name')->get();

        $stats = [
            'total'  => EmploymentListing::count(),
            'open'   => EmploymentListing::where('status', 'open')->count(),
            'closed' => EmploymentListing::where('status', 'closed')->count(),
            'draft'  => EmploymentListing::where('status', 'draft')->count(),
        ];

        return view('hr.employment-listings.index', compact('listings', 'departments', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'           => 'required|string|max:255',
            'department_id'   => 'nullable|exists:departments,id',
            'employment_type' => 'required|in:full-time,part-time,contract,internship',
            'vacancies'       => 'required|integer|min:1',
            'deadline'        => 'nullable|date',
            'status'          => 'required|in:open,closed,draft',
            'description'     => 'nullable|string',
            'requirements'    => 'nullable|string',
        ]);

        EmploymentListing::create($request->only([
            'title', 'department_id', 'employment_type', 'vacancies',
            'deadline', 'status', 'description', 'requirements',
        ]));

        return back()->with('success', 'Job listing created successfully.');
    }

    public function update(Request $request, EmploymentListing $employmentListing)
    {
        $request->validate([
            'title'           => 'required|string|max:255',
            'department_id'   => 'nullable|exists:departments,id',
            'employment_type' => 'required|in:full-time,part-time,contract,internship',
            'vacancies'       => 'required|integer|min:1',
            'deadline'        => 'nullable|date',
            'status'          => 'required|in:open,closed,draft',
            'description'     => 'nullable|string',
            'requirements'    => 'nullable|string',
        ]);

        $employmentListing->update($request->only([
            'title', 'department_id', 'employment_type', 'vacancies',
            'deadline', 'status', 'description', 'requirements',
        ]));

        return back()->with('success', 'Job listing updated successfully.');
    }

    public function destroy(EmploymentListing $employmentListing)
    {
        $employmentListing->delete();
        return back()->with('success', 'Job listing deleted.');
    }
}
