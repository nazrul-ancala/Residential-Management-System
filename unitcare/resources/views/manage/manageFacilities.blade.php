@extends('master_page.master_page')
@section('page_title', 'Manage Facilities')

@push('styles')
<link href="../assets/plugins/datatable/css/dataTables.bootstrap5.css" rel="stylesheet">
<link href="../assets/plugins/sweet-alert/sweetalert.css" rel="stylesheet">
@endpush

@section('content')

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Manage Facilities</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard_utama') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Facilities</li>
        </ol>
    </div>
    <div class="d-flex">
        <button type="button" class="btn btn-primary btn-icon-text" data-bs-toggle="modal" data-bs-target="#addFacilityModal">
            <i class="fe fe-plus-circle me-2"></i> Add Facility
        </button>
    </div>
</div>

<div class="row row-sm">
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="facilities-datatable" class="table table-striped table-bordered text-nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Facility Name</th>
                                <th>Description</th>
                                <th>Max Booking (hrs)</th>
                                <th>Capacity</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Populated from the API via DataTables --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ======================= ADD FACILITY MODAL ======================= --}}
<div class="modal fade" id="addFacilityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fe fe-plus-circle me-2"></i>Add Facility</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addFacilityForm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Facility Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" placeholder="e.g. Gym, BBQ Area" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="2" placeholder="Brief description…"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Max Booking Hours</label>
                            <input type="number" class="form-control" name="max_booking_hours" placeholder="e.g. 3" min="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Capacity (persons)</label>
                            <input type="number" class="form-control" name="capacity" placeholder="e.g. 50" min="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-control" name="status">
                                <option value="available" selected>Available</option>
                                <option value="unavailable">Unavailable</option>
                                <option value="maintenance">Under Maintenance</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnSaveFacility">
                    <i class="fe fe-save me-1"></i> Save Facility
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ======================= VIEW FACILITY MODAL ======================= --}}
<div class="modal fade" id="viewFacilityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fe fe-eye me-2"></i>Facility Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless mb-0">
                    <tbody>
                        <tr><th style="width:40%">Facility Name</th><td id="view-fac-name">-</td></tr>
                        <tr><th>Description</th><td id="view-fac-desc">-</td></tr>
                        <tr><th>Max Booking (hrs)</th><td id="view-fac-hours">-</td></tr>
                        <tr><th>Capacity</th><td id="view-fac-capacity">-</td></tr>
                        <tr><th>Status</th><td id="view-fac-status">-</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- ======================= EDIT FACILITY MODAL ======================= --}}
<div class="modal fade" id="editFacilityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fe fe-edit-2 me-2"></i>Edit Facility</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editFacilityForm">
                    @csrf
                    <input type="hidden" id="edit-fac-id" name="id">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Facility Name</label>
                            <input type="text" class="form-control" id="edit-fac-name" name="name">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="edit-fac-desc" name="description" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Max Booking Hours</label>
                            <input type="number" class="form-control" id="edit-fac-hours" name="max_booking_hours" min="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Capacity (persons)</label>
                            <input type="number" class="form-control" id="edit-fac-capacity" name="capacity" min="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-control" id="edit-fac-status" name="status">
                                <option value="available">Available</option>
                                <option value="unavailable">Unavailable</option>
                                <option value="maintenance">Under Maintenance</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnUpdateFacility">
                    <i class="fe fe-save me-1"></i> Update
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="../assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatable/js/dataTables.bootstrap5.js"></script>
<script src="../assets/plugins/sweet-alert/sweetalert.min.js"></script>
<script>
$(function () {
    // Send the CSRF token with every AJAX request to the frontend proxy routes.
    var csrfToken = $('#addFacilityForm input[name="_token"]').val();
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': csrfToken } });

    function statusBadge(status) {
        var map = {
            available:   'badge bg-success',
            unavailable: 'badge bg-secondary',
            maintenance: 'badge bg-warning'
        };
        var cls = map[status] || 'badge bg-light text-dark';
        var label = status ? status.charAt(0).toUpperCase() + status.slice(1) : '-';
        return '<span class="' + cls + '">' + label + '</span>';
    }

    var table = $('#facilities-datatable').DataTable({
        ajax: {
            url: '{{ route('facilities.list') }}',
            dataSrc: 'data'
        },
        language: { emptyTable: 'No facilities added yet.' },
        columns: [
            { data: null, orderable: false, render: function (d, t, r, meta) { return meta.row + 1; } },
            { data: 'name' },
            { data: 'description', defaultContent: '-', render: function (d) { return d || '-'; } },
            { data: 'max_booking_hours', defaultContent: '-', render: function (d) { return (d === null || d === '') ? '-' : d; } },
            { data: 'capacity', defaultContent: '-', render: function (d) { return (d === null || d === '') ? '-' : d; } },
            { data: 'status', render: function (d) { return statusBadge(d); } },
            {
                data: null,
                orderable: false,
                className: 'text-center',
                render: function (d, t, r) {
                    var payload = encodeURIComponent(JSON.stringify(r));
                    return '<button class="btn btn-sm btn-secondary btn-view-facility me-1" data-row="' + payload + '" title="View">' +
                               '<i class="fe fe-eye"></i></button>' +
                           '<button class="btn btn-sm btn-info btn-edit-facility me-1" data-row="' + payload + '" title="Edit">' +
                               '<i class="fe fe-edit-2"></i></button>' +
                           '<button class="btn btn-sm btn-danger btn-delete-facility" data-id="' + r.id + '" data-name="' + (r.name || '') + '" title="Delete">' +
                               '<i class="fe fe-trash-2"></i></button>';
                }
            }
        ],
        columnDefs: [{ orderable: false, targets: 6 }]
    });

    // ---- View ----
    $('#facilities-datatable').on('click', '.btn-view-facility', function () {
        var r = JSON.parse(decodeURIComponent($(this).data('row')));
        $('#view-fac-name').text(r.name || '-');
        $('#view-fac-desc').text(r.description || '-');
        $('#view-fac-hours').text(r.max_booking_hours != null ? r.max_booking_hours + ' hr(s)' : '-');
        $('#view-fac-capacity').text(r.capacity != null ? r.capacity + ' persons' : '-');
        $('#view-fac-status').html(statusBadge(r.status));
        $('#viewFacilityModal').modal('show');
    });

    // ---- Create ----
    $('#btnSaveFacility').on('click', function () {
        var $btn = $(this);
        var $form = $('#addFacilityForm');
        if (!$form[0].checkValidity()) { $form[0].reportValidity(); return; }

        var data = $form.serializeArray().reduce(function (o, f) { o[f.name] = f.value; return o; }, {});
        data.action = 'save';

        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving…');

        $.post('{{ route('facilities.save') }}', data, function (res) {
            $btn.prop('disabled', false).html('<i class="fe fe-save me-1"></i> Save Facility');
            $('#addFacilityModal').modal('hide');
            if (res.status) {
                $form[0].reset();
                table.ajax.reload(null, false);
                swal('Saved!', res.message, 'success');
            } else {
                swal('Failed', res.message, 'error');
            }
        }).fail(function () {
            $btn.prop('disabled', false).html('<i class="fe fe-save me-1"></i> Save Facility');
            swal('Error', 'Could not reach the server.', 'error');
        });
    });

    // ---- Open edit modal ----
    $('#facilities-datatable').on('click', '.btn-edit-facility', function () {
        var r = JSON.parse(decodeURIComponent($(this).data('row')));
        $('#edit-fac-id').val(r.id);
        $('#edit-fac-name').val(r.name);
        $('#edit-fac-desc').val(r.description);
        $('#edit-fac-hours').val(r.max_booking_hours);
        $('#edit-fac-capacity').val(r.capacity);
        $('#edit-fac-status').val(r.status);
        $('#editFacilityModal').modal('show');
    });

    // ---- Update ----
    $('#btnUpdateFacility').on('click', function () {
        var $btn = $(this);
        var $form = $('#editFacilityForm');
        var data = $form.serializeArray().reduce(function (o, f) { o[f.name] = f.value; return o; }, {});
        data.action = 'update';

        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Updating…');

        $.post('{{ route('facilities.save') }}', data, function (res) {
            $btn.prop('disabled', false).html('<i class="fe fe-save me-1"></i> Update');
            $('#editFacilityModal').modal('hide');
            if (res.status) {
                table.ajax.reload(null, false);
                swal('Updated!', res.message, 'success');
            } else {
                swal('Failed', res.message, 'error');
            }
        }).fail(function () {
            $btn.prop('disabled', false).html('<i class="fe fe-save me-1"></i> Update');
            swal('Error', 'Could not reach the server.', 'error');
        });
    });

    // ---- Delete ----
    $('#facilities-datatable').on('click', '.btn-delete-facility', function () {
        var $btn = $(this);
        var id = $(this).data('id');
        var name = $(this).data('name');
        swal({
            title: 'Delete Facility?',
            text: '"' + name + '" will be permanently removed.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d9534f',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }, function (confirmed) {
            if (!confirmed) return;
            $btn.prop('disabled', true);
            $.post('{{ route('facilities.save') }}', { action: 'delete', id: id }, function (res) {
                if (res.status) {
                    table.ajax.reload(null, false);
                    swal('Deleted!', res.message, 'success');
                } else {
                    $btn.prop('disabled', false);
                    swal('Failed', res.message, 'error');
                }
            }).fail(function () {
                $btn.prop('disabled', false);
                swal('Error', 'Could not reach the server.', 'error');
            });
        });
    });
});
</script>
@endpush
