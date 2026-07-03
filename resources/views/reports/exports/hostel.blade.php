<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; margin: 0; padding: 20px; }
    h1 { font-size: 16px; margin: 0 0 4px; }
    .sub { font-size: 10px; color: #666; margin-bottom: 16px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
    thead th { background: #0B1F3A; color: #fff; padding: 6px 8px; text-align: left; font-size: 10px; }
    tbody td { padding: 5px 8px; border-bottom: 1px solid #e5e5e5; vertical-align: top; }
    tbody tr:nth-child(even) td { background: #f8f8f8; }
    .tfoot td { background: #f0f0f0; font-weight: bold; padding: 5px 8px; border-top: 2px solid #ccc; }
    .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 9px; color: #fff; }
    .bg-success { background: #198754; }
    .bg-secondary { background: #6c757d; }
    .bg-danger { background: #dc3545; }
    h2 { font-size: 13px; margin: 14px 0 6px; border-bottom: 1px solid #ddd; padding-bottom: 4px; }
    .stat-row { display: flex; gap: 20px; margin-bottom: 14px; }
    .stat-box { flex: 1; border: 1px solid #ddd; border-radius: 4px; padding: 8px 12px; text-align: center; }
    .stat-box .val { font-size: 20px; font-weight: bold; color: #0B1F3A; }
    .stat-box .lbl { font-size: 10px; color: #666; }
</style>
</head>
<body>
<h1>Hostel Report</h1>
<p class="sub">Generated: {{ now()->format('d M Y H:i') }}</p>

<div class="stat-row">
    <div class="stat-box"><div class="val">{{ number_format($totalCapacity) }}</div><div class="lbl">Total Capacity</div></div>
    <div class="stat-box"><div class="val">{{ number_format($totalOccupied) }}</div><div class="lbl">Occupied</div></div>
    <div class="stat-box"><div class="val">{{ number_format($totalCapacity - $totalOccupied) }}</div><div class="lbl">Available</div></div>
    <div class="stat-box"><div class="val">{{ $occupancyRate }}%</div><div class="lbl">Occupancy Rate</div></div>
</div>

<h2>Per-Hostel Occupancy</h2>
<table>
    <thead>
        <tr>
            <th>Hostel</th>
            <th>Type</th>
            <th>Rooms</th>
            <th>Capacity</th>
            <th>Occupied</th>
            <th>Available</th>
            <th>Rate</th>
        </tr>
    </thead>
    <tbody>
        @foreach($hostelStats as $row)
        <tr>
            <td>{{ $row['hostel']->name }}</td>
            <td>{{ ucfirst($row['hostel']->type) }}</td>
            <td>{{ $row['rooms'] }}</td>
            <td>{{ $row['capacity'] }}</td>
            <td>{{ $row['occupied'] }}</td>
            <td>{{ $row['available'] }}</td>
            <td>{{ $row['rate'] }}%</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="tfoot">
            <td colspan="2">Total</td>
            <td>{{ $hostelStats->sum('rooms') }}</td>
            <td>{{ $hostelStats->sum('capacity') }}</td>
            <td>{{ $hostelStats->sum('occupied') }}</td>
            <td>{{ $hostelStats->sum('available') }}</td>
            <td>{{ $occupancyRate }}%</td>
        </tr>
    </tfoot>
</table>

<h2>Allocation Records</h2>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Student</th>
            <th>Hostel</th>
            <th>Room</th>
            <th>Allocated</th>
            <th>Expected Vacate</th>
            <th>Actual Vacate</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($allocations as $i => $alloc)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ optional($alloc->student?->user)->name ?? '—' }}</td>
            <td>{{ optional($alloc->hostelRoom?->hostel)->name ?? '—' }}</td>
            <td>{{ optional($alloc->hostelRoom)->room_number ?? '—' }}</td>
            <td>{{ $alloc->allocation_date?->format('d M Y') ?? '—' }}</td>
            <td>{{ $alloc->expected_vacate_date?->format('d M Y') ?? '—' }}</td>
            <td>{{ $alloc->actual_vacate_date?->format('d M Y') ?? '—' }}</td>
            <td>
                <span class="badge {{ $alloc->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                    {{ ucfirst($alloc->status) }}
                </span>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" style="text-align:center;color:#999">No records</td></tr>
        @endforelse
    </tbody>
</table>
</body>
</html>
