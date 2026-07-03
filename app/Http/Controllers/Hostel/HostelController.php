<?php
namespace App\Http\Controllers\Hostel;

use App\Http\Controllers\Controller;
use App\Models\Hostel;
use App\Models\Staff;
use Illuminate\Http\Request;

class HostelController extends Controller
{
    public function index()
    {
        $hostels = Hostel::withCount('rooms')->with('warden')->get();
        return view('hostel.hostels.index', compact('hostels'));
    }

    public function create()
    {
        $wardens = Staff::active()->get();
        return view('hostel.create', compact('wardens'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255', 'type' => 'required|in:male,female,mixed']);
        Hostel::create($request->only(['name', 'type', 'warden_id', 'location', 'description', 'status']));
        return redirect()->route('hostel.hostels.index')->with('success', 'Hostel created.');
    }

    public function show(Hostel $hostel)
    {
        $hostel->load(['rooms', 'warden']);
        return view('hostel.show', compact('hostel'));
    }

    public function edit(Hostel $hostel)
    {
        $wardens = Staff::active()->get();
        return view('hostel.edit', compact('hostel', 'wardens'));
    }

    public function update(Request $request, Hostel $hostel)
    {
        $hostel->update($request->except('_token', '_method'));
        return redirect()->route('hostel.hostels.index')->with('success', 'Hostel updated.');
    }

    public function destroy(Hostel $hostel)
    {
        if ($hostel->rooms()->exists()) {
            return redirect()->route('hostel.hostels.index')
                ->with('error', 'Cannot delete hostel "' . $hostel->name . '" — it still has rooms. Remove all rooms first.');
        }

        $hostel->delete();
        return redirect()->route('hostel.hostels.index')->with('success', 'Hostel deleted.');
    }
}
