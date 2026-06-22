@extends('master_page.master_page')
@section('page_title', 'Facility Bookings')

@push('styles')
<link href="../assets/plugins/datatable/css/dataTables.bootstrap5.css" rel="stylesheet">
@endpush

@section('content')

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Facility Bookings</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard_utama') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Manage Bookings</li>
        </ol>
    </div>
</div>

{{-- Tabs --}}
<ul class="nav nav-tabs mg-b-20" id="bookingAdminTabs" role="tablist">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-pending">
        Pending Approval <span class="badge bg-warning text-dark ms-1" id="pendingCountBadge">0</span>
    </a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-all-bookings">All Bookings</a></li>
</ul>

<div class="tab-content">
    {{-- Pending Tab --}}
    <div class="tab-pane fade show active" id="tab-pending">
        <div class="card custom-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="pending-datatable" class="table table-striped table-bordered text-nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Resident</th>
                                <th>Facility</th>
                                <th>Date</th>
                                <th>Time Slot</th>
                                <th>Purpose</th>
                                <th>Submitted</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{-- All Bookings Tab --}}
    <div class="tab-pane fade" id="tab-all-bookings">
        <div class="card custom-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="allbookings-datatable" class="table table-striped table-bordered text-nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Resident</th>
                                <th>Facility</th>
                                <th>Date</th>
                                <th>Time Slot</th>
                                <th>Purpose</th>
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

{{-- View Booking Modal (admin) --}}
<div class="modal fade" id="adminViewBookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fe fe-calendar me-2"></i>Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless tx-13 mb-0">
                    <tr><th width="40%">Resident</th><td id="adm-bk-resident">—</td></tr>
                    <tr><th>Email</th><td id="adm-bk-email">—</td></tr>
                    <tr><th>Facility</th><td id="adm-bk-facility">—</td></tr>
                    <tr><th>Date</th><td id="adm-bk-date">—</td></tr>
                    <tr><th>Time Slot</th><td id="adm-bk-slot">—</td></tr>
                    <tr><th>Purpose</th><td id="adm-bk-purpose">—</td></tr>
                    <tr><th>Attendees</th><td id="adm-bk-attendees">—</td></tr>
                    <tr><th>Status</th><td id="adm-bk-status">—</td></tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger btn-sm d-none" id="adminRejectBtn">
                    <i class="fe fe-x me-1"></i> Reject
                </button>
                <button type="button" class="btn btn-success btn-sm d-none" id="adminApproveBtn">
                    <i class="fe fe-check me-1"></i> Approve
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="../assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatable/js/dataTables.bootstrap5.js"></script>
<script>
var statusConfig = {
    pending:   { cls: 'bg-warning text-dark', label: 'Pending'   },
    approved:  { cls: 'bg-success',           label: 'Approved'  },
    rejected:  { cls: 'bg-danger',            label: 'Rejected'  },
    cancelled: { cls: 'bg-secondary',         label: 'Cancelled' },
};

function slotLabel(start) {
    if (!start) return '—';
    if (start < '12:00:00') return 'Morning (6AM–12PM)';
    if (start < '18:00:00') return 'Afternoon (12PM–6PM)';
    return 'Evening (6PM–10PM)';
}

var allData     = {};
var pendingData = {};

function reloadBoth() {
    dtPending.ajax.reload(null, false);
    dtAll.ajax.reload(null, false);
}

$(function () {
    var dtPending = $('#pending-datatable').DataTable({
        ajax: {
            url:     "{{ route('allBookings.pending') }}",
            dataSrc: function (json) {
                pendingData = {};
                var rows = json.data || [];
                rows.forEach(function (r) { pendingData[r.id] = r; });
                $('#pendingCountBadge').text(rows.length);
                return rows;
            },
        },
        columns: [
            { data: null, render: function (d, t, r, m) { return m.row + 1; } },
            { data: 'resident_name' },
            { data: 'facility_name' },
            { data: 'booking_date' },
            { data: 'start_time', render: function (d) { return slotLabel(d); } },
            { data: 'purpose', defaultContent: '—', render: function (d) {
                return d && d.length > 30 ? d.slice(0, 30) + '…' : (d || '—');
            }},
            { data: 'created_at', render: function (d) { return d ? d.slice(0, 10) : '—'; } },
            { data: null, render: function (d, t, r) {
                return '<div class="text-center">'
                    + '<button class="btn btn-sm btn-outline-success me-1 btn-approve-inline" data-id="' + r.id + '" title="Approve"><i class="fe fe-check"></i></button>'
                    + '<button class="btn btn-sm btn-outline-danger me-1 btn-reject-inline" data-id="' + r.id + '" title="Reject"><i class="fe fe-x"></i></button>'
                    + '<button class="btn btn-sm btn-outline-info btn-view-adm" data-id="' + r.id + '" data-src="pending" title="View"><i class="fe fe-eye"></i></button>'
                    + '</div>';
            }},
        ],
        columnDefs: [{ orderable: false, targets: [0, 7] }],
        language: { emptyTable: 'No pending bookings.' },
    });

    var dtAll = $('#allbookings-datatable').DataTable({
        ajax: {
            url:     "{{ route('allBookings.list') }}",
            dataSrc: function (json) {
                allData = {};
                (json.data || []).forEach(function (r) { allData[r.id] = r; });
                return json.data || [];
            },
        },
        columns: [
            { data: null, render: function (d, t, r, m) { return m.row + 1; } },
            { data: 'resident_name' },
            { data: 'facility_name' },
            { data: 'booking_date' },
            { data: 'start_time', render: function (d) { return slotLabel(d); } },
            { data: 'purpose', defaultContent: '—', render: function (d) {
                return d && d.length > 30 ? d.slice(0, 30) + '…' : (d || '—');
            }},
            { data: 'status', render: function (d) {
                var c = statusConfig[d] || { cls: 'bg-light text-dark', label: d };
                return '<span class="badge ' + c.cls + '">' + c.label + '</span>';
            }},
            { data: null, render: function (d, t, r) {
                return '<div class="text-center">'
                    + '<button class="btn btn-sm btn-outline-info btn-view-adm" data-id="' + r.id + '" data-src="all"><i class="fe fe-eye"></i></button>'
                    + '</div>';
            }},
        ],
        columnDefs: [{ orderable: false, targets: [0, 7] }],
        language: { emptyTable: 'No bookings found.' },
    });

    // View detail
    $(document).on('click', '.btn-view-adm', function () {
        var src = $(this).data('src');
        var r   = src === 'pending' ? pendingData[$(this).data('id')] : allData[$(this).data('id')];
        if (!r) return;
        var sc = statusConfig[r.status] || { cls: 'bg-secondary', label: r.status };
        $('#adm-bk-resident').text(r.resident_name);
        $('#adm-bk-email').text(r.resident_email || '—');
        $('#adm-bk-facility').text(r.facility_name);
        $('#adm-bk-date').text(r.booking_date);
        $('#adm-bk-slot').text(slotLabel(r.start_time));
        $('#adm-bk-purpose').text(r.purpose || '—');
        $('#adm-bk-attendees').text(r.attendees || '—');
        $('#adm-bk-status').html('<span class="badge ' + sc.cls + '">' + sc.label + '</span>');

        if (r.status === 'pending') {
            $('#adminApproveBtn').removeClass('d-none').data('id', r.id);
            $('#adminRejectBtn').removeClass('d-none').data('id', r.id);
        } else {
            $('#adminApproveBtn').addClass('d-none');
            $('#adminRejectBtn').addClass('d-none');
        }
        $('#adminViewBookingModal').modal('show');
    });

    // Inline approve/reject from pending table
    $(document).on('click', '.btn-approve-inline', function () { doAction($(this).data('id'), 'approve'); });
    $(document).on('click', '.btn-reject-inline',  function () { doAction($(this).data('id'), 'reject');  });

    // Modal approve/reject
    $('#adminApproveBtn').on('click', function () { $('#adminViewBookingModal').modal('hide'); doAction($(this).data('id'), 'approve'); });
    $('#adminRejectBtn').on('click',  function () { $('#adminViewBookingModal').modal('hide'); doAction($(this).data('id'), 'reject');  });

    function doAction(id, action) {
        var label = action === 'approve' ? 'Approve' : 'Reject';
        Swal.fire({
            icon:              action === 'approve' ? 'question' : 'warning',
            title:             label + ' Booking?',
            showCancelButton:  true,
            confirmButtonText: 'Yes, ' + label,
            cancelButtonText:  'Cancel',
            confirmButtonColor: action === 'approve' ? '#28a745' : '#dc3545',
            cancelButtonColor:  '#6c757d',
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.post({
                url:         "{{ route('allBookings.update') }}",
                data:        JSON.stringify({ action: action, id: id }),
                contentType: 'application/json',
                success: function (res) {
                    if (res.status) {
                        dtPending.ajax.reload(null, false);
                        dtAll.ajax.reload(null, false);
                        Swal.fire({ icon: 'success', title: 'Done', text: res.message, confirmButtonColor: '#6259ca', timer: 2000, showConfirmButton: false });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Failed', text: res.message, confirmButtonColor: '#6259ca' });
                    }
                },
                error: function () {
                    Swal.fire({ icon: 'error', title: 'Server Error', confirmButtonColor: '#6259ca' });
                },
            });
        });
    }
});
</script>
@endpush
