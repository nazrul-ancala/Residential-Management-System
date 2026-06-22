@extends('master_page.master_page')
@section('page_title', 'Manage Parcel Pickups')

@push('styles')
<link href="../assets/plugins/datatable/css/dataTables.bootstrap5.css" rel="stylesheet">
@endpush

@section('content')

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Manage Parcel Pickups</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard_utama') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Manage Parcels</li>
        </ol>
    </div>
    <div class="d-flex">
        <a href="{{ route('parcels.log') }}" class="btn btn-primary btn-icon-text">
            <i class="fe fe-plus me-2"></i> Log New Parcel
        </a>
    </div>
</div>

{{-- Stat Cards --}}
<div class="row row-sm mg-b-20">
    <div class="col-sm-6 col-lg-3">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3 bg-primary-transparent rounded p-2">
                        <i class="fe fe-package tx-20 text-primary"></i>
                    </div>
                    <div>
                        <p class="tx-12 mb-0 text-muted">Total</p>
                        <h4 class="mb-0 fw-bold" id="stat-total">—</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3 bg-warning-transparent rounded p-2">
                        <i class="fe fe-clock tx-20 text-warning"></i>
                    </div>
                    <div>
                        <p class="tx-12 mb-0 text-muted">Pending</p>
                        <h4 class="mb-0 fw-bold" id="stat-pending">—</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3 bg-success-transparent rounded p-2">
                        <i class="fe fe-check-circle tx-20 text-success"></i>
                    </div>
                    <div>
                        <p class="tx-12 mb-0 text-muted">Collected</p>
                        <h4 class="mb-0 fw-bold" id="stat-collected">—</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3 bg-danger-transparent rounded p-2">
                        <i class="fe fe-alert-triangle tx-20 text-danger"></i>
                    </div>
                    <div>
                        <p class="tx-12 mb-0 text-muted">Overdue</p>
                        <h4 class="mb-0 fw-bold text-danger" id="stat-overdue">—</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabs --}}
<ul class="nav nav-tabs mg-b-20" role="tablist">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-pending">
        Pending <span class="badge bg-warning text-dark ms-1" id="pendingBadge">0</span>
    </a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-all">All Parcels</a></li>
</ul>

<div class="tab-content">
    {{-- Pending Tab --}}
    <div class="tab-pane fade show active" id="tab-pending">
        <div class="card custom-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="pending-dt" class="table table-striped table-bordered text-nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th><th>Resident</th><th>Unit</th>
                                <th>Courier</th><th>Type</th><th>Tracking</th>
                                <th>Received</th><th>Status</th><th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- All Parcels Tab --}}
    <div class="tab-pane fade" id="tab-all">
        <div class="card custom-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="all-dt" class="table table-striped table-bordered text-nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th><th>Resident</th><th>Unit</th>
                                <th>Courier</th><th>Type</th><th>Tracking</th>
                                <th>Received</th><th>Collected At</th>
                                <th>Status</th><th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- PIN Verification Modal --}}
<div class="modal fade" id="collectModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-semibold">
                    <i class="fe fe-shield me-2 text-success"></i>Verify Collection
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center pt-2">
                <p class="tx-13 text-muted mb-3">
                    Ask the resident to open <strong>My Parcels</strong> and show their Collection PIN.
                </p>
                <input id="collectPinInput" type="text" inputmode="numeric" maxlength="6"
                       class="form-control text-center fw-bold tx-28"
                       style="letter-spacing:.4em;font-family:monospace;"
                       placeholder="— — — — — —" autocomplete="off">
                <div id="collectPinError" class="text-danger tx-12 mt-2" style="display:none;">
                    <i class="fe fe-alert-circle me-1"></i>Incorrect PIN — ask the resident to check My Parcels.
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success btn-sm" id="btnConfirmCollect">
                    <i class="fe fe-check me-1"></i> Confirm
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Detail Modal --}}
<div class="modal fade" id="parcelDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fe fe-package me-2"></i>Parcel Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless tx-13 mb-0">
                    <tr><th width="38%">Resident</th><td id="pd-resident">—</td></tr>
                    <tr><th>Unit</th><td id="pd-unit">—</td></tr>
                    <tr><th>Phone</th><td id="pd-phone">—</td></tr>
                    <tr><th>Courier</th><td id="pd-courier">—</td></tr>
                    <tr><th>Parcel Type</th><td id="pd-type">—</td></tr>
                    <tr><th>Tracking No.</th><td id="pd-tracking">—</td></tr>
                    <tr><th>Received At</th><td id="pd-received">—</td></tr>
                    <tr><th>Collected At</th><td id="pd-collected">—</td></tr>
                    <tr><th>Logged By</th><td id="pd-loggedby">—</td></tr>
                    <tr><th>Collected By</th><td id="pd-collectedby">—</td></tr>
                    <tr><th>Notes</th><td id="pd-notes">—</td></tr>
                    <tr id="pd-photo-row" style="display:none;"><th>Photo</th><td><a id="pd-photo-link" href="#" target="_blank"><img id="pd-photo-thumb" src="" style="max-height:100px;border-radius:4px;"></a></td></tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success d-none" id="btnModalCollect">
                    <i class="fe fe-check-circle me-1"></i> Mark as Collected
                </button>
                <button type="button" class="btn btn-outline-danger d-none" id="btnModalDelete">
                    <i class="fe fe-trash-2 me-1"></i> Delete Record
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
var typeLabels = { letter: 'Letter / Document', small_box: 'Small Box', large_box: 'Large Box', fragile: 'Fragile' };
var pendingRows = {}, allRows = {};
var currentId = null;

function typeLabel(t) { return typeLabels[t] || t; }

function statusBadge(row) {
    if (row.status === 'collected') return '<span class="badge bg-success">Collected</span>';
    if (row.is_overdue == 1)        return '<span class="badge bg-danger">Overdue</span>';
    return '<span class="badge bg-warning text-dark">Pending</span>';
}

function openDetail(r) {
    currentId = r.id;
    $('#pd-resident').text(r.resident_name || '—');
    $('#pd-unit').text((r.block_name || '') + (r.unit_number ? '-' + r.unit_number : '—'));
    $('#pd-phone').text(r.resident_phone || '—');
    $('#pd-courier').text(r.courier || '—');
    $('#pd-type').text(typeLabel(r.parcel_type));
    $('#pd-tracking').text(r.tracking_no || '—');
    $('#pd-received').text(r.received_at ? r.received_at.slice(0, 16) : '—');
    $('#pd-collected').text(r.collected_at ? r.collected_at.slice(0, 16) : '—');
    $('#pd-loggedby').text(r.logged_by_name || '—');
    $('#pd-collectedby').text(r.collected_by_name || '—');
    $('#pd-notes').text(r.notes || '—');
    if (r.photo_path) {
        $('#pd-photo-thumb').attr('src', '/storage/' + r.photo_path);
        $('#pd-photo-link').attr('href', '/storage/' + r.photo_path);
        $('#pd-photo-row').show();
    } else { $('#pd-photo-row').hide(); }

    if (r.status === 'pending') {
        $('#btnModalCollect').removeClass('d-none');
        $('#btnModalDelete').removeClass('d-none');
    } else {
        $('#btnModalCollect').addClass('d-none');
        $('#btnModalDelete').removeClass('d-none');
    }
    $('#parcelDetailModal').modal('show');
}

$(function () {
    var dtPending = $('#pending-dt').DataTable({
        ajax: {
            url:     "{{ route('allParcels.pending') }}",
            dataSrc: function (json) {
                var rows = json.data || [];
                pendingRows = {};
                rows.forEach(function (r) { pendingRows[r.id] = r; });
                $('#pendingBadge').text(rows.length);
                return rows;
            },
        },
        columns: [
            { data: null, render: function (d, t, r, m) { return m.row + 1; } },
            { data: 'resident_name' },
            { data: null, render: function (d, t, r) { return (r.block_name || '') + (r.unit_number ? '-' + r.unit_number : ''); } },
            { data: 'courier' },
            { data: 'parcel_type', render: function (d) { return typeLabel(d); } },
            { data: 'tracking_no', defaultContent: '—' },
            { data: 'received_at', render: function (d) { return d ? d.slice(0, 16) : '—'; } },
            { data: null, render: function (d, t, r) { return statusBadge(r); } },
            { data: null, render: function (d, t, r) {
                return '<div class="text-center">'
                    + '<button class="btn btn-sm btn-outline-success me-1 btn-collect" data-id="' + r.id + '" data-src="pending" title="Collect"><i class="fe fe-check"></i></button>'
                    + '<button class="btn btn-sm btn-outline-info btn-view" data-id="' + r.id + '" data-src="pending" title="View"><i class="fe fe-eye"></i></button>'
                    + '</div>';
            }},
        ],
        columnDefs: [{ orderable: false, targets: [0, 8] }],
        language: { emptyTable: 'No pending parcels.' },
    });

    var dtAll = $('#all-dt').DataTable({
        ajax: {
            url:     "{{ route('allParcels.list') }}",
            dataSrc: function (json) {
                var rows = json.data || [];
                allRows = {};
                rows.forEach(function (r) { allRows[r.id] = r; });
                $('#stat-total').text(rows.length);
                $('#stat-pending').text(rows.filter(function (r) { return r.status === 'pending'; }).length);
                $('#stat-collected').text(rows.filter(function (r) { return r.status === 'collected'; }).length);
                $('#stat-overdue').text(rows.filter(function (r) { return r.is_overdue == 1; }).length);
                return rows;
            },
        },
        columns: [
            { data: null, render: function (d, t, r, m) { return m.row + 1; } },
            { data: 'resident_name' },
            { data: null, render: function (d, t, r) { return (r.block_name || '') + (r.unit_number ? '-' + r.unit_number : ''); } },
            { data: 'courier' },
            { data: 'parcel_type', render: function (d) { return typeLabel(d); } },
            { data: 'tracking_no', defaultContent: '—' },
            { data: 'received_at', render: function (d) { return d ? d.slice(0, 16) : '—'; } },
            { data: 'collected_at', render: function (d) { return d ? d.slice(0, 16) : '—'; }, defaultContent: '—' },
            { data: null, render: function (d, t, r) { return statusBadge(r); } },
            { data: null, render: function (d, t, r) {
                return '<div class="text-center">'
                    + '<button class="btn btn-sm btn-outline-info btn-view" data-id="' + r.id + '" data-src="all" title="View"><i class="fe fe-eye"></i></button>'
                    + '</div>';
            }},
        ],
        columnDefs: [{ orderable: false, targets: [0, 9] }],
        language: { emptyTable: 'No parcel records found.' },
    });

    function reloadBoth() {
        dtPending.ajax.reload(null, false);
        dtAll.ajax.reload(null, false);
    }

    // View
    $(document).on('click', '.btn-view', function () {
        var src = $(this).data('src');
        var r   = src === 'pending' ? pendingRows[$(this).data('id')] : allRows[$(this).data('id')];
        if (r) openDetail(r);
    });

    // Inline collect
    $(document).on('click', '.btn-collect', function () {
        currentId = $(this).data('id');
        openCollectModal();
    });

    // Detail modal "Mark as Collected" → PIN modal
    $('#btnModalCollect').on('click', function () {
        $('#parcelDetailModal').modal('hide');
        openCollectModal();
    });

    // Delete (no PIN needed)
    $('#btnModalDelete').on('click', function () {
        $('#parcelDetailModal').modal('hide');
        doDelete();
    });

    function openCollectModal() {
        $('#collectPinInput').val('');
        $('#collectPinError').hide();
        $('#collectModal').modal('show');
        setTimeout(function () { $('#collectPinInput').focus(); }, 400);
    }

    $('#btnConfirmCollect').on('click', function () {
        var pin = $('#collectPinInput').val().trim();
        if (pin.length !== 6) {
            $('#collectPinError').text('Please enter the 6-digit Collection PIN.').show();
            return;
        }
        $('#collectPinError').hide();
        $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.post({
            url:         "{{ route('allParcels.update') }}",
            data:        JSON.stringify({ action: 'collect', id: currentId, pin: pin }),
            contentType: 'application/json',
            success: function (res) {
                $('#btnConfirmCollect').prop('disabled', false).html('<i class="fe fe-check me-1"></i> Confirm');
                if (res.status) {
                    $('#collectModal').modal('hide');
                    reloadBoth();
                    Swal.fire({ icon: 'success', title: 'Collected', text: res.message, confirmButtonColor: '#6259ca', timer: 2000, showConfirmButton: false });
                } else {
                    $('#collectPinError').text(res.message || 'Incorrect PIN — ask the resident to check My Parcels.').show();
                    $('#collectPinInput').val('').focus();
                }
            },
            error: function () {
                $('#btnConfirmCollect').prop('disabled', false).html('<i class="fe fe-check me-1"></i> Confirm');
                $('#collectPinError').text('Server error. Please try again.').show();
            }
        });
    });

    function doDelete() {
        Swal.fire({
            icon: 'warning', title: 'Delete Record?',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete', cancelButtonText: 'Cancel',
            confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.post({
                url:         "{{ route('allParcels.update') }}",
                data:        JSON.stringify({ action: 'delete', id: currentId }),
                contentType: 'application/json',
                success: function (res) {
                    if (res.status) {
                        reloadBoth();
                        Swal.fire({ icon: 'success', title: 'Deleted', text: res.message, confirmButtonColor: '#6259ca', timer: 2000, showConfirmButton: false });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Failed', text: res.message, confirmButtonColor: '#6259ca' });
                    }
                },
                error: function () {
                    Swal.fire({ icon: 'error', title: 'Server Error', confirmButtonColor: '#6259ca' });
                }
            });
        });
    }
});
</script>
@endpush
