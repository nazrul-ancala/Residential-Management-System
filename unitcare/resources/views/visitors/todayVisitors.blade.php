@extends('master_page.master_page')
@section('page_title', "Today's Visitors")

@push('styles')
<link href="../assets/plugins/datatable/css/dataTables.bootstrap5.css" rel="stylesheet">
@endpush

@section('content')

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Today's Visitors</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard_utama') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Today's Visitors</li>
        </ol>
    </div>
    <div class="d-flex">
        <a href="{{ route('checkIn') }}" class="btn btn-primary btn-icon-text">
            <i class="fe fe-log-in me-2"></i> Check-In / Out
        </a>
    </div>
</div>

{{-- Summary cards --}}
<div class="row row-sm mg-b-20">
    <div class="col-sm-6 col-lg-4">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <span class="text-muted tx-12">Expected Today</span>
                        <h3 class="mb-0 mt-1" id="count-expected">—</h3>
                    </div>
                    <span class="ms-auto bg-primary-transparent text-primary avatar avatar-md rounded-circle">
                        <i class="fe fe-users tx-18"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <span class="text-muted tx-12">Checked-In</span>
                        <h3 class="mb-0 mt-1" id="count-checkedin">—</h3>
                    </div>
                    <span class="ms-auto bg-info-transparent text-info avatar avatar-md rounded-circle">
                        <i class="fe fe-user-check tx-18"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <span class="text-muted tx-12">Completed</span>
                        <h3 class="mb-0 mt-1" id="count-completed">—</h3>
                    </div>
                    <span class="ms-auto bg-success-transparent text-success avatar avatar-md rounded-circle">
                        <i class="fe fe-check-circle tx-18"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Visitors Table --}}
<div class="row row-sm">
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="today-visitors-datatable" class="table table-striped table-bordered text-nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Visitor Name</th>
                                <th>IC / Passport</th>
                                <th>Host (Resident)</th>
                                <th>Unit</th>
                                <th>Visit Time</th>
                                <th>Check-In</th>
                                <th>Check-Out</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="../assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatable/js/dataTables.bootstrap5.js"></script>
<script>
$(function () {
    function statusBadge(s) {
        var map = {
            'pending':    '<span class="badge bg-secondary">Pending</span>',
            'checked_in': '<span class="badge bg-primary">Checked-In</span>',
            'completed':  '<span class="badge bg-success">Completed</span>',
            'cancelled':  '<span class="badge bg-dark">Cancelled</span>',
        };
        return map[s] || '<span class="badge bg-light text-dark">' + s + '</span>';
    }

    $('#today-visitors-datatable').DataTable({
        ajax: {
            url: '{{ route('todayVisitors.list') }}',
            dataSrc: 'data',
        },
        columns: [
            { data: null, render: function (d, t, r, m) { return m.row + 1; } },
            { data: 'name' },
            { data: 'ic_passport' },
            { data: 'resident_name', defaultContent: '—' },
            { data: function (r) {
                return (r.block_name || '') + (r.unit_number ? '-' + r.unit_number : '');
            }},
            { data: 'visit_time' },
            { data: 'check_in_time',  defaultContent: '—' },
            { data: 'check_out_time', defaultContent: '—' },
            { data: 'status', render: function (d) { return statusBadge(d); } },
        ],
        columnDefs: [{ orderable: false, targets: [0] }],
        language: { emptyTable: 'No visitors scheduled for today.' },
        initComplete: function () {
            var data = this.api().data().toArray();
            var expected   = data.length;
            var checkedIn  = data.filter(function (r) { return r.status === 'checked_in'; }).length;
            var completed  = data.filter(function (r) { return r.status === 'completed'; }).length;
            $('#count-expected').text(expected);
            $('#count-checkedin').text(checkedIn);
            $('#count-completed').text(completed);
        },
    });
});
</script>
@endpush
