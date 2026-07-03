<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #222; margin:0; padding:20px; }
    h1  { font-size:15px; margin:0 0 3px; }
    .sub { font-size:9px; color:#666; margin-bottom:14px; }
    .stat-row { display:table; width:100%; margin-bottom:14px; }
    .stat-box { display:table-cell; border:1px solid #ddd; padding:8px 10px; text-align:center; width:25%; }
    .stat-box .val { font-size:18px; font-weight:bold; color:#0B1F3A; }
    .stat-box .lbl { font-size:9px; color:#666; }
    h2 { font-size:12px; background:#0B1F3A; color:#fff; padding:5px 8px; margin:14px 0 0; }
    h3 { font-size:10px; margin:8px 0 4px; color:#555; border-bottom:1px solid #eee; padding-bottom:2px; }
    table { width:100%; border-collapse:collapse; margin-bottom:8px; }
    thead th { background:#f0f0f0; padding:4px 6px; text-align:left; font-size:9px; border-bottom:1px solid #ccc; }
    tbody td { padding:4px 6px; border-bottom:1px solid #eee; vertical-align:top; }
    .badge { display:inline-block; padding:1px 5px; border-radius:2px; font-size:8px; color:#fff; }
    .s-full { background:#dc3545; }
    .s-partial { background:#ffc107; color:#333; }
    .s-empty { background:#198754; }
    .s-maintenance { background:#6c757d; }
    .summary-row { background:#f8f8f8; font-weight:bold; }
</style>
</head>
<body>
<h1>Hostel Occupancy Report</h1>
<p class="sub">Generated: {{ now()->format('d M Y H:i') }}</p>

<div class="stat-row">
    <div class="stat-box"><div class="val">{{ number_format($totalCapacity) }}</div><div class="lbl">Total Beds</div></div>
    <div class="stat-box"><div class="val">{{ number_format($totalOccupied) }}</div><div class="lbl">Occupied</div></div>
    <div class="stat-box"><div class="val">{{ number_format($totalCapacity - $totalOccupied) }}</div><div class="lbl">Available</div></div>
    <div class="stat-box"><div class="val">{{ $overallRate }}%</div><div class="lbl">Occupancy Rate</div></div>
</div>

@foreach($hostels as $hData)
@php $hostel = $hData['hostel']; @endphp
<h2>{{ $hostel->name }} &mdash; {{ ucfirst($hostel->type) }}
    &nbsp;({{ $hData['occupied'] }}/{{ $hData['capacity'] }} occupied, {{ $hData['rate'] }}%)
</h2>

@forelse($hData['floors'] as $floor => $rooms)
<h3>Flat {{ $floor ?? 'Ground' }}</h3>
<table>
    <thead>
        <tr>
            <th>Room</th>
            <th>Type</th>
            <th>Capacity</th>
            <th>Occupied</th>
            <th>Available</th>
            <th>Status</th>
            <th>Current Residents</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rooms as $room)
        @php
            $occ = $room->activeAllocations->count();
            $avail = max(0, $room->capacity - $occ);
            $isMaint = $room->status === 'maintenance';
            $cls = $isMaint ? 's-maintenance' : ($occ >= $room->capacity ? 's-full' : ($occ === 0 ? 's-empty' : 's-partial'));
            $label = $isMaint ? 'Maintenance' : ($occ >= $room->capacity ? 'Full' : ($occ === 0 ? 'Available' : 'Partial'));
        @endphp
        <tr>
            <td><strong>{{ $room->room_number }}</strong></td>
            <td>{{ ucfirst($room->room_type) }}</td>
            <td>{{ $room->capacity }}</td>
            <td>{{ $occ }}</td>
            <td>{{ $avail }}</td>
            <td><span class="badge {{ $cls }}">{{ $label }}</span></td>
            <td>
                @if($room->activeAllocations->isEmpty())
                    <span style="color:#999">—</span>
                @else
                    {{ $room->activeAllocations->map(fn($a) => optional($a->student?->user)->name ?? '?')->implode(', ') }}
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@empty
<p style="color:#999;font-style:italic">No rooms configured.</p>
@endforelse

@endforeach
</body>
</html>
