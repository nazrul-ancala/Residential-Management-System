@extends('master_page.master_page')
@section('page_title', 'Dashboard')

@push('styles')
<style>
.quick-action-tile {
    transition: box-shadow 0.2s ease, transform 0.2s ease;
    border: 1px solid #e8e8f7;
}
.quick-action-tile:hover {
    box-shadow: 0 6px 20px rgba(0,0,0,0.08) !important;
    transform: translateY(-3px);
    border-color: #c8c8e8;
}
.quick-action-tile .tile-icon {
    font-size: 2rem;
    opacity: 0.55;
    display: block;
    margin-bottom: 0.6rem;
}
</style>
@endpush

@section('content')

{{-- ======================================================= ADMIN DASHBOARD --}}
@if(auth()->user()->role === 'admin')

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Admin Dashboard</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </div>
</div>

{{-- Stat cards --}}
<div class="row row-sm mg-b-20">
    <div class="col-sm-6 col-lg-3">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <span class="text-muted tx-12">Total Residents</span>
                        <h3 class="mb-0 mt-1" id="admin-stat-residents">—</h3>
                    </div>
                    <span class="ms-auto bg-primary-transparent text-primary avatar avatar-md rounded-circle">
                        <i class="fe fe-users tx-18"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <span class="text-muted tx-12">Open Maintenance Tickets</span>
                        <h3 class="mb-0 mt-1" id="admin-stat-tickets">—</h3>
                    </div>
                    <span class="ms-auto bg-warning-transparent text-warning avatar avatar-md rounded-circle">
                        <i class="fe fe-clipboard tile-icon"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <span class="text-muted tx-12">Pending Bookings</span>
                        <h3 class="mb-0 mt-1" id="admin-stat-bookings">—</h3>
                    </div>
                    <span class="ms-auto bg-success-transparent text-success avatar avatar-md rounded-circle">
                        <i class="fe fe-calendar tx-18"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <span class="text-muted tx-12">Visitors Today</span>
                        <h3 class="mb-0 mt-1" id="admin-stat-visitors">—</h3>
                    </div>
                    <span class="ms-auto bg-danger-transparent text-danger avatar avatar-md rounded-circle">
                        <i class="fe fe-user-check tx-18"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="row row-sm mg-b-20">
    <div class="col-12">
        <p class="text-muted tx-12 fw-semibold mb-2 text-uppercase" style="letter-spacing:.06em;">Quick Actions</p>
    </div>
    <div class="col-sm-6 col-lg-3">
        <a href="{{ route('manageResidents') }}" class="text-decoration-none text-dark">
            <div class="card custom-card quick-action-tile">
                <div class="card-body text-center py-4">
                    <i class="fe fe-users tile-icon"></i>
                    <p class="mb-0 fw-semibold tx-14">Manage Residents</p>
                    <span class="text-muted tx-12">View &amp; manage accounts</span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-3">
        <a href="{{ route('manageAllRequests') }}" class="text-decoration-none text-dark">
            <div class="card custom-card quick-action-tile">
                <div class="card-body text-center py-4">
                    <i class="fe fe-clipboard tile-icon"></i>
                    <p class="mb-0 fw-semibold tx-14">Maintenance Requests</p>
                    <span class="text-muted tx-12">Review open tickets</span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-3">
        <a href="{{ route('manageAllBookings') }}" class="text-decoration-none text-dark">
            <div class="card custom-card quick-action-tile">
                <div class="card-body text-center py-4">
                    <i class="fe fe-calendar tile-icon"></i>
                    <p class="mb-0 fw-semibold tx-14">Facility Bookings</p>
                    <span class="text-muted tx-12">Approve or reject bookings</span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-3">
        <a href="{{ route('manageReports') }}" class="text-decoration-none text-dark">
            <div class="card custom-card quick-action-tile">
                <div class="card-body text-center py-4">
                    <i class="fe fe-bar-chart-2 tile-icon"></i>
                    <p class="mb-0 fw-semibold tx-14">Reports</p>
                    <span class="text-muted tx-12">View system reports</span>
                </div>
            </div>
        </a>
    </div>
</div>

{{-- Recent activity --}}
<div class="row row-sm">
    <div class="col-lg-7">
        <div class="card custom-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="card-title mb-0">Recent Maintenance Requests</h6>
                <a href="{{ route('manageAllRequests') }}" class="btn btn-sm btn-light">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Ticket No.</th>
                                <th>Resident</th>
                                <th>Category</th>
                                <th>Priority</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="admin-maint-tbody">
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">
                                    <span class="spinner-border spinner-border-sm"></span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card custom-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="card-title mb-0">Pending Facility Bookings</h6>
                <a href="{{ route('manageAllBookings') }}" class="btn btn-sm btn-light">View All</a>
            </div>
            <div class="card-body" id="admin-bookings-list">
                <div class="text-center py-3">
                    <span class="spinner-border spinner-border-sm"></span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function () {
    function priorityBadge(p) {
        var map = { high:'danger', medium:'warning text-dark', low:'info text-dark' };
        return '<span class="badge bg-' + (map[p]||'secondary') + '">' + (p ? p.charAt(0).toUpperCase()+p.slice(1) : '-') + '</span>';
    }
    function statusBadge(s) {
        var map = { open:'secondary', assigned:'primary', in_progress:'warning text-dark', completed:'success', closed:'dark',
                    pending:'warning text-dark', approved:'success', rejected:'danger' };
        var label = s ? s.replace(/_/g,' ').replace(/\b\w/g,function(c){return c.toUpperCase();}) : '-';
        return '<span class="badge bg-' + (map[s]||'secondary') + '">' + label + '</span>';
    }
    function fmtDate(d) {
        if (!d) return '-';
        return new Date(d).toLocaleDateString('en-GB', {day:'2-digit',month:'short',year:'numeric'});
    }
    function emptyRow(cols, msg) {
        return '<tr><td colspan="' + cols + '" class="text-center text-muted py-4">' +
               '<i class="fe fe-inbox d-block mb-2" style="font-size:1.8rem;opacity:.3;"></i>' + msg + '</td></tr>';
    }

    // Total Residents
    $.get("{{ route('reports.summary') }}", { report_type:'resident', date_from:'1900-01-01', date_to:'2099-12-31' }, function(res) {
        $('#admin-stat-residents').text(res.status && res.data ? res.data.count1 : '—');
    }).fail(function(){ $('#admin-stat-residents').text('—'); });

    // Visitors Today
    $.get("{{ route('todayVisitors.list') }}", function(res) {
        $('#admin-stat-visitors').text(Array.isArray(res.data) ? res.data.length : '—');
    }).fail(function(){ $('#admin-stat-visitors').text('—'); });

    // Maintenance tickets → stat card + recent table
    $.get("{{ route('allRequests.list') }}", function(res) {
        var data = res.data || [];
        $('#admin-stat-tickets').text(data.filter(function(t){ return t.status === 'open'; }).length);

        var recent = data.slice(0, 5);
        if (!recent.length) {
            $('#admin-maint-tbody').html(emptyRow(5, 'No recent requests.'));
            return;
        }
        var rows = '';
        $.each(recent, function(i, t) {
            rows += '<tr>' +
                '<td>TKT-' + String(t.id).padStart(5,'0') + '</td>' +
                '<td>' + (t.resident_name || '-') + '</td>' +
                '<td>' + (t.category || '-') + '</td>' +
                '<td>' + priorityBadge(t.priority) + '</td>' +
                '<td>' + statusBadge(t.status) + '</td>' +
                '</tr>';
        });
        $('#admin-maint-tbody').html(rows);
    }).fail(function(){
        $('#admin-stat-tickets').text('—');
        $('#admin-maint-tbody').html(emptyRow(5, 'Could not load requests.'));
    });

    // Pending bookings → stat card + bookings list
    $.get("{{ route('allBookings.pending') }}", function(res) {
        var data = res.data || [];
        $('#admin-stat-bookings').text(data.length);

        var pending = data.slice(0, 5);
        if (!pending.length) {
            $('#admin-bookings-list').html(
                '<div class="text-center text-muted py-4">' +
                '<i class="fe fe-calendar d-block mb-2" style="font-size:1.8rem;opacity:.3;"></i>No pending bookings.</div>'
            );
            return;
        }
        var html = '';
        $.each(pending, function(i, b) {
            html += '<div class="d-flex align-items-start' + (i < pending.length-1 ? ' border-bottom pb-2 mb-2' : '') + '">' +
                '<div class="me-2"><i class="fe fe-calendar text-primary" style="font-size:1.2rem;opacity:.6;"></i></div>' +
                '<div>' +
                '<p class="mb-0 fw-semibold tx-13">' + (b.facility_name || '-') + '</p>' +
                '<span class="text-muted tx-12">' + (b.resident_name || '-') + ' &bull; ' + (b.booking_date || '-') + '</span>' +
                '</div>' +
                '<span class="ms-auto badge bg-warning text-dark">Pending</span>' +
                '</div>';
        });
        $('#admin-bookings-list').html(html);
    }).fail(function(){
        $('#admin-stat-bookings').text('—');
        $('#admin-bookings-list').html('<div class="text-center text-muted py-4">Could not load bookings.</div>');
    });
});
</script>
@endpush

{{-- ==================================================== RESIDENT DASHBOARD --}}
@elseif(auth()->user()->role === 'resident')

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Welcome, {{ auth()->user()->name }}</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </div>
</div>

{{-- Stat cards --}}
<div class="row row-sm mg-b-20">
    <div class="col-sm-6 col-lg-3">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <span class="text-muted tx-12">My Active Visitors</span>
                        <h3 class="mb-0 mt-1" id="res-stat-visitors">—</h3>
                    </div>
                    <span class="ms-auto bg-primary-transparent text-primary avatar avatar-md rounded-circle">
                        <i class="fe fe-user-plus tx-18"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <span class="text-muted tx-12">Open Requests</span>
                        <h3 class="mb-0 mt-1" id="res-stat-requests">—</h3>
                    </div>
                    <span class="ms-auto bg-warning-transparent text-warning avatar avatar-md rounded-circle">
                        <i class="fe fe-clipboard tile-icon"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <span class="text-muted tx-12">Upcoming Bookings</span>
                        <h3 class="mb-0 mt-1" id="res-stat-bookings">—</h3>
                    </div>
                    <span class="ms-auto bg-success-transparent text-success avatar avatar-md rounded-circle">
                        <i class="fe fe-calendar tx-18"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="row row-sm mg-b-20">
    <div class="col-12">
        <p class="text-muted tx-12 fw-semibold mb-2 text-uppercase" style="letter-spacing:.06em;">Quick Actions</p>
    </div>
    <div class="col-sm-6 col-lg-3">
        <a href="{{ route('registerVisitor') }}" class="text-decoration-none text-dark">
            <div class="card custom-card quick-action-tile">
                <div class="card-body text-center py-4">
                    <i class="fe fe-user-plus tile-icon"></i>
                    <p class="mb-0 fw-semibold tx-14">Register a Visitor</p>
                    <span class="text-muted tx-12">Generate a visitor pass</span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-3">
        <a href="{{ route('manageMyRequests') }}" class="text-decoration-none text-dark">
            <div class="card custom-card quick-action-tile">
                <div class="card-body text-center py-4">
                    <i class="fe fe-clipboard tile-icon"></i>
                    <p class="mb-0 fw-semibold tx-14">Submit a Request</p>
                    <span class="text-muted tx-12">Report a maintenance issue</span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-3">
        <a href="{{ route('bookFacility') }}" class="text-decoration-none text-dark">
            <div class="card custom-card quick-action-tile">
                <div class="card-body text-center py-4">
                    <i class="fe fe-calendar tile-icon"></i>
                    <p class="mb-0 fw-semibold tx-14">Book a Facility</p>
                    <span class="text-muted tx-12">Reserve gym, hall &amp; more</span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-3">
        <a href="{{ route('viewAnnouncements') }}" class="text-decoration-none text-dark">
            <div class="card custom-card quick-action-tile">
                <div class="card-body text-center py-4">
                    <i class="fe fe-bell tile-icon"></i>
                    <p class="mb-0 fw-semibold tx-14">Announcements</p>
                    <span class="text-muted tx-12">View latest notices</span>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row row-sm">
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="card-title mb-0">Latest Announcements</h6>
                <a href="{{ route('viewAnnouncements') }}" class="btn btn-sm btn-light">View All</a>
            </div>
            <div class="card-body" id="res-announcements-list">
                <div class="text-center py-3">
                    <span class="spinner-border spinner-border-sm"></span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function () {
    var today = new Date().toISOString().split('T')[0];

    // Active visitors (pending or checked_in)
    $.get("{{ route('myVisitors.list') }}", function(res) {
        var data = res.data || [];
        var active = data.filter(function(v){ return v.status === 'pending' || v.status === 'checked_in'; }).length;
        $('#res-stat-visitors').text(active);
    }).fail(function(){ $('#res-stat-visitors').text('—'); });

    // Open maintenance requests
    $.get("{{ route('myRequests.list') }}", function(res) {
        var data = res.data || [];
        $('#res-stat-requests').text(data.filter(function(t){ return t.status === 'open'; }).length);
    }).fail(function(){ $('#res-stat-requests').text('—'); });

    // Upcoming bookings (pending or approved, future date)
    $.get("{{ route('myBookings.list') }}", function(res) {
        var data = res.data || [];
        var upcoming = data.filter(function(b){
            return (b.status === 'pending' || b.status === 'approved') && b.booking_date >= today;
        }).length;
        $('#res-stat-bookings').text(upcoming);
    }).fail(function(){ $('#res-stat-bookings').text('—'); });

    // Latest announcements
    $.get("{{ route('viewAnnouncements.list') }}", function(res) {
        var data = Array.isArray(res.data) ? res.data : [];
        var latest = data.slice(0, 3);
        if (!latest.length) {
            $('#res-announcements-list').html(
                '<div class="text-center text-muted py-4">' +
                '<i class="fe fe-bell d-block mb-2" style="font-size:1.8rem;opacity:.3;"></i>No announcements yet.</div>'
            );
            return;
        }
        var typeColors = { general:'primary', emergency:'danger', event:'info', maintenance:'warning' };
        var html = '';
        $.each(latest, function(i, a) {
            var color = typeColors[a.type] || 'secondary';
            var date  = a.published_at ? new Date(a.published_at).toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'numeric'}) : '';
            html += '<div class="' + (i < latest.length-1 ? 'border-bottom pb-3 mb-3' : '') + '">' +
                '<span class="badge bg-' + color + ' tx-10 mb-1">' + (a.type ? a.type.charAt(0).toUpperCase()+a.type.slice(1) : '') + '</span>' +
                '<p class="mb-0 fw-semibold tx-13">' + (a.title || '') + '</p>' +
                (date ? '<span class="text-muted tx-11">' + date + '</span>' : '') +
                '</div>';
        });
        $('#res-announcements-list').html(html);
    }).fail(function(){
        $('#res-announcements-list').html('<div class="text-center text-muted py-4">Could not load announcements.</div>');
    });
});
</script>
@endpush

{{-- ==================================================== SECURITY DASHBOARD --}}
@elseif(auth()->user()->role === 'security')

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Security Dashboard</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </div>
</div>

{{-- Stat cards --}}
<div class="row row-sm mg-b-20">
    <div class="col-sm-6 col-lg-3">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <span class="text-muted tx-12">Visitors Expected Today</span>
                        <h3 class="mb-0 mt-1" id="sec-stat-expected">—</h3>
                    </div>
                    <span class="ms-auto bg-primary-transparent text-primary avatar avatar-md rounded-circle">
                        <i class="fe fe-users tx-18"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <span class="text-muted tx-12">Checked-In</span>
                        <h3 class="mb-0 mt-1" id="sec-stat-checkedin">—</h3>
                    </div>
                    <span class="ms-auto bg-success-transparent text-success avatar avatar-md rounded-circle">
                        <i class="fe fe-user-check tx-18"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="row row-sm mg-b-20">
    <div class="col-12">
        <p class="text-muted tx-12 fw-semibold mb-2 text-uppercase" style="letter-spacing:.06em;">Quick Actions</p>
    </div>
    <div class="col-sm-6 col-lg-3">
        <a href="{{ route('checkIn') }}" class="text-decoration-none text-dark">
            <div class="card custom-card quick-action-tile">
                <div class="card-body text-center py-4">
                    <i class="fe fe-log-in tile-icon"></i>
                    <p class="mb-0 fw-semibold tx-14">Check In Visitor</p>
                    <span class="text-muted tx-12">Search &amp; process visitor arrival</span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-3">
        <a href="{{ route('todayVisitors') }}" class="text-decoration-none text-dark">
            <div class="card custom-card quick-action-tile">
                <div class="card-body text-center py-4">
                    <i class="fe fe-list tile-icon"></i>
                    <p class="mb-0 fw-semibold tx-14">Today's Visitors</p>
                    <span class="text-muted tx-12">View all visitors for today</span>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row row-sm">
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="card-title mb-0">Today's Visitors</h6>
                <a href="{{ route('todayVisitors') }}" class="btn btn-sm btn-light">Full List</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>#</th>
                                <th>Visitor Name</th>
                                <th>Host (Resident)</th>
                                <th>Unit</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="sec-visitors-tbody">
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">
                                    <span class="spinner-border spinner-border-sm"></span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function () {
    function statusBadge(s) {
        var map = { pending:'warning text-dark', checked_in:'success', completed:'secondary' };
        var label = s ? s.replace(/_/g,' ').replace(/\b\w/g,function(c){return c.toUpperCase();}) : '-';
        return '<span class="badge bg-' + (map[s]||'secondary') + '">' + label + '</span>';
    }

    $.get("{{ route('todayVisitors.list') }}", function(res) {
        var data = res.data || [];
        $('#sec-stat-expected').text(data.length);
        $('#sec-stat-checkedin').text(data.filter(function(v){ return v.status === 'checked_in'; }).length);

        if (!data.length) {
            $('#sec-visitors-tbody').html(
                '<tr><td colspan="6" class="text-center text-muted py-4">' +
                '<i class="fe fe-inbox d-block mb-2" style="font-size:1.8rem;opacity:.3;"></i>No visitors today.</td></tr>'
            );
            return;
        }
        var rows = '';
        $.each(data, function(i, v) {
            var unit = ((v.block_name || '') + ' ' + (v.unit_number || '')).trim() || '-';
            rows += '<tr>' +
                '<td>' + (i+1) + '</td>' +
                '<td>' + (v.name || '-') + '</td>' +
                '<td>' + (v.resident_name || '-') + '</td>' +
                '<td>' + unit + '</td>' +
                '<td>' + statusBadge(v.status) + '</td>' +
                '<td><a href="{{ route("checkIn") }}" class="btn btn-sm btn-outline-primary">Check In</a></td>' +
                '</tr>';
        });
        $('#sec-visitors-tbody').html(rows);
    }).fail(function(){
        $('#sec-stat-expected').text('—');
        $('#sec-stat-checkedin').text('—');
        $('#sec-visitors-tbody').html(
            '<tr><td colspan="6" class="text-center text-muted py-4">Could not load visitors.</td></tr>'
        );
    });
});
</script>
@endpush

{{-- ================================================= TECHNICIAN DASHBOARD --}}
@elseif(auth()->user()->role === 'technician')

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Welcome, {{ auth()->user()->name }}</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </div>
</div>

{{-- Stat cards --}}
<div class="row row-sm mg-b-20">
    <div class="col-sm-6 col-lg-3">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <span class="text-muted tx-12">Assigned Tasks</span>
                        <h3 class="mb-0 mt-1" id="tech-stat-total">—</h3>
                    </div>
                    <span class="ms-auto bg-primary-transparent text-primary avatar avatar-md rounded-circle">
                        <i class="fe fe-clipboard tx-18"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <span class="text-muted tx-12">In Progress</span>
                        <h3 class="mb-0 mt-1" id="tech-stat-inprogress">—</h3>
                    </div>
                    <span class="ms-auto bg-warning-transparent text-warning avatar avatar-md rounded-circle">
                        <i class="fe fe-loader tx-18"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <span class="text-muted tx-12">Completed</span>
                        <h3 class="mb-0 mt-1" id="tech-stat-completed">—</h3>
                    </div>
                    <span class="ms-auto bg-success-transparent text-success avatar avatar-md rounded-circle">
                        <i class="fe fe-check-circle tx-18"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="row row-sm mg-b-20">
    <div class="col-12">
        <p class="text-muted tx-12 fw-semibold mb-2 text-uppercase" style="letter-spacing:.06em;">Quick Actions</p>
    </div>
    <div class="col-sm-6 col-lg-3">
        <a href="{{ route('myTasks') }}" class="text-decoration-none text-dark">
            <div class="card custom-card quick-action-tile">
                <div class="card-body text-center py-4">
                    <i class="fe fe-sliders tile-icon"></i>
                    <p class="mb-0 fw-semibold tx-14">My Tasks</p>
                    <span class="text-muted tx-12">View &amp; update assigned tickets</span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-3">
        <a href="{{ route('myTasks') }}" class="text-decoration-none text-dark">
            <div class="card custom-card quick-action-tile">
                <div class="card-body text-center py-4">
                    <i class="fe fe-loader tile-icon"></i>
                    <p class="mb-0 fw-semibold tx-14">In Progress</p>
                    <span class="text-muted tx-12">Continue ongoing jobs</span>
                </div>
            </div>
        </a>
    </div>
</div>

{{-- Recent tasks table --}}
<div class="row row-sm">
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="card-title mb-0">Recent Assigned Tasks</h6>
                <a href="{{ route('myTasks') }}" class="btn btn-sm btn-light">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="tech-recent-table">
                        <thead class="bg-light">
                            <tr>
                                <th>Ticket No.</th>
                                <th>Category</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="tech-recent-tbody">
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fe fe-inbox d-block mb-2" style="font-size:1.8rem;opacity:.3;"></i>
                                    Loading…
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function () {
    function priorityBadge(p) {
        var map = { high: 'danger', medium: 'warning text-dark', low: 'info text-dark' };
        return '<span class="badge bg-' + (map[p] || 'secondary') + '">' + (p ? p.charAt(0).toUpperCase() + p.slice(1) : '-') + '</span>';
    }
    function statusBadge(s) {
        var map = { open:'secondary', assigned:'primary', in_progress:'warning text-dark', completed:'success', closed:'dark' };
        var label = s ? s.replace('_',' ').replace(/\b\w/g, function(c){ return c.toUpperCase(); }) : '-';
        return '<span class="badge bg-' + (map[s]||'secondary') + '">' + label + '</span>';
    }
    function fmtDate(d) {
        if (!d) return '-';
        return new Date(d).toLocaleDateString('en-GB', { day:'2-digit', month:'short', year:'numeric' });
    }

    $.get("{{ route('myTasks.list') }}", function (res) {
        var tasks = res.data || [];
        $('#tech-stat-total').text(tasks.length);
        $('#tech-stat-inprogress').text(tasks.filter(function(t){ return t.status === 'in_progress'; }).length);
        $('#tech-stat-completed').text(tasks.filter(function(t){ return t.status === 'completed'; }).length);

        var recent = tasks.slice(0, 5);
        if (!recent.length) {
            $('#tech-recent-tbody').html('<tr><td colspan="5" class="text-center text-muted py-4">' +
                '<i class="fe fe-inbox d-block mb-2" style="font-size:1.8rem;opacity:.3;"></i>No tasks assigned yet.</td></tr>');
            return;
        }
        var rows = '';
        $.each(recent, function (i, t) {
            rows += '<tr>' +
                '<td>' + (t.ticket_no || 'TKT-' + String(t.id).padStart(5,'0')) + '</td>' +
                '<td>' + (t.category  || '-') + '</td>' +
                '<td>' + priorityBadge(t.priority) + '</td>' +
                '<td>' + statusBadge(t.status) + '</td>' +
                '<td>' + fmtDate(t.created_at) + '</td>' +
                '</tr>';
        });
        $('#tech-recent-tbody').html(rows);
    });
});
</script>
@endpush

@endif

@endsection
