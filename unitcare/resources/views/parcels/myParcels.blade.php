@extends('master_page.master_page')
@section('page_title', 'My Parcels')

@push('styles')
<link href="../assets/plugins/datatable/css/dataTables.bootstrap5.css" rel="stylesheet">
<style>
.collection-pin-box { display:inline-block; border:2px solid #6259ca; border-radius:8px; padding:4px 10px; background:#f4f3ff; text-align:center; }
.pin-digits { font-family:monospace; font-size:1.4rem; font-weight:700; letter-spacing:.25em; color:#6259ca; line-height:1.2; }
</style>
@endpush

@section('content')

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">
            {{ auth()->user()->role === 'admin' ? 'All Parcels' : 'My Parcels' }}
        </h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard_utama') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">
                {{ auth()->user()->role === 'admin' ? 'All Parcels' : 'My Parcels' }}
            </li>
        </ol>
    </div>
</div>

{{-- Info banner for residents --}}
@if(auth()->user()->role === 'resident')
<div class="alert alert-info d-flex align-items-center gap-2 mb-4" role="alert">
    <i class="fe fe-info"></i>
    <div class="tx-13">
        Parcels are logged by security when they arrive at the lobby.
        <strong>Show your Collection PIN</strong> (visible below for pending parcels) to security when collecting.
    </div>
</div>
@endif

<div class="row row-sm">
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="my-parcels-dt" class="table table-striped table-bordered text-nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Courier</th>
                                <th>Parcel Type</th>
                                <th>Tracking No.</th>
                                @if(auth()->user()->role === 'admin')
                                <th>Resident</th>
                                <th>Unit</th>
                                @endif
                                <th>Received At</th>
                                <th>Collected At</th>
                                <th>Status</th>
                                <th>Collection PIN</th>
                                <th class="text-center">Details</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Detail Modal --}}
<div class="modal fade" id="myParcelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fe fe-package me-2"></i>Parcel Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless tx-13 mb-0">
                    <tr><th width="38%">Courier</th><td id="mp-courier">—</td></tr>
                    <tr><th>Parcel Type</th><td id="mp-type">—</td></tr>
                    <tr><th>Tracking No.</th><td id="mp-tracking">—</td></tr>
                    <tr id="mp-resident-row"><th>Resident</th><td id="mp-resident">—</td></tr>
                    <tr id="mp-unit-row"><th>Unit</th><td id="mp-unit">—</td></tr>
                    <tr><th>Received At</th><td id="mp-received">—</td></tr>
                    <tr><th>Collected At</th><td id="mp-collected">—</td></tr>
                    <tr><th>Notes</th><td id="mp-notes">—</td></tr>
                    <tr><th>Status</th><td id="mp-status">—</td></tr>
                    <tr id="mp-pin-row" style="display:none;">
                        <th>Collection PIN</th>
                        <td>
                            <div class="collection-pin-box d-inline-block">
                                <div class="pin-digits" id="mp-pin">—</div>
                                <div class="tx-10 text-muted">Show this to security</div>
                            </div>
                        </td>
                    </tr>
                    <tr id="mp-photo-row" style="display:none;"><th>Photo</th><td><a id="mp-photo-link" href="#" target="_blank"><img id="mp-photo-thumb" src="" style="max-height:100px;border-radius:4px;"></a></td></tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="../assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatable/js/dataTables.bootstrap5.js"></script>
<script>
var typeLabels = { letter: 'Letter / Document', small_box: 'Small Box', large_box: 'Large Box', fragile: 'Fragile' };
var rowData    = {};
var isAdmin    = @json(auth()->user()->role === 'admin');

function typeLabel(t) { return typeLabels[t] || t; }

function statusBadge(row) {
    if (row.status === 'collected') return '<span class="badge bg-success">Collected</span>';
    if (row.is_overdue == 1)        return '<span class="badge bg-danger">Overdue</span>';
    return '<span class="badge bg-warning text-dark">Pending</span>';
}

$(function () {
    var columns = [
        { data: null, render: function (d, t, r, m) { return m.row + 1; } },
        { data: 'courier' },
        { data: 'parcel_type', render: function (d) { return typeLabel(d); } },
        { data: 'tracking_no', defaultContent: '—' },
    ];

    if (isAdmin) {
        columns.push({ data: 'resident_name', defaultContent: '—' });
        columns.push({ data: null, render: function (d, t, r) {
            return (r.block_name || '') + (r.unit_number ? '-' + r.unit_number : '');
        }});
    }

    columns.push({ data: 'received_at', render: function (d) { return d ? d.slice(0, 16) : '—'; } });
    columns.push({ data: 'collected_at', render: function (d) { return d ? d.slice(0, 16) : '—'; }, defaultContent: '—' });
    columns.push({ data: null, render: function (d, t, r) { return statusBadge(r); } });
    columns.push({
        data: null,
        render: function (d, t, r) {
            if (r.status === 'pending' && r.collection_pin) {
                return '<div class="collection-pin-box">'
                    + '<div class="pin-digits">' + r.collection_pin + '</div>'
                    + '<div class="tx-10 text-muted">Show to security</div>'
                    + '</div>';
            }
            return '<span class="text-muted tx-12">—</span>';
        }
    });
    columns.push({
        data: null,
        render: function (d, t, r) {
            return '<div class="text-center"><button class="btn btn-sm btn-outline-info btn-view" data-id="' + r.id + '"><i class="fe fe-eye"></i></button></div>';
        }
    });

    $('#my-parcels-dt').DataTable({
        ajax: {
            url:     "{{ route('myParcels.list') }}",
            dataSrc: function (json) {
                var rows = json.data || [];
                rowData = {};
                rows.forEach(function (r) { rowData[r.id] = r; });
                return rows;
            },
        },
        columns: columns,
        columnDefs: [{ orderable: false, targets: isAdmin ? [0, 10] : [0, 8] }],
        language: {
            emptyTable: '<div class="text-center py-4"><i class="fe fe-package" style="font-size:2.2rem;opacity:.3;display:block;margin-bottom:.5rem;"></i><p class="text-muted mb-2">No parcel records found.</p></div>'
        },
    });

    $(document).on('click', '.btn-view', function () {
        var r = rowData[$(this).data('id')];
        if (!r) return;
        $('#mp-courier').text(r.courier || '—');
        $('#mp-type').text(typeLabel(r.parcel_type));
        $('#mp-tracking').text(r.tracking_no || '—');
        if (isAdmin) {
            $('#mp-resident').text(r.resident_name || '—');
            $('#mp-unit').text((r.block_name || '') + (r.unit_number ? '-' + r.unit_number : '—'));
            $('#mp-resident-row, #mp-unit-row').show();
        } else {
            $('#mp-resident-row, #mp-unit-row').hide();
        }
        $('#mp-received').text(r.received_at ? r.received_at.slice(0, 16) : '—');
        $('#mp-collected').text(r.collected_at ? r.collected_at.slice(0, 16) : '—');
        $('#mp-notes').text(r.notes || '—');
        $('#mp-status').html(statusBadge(r));
        if (r.status === 'pending' && r.collection_pin) {
            $('#mp-pin').text(r.collection_pin);
            $('#mp-pin-row').show();
        } else {
            $('#mp-pin-row').hide();
        }
        if (r.photo_path) {
            $('#mp-photo-thumb').attr('src', '/storage/' + r.photo_path);
            $('#mp-photo-link').attr('href', '/storage/' + r.photo_path);
            $('#mp-photo-row').show();
        } else { $('#mp-photo-row').hide(); }
        $('#myParcelModal').modal('show');
    });
});
</script>
@endpush
