@extends('layouts.app')
@section('title', 'Room Allocations')

@push('styles')
<style>
.searchable-select { position: relative; }
.searchable-select .ss-input-wrap {
    display: flex; align-items: center; gap: 6px;
    border: 1px solid #dee2e6; border-radius: 0.375rem;
    padding: 0.375rem 0.75rem; background: #fff; cursor: text;
}
.searchable-select .ss-input-wrap.is-invalid { border-color: #dc3545; }
.searchable-select .ss-input-wrap input {
    border: none; outline: none; flex: 1; font-size: 0.875rem;
    background: transparent; padding: 0; min-width: 0;
}
.searchable-select .ss-badge {
    display: flex; align-items: center; gap: 4px;
    background: #e9ecef; border-radius: 0.25rem;
    padding: 2px 6px; font-size: 0.8rem; white-space: nowrap;
}
.searchable-select .ss-badge button {
    background: none; border: none; padding: 0; line-height: 1;
    color: #6c757d; cursor: pointer; font-size: 1rem;
}
.searchable-select .ss-badge button:hover { color: #dc3545; }
.searchable-select .ss-dropdown {
    position: absolute; top: 100%; left: 0; right: 0; z-index: 9999;
    background: #fff; border: 1px solid #dee2e6; border-radius: 0.375rem;
    box-shadow: 0 4px 16px rgba(0,0,0,.12); max-height: 240px;
    overflow-y: auto; display: none; margin-top: 2px;
}
.searchable-select .ss-dropdown.open { display: block; }
.searchable-select .ss-option {
    padding: 8px 12px; cursor: pointer; font-size: 0.875rem;
    border-bottom: 1px solid #f0f0f0;
}
.searchable-select .ss-option:last-child { border-bottom: none; }
.searchable-select .ss-option:hover,
.searchable-select .ss-option.focused { background: #e8f0fe; }
.searchable-select .ss-option .ss-id {
    font-size: 0.75rem; color: #6c757d; margin-left: 4px;
}
.searchable-select .ss-empty {
    padding: 10px 12px; font-size: 0.85rem; color: #6c757d; text-align: center;
}
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Room Allocations</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('hostel.hostels.index') }}">Hostels</a></li>
            <li class="breadcrumb-item active">Allocations</li>
        </ol></nav>
    </div>
    @can('manage-hostel')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#allocateModal">
        <i class="bi bi-plus-circle me-1"></i> Allocate Room
    </button>
    @endcan
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="hostel_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Hostels</option>
                    @foreach($hostels as $hostel)
                    <option value="{{ $hostel->id }}" {{ request('hostel_id') == $hostel->id ? 'selected' : '' }}>{{ $hostel->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="vacated" {{ request('status') == 'vacated' ? 'selected' : '' }}>Vacated</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search student..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-primary">Filter</button>
                <a href="{{ route('hostel.allocations.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table class="table datatable table-hover">
            <thead class="table-light">
                <tr>
                    <th>Student ID</th><th>Student Name</th><th>Room No.</th><th>Hostel</th><th>Allocated</th><th>Expected Vacate</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($allocations as $alloc)
                <tr>
                    <td><code>{{ optional($alloc->student)->student_id }}</code></td>
                    <td>{{ optional(optional($alloc->student)->user)->name }}</td>
                    <td class="fw-semibold">{{ optional($alloc->hostelRoom)->room_number }}</td>
                    <td>{{ optional(optional($alloc->hostelRoom)->hostel)->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($alloc->allocation_date)->format('d M Y') }}</td>
                    <td>{{ $alloc->expected_vacate_date ? \Carbon\Carbon::parse($alloc->expected_vacate_date)->format('d M Y') : '—' }}</td>
                    <td>
                        <span class="badge bg-{{ $alloc->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($alloc->status) }}</span>
                    </td>
                    <td>
                        @can('manage-hostel')
                        @if($alloc->status === 'active')
                        <form method="POST" action="{{ route('hostel.allocations.checkout', $alloc) }}" class="d-inline" onsubmit="return confirm('Mark this allocation as vacated?')">
                            @csrf
                            <button class="btn btn-sm btn-outline-warning">Vacate</button>
                        </form>
                        @endif
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $allocations->withQueryString()->links() }}
    </div>
</div>

@can('manage-hostel')
<div class="modal fade" id="allocateModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('hostel.allocations.assign') }}" id="allocateForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Allocate Room</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    {{-- Student searchable select --}}
                    <div class="mb-3">
                        <label class="form-label">Student <span class="text-danger">*</span></label>
                        <input type="hidden" name="student_id" id="student_id_input">
                        <div class="searchable-select" id="studentPicker">
                            <div class="ss-input-wrap" id="studentWrap">
                                <input type="text" id="studentSearch" placeholder="Type name or student ID…" autocomplete="off">
                            </div>
                            <div class="ss-dropdown" id="studentDropdown">
                                @forelse($students as $s)
                                <div class="ss-option"
                                     data-value="{{ $s->id }}"
                                     data-label="{{ optional($s->user)->name }}"
                                     data-id="{{ $s->student_id }}">
                                    {{ optional($s->user)->name }}
                                    <span class="ss-id">{{ $s->student_id }}</span>
                                </div>
                                @empty
                                <div class="ss-empty">No registered students found.</div>
                                @endforelse
                            </div>
                        </div>
                        <div class="invalid-feedback" style="display:none" id="studentError">Please select a student.</div>
                    </div>

                    {{-- Room searchable select --}}
                    <div class="mb-3">
                        <label class="form-label">Room <span class="text-danger">*</span></label>
                        <input type="hidden" name="hostel_room_id" id="room_id_input">
                        <div class="searchable-select" id="roomPicker">
                            <div class="ss-input-wrap" id="roomWrap">
                                <input type="text" id="roomSearch" placeholder="Type hostel name or room number…" autocomplete="off">
                            </div>
                            <div class="ss-dropdown" id="roomDropdown">
                                @forelse($availableRooms as $room)
                                <div class="ss-option"
                                     data-value="{{ $room->id }}"
                                     data-label="{{ optional($room->hostel)->name }} — {{ $room->room_number }}"
                                     data-meta="{{ ucfirst($room->room_type) }}, {{ $room->available_beds }} bed{{ $room->available_beds == 1 ? '' : 's' }} free">
                                    {{ optional($room->hostel)->name }} — {{ $room->room_number }}
                                    <span class="ss-id">{{ ucfirst($room->room_type) }}, {{ $room->available_beds }} bed{{ $room->available_beds == 1 ? '' : 's' }} free</span>
                                </div>
                                @empty
                                <div class="ss-empty">No available rooms found.</div>
                                @endforelse
                            </div>
                        </div>
                        <div class="invalid-feedback" style="display:none" id="roomError">Please select a room.</div>
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label">Allocation Date <span class="text-danger">*</span></label>
                            <input type="date" name="allocation_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Expected Vacate Date</label>
                            <input type="date" name="expected_vacate_date" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Allocate</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endcan
@endsection

@push('scripts')
<script>
(function () {
    function makeSearchable(cfg) {
        // cfg: { searchId, dropdownId, wrapperId, hiddenId, errorId }
        const search   = document.getElementById(cfg.searchId);
        const dropdown = document.getElementById(cfg.dropdownId);
        const wrap     = document.getElementById(cfg.wrapperId);
        const hidden   = document.getElementById(cfg.hiddenId);
        const errEl    = document.getElementById(cfg.errorId);

        if (!search) return;

        const allOptions = Array.from(dropdown.querySelectorAll('.ss-option'));
        let selectedValue = '';

        function filterOptions(q) {
            const term = q.toLowerCase();
            let visible = 0;
            allOptions.forEach(opt => {
                const text = (opt.textContent || '').toLowerCase();
                const match = !term || text.includes(term);
                opt.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            // show/hide empty message
            let empty = dropdown.querySelector('.ss-empty');
            if (!empty && visible === 0) {
                empty = document.createElement('div');
                empty.className = 'ss-empty';
                empty.textContent = 'No results found.';
                dropdown.appendChild(empty);
            } else if (empty) {
                empty.style.display = visible === 0 ? '' : 'none';
            }
        }

        function openDropdown() {
            dropdown.classList.add('open');
            filterOptions(search.value);
        }

        function closeDropdown() {
            dropdown.classList.remove('open');
        }

        function selectOption(opt) {
            selectedValue = opt.dataset.value;
            hidden.value  = selectedValue;
            // show badge inside wrap
            clearBadge();
            const badge = document.createElement('span');
            badge.className = 'ss-badge';
            badge.innerHTML = `${opt.dataset.label} <button type="button" aria-label="Clear">&times;</button>`;
            badge.querySelector('button').addEventListener('click', function (e) {
                e.stopPropagation();
                clearSelection();
            });
            wrap.insertBefore(badge, search);
            search.value = '';
            search.placeholder = '';
            wrap.classList.remove('is-invalid');
            errEl.style.display = 'none';
            closeDropdown();
        }

        function clearBadge() {
            const b = wrap.querySelector('.ss-badge');
            if (b) b.remove();
        }

        function clearSelection() {
            selectedValue = '';
            hidden.value  = '';
            clearBadge();
            search.placeholder = cfg.placeholder;
            search.focus();
            filterOptions('');
            openDropdown();
        }

        // events
        search.addEventListener('focus', openDropdown);
        search.addEventListener('input', function () {
            filterOptions(this.value);
            if (!dropdown.classList.contains('open')) openDropdown();
        });

        dropdown.addEventListener('mousedown', function (e) {
            const opt = e.target.closest('.ss-option');
            if (!opt) return;
            e.preventDefault();
            selectOption(opt);
        });

        // keyboard navigation
        search.addEventListener('keydown', function (e) {
            const visible = allOptions.filter(o => o.style.display !== 'none');
            const focused = dropdown.querySelector('.ss-option.focused');
            let idx = visible.indexOf(focused);
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (focused) focused.classList.remove('focused');
                idx = Math.min(idx + 1, visible.length - 1);
                if (visible[idx]) { visible[idx].classList.add('focused'); visible[idx].scrollIntoView({block:'nearest'}); }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (focused) focused.classList.remove('focused');
                idx = Math.max(idx - 1, 0);
                if (visible[idx]) { visible[idx].classList.add('focused'); visible[idx].scrollIntoView({block:'nearest'}); }
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (focused) selectOption(focused);
            } else if (e.key === 'Escape') {
                closeDropdown();
            }
        });

        // close on outside click
        document.addEventListener('mousedown', function (e) {
            if (!wrap.closest('.searchable-select').contains(e.target)) closeDropdown();
        });

        // validate on form submit
        const form = document.getElementById('allocateForm');
        if (form && !form._ssValidatorAttached) {
            form._ssValidatorAttached = true;
            form.addEventListener('submit', function (e) {
                let ok = true;
                // validate student
                if (!document.getElementById('student_id_input').value) {
                    document.getElementById('studentWrap').classList.add('is-invalid');
                    document.getElementById('studentError').style.display = 'block';
                    ok = false;
                }
                // validate room
                if (!document.getElementById('room_id_input').value) {
                    document.getElementById('roomWrap').classList.add('is-invalid');
                    document.getElementById('roomError').style.display = 'block';
                    ok = false;
                }
                if (!ok) e.preventDefault();
            });
        }

        // reset on modal close
        const modal = document.getElementById('allocateModal');
        if (modal) {
            modal.addEventListener('hidden.bs.modal', function () {
                clearSelection();
                wrap.classList.remove('is-invalid');
                errEl.style.display = 'none';
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        makeSearchable({
            searchId:   'studentSearch',
            dropdownId: 'studentDropdown',
            wrapperId:  'studentWrap',
            hiddenId:   'student_id_input',
            errorId:    'studentError',
            placeholder: 'Type name or student ID…',
        });
        makeSearchable({
            searchId:   'roomSearch',
            dropdownId: 'roomDropdown',
            wrapperId:  'roomWrap',
            hiddenId:   'room_id_input',
            errorId:    'roomError',
            placeholder: 'Type hostel name or room number…',
        });
    });
})();
</script>
@endpush
