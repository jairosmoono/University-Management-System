@extends('layouts.app')
@section('title', 'Record Payment')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Record Payment</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('finance.payments.index') }}">Payments</a></li>
            <li class="breadcrumb-item active">Record</li>
        </ol></nav>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-search me-2 text-primary"></i>Find Student Bill</h6>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <label class="form-label">Search Student</label>
                    <input type="text" id="studentSearch" class="form-control" placeholder="Type student ID or name...">
                    <div id="searchResults" class="list-group mt-2" style="display:none"></div>
                </div>

                <div id="billDetails" style="display:none">
                    <div class="alert alert-info d-flex align-items-center gap-3 mb-4">
                        <i class="bi bi-info-circle fs-4"></i>
                        <div>
                            <strong id="studentName"></strong><br>
                            <small id="studentInfo" class="text-muted"></small>
                        </div>
                    </div>

                    <div class="row g-3 mb-4" id="billSummary">
                        <div class="col-4 text-center">
                            <div class="border rounded p-3">
                                <div class="fs-5 fw-bold text-primary" id="totalAmount">—</div>
                                <small class="text-muted">Total Bill</small>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="border rounded p-3">
                                <div class="fs-5 fw-bold text-success" id="amountPaid">—</div>
                                <small class="text-muted">Paid</small>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="border rounded p-3">
                                <div class="fs-5 fw-bold text-danger" id="balance">—</div>
                                <small class="text-muted">Balance</small>
                            </div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('finance.payments.store') }}" id="paymentForm">
                    @csrf
                    <input type="hidden" name="student_bill_id" id="billId">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Payment Amount (ZMW) *</label>
                            <div class="input-group">
                                <span class="input-group-text">K</span>
                                <input type="number" name="amount" id="payAmount" class="form-control" min="0.01" step="0.01" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Payment Date *</label>
                            <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Payment Method *</label>
                            <div class="row g-2" id="methodSelect">
                                @foreach([
                                    ['Airtel Money','bi-phone','#E40000'],
                                    ['MTN','bi-phone','#FFCC00'],
                                    ['Zamtel','bi-phone','#00A550'],
                                    ['Visa','bi-credit-card','#1A1F71'],
                                    ['Mastercard','bi-credit-card-2-front','#EB001B'],
                                    ['Cash','bi-cash','#28a745'],
                                    ['Bank Transfer','bi-bank','#0B1F3A'],
                                ] as [$method, $icon, $color])
                                <div class="col-md-3 col-6">
                                    <label class="d-block">
                                        <input type="radio" name="payment_method" value="{{ $method }}" class="d-none method-radio">
                                        <div class="border rounded p-2 text-center cursor-pointer method-card" style="transition:all 0.2s">
                                            <i class="bi {{ $icon }} fs-4" style="color:{{ $color }}"></i>
                                            <div class="small mt-1">{{ $method }}</div>
                                        </div>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Transaction Reference</label>
                            <div class="input-group">
                                <input type="text" name="transaction_reference" id="txnRef" class="form-control" placeholder="Auto-generated">
                                <button type="button" class="btn btn-outline-secondary" onclick="generateTxnRef()" title="Regenerate">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </div>
                            <small class="text-muted">Auto-generated. Override if you have an external reference.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Notes</label>
                            <input type="text" name="notes" class="form-control" placeholder="Optional notes">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-lg w-100" id="submitBtn" disabled>
                                <i class="bi bi-check-circle me-1"></i> Record Payment
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-semibold mb-3"><i class="bi bi-info-circle me-2 text-primary"></i>Payment Info</h6>
                <ul class="list-unstyled small text-muted">
                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Payments are immediately applied to the student's bill</li>
                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>A receipt PDF can be generated after recording</li>
                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Mobile money: enter the transaction ID from your phone</li>
                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Bank transfers: enter the bank reference number</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function generateTxnRef() {
    const now = new Date();
    const date = now.getFullYear().toString()
        + String(now.getMonth()+1).padStart(2,'0')
        + String(now.getDate()).padStart(2,'0');
    const rand = Math.random().toString(36).substring(2,10).toUpperCase();
    document.getElementById('txnRef').value = 'TXN' + date + rand;
}
generateTxnRef();

let searchTimeout;
const searchInput = document.getElementById('studentSearch');
const searchResults = document.getElementById('searchResults');

searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    if (this.value.length < 2) { searchResults.style.display = 'none'; return; }
    searchTimeout = setTimeout(() => {
        fetch(`/ajax/students?q=${encodeURIComponent(this.value)}`)
            .then(r => r.json())
            .then(data => {
                searchResults.innerHTML = '';
                if (data.length === 0) {
                    searchResults.innerHTML = '<div class="list-group-item text-muted">No students found</div>';
                } else {
                    data.forEach(student => {
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.className = 'list-group-item list-group-item-action';
                        item.innerHTML = `<strong>${student.student_id}</strong> — ${student.name} <small class="text-muted">(${student.program})</small>`;
                        item.onclick = () => loadStudentBills(student.id, student.name, student.student_id, student.program);
                        searchResults.appendChild(item);
                    });
                }
                searchResults.style.display = 'block';
            });
    }, 300);
});

function loadStudentBills(studentId, name, sid, program) {
    searchResults.style.display = 'none';
    searchInput.value = `${sid} — ${name}`;
    fetch(`/ajax/student-bills?student_id=${studentId}`)
        .then(r => r.json())
        .then(bill => {
            if (!bill) { alert('No outstanding bill found for this student.'); return; }
            document.getElementById('billDetails').style.display = 'block';
            document.getElementById('studentName').textContent = name;
            document.getElementById('studentInfo').textContent = `${sid} • ${program}`;
            document.getElementById('totalAmount').textContent = 'K ' + parseFloat(bill.total_amount).toFixed(2);
            document.getElementById('amountPaid').textContent = 'K ' + parseFloat(bill.amount_paid).toFixed(2);
            document.getElementById('balance').textContent = 'K ' + parseFloat(bill.balance).toFixed(2);
            document.getElementById('billId').value = bill.id;
            document.getElementById('payAmount').max = bill.balance;
            checkSubmitReady();
        });
}

// Method card selection
document.querySelectorAll('.method-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.method-card').forEach(c => {
            c.classList.remove('border-primary', 'bg-primary-subtle');
            c.style.borderColor = '';
        });
        this.nextElementSibling.classList.add('border-primary', 'bg-primary-subtle');
        checkSubmitReady();
    });
});

function checkSubmitReady() {
    const billSelected   = document.getElementById('billId').value !== '';
    const methodSelected = document.querySelector('.method-radio:checked') !== null;
    document.getElementById('submitBtn').disabled = !(billSelected && methodSelected);
}

// Prevent submitting without a method selected
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    if (!document.querySelector('.method-radio:checked')) {
        e.preventDefault();
        document.getElementById('methodSelect').scrollIntoView({behavior:'smooth'});
        document.getElementById('methodSelect').classList.add('border', 'border-danger', 'rounded', 'p-2');
        setTimeout(() => document.getElementById('methodSelect').classList.remove('border', 'border-danger', 'rounded', 'p-2'), 2000);
    }
});
</script>
@endpush
@endsection
