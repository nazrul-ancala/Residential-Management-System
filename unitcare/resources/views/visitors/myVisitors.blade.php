@extends('master_page.master_page')
@section('page_title', 'My Visitors')

@push('styles')
<link href="../assets/plugins/datatable/css/dataTables.bootstrap5.css" rel="stylesheet">
<style>
@media print {
    @page { margin: 1cm; }
    * { visibility: hidden !important; }
    #printable-pass { display: block !important; }
    #printable-pass,
    #printable-pass * { visibility: visible !important; }
    #printable-pass {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
    }
}
</style>
@endpush

@section('content')

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">
            {{ auth()->user()->role === 'admin' ? 'All Visitors' : 'My Visitors' }}
        </h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard_utama') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">
                {{ auth()->user()->role === 'admin' ? 'All Visitors' : 'My Visitors' }}
            </li>
        </ol>
    </div>
    <div class="d-flex">
        <a href="{{ route('registerVisitor') }}" class="btn btn-primary btn-icon-text">
            <i class="fe fe-user-plus me-2"></i> Register New Visitor
        </a>
    </div>
</div>

{{-- Visitors Table --}}
<div class="row row-sm">
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="visitors-datatable" class="table table-striped table-bordered text-nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Visitor Name</th>
                                <th>IC / Passport</th>
                                @if(auth()->user()->role === 'admin')
                                <th>Resident</th>
                                <th>Unit</th>
                                @endif
                                <th>Visit Date</th>
                                <th>Visit Time</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- QR Pass Modal --}}
<div class="modal fade" id="qrPassModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fe fe-grid me-2"></i>Visitor Pass</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <p class="fw-semibold mb-1" id="qr-modal-name">—</p>
                <p class="text-muted tx-12 mb-3" id="qr-modal-datetime">—</p>
                <div class="border rounded p-3 d-inline-block mb-3" style="background:#f8f9fa;">
                    <div id="qr-modal-code" style="display:inline-block;"></div>
                </div>
                <p class="text-muted tx-11">Show this pass code to the security guard upon arrival.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button class="btn btn-outline-primary btn-sm" onclick="printPass()">
                    <i class="fe fe-printer me-1"></i> Print
                </button>
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


{{-- Hidden print-only pass card --}}
<div id="printable-pass" style="display:none;">
    <div style="font-family:sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:2rem;box-sizing:border-box;">
        <div style="width:100%;max-width:480px;border:2px solid #6259ca;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.12);">
            <div style="background:#6259ca;color:#fff;padding:1.4rem 1.8rem;text-align:center;">
                <div style="font-size:1.5rem;font-weight:800;letter-spacing:.08em;">UNITCARE</div>
                <div style="font-size:.8rem;letter-spacing:.18em;opacity:.85;margin-top:.2rem;">VISITOR ACCESS PASS</div>
            </div>
            <div style="background:#f8f8ff;padding:1.8rem 1.8rem 1rem;text-align:center;border-bottom:1px solid #e5e5f0;">
                <div id="pp-code" style="display:inline-block;border:6px solid #fff;box-shadow:0 2px 10px rgba(0,0,0,.1);border-radius:6px;"></div>
                <div id="pp-qrtext" style="font-family:monospace;font-size:.85rem;color:#6259ca;font-weight:700;margin-top:.6rem;letter-spacing:.05em;"></div>
            </div>
            <div style="padding:1.2rem 1.8rem;border-bottom:1px solid #eef0f8;">
                <div style="font-size:.65rem;letter-spacing:.12em;color:#999;text-transform:uppercase;margin-bottom:.35rem;">Visitor</div>
                <div id="pp-name" style="font-size:1.25rem;font-weight:700;color:#1a1a2e;"></div>
                <div id="pp-ic"   style="font-size:.85rem;color:#666;margin-top:.2rem;"></div>
            </div>
            <div style="display:flex;padding:1rem 1.8rem;border-bottom:1px solid #eef0f8;gap:1rem;">
                <div style="flex:1;">
                    <div style="font-size:.65rem;letter-spacing:.12em;color:#999;text-transform:uppercase;margin-bottom:.25rem;">Visit Date</div>
                    <div id="pp-date" style="font-weight:600;color:#1a1a2e;"></div>
                </div>
                <div style="flex:1;">
                    <div style="font-size:.65rem;letter-spacing:.12em;color:#999;text-transform:uppercase;margin-bottom:.25rem;">Visit Time</div>
                    <div id="pp-time" style="font-weight:600;color:#1a1a2e;"></div>
                </div>
            </div>
            <div style="padding:1rem 1.8rem;border-bottom:1px solid #eef0f8;">
                <div style="font-size:.65rem;letter-spacing:.12em;color:#999;text-transform:uppercase;margin-bottom:.25rem;">Purpose of Visit</div>
                <div id="pp-purpose" style="font-size:.875rem;color:#333;"></div>
            </div>
            <div style="background:#f5f5fb;padding:.8rem 1.8rem;text-align:center;font-size:.75rem;color:#888;">
                Valid for selected date only &nbsp;&middot;&nbsp; Present to security guard upon arrival
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="../assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatable/js/dataTables.bootstrap5.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
var currentQrCode = '', currentIc = '', currentPurpose = '', currentDate = '', currentTime = '', currentName = '';

function generateQR(elementId, text, size) {
    size = size || 160;
    var el = document.getElementById(elementId);
    el.innerHTML = '';
    if (text) {
        new QRCode(el, { text: text, width: size, height: size,
                         colorDark: '#000000', colorLight: '#ffffff' });
    }
}

function printPass() {
    document.getElementById('pp-name').textContent    = currentName;
    document.getElementById('pp-ic').textContent      = currentIc;
    document.getElementById('pp-date').textContent    = currentDate;
    document.getElementById('pp-time').textContent    = currentTime;
    document.getElementById('pp-purpose').textContent = currentPurpose;
    document.getElementById('pp-qrtext').textContent  = currentQrCode;
    generateQR('pp-code', currentQrCode, 200);
    var origTitle = document.title;
    document.title = 'Visitor Pass - ' + currentQrCode;
    window.onafterprint = function () { document.title = origTitle; window.onafterprint = null; };
    setTimeout(function () { window.print(); }, 150);
}

$(function () {
    var isAdmin = @json(auth()->user()->role === 'admin');
    var registerUrl = "{{ route('registerVisitor') }}";

    function statusBadge(s) {
        var map = {
            'pending':    '<span class="badge bg-secondary">Pending</span>',
            'checked_in': '<span class="badge bg-primary">Checked-In</span>',
            'completed':  '<span class="badge bg-success">Completed</span>',
            'cancelled':  '<span class="badge bg-dark">Cancelled</span>',
        };
        return map[s] || '<span class="badge bg-light text-dark">' + s + '</span>';
    }

    var colDefs = [];
    var columns = [
        { data: null, render: function (d, t, r, m) { return m.row + 1; } },
        { data: 'name' },
        { data: 'ic_passport' },
    ];

    if (isAdmin) {
        columns.push({ data: 'resident_name', defaultContent: '—' });
        columns.push({ data: function (r) {
            return (r.block_name || '') + (r.unit_number ? '-' + r.unit_number : '');
        }});
        colDefs = [{ orderable: false, targets: 8 }];
    } else {
        colDefs = [{ orderable: false, targets: 6 }];
    }

    columns.push({ data: 'visit_date' });
    columns.push({ data: 'visit_time' });
    columns.push({ data: 'status', render: function (d) { return statusBadge(d); } });
    columns.push({
        data: null,
        render: function (d, t, r) {
            var btns = '<button class="btn btn-sm btn-outline-primary me-1 btn-view-pass" '
                + 'data-name="'    + r.name              + '" '
                + 'data-date="'    + r.visit_date         + '" '
                + 'data-time="'    + r.visit_time         + '" '
                + 'data-code="'    + (r.qr_code      || '') + '" '
                + 'data-ic="'      + (r.ic_passport   || '') + '" '
                + 'data-purpose="' + (r.purpose       || '') + '">'
                + '<i class="fe fe-grid"></i></button>';
            if (r.status === 'pending') {
                btns += '<button class="btn btn-sm btn-outline-danger btn-cancel-pass" '
                    + 'data-id="' + r.id + '" data-name="' + r.name + '">'
                    + '<i class="fe fe-x"></i></button>';
            }
            return '<div class="text-center">' + btns + '</div>';
        }
    });

    $('#visitors-datatable').DataTable({
        ajax: {
            url: "{{ route('myVisitors.list') }}",
            dataSrc: 'data',
        },
        columns: columns,
        columnDefs: colDefs,
        language: {
            emptyTable: '<div class="text-center py-4"><i class="fe fe-users" style="font-size:2.2rem;opacity:.3;display:block;margin-bottom:.5rem;"></i><p class="text-muted mb-2">No visitor records found.</p><a href="' + registerUrl + '" class="btn btn-sm btn-primary">Register a Visitor</a></div>'
        },
    });

    $(document).on('click', '.btn-view-pass', function () {
        currentName    = $(this).data('name')    || '';
        currentDate    = $(this).data('date')    || '';
        currentTime    = $(this).data('time')    || '';
        currentQrCode  = $(this).data('code')    || '';
        currentIc      = $(this).data('ic')      || '';
        currentPurpose = $(this).data('purpose') || '';
        $('#qr-modal-name').text(currentName);
        $('#qr-modal-datetime').text(currentDate + ' at ' + currentTime);
        generateQR('qr-modal-code', currentQrCode, 160);
        $('#qrPassModal').modal('show');
    });

    $(document).on('click', '.btn-cancel-pass', function () {
        var id   = $(this).data('id');
        var name = $(this).data('name');
        Swal.fire({
            icon: 'warning',
            title: 'Cancel Pass?',
            text: 'Cancel the visitor pass for ' + name + '? This cannot be undone.',
            showCancelButton: true,
            confirmButtonText: 'Yes, Cancel Pass',
            cancelButtonText: 'Keep',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d'
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.post({
                url: "{{ route('visitor.store') }}",
                data: JSON.stringify({ action: 'cancel', id: id }),
                contentType: 'application/json',
                success: function (res) {
                    if (res.status) {
                        $('#visitors-datatable').DataTable().ajax.reload(null, false);
                    } else {
                        Swal.fire({ icon: 'error', title: 'Failed', text: res.message || 'Unknown error.', confirmButtonColor: '#6259ca' });
                    }
                },
                error: function () {
                    Swal.fire({ icon: 'error', title: 'Server Error', text: 'Could not reach the server.', confirmButtonColor: '#6259ca' });
                }
            });
        });
    });
});
</script>
@endpush
