@extends('master_page.master_page')
@section('page_title', 'Reports')

@push('styles')
<link href="../assets/plugins/datatable/css/dataTables.bootstrap5.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
@endpush

@section('content')

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Reports</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard_utama') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Reports</li>
        </ol>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-light btn-icon-text" id="exportPdfBtn">
            <i class="fe fe-file-text me-2"></i> Download PDF
        </button>
        <button class="btn btn-light btn-icon-text" id="exportExcelBtn">
            <i class="fe fe-download me-2"></i> Export Excel
        </button>
    </div>
</div>

{{-- Filter card --}}
<div class="row row-sm mg-b-20">
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-body py-3">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label mb-1">Report Type</label>
                        <select class="form-control" id="reportType">
                            <option value="visitor" selected>Visitor Report</option>
                            <option value="maintenance">Maintenance Report</option>
                            <option value="booking">Facility Booking Report</option>
                            <option value="resident">Resident Report</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1">From</label>
                        <input type="date" class="form-control" id="reportFrom">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1">To</label>
                        <input type="date" class="form-control" id="reportTo">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label mb-1">&nbsp;</label>
                        <button class="btn btn-primary w-100" id="applyFilterBtn">
                            <span id="applyBtnText">Apply</span>
                            <span id="applyBtnSpinner" class="spinner-border spinner-border-sm ms-1 d-none"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Summary cards --}}
<div class="row row-sm mg-b-20">
    <div class="col-sm-6 col-lg-3">
        <div class="card custom-card">
            <div class="card-body">
                <span class="text-muted tx-12" id="card1-label">Total Visits</span>
                <h3 class="mb-0 mt-1" id="card1-value">—</h3>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card custom-card">
            <div class="card-body">
                <span class="text-muted tx-12" id="card2-label">Checked In</span>
                <h3 class="mb-0 mt-1" id="card2-value">—</h3>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card custom-card">
            <div class="card-body">
                <span class="text-muted tx-12" id="card3-label">Completed</span>
                <h3 class="mb-0 mt-1" id="card3-value">—</h3>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card custom-card">
            <div class="card-body">
                <span class="text-muted tx-12" id="card4-label">Pending</span>
                <h3 class="mb-0 mt-1" id="card4-value">—</h3>
            </div>
        </div>
    </div>
</div>

{{-- Report table --}}
<div class="row row-sm">
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-header">
                <h6 class="card-title mb-0" id="reportTableTitle">Visitor Report</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="report-datatable" class="table table-striped table-bordered text-nowrap w-100">
                        <thead id="reportTableHead">
                            <tr>
                                <th>#</th>
                                <th>Visitor Name</th>
                                <th>IC / Passport</th>
                                <th>Host Resident</th>
                                <th>Unit</th>
                                <th>Visit Date</th>
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
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script>
var reportTable = null;

var reportHeaders = {
    visitor:     ['#','Visitor Name','IC / Passport','Host Resident','Unit','Visit Date','Check-In','Check-Out','Status'],
    maintenance: ['#','Ticket No.','Resident','Unit','Category','Priority','Status','Submitted','Updated'],
    booking:     ['#','Resident','Unit','Facility','Date','Time Slot','Purpose','Status','Approved By'],
    resident:    ['#','Name','Email','Phone','Block','Unit','Status','Registered'],
};

var reportColumns = {
    visitor: [
        { data: null, orderable: false, render: function(d,t,r,m){ return m.row+1; } },
        { data: 'visitor_name' },
        { data: 'ic_passport',   defaultContent: '—' },
        { data: 'host_resident', defaultContent: '—' },
        { data: 'unit',          defaultContent: '—' },
        { data: 'visit_date',    defaultContent: '—' },
        { data: 'check_in_time', defaultContent: '—' },
        { data: 'check_out_time',defaultContent: '—' },
        { data: 'status',        render: function(d){ return statusBadge(d, 'visitor'); } },
    ],
    maintenance: [
        { data: null, orderable: false, render: function(d,t,r,m){ return m.row+1; } },
        { data: 'ticket_no' },
        { data: 'resident',     defaultContent: '—' },
        { data: 'unit',         defaultContent: '—' },
        { data: 'category',     defaultContent: '—' },
        { data: 'priority',     defaultContent: '—' },
        { data: 'status',       render: function(d){ return statusBadge(d, 'maintenance'); } },
        { data: 'submitted_at', defaultContent: '—' },
        { data: 'updated_at',   defaultContent: '—' },
    ],
    booking: [
        { data: null, orderable: false, render: function(d,t,r,m){ return m.row+1; } },
        { data: 'resident',    defaultContent: '—' },
        { data: 'unit',        defaultContent: '—' },
        { data: 'facility',    defaultContent: '—' },
        { data: 'booking_date',defaultContent: '—' },
        { data: 'time_slot',   defaultContent: '—' },
        { data: 'purpose',     defaultContent: '—' },
        { data: 'status',      render: function(d){ return statusBadge(d, 'booking'); } },
        { data: 'approved_by', defaultContent: '—' },
    ],
    resident: [
        { data: null, orderable: false, render: function(d,t,r,m){ return m.row+1; } },
        { data: 'name',          defaultContent: '—' },
        { data: 'email',         defaultContent: '—' },
        { data: 'phone',         defaultContent: '—' },
        { data: 'block',         defaultContent: '—' },
        { data: 'unit_no',       defaultContent: '—' },
        { data: 'status',        render: function(d){ return statusBadge(d, 'resident'); } },
        { data: 'registered_at', defaultContent: '—' },
    ],
};

var summaryLabels = {
    visitor:     ['Total Visits',    'Checked In',  'Completed',    'Pending'],
    maintenance: ['Total Tickets',   'Open',        'In Progress',  'Resolved'],
    booking:     ['Total Bookings',  'Pending',     'Approved',     'Rejected'],
    resident:    ['Total Residents', 'Active',      'Inactive',     'New in Period'],
};

var reportTitles = {
    visitor:     'Visitor Report',
    maintenance: 'Maintenance Report',
    booking:     'Facility Booking Report',
    resident:    'Resident Report',
};

function statusBadge(val, type) {
    var maps = {
        visitor:     { pending:'warning', checked_in:'info', completed:'success', cancelled:'secondary' },
        maintenance: { open:'primary', in_progress:'warning', resolved:'success', closed:'secondary', cancelled:'secondary' },
        booking:     { pending:'warning', approved:'success', rejected:'danger', cancelled:'secondary' },
        resident:    { active:'success', inactive:'secondary' },
    };
    var map = maps[type] || {};
    var cls = map[val] || 'light text-dark';
    var label = val ? val.replace(/_/g,' ').replace(/\b\w/g, function(c){ return c.toUpperCase(); }) : '—';
    return '<span class="badge bg-' + cls + '">' + label + '</span>';
}

function localDateStr(d) {
    var y = d.getFullYear();
    var m = ('0' + (d.getMonth()+1)).slice(-2);
    var day = ('0' + d.getDate()).slice(-2);
    return y + '-' + m + '-' + day;
}

function buildAjaxUrl(type, dfrom, dto) {
    return "{{ route('reports.data') }}?report_type=" + encodeURIComponent(type) +
           "&date_from=" + encodeURIComponent(dfrom) +
           "&date_to="   + encodeURIComponent(dto);
}

function initTable(type, dfrom, dto) {
    if (reportTable) {
        reportTable.destroy();
        $('.dt-buttons').remove();
    }

    var headers = reportHeaders[type];
    $('#reportTableHead').html(
        '<tr>' + headers.map(function(h){ return '<th>' + h + '</th>'; }).join('') + '</tr>'
    );
    $('#report-datatable tbody').empty();
    $('#reportTableTitle').text(reportTitles[type]);

    reportTable = $('#report-datatable').DataTable({
        ajax:     { url: buildAjaxUrl(type, dfrom, dto), dataSrc: 'data' },
        columns:  reportColumns[type],
        language: { emptyTable: 'No data for selected filter.' },
        dom:      'Bfrtip',
        buttons: [
            { extend: 'excelHtml5', title: reportTitles[type], className: 'btn-dt-excel' },
            { extend: 'pdfHtml5',   title: reportTitles[type], className: 'btn-dt-pdf',
              orientation: 'landscape', pageSize: 'A4' }
        ],
    });

    $('.dt-buttons').addClass('d-none');
}

function loadSummary(type, dfrom, dto) {
    var labels = summaryLabels[type];
    labels.forEach(function(lbl, i) {
        $('#card' + (i+1) + '-label').text(lbl);
        $('#card' + (i+1) + '-value').html('<span class="spinner-border spinner-border-sm text-primary"></span>');
    });

    $.get("{{ route('reports.summary') }}", { report_type: type, date_from: dfrom, date_to: dto },
        function(res) {
            if (res.status && res.data) {
                var d = res.data;
                $('#card1-value').text(d.count1 ?? '0');
                $('#card2-value').text(d.count2 ?? '0');
                $('#card3-value').text(d.count3 ?? '0');
                $('#card4-value').text(d.count4 ?? '0');
            } else {
                for (var i = 1; i <= 4; i++) { $('#card' + i + '-value').text('—'); }
            }
        }
    ).fail(function() {
        for (var i = 1; i <= 4; i++) { $('#card' + i + '-value').text('—'); }
    });
}

function loadReport() {
    var type  = $('#reportType').val();
    var dfrom = $('#reportFrom').val();
    var dto   = $('#reportTo').val();

    if (!dfrom || !dto) return;

    $('#applyBtnSpinner').removeClass('d-none');
    $('#applyFilterBtn').prop('disabled', true);

    loadSummary(type, dfrom, dto);
    initTable(type, dfrom, dto);

    // re-enable button after table draws
    $('#report-datatable').one('draw.dt', function() {
        $('#applyBtnSpinner').addClass('d-none');
        $('#applyFilterBtn').prop('disabled', false);
    });
}

$(function () {
    // Default: first day of current month → today
    var today  = new Date();
    var first  = new Date(today.getFullYear(), today.getMonth(), 1);
    $('#reportFrom').val(localDateStr(first));
    $('#reportTo').val(localDateStr(today));

    loadReport();

    $('#applyFilterBtn').on('click', loadReport);

    $('#exportExcelBtn').on('click', function() {
        if (reportTable) { reportTable.button('.btn-dt-excel').trigger(); }
    });
    $('#exportPdfBtn').on('click', function() {
        if (reportTable) { reportTable.button('.btn-dt-pdf').trigger(); }
    });
});
</script>
@endpush
