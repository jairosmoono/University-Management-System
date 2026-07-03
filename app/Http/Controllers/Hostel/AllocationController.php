<?php
namespace App\Http\Controllers\Hostel;

use App\Http\Controllers\Controller;
use App\Models\RoomAllocation;
use App\Models\HostelRoom;
use App\Models\Hostel;
use App\Models\Student;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class AllocationController extends Controller
{
    public function index(Request $request)
    {
        $query = RoomAllocation::with(['hostelRoom.hostel', 'student.user']);
        if ($request->hostel_id) $query->whereHas('hostelRoom', fn($q) => $q->where('hostel_id', $request->hostel_id));
        if ($request->status) $query->where('status', $request->status);
        if ($request->search) {
            $query->whereHas('student.user', fn($q) => $q->where('name', 'like', '%'.$request->search.'%'));
        }
        $allocations = $query->latest()->paginate(20);
        $hostels = Hostel::all();
        $students = Student::with('user')->whereHas('user')->orderBy('id')->get();
        $availableRooms = HostelRoom::with('hostel')->where('status', 'available')->get();
        return view('hostel.allocations.index', compact('allocations', 'hostels', 'students', 'availableRooms'));
    }

    public function assign(Request $request)
    {
        $request->validate([
            'hostel_room_id' => 'required|exists:hostel_rooms,id',
            'student_id'     => 'required|exists:students,id',
            'allocation_date' => 'required|date',
        ]);
        $room = HostelRoom::findOrFail($request->hostel_room_id);
        if ($room->available_beds <= 0) return back()->with('error', 'Room is full.');
        RoomAllocation::create([
            'hostel_room_id'       => $request->hostel_room_id,
            'student_id'           => $request->student_id,
            'allocation_date'      => $request->allocation_date,
            'expected_vacate_date' => $request->expected_vacate_date,
            'allocated_by'         => auth()->id(),
            'status'               => 'active',
        ]);
        if ($room->available_beds <= 1) {
            $room->update(['status' => 'occupied']);
        }
        return back()->with('success', 'Student allocated to room successfully.');
    }

    public function checkout(RoomAllocation $allocation)
    {
        $allocation->update(['status' => 'vacated', 'actual_vacate_date' => now()->toDateString()]);
        $room = $allocation->hostelRoom;
        if ($room && $room->status === 'occupied') {
            $room->update(['status' => 'available']);
        }
        return back()->with('success', 'Student checked out successfully.');
    }

    public function occupancy(Request $request)
    {
        $allHostels = Hostel::orderBy('name')->get();

        // Load the selected hostel or all of them
        $query = Hostel::withCount('rooms');
        if ($request->hostel_id) {
            $query->where('id', $request->hostel_id);
        }

        $hostels = $query->get()->map(function ($hostel) {
            // Rooms grouped by floor, with active allocations + student data eager-loaded
            $rooms = HostelRoom::where('hostel_id', $hostel->id)
                ->with(['activeAllocations.student.user'])
                ->orderBy('floor')
                ->orderBy('room_number')
                ->get()
                ->groupBy('floor');

            $capacity  = HostelRoom::where('hostel_id', $hostel->id)->sum('capacity');
            $occupied  = RoomAllocation::whereHas('hostelRoom', fn($q) => $q->where('hostel_id', $hostel->id))
                ->where('status', 'active')->count();

            return [
                'hostel'    => $hostel,
                'floors'    => $rooms,
                'capacity'  => $capacity,
                'occupied'  => $occupied,
                'available' => max(0, $capacity - $occupied),
                'rate'      => $capacity > 0 ? round(($occupied / $capacity) * 100) : 0,
            ];
        });

        // Overall totals across filtered set
        $totalCapacity = $hostels->sum('capacity');
        $totalOccupied = $hostels->sum('occupied');
        $totalRooms    = $hostels->sum(fn($h) => $h['floors']->flatten()->count());
        $overallRate   = $totalCapacity > 0 ? round(($totalOccupied / $totalCapacity) * 100, 1) : 0;

        return view('hostel.occupancy', compact(
            'hostels', 'allHostels', 'totalCapacity', 'totalOccupied', 'totalRooms', 'overallRate'
        ));
    }

    public function occupancyExport(Request $request)
    {
        $allHostels = Hostel::orderBy('name')->get();
        $query = Hostel::withCount('rooms');
        if ($request->hostel_id) $query->where('id', $request->hostel_id);

        $hostels = $query->get()->map(function ($hostel) {
            $rooms     = HostelRoom::where('hostel_id', $hostel->id)
                ->with(['activeAllocations.student.user'])
                ->orderBy('floor')->orderBy('room_number')->get()
                ->groupBy('floor');
            $capacity  = HostelRoom::where('hostel_id', $hostel->id)->sum('capacity');
            $occupied  = RoomAllocation::whereHas('hostelRoom', fn($q) => $q->where('hostel_id', $hostel->id))
                ->where('status', 'active')->count();
            return [
                'hostel'    => $hostel,
                'floors'    => $rooms,
                'capacity'  => $capacity,
                'occupied'  => $occupied,
                'available' => max(0, $capacity - $occupied),
                'rate'      => $capacity > 0 ? round(($occupied / $capacity) * 100) : 0,
            ];
        });

        $totalCapacity = $hostels->sum('capacity');
        $totalOccupied = $hostels->sum('occupied');
        $overallRate   = $totalCapacity > 0 ? round(($totalOccupied / $totalCapacity) * 100, 1) : 0;

        $pdf = Pdf::loadView('hostel.occupancy-pdf', compact('hostels', 'totalCapacity', 'totalOccupied', 'overallRate'))
            ->setPaper('a4', 'landscape');
        return $pdf->download('hostel_occupancy_' . date('Ymd') . '.pdf');
    }
}
