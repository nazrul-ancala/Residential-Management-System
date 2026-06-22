@extends('master_page.master_page')
@section('page_title', 'Manage Residents')

@push('styles')
<link href="../assets/plugins/datatable/css/dataTables.bootstrap5.css" rel="stylesheet">
<link href="../assets/plugins/datatable/css/buttons.bootstrap5.min.css" rel="stylesheet">
<link href="../assets/plugins/sweet-alert/sweetalert.css" rel="stylesheet">
@endpush

@section('content')

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Manage Residents</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard_utama') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Residents</li>
        </ol>
    </div>
    <div class="d-flex">
        <button type="button" class="btn btn-primary btn-icon-text" data-bs-toggle="modal" data-bs-target="#addResidentModal">
            <i class="fe fe-user-plus me-2"></i> Add Resident
        </button>
    </div>
</div>

{{-- Summary cards --}}
<div class="row row-sm mg-b-20">
    <div class="col-sm-6 col-lg-3">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <span class="text-muted tx-12">Total Residents</span>
                        <h3 class="mb-0 mt-1" id="stat-total">—</h3>
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
                        <span class="text-muted tx-12">Active Residents</span>
                        <h3 class="mb-0 mt-1" id="stat-active">—</h3>
                    </div>
                    <span class="ms-auto bg-success-transparent text-success avatar avatar-md rounded-circle">
                        <i class="fe fe-home tx-18"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Residents Table --}}
<div class="row row-sm">
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="residents-datatable" class="table table-striped table-bordered text-nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Block</th>
                                <th>Unit</th>
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

{{-- ======================= ADD RESIDENT MODAL ======================= --}}
<div class="modal fade" id="addResidentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fe fe-user-plus me-2"></i>Add Resident</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addResidentForm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" placeholder="e.g. Ahmad bin Ali" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" placeholder="resident@email.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="phone" placeholder="01X-XXXXXXX">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Block <span class="text-danger">*</span></label>
                            <select class="form-control" name="block" required>
                                <option value="" disabled selected>Select block…</option>
                                <option value="Block A">Block A</option>
                                <option value="Block B">Block B</option>
                                <option value="Block C">Block C</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Unit No. <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="unit" placeholder="e.g. A-12-03" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Status</label>
                            <select class="form-control" name="status">
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex align-items-center justify-content-between">
                <p class="text-muted tx-12 mb-0"><i class="fe fe-info me-1"></i>Default password: <strong>unitcare@123</strong> — change via Manage Users.</p>
                <div>
                    <button type="button" class="btn btn-light me-1" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveResidentBtn">
                        <i class="fe fe-save me-1"></i> Save Resident
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ======================= VIEW RESIDENT MODAL ======================= --}}
<div class="modal fade" id="viewResidentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fe fe-eye me-2"></i>Resident Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless mb-0">
                    <tbody>
                        <tr><th width="35%">Name</th><td id="view-name">—</td></tr>
                        <tr><th>Email</th><td id="view-email">—</td></tr>
                        <tr><th>Phone</th><td id="view-phone">—</td></tr>
                        <tr><th>Block</th><td id="view-block">—</td></tr>
                        <tr><th>Unit</th><td id="view-unit">—</td></tr>
                        <tr><th>Status</th><td id="view-status">—</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- ======================= EDIT RESIDENT MODAL ======================= --}}
<div class="modal fade" id="editResidentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fe fe-edit-2 me-2"></i>Edit Resident</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editResidentForm">
                    @csrf
                    <input type="hidden" id="edit-id" name="id">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit-name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="edit-email" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" id="edit-phone" name="phone">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Block</label>
                            <select class="form-control" id="edit-block" name="block">
                                <option value="">— keep current —</option>
                                <option value="Block A">Block A</option>
                                <option value="Block B">Block B</option>
                                <option value="Block C">Block C</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Unit No.</label>
                            <input type="text" class="form-control" id="edit-unit" name="unit" placeholder="Leave blank to keep current">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Status</label>
                            <select class="form-control" id="edit-status" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="updateResidentBtn">
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
var RESIDENTS_LIST_URL = '{{ route("residents.list") }}';
var RESIDENTS_SAVE_URL = '{{ route("residents.save") }}';
var CSRF_TOKEN = '{{ csrf_token() }}';

$(function () {

    // ── DataTable ──────────────────────────────────────────────────────────
    var table = $('#residents-datatable').DataTable({
        ajax: { url: RESIDENTS_LIST_URL, dataSrc: 'data' },
        columns: [
            { data: null, render: function (d, t, r, m) { return m.row + 1; }, orderable: false },
            { data: 'name' },
            { data: 'email' },
            { data: 'phone', render: function (d) { return d || '—'; } },
            { data: 'block', render: function (d) { return d || '—'; } },
            { data: 'unit_no', render: function (d) { return d || '—'; } },
            { data: 'status', render: function (d) { return statusBadge(d); } },
            {
                data: null, orderable: false, className: 'text-center',
                render: function (d, t, r) {
                    var enc = encodeURIComponent(JSON.stringify(r));
                    return '<button class="btn btn-sm btn-info me-1 btn-view-resident" data-row="' + enc + '" title="View"><i class="fe fe-eye"></i></button>' +
                           '<button class="btn btn-sm btn-warning me-1 btn-edit-resident" data-row="' + enc + '" title="Edit"><i class="fe fe-edit-2"></i></button>' +
                           '<button class="btn btn-sm btn-danger btn-deactivate-resident" data-id="' + r.id + '" data-name="' + r.name + '" title="Delete"><i class="fe fe-trash-2"></i></button>';
                }
            }
        ],
        language: {
            emptyTable: '<div class="text-center py-4"><i class="fe fe-users" style="font-size:2.2rem;opacity:.3;display:block;margin-bottom:.5rem;"></i><p class="text-muted mb-2">No residents registered yet.</p><a href="#" data-bs-toggle="modal" data-bs-target="#addResidentModal" class="btn btn-sm btn-primary">Add First Resident</a></div>'
        },
        drawCallback: function () {
            var all    = this.api().data().length;
            var active = 0;
            this.api().data().each(function (r) { if (r.status === 'active') active++; });
            $('#stat-total').text(all);
            $('#stat-active').text(active);
        }
    });

    function statusBadge(s) {
        return s === 'active'
            ? '<span class="badge bg-success">Active</span>'
            : '<span class="badge bg-secondary">Inactive</span>';
    }

    // ── View modal ─────────────────────────────────────────────────────────
    $(document).on('click', '.btn-view-resident', function () {
        var r = JSON.parse(decodeURIComponent($(this).data('row')));
        $('#view-name').text(r.name);
        $('#view-email').text(r.email);
        $('#view-phone').text(r.phone || '—');
        $('#view-block').text(r.block || '—');
        $('#view-unit').text(r.unit_no || '—');
        $('#view-status').html(statusBadge(r.status));
        $('#viewResidentModal').modal('show');
    });

    // ── Edit modal ─────────────────────────────────────────────────────────
    $(document).on('click', '.btn-edit-resident', function () {
        var r = JSON.parse(decodeURIComponent($(this).data('row')));
        $('#edit-id').val(r.id);
        $('#edit-name').val(r.name);
        $('#edit-email').val(r.email);
        $('#edit-phone').val(r.phone);
        $('#edit-block').val(r.block);
        $('#edit-unit').val(r.unit_no);
        $('#edit-status').val(r.status);
        $('#editResidentModal').modal('show');
    });

    // ── Save Resident ──────────────────────────────────────────────────────
    $('#saveResidentBtn').on('click', function () {
        var $btn = $(this);
        var form = $('#addResidentForm')[0];
        if (!form.checkValidity()) { form.reportValidity(); return; }

        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving…');

        var data = Object.fromEntries(new FormData(form));
        data.action = 'save';
        data._token = CSRF_TOKEN;

        $.post(RESIDENTS_SAVE_URL, data)
            .done(function (res) {
                if (res.status) {
                    swal('Success!', res.message, 'success');
                    $('#addResidentModal').modal('hide');
                    form.reset();
                    table.ajax.reload(null, false);
                } else {
                    swal('Error', res.message, 'error');
                }
            })
            .fail(function () { swal('Error', 'Server error. Please try again.', 'error'); })
            .always(function () {
                $btn.prop('disabled', false).html('<i class="fe fe-save me-1"></i> Save Resident');
            });
    });

    // ── Update Resident ────────────────────────────────────────────────────
    $('#updateResidentBtn').on('click', function () {
        var $btn = $(this);
        var form = $('#editResidentForm')[0];
        if (!form.checkValidity()) { form.reportValidity(); return; }

        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Updating…');

        var data = Object.fromEntries(new FormData(form));
        data.action = 'update';
        data._token = CSRF_TOKEN;

        $.post(RESIDENTS_SAVE_URL, data)
            .done(function (res) {
                if (res.status) {
                    swal('Updated!', res.message, 'success');
                    $('#editResidentModal').modal('hide');
                    table.ajax.reload(null, false);
                } else {
                    swal('Error', res.message, 'error');
                }
            })
            .fail(function () { swal('Error', 'Server error. Please try again.', 'error'); })
            .always(function () {
                $btn.prop('disabled', false).html('<i class="fe fe-save me-1"></i> Update');
            });
    });

    // ── Delete Resident ────────────────────────────────────────────────
    $(document).on('click', '.btn-deactivate-resident', function () {
        var id   = $(this).data('id');
        var name = $(this).data('name');
        var $btn = $(this);

        swal({
            title: 'Delete Resident?',
            text: '"' + name + '" will be permanently deleted.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d9534f',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }, function (confirmed) {
            if (!confirmed) return;

            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

            $.post(RESIDENTS_SAVE_URL, { _token: CSRF_TOKEN, action: 'delete', id: id })
                .done(function (res) {
                    if (res.status) {
                        swal('Deleted!', res.message, 'success');
                        table.ajax.reload(null, false);
                    } else {
                        swal('Error', res.message, 'error');
                        $btn.prop('disabled', false).html('<i class="fe fe-trash-2"></i>');
                    }
                })
                .fail(function () {
                    swal('Error', 'Server error. Please try again.', 'error');
                    $btn.prop('disabled', false).html('<i class="fe fe-trash-2"></i>');
                });
        });
    });

});
</script>
@endpush
