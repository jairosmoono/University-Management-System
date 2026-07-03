<?php
namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::with('uploadedBy');
        if (!auth()->user()->hasRole('super-admin')) $query->where(fn($q) => $q->where('is_public', true)->orWhere('uploaded_by', auth()->id()));
        if ($request->category) $query->where('category', $request->category);
        if ($request->search) $query->where('title', 'like', '%' . $request->search . '%');
        $documents = $query->latest()->paginate(20);
        $students  = Student::with('user')->whereHas('user')->orderBy('id')->get();
        return view('documents.index', compact('documents', 'students'));
    }

    public function create() { return view('documents.create'); }

    public function store(Request $request)
    {
        $request->validate(['title' => 'required|string|max:255', 'file' => 'required|file|max:10240']);
        $file = $request->file('file');
        $path = $file->store('documents', 'public');
        Document::create(['title' => $request->title, 'description' => $request->description, 'file_path' => $path, 'file_name' => $file->getClientOriginalName(), 'file_type' => $file->getMimeType(), 'file_size' => $file->getSize(), 'category' => $request->category, 'uploaded_by' => auth()->id(), 'is_public' => $request->boolean('is_public'), 'status' => 'active']);
        return redirect()->route('documents.index')->with('success', 'Document uploaded successfully.');
    }

    public function show(Document $document) { return view('documents.show', compact('document')); }

    public function edit(Document $document) { return view('documents.edit', compact('document')); }

    public function update(Request $request, Document $document)
    {
        $document->update($request->only('title', 'description', 'category', 'is_public'));
        return redirect()->route('documents.index')->with('success', 'Document updated.');
    }

    public function destroy(Document $document)
    {
        Storage::disk('public')->delete($document->file_path);
        $document->delete();
        return redirect()->route('documents.index')->with('success', 'Document deleted.');
    }

    public function download(Document $document)
    {
        $document->increment('download_count');
        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }
}
