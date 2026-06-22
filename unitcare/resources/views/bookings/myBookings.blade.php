@extends('master_page.master_page')
@section('page_title', 'My Bookings')

@push('styles')
<link href="../assets/plugins/datatable/css/dataTables.bootstrap5.css" rel="stylesheet">
@endpush

@section('content')

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">My Bookings</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard_utama') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">My Bookings</li>
        </ol>
    </div>
    <div class="d-flex">
        <a href="{{ route('bookFacility') }}" class="btn btn-primary btn-icon-text">
            <i class="fe fe-calendar me-2"></i> Book a Facility
        </a>
    </div>
</div>

<div class="row row-sm">
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="mybookings-datatable" class="table table-striped table-bordered text-nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
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

{{-- View Booking Modal --}}
<div class="modal fade" id="viewBookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fe fe-calendar me-2"></i>Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless tx-13 mb-0">
                    <tr><th width="40%">Facility</th><td id="bk-facility">—</td></tr>
                    <tr><th>Date</th><td id="bk-date">—</td></tr>
                    <tr><th>Time Slot</th><td id="bk-slot">—</td></tr>
                    <tr><th>Purpose</th><td id="bk-purpose">—</td></tr>
                    <tr><th>Attendees</th><td id="bk-attendees">—</td></tr>
                    <tr><th>Status</th><td id="bk-status">—</td></tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-outline-danger btn-sm d-none" id="cancelBookingBtn">
                    <i class="fe fe-x me-1"></i> Cancel Booking
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
    if (start <= '06:00:00' || start < '12:00:00') return 'Morning (6AM–12PM)';
    if (start < '18:00:00') return 'Afternoon (12PM–6PM)';
    return 'Evening (6PM–10PM)';
}

var bookingsData = {};

$(function () {
    var bookUrl = "{{ route('bookFacility') }}";

    var dt = $('#mybookings-datatable').DataTable({
        ajax: {
            url:     "{{ route('myBookings.list') }}",
            dataSrc: function (json) {
                bookingsData = {};
                (json.data || []).forEach(function (r) { bookingsData[r.id] = r; });
                return json.data || [];
            },
        },
        columns: [
            { data: null, render: function (d, t, r, m) { return m.row + 1; } },
            { data: 'facility_name' },
            { data: 'booking_date' },
            { data: 'start_time', render: function (d) { return slotLabel(d); } },
            { data: 'purpose', defaultContent: '—', render: function (d) {
                return d && d.length > 40 ? d.slice(0, 40) + '…' : (d || '—');
            }},
            { data: 'status', render: function (d) {
                var c = statusConfig[d] || { cls: 'bg-light text-dark', label: d };
                return '<span class="badge ' + c.cls + '">' + c.label + '</span>';
            }},
            { data: null, render: function (d, t, r) {
                return '<div class="text-center">'
                    + '<button class="btn btn-sm btn-outline-info btn-view-bk" data-id="' + r.id + '"><i class="fe fe-eye"></i></button>'
                    + '</div>';
            }},
        ],
        columnDefs: [{ orderable: false, targets: [0, 6] }],
        language: { emptyTable: '<div class="text-center py-4"><i class="fe fe-calendar" style="font-size:2.2rem;opacity:.3;display:block;margin-bottom:.5rem;"></i><p class="text-muted mb-2">You have no bookings yet.</p><a href="' + bookUrl + '" class="btn btn-sm btn-primary">Book a Facility</a></div>' },
    });

    // View
    $(document).on('click', '.btn-view-bk', function () {
        var r = bookingsData[$(this).data('id')];
        if (!r) return;
        var sc = statusConfig[r.status] || { cls: 'bg-secondary', label: r.status };
        $('#bk-facility').text(r.facility_name);
        $('#bk-date').text(r.booking_date);
        $('#bk-slot').text(slotLabel(r.start_time));
        $('#bk-purpose').text(r.purpose || '—');
        $('#bk-attendees').text(r.attendees || '—');
        $('#bk-status').html('<span class="badge ' + sc.cls + '">' + sc.label + '</span>');

        if (r.status === 'pending') {
            $('#cancelBookingBtn').removeClass('d-none').data('id', r.id);
        } else {
            $('#cancelBookingBtn').addClass('d-none');
        }
        $('#viewBookingModal').modal('show');
    });

    // Cancel
    $('#cancelBookingBtn').on('click', function () {
        var id = $(this).data('id');
        $('#viewBookingModal').modal('hide');
        Swal.fire({
            icon:              'warning',
            title:             'Cancel Booking?',
            text:              'This will cancel your pending booking request.',
            showCancelButton:  true,
            confirmButtonText: 'Yes, Cancel It',
            cancelButtonText:  'Keep It',
            confirmButtonColor:'#dc3545',
            cancelButtonColor: '#6c757d',
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.post({
                url:         "{{ route('myBookings.cancel') }}",
                data:        JSON.stringify({ id: id }),
                contentType: 'application/json',
                success: function (res) {
                    if (res.status) {
                        dt.ajax.reload(null, false);
                        Swal.fire({ icon: 'success', title: 'Cancelled', text: res.message, confirmButtonColor: '#6259ca', timer: 2000, showConfirmButton: false });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Failed', text: res.message, confirmButtonColor: '#6259ca' });
                    }
                },
                error: function () {
                    Swal.fire({ icon: 'error', title: 'Server Error', confirmButtonColor: '#6259ca' });
                },
            });
        });
    });
});
</script>
@endpush
