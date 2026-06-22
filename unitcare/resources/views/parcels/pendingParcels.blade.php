@extends('master_page.master_page')
@section('page_title', 'Pending Parcel Pickups')

@push('styles')
<link href="../assets/plugins/datatable/css/dataTables.bootstrap5.css" rel="stylesheet">
@endpush

@section('content')

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Pending Parcel Pickups</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard_utama') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pending Pickups</li>
        </ol>
    </div>
    <div class="d-flex">
        <a href="{{ route('parcels.log') }}" class="btn btn-primary btn-icon-text">
            <i class="fe fe-plus me-2"></i> Log New Parcel
        </a>
    </div>
</div>

{{-- Stats --}}
<div class="row row-sm mg-b-20">
    <div class="col-sm-6 col-lg-3">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3 bg-primary-transparent rounded p-2">
                        <i class="fe fe-package tx-20 text-primary"></i>
                    </div>
                    <div>
                        <p class="tx-12 mb-0 text-muted">Total Pending</p>
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
                    <div class="me-3 bg-danger-transparent rounded p-2">
                        <i class="fe fe-alert-triangle tx-20 text-danger"></i>
                    </div>
                    <div>
                        <p class="tx-12 mb-0 text-muted">Overdue (&gt;7 days)</p>
                        <h4 class="mb-0 fw-bold text-danger" id="stat-overdue">—</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Pending Table --}}
<div class="row row-sm">
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="pending-datatable" class="table table-striped table-bordered text-nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Resident</th>
                                <th>Unit</th>
                                <th>Courier</th>
                                <th>Type</th>
                                <th>Tracking No.</th>
                                <th>Received</th>
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

{{-- View Detail Modal --}}
<div class="modal fade" id="parcelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fe fe-package me-2"></i>Parcel Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless tx-13 mb-0">
                    <tr><th width="38%">Resident</th><td id="pm-resident">—</td></tr>
                    <tr><th>Unit</th><td id="pm-unit">—</td></tr>
                    <tr><th>Phone</th><td id="pm-phone">—</td></tr>
                    <tr><th>Courier</th><td id="pm-courier">—</td></tr>
                    <tr><th>Parcel Type</th><td id="pm-type">—</td></tr>
                    <tr><th>Tracking No.</th><td id="pm-tracking">—</td></tr>
                    <tr><th>Received At</th><td id="pm-received">—</td></tr>
                    <tr><th>Notes</th><td id="pm-notes">—</td></tr>
                    <tr id="pm-photo-row" style="display:none;"><th>Photo</th><td><a id="pm-photo-link" href="#" target="_blank"><img id="pm-photo-thumb" src="" style="max-height:100px;border-radius:4px;"></a></td></tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="btnCollect">
                    <i class="fe fe-check-circle me-1"></i> Mark as Collected
                </button>
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

@endsection

@push('scripts')
<script src="../assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatable/js/dataTables.bootstrap5.js"></script>
<script>
var typeLabels = { letter: 'Letter / Document', small_box: 'Small Box', large_box: 'Large Box', fragile: 'Fragile' };
var rowData    = {};
var currentId  = null;

function typeLabel(t) { return typeLabels[t] || t; }

function statusBadge(row) {
    if (row.is_overdue == 1) {
        return '<span class="badge bg-danger">Overdue</span>';
    }
    return '<span class="badge bg-warning text-dark">Pending</span>';
}

$(function () {
    var dt = $('#pending-datatable').DataTable({
        ajax: {
            url:     "{{ route('parcels.pending.list') }}",
            dataSrc: function (json) {
                var rows = json.data || [];
                rowData  = {};
                rows.forEach(function (r) { rowData[r.id] = r; });
                $('#stat-total').text(rows.length);
                $('#stat-overdue').text(rows.filter(function (r) { return r.is_overdue == 1; }).length);
                return rows;
            },
        },
        columns: [
            { data: null, render: function (d, t, r, m) { return m.row + 1; } },
            { data: 'resident_name' },
            { data: null, render: function (d, t, r) {
                return (r.block_name || '') + (r.unit_number ? '-' + r.unit_number : '');
            }},
            { data: 'courier' },
            { data: 'parcel_type', render: function (d) { return typeLabel(d); } },
            { data: 'tracking_no', defaultContent: '—' },
            { data: 'received_at', render: function (d) { return d ? d.slice(0, 16) : '—'; } },
            { data: null, render: function (d, t, r) { return statusBadge(r); } },
            { data: null, render: function (d, t, r) {
                return '<div class="text-center">'
                    + '<button class="btn btn-sm btn-outline-success me-1 btn-collect-inline" data-id="' + r.id + '" title="Mark Collected"><i class="fe fe-check"></i></button>'
                    + '<button class="btn btn-sm btn-outline-info btn-view" data-id="' + r.id + '" title="View"><i class="fe fe-eye"></i></button>'
                    + '</div>';
            }},
        ],
        columnDefs: [{ orderable: false, targets: [0, 8] }],
        language: {
            emptyTable: '<div class="text-center py-4"><i class="fe fe-package" style="font-size:2.2rem;opacity:.3;display:block;margin-bottom:.5rem;"></i><p class="text-muted mb-2">No pending parcels.</p><a href="{{ route('parcels.log') }}" class="btn btn-sm btn-primary">Log a Parcel</a></div>'
        },
    });

    // View detail
    $(document).on('click', '.btn-view', function () {
        var r = rowData[$(this).data('id')];
        if (!r) return;
        currentId = r.id;
        $('#pm-resident').text(r.resident_name || '—');
        $('#pm-unit').text((r.block_name || '') + (r.unit_number ? '-' + r.unit_number : '—'));
        $('#pm-phone').text(r.resident_phone || '—');
        $('#pm-courier').text(r.courier || '—');
        $('#pm-type').text(typeLabel(r.parcel_type));
        $('#pm-tracking').text(r.tracking_no || '—');
        $('#pm-received').text(r.received_at ? r.received_at.slice(0, 16) : '—');
        $('#pm-notes').text(r.notes || '—');
        if (r.photo_path) {
            $('#pm-photo-thumb').attr('src', '/storage/' + r.photo_path);
            $('#pm-photo-link').attr('href', '/storage/' + r.photo_path);
            $('#pm-photo-row').show();
        } else {
            $('#pm-photo-row').hide();
        }
        $('#parcelModal').modal('show');
    });

    // Inline collect button
    $(document).on('click', '.btn-collect-inline', function () {
        currentId = $(this).data('id');
        openCollectModal();
    });

    // Detail modal "Mark as Collected" → switch to PIN modal
    $('#btnCollect').on('click', function () {
        $('#parcelModal').modal('hide');
        openCollectModal();
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
            url:         "{{ route('parcels.collect') }}",
            data:        JSON.stringify({ id: currentId, pin: pin }),
            contentType: 'application/json',
            success: function (res) {
                $('#btnConfirmCollect').prop('disabled', false).html('<i class="fe fe-check me-1"></i> Confirm');
                if (res.status) {
                    $('#collectModal').modal('hide');
                    dt.ajax.reload(null, false);
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
});
</script>
@endpush
