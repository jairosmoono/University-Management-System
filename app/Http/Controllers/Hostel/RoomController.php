<?php
namespace App\Http\Controllers\Hostel;

use App\Http\Controllers\Controller;
use App\Models\HostelRoom;
use App\Models\Hostel;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = HostelRoom::with('hostel');
        if ($request->hostel_id) $query->where('hostel_id', $request->hostel_id);
        if ($request->status) $query->where('status', $request->status);
        $rooms = $query->paginate(20);
        $hostels = Hostel::all();
        return view('hostel.rooms.index', compact('rooms', 'hostels'));
    }

    public function create()
    {
        $hostels = Hostel::all();
        return view('hostel.rooms.create', compact('hostels'));
    }

    public function store(Request $request)
    {
        $request->validate(['hostel_id' => 'required|exists:hostels,id', 'room_number' => 'required|string', 'capacity' => 'required|integer|min:1', 'type' => 'required|string']);
        HostelRoom::create($request->except('_token'));
        return redirect()->route('hostel.rooms.index')->with('success', 'Room added.');
    }

    public function show(HostelRoom $room)
    {
        $room->load(['hostel', 'activeAllocations.student']);
        return view('hostel.rooms.show', compact('room'));
    }

    public function edit(HostelRoom $room)
    {
        $hostels = Hostel::all();
        return view('hostel.rooms.edit', compact('room', 'hostels'));
    }

    public function update(Request $request, HostelRoom $room)
    {
        $room->update($request->except('_token', '_method'));
        return redirect()->route('hostel.rooms.index')->with('success', 'Room updated.');
    }

    public function destroy(HostelRoom $room)
    {
        $room->delete();
        return redirect()->route('hostel.rooms.index')->with('success', 'Room deleted.');
    }

    public function available(Hostel $hostel)
    {
        $rooms = HostelRoom::where('hostel_id', $hostel->id)->where('status', 'available')->where('occupied', '<', \DB::raw('capacity'))->get(['id', 'room_number', 'type', 'capacity', 'occupied']);
        return response()->json($rooms);
    }
}
