<?php

namespace App\Http\Controllers;

use App\Models\ResearchProject;
use App\Models\ResearchPaper;
use App\Models\Publication;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ResearchController extends Controller
{
    public function index(Request $request)
    {
        $projects = ResearchProject::with(['principalInvestigator'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) => $q->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('abstract', 'like', "%{$request->search}%");
            }))
            ->latest()
            ->paginate(20);

        $publications = Publication::with(['staff'])
            ->latest()
            ->take(10)
            ->get();

        $stats = [
            'total_projects'     => ResearchProject::count(),
            'ongoing'            => ResearchProject::where('status', 'ongoing')->count(),
            'completed'          => ResearchProject::where('status', 'completed')->count(),
            'total_publications' => Publication::count(),
            'total_papers'       => ResearchPaper::count(),
        ];

        return view('research.index', compact('projects', 'publications', 'stats'));
    }

    public function create()
    {
        $staff = Staff::with('user')->get();
        return view('research.create', compact('staff'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'                  => 'required|string|max:500',
            'abstract'               => 'required|string',
            'principal_investigator_id' => 'required|exists:staff,id',
            'co_investigators'       => 'nullable|string',
            'start_date'             => 'required|date',
            'end_date'               => 'nullable|date|after:start_date',
            'budget'                 => 'nullable|numeric|min:0',
            'funding_source'         => 'nullable|string|max:255',
            'status'                 => 'required|in:proposal,ongoing,completed,suspended',
            'keywords'               => 'nullable|string|max:500',
        ]);

        $project = ResearchProject::create($validated);

        return redirect()->route('research.show', $project)->with('success', 'Research project created successfully.');
    }

    public function show(ResearchProject $research)
    {
        $research->load(['principalInvestigator.user']);
        $publications = Publication::where('research_project_id', $research->id)->get();
        $staff   = Staff::with('user')->get();
        $project = $research;
        return view('research.show', compact('project', 'publications', 'staff'));
    }

    public function edit(ResearchProject $research)
    {
        $staff = Staff::with('user')->get();
        return view('research.edit', compact('research', 'staff'));
    }

    public function update(Request $request, ResearchProject $research)
    {
        $validated = $request->validate([
            'title'                  => 'required|string|max:500',
            'abstract'               => 'required|string',
            'principal_investigator_id' => 'required|exists:staff,id',
            'co_investigators'       => 'nullable|string',
            'start_date'             => 'required|date',
            'end_date'               => 'nullable|date|after:start_date',
            'budget'                 => 'nullable|numeric|min:0',
            'funding_source'         => 'nullable|string|max:255',
            'status'                 => 'required|in:proposal,ongoing,completed,suspended',
            'keywords'               => 'nullable|string|max:500',
        ]);

        $research->update($validated);

        return redirect()->route('research.show', $research)->with('success', 'Research project updated.');
    }

    public function destroy(ResearchProject $research)
    {
        $research->delete();
        return redirect()->route('research.index')->with('success', 'Research project deleted.');
    }

    // ── Research Papers (file upload/download) ───────────────────────────────

    public function papers(Request $request)
    {
        $query = ResearchPaper::with(['researchProject', 'uploader']);

        if ($request->search) {
            $query->where(fn($q) =>
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('authors', 'like', '%' . $request->search . '%')
                  ->orWhere('keywords', 'like', '%' . $request->search . '%')
            );
        }
        if ($request->category)   $query->where('category', $request->category);
        if ($request->project_id) $query->where('research_project_id', $request->project_id);

        $papers    = $query->latest()->paginate(20)->withQueryString();
        $projects  = ResearchProject::orderBy('title')->get();
        $totalPapers = ResearchPaper::count();
        $byCategory  = ResearchPaper::selectRaw('category, COUNT(*) as count')
            ->groupBy('category')->pluck('count', 'category');

        return view('research.papers.index', compact('papers', 'projects', 'totalPapers', 'byCategory'));
    }

    public function uploadPaper(Request $request)
    {
        $projects = ResearchProject::orderBy('title')->get();
        $preselected = $request->project_id ? ResearchProject::find($request->project_id) : null;
        return view('research.papers.upload', compact('projects', 'preselected'));
    }

    public function storePaper(Request $request)
    {
        $request->validate([
            'title'               => 'required|string|max:255',
            'authors'             => 'nullable|string|max:500',
            'abstract'            => 'nullable|string',
            'keywords'            => 'nullable|string|max:255',
            'research_project_id' => 'nullable|exists:research_projects,id',
            'category'            => 'required|in:journal_article,conference_paper,thesis,technical_report,book_chapter,other',
            'publication_year'    => 'nullable|digits:4|integer|min:1900|max:' . (date('Y') + 1),
            'doi'                 => 'nullable|string|max:255',
            'is_public'           => 'boolean',
            'paper_file'          => 'required|file|mimes:pdf,doc,docx|max:20480',
        ]);

        $file = $request->file('paper_file');
        $path = $file->store('research_papers', 'local');

        ResearchPaper::create([
            'research_project_id' => $request->research_project_id,
            'uploaded_by'         => auth()->id(),
            'title'               => $request->title,
            'authors'             => $request->authors,
            'abstract'            => $request->abstract,
            'keywords'            => $request->keywords,
            'file_path'           => $path,
            'file_original_name'  => $file->getClientOriginalName(),
            'file_size'           => $file->getSize(),
            'file_mime'           => $file->getMimeType(),
            'category'            => $request->category,
            'publication_year'    => $request->publication_year,
            'doi'                 => $request->doi,
            'is_public'           => $request->boolean('is_public', true),
        ]);

        return redirect()->route('research.papers.index')
            ->with('success', 'Research paper uploaded successfully.');
    }

    public function downloadPaper(ResearchPaper $paper)
    {
        abort_unless(
            $paper->is_public || auth()->user()?->can('manage-research'),
            403,
            'This paper is restricted.'
        );

        abort_unless(
            Storage::disk('local')->exists($paper->file_path),
            404,
            'File not found on disk.'
        );

        return Storage::disk('local')->download($paper->file_path, $paper->file_original_name);
    }

    public function destroyPaper(ResearchPaper $paper)
    {
        Storage::disk('local')->delete($paper->file_path);
        $paper->delete();

        return back()->with('success', 'Research paper deleted.');
    }

    // Publications
    public function publications(Request $request)
    {
        $publications = Publication::with(['staff'])
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->when($request->year, fn($q) => $q->where('publication_year', $request->year))
            ->latest()
            ->paginate(20);

        return view('research.publications', compact('publications'));
    }

    public function storePublication(Request $request, ResearchProject $research)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:500',
            'staff_id'         => 'required|exists:staff,id',
            'type'             => 'required|in:journal,conference,book,thesis,report',
            'publisher'        => 'nullable|string|max:255',
            'publication_year' => 'required|integer|min:1990|max:2100',
            'doi'              => 'nullable|string|max:255',
            'url'              => 'nullable|url',
            'abstract'         => 'nullable|string',
        ]);

        $validated['research_project_id'] = $research->id;
        Publication::create($validated);

        return back()->with('success', 'Publication added.');
    }
}
