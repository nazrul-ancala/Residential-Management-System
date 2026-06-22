@extends('master_page.master_page')
@section('page_title', 'Manage Staff')

@push('styles')
<link href="../assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="../assets/plugins/sweet-alert/sweetalert.css" rel="stylesheet">
@endpush

@section('content')
<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Manage Staff</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard_utama') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Staff</li>
        </ol>
    </div>
    <div class="d-flex">
        <button type="button" class="btn btn-primary my-2 btn-icon-text" data-bs-toggle="modal" data-bs-target="#addStaffModal">
            <i class="fe fe-user-plus me-2"></i> Add Staff
        </button>
    </div>
</div>

<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-flex justify-content-end gap-2 mb-3">
                    <select id="filter-role" class="form-select form-select-sm w-auto">
                        <option value="">All Roles</option>
                        <option value="technician">Technician</option>
                        <option value="security">Security</option>
                    </select>
                    <select id="filter-status" class="form-select form-select-sm w-auto">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <button id="btn-clear-filters" class="btn btn-sm btn-light">Clear</button>
                </div>
                <div class="table-responsive">
                    <table id="staff-datatable" class="table table-striped table-bordered text-nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>{{-- Populated from API via DataTables --}}</tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ======================= ADD STAFF MODAL ======================= --}}
<div class="modal fade" id="addStaffModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fe fe-user-plus me-2"></i>Add New Staff</h6>
                <button class="btn-close" data-bs-dismiss="modal" type="button"></button>
            </div>
            <div class="modal-body">
                <form id="addStaffForm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" placeholder="Enter full name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" placeholder="Enter email address" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="phone" placeholder="01X-XXXXXXX">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" placeholder="Enter password" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-control" name="role" required>
                                <option value="" disabled selected>Select role</option>
                                <option value="technician">Technician</option>
                                <option value="security">Security</option>
                            </select>
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
            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal" type="button">Cancel</button>
                <button class="btn btn-primary" type="button" id="btnSaveStaff">
                    <i class="fe fe-save me-1"></i> Add Staff
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ======================= VIEW STAFF MODAL ======================= --}}
<div class="modal fade" id="viewStaffModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fe fe-eye me-2"></i>Staff Details</h6>
                <button class="btn-close" data-bs-dismiss="modal" type="button"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless mb-0">
                    <tr><th style="width:35%">Name</th><td id="view-name"></td></tr>
                    <tr><th>Email</th><td id="view-email"></td></tr>
                    <tr><th>Phone</th><td id="view-phone"></td></tr>
                    <tr><th>Role</th><td id="view-role"></td></tr>
                    <tr><th>Status</th><td id="view-status"></td></tr>
                    <tr><th>Joined</th><td id="view-joined"></td></tr>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal" type="button">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- ======================= EDIT STAFF MODAL ======================= --}}
<div class="modal fade" id="editStaffModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fe fe-edit-2 me-2"></i>Edit Staff</h6>
                <button class="btn-close" data-bs-dismiss="modal" type="button"></button>
            </div>
            <div class="modal-body">
                <form id="editStaffForm">
                    @csrf
                    <input type="hidden" id="edit-id" name="id">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="edit-name" name="name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit-email" name="email">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" id="edit-phone" name="phone">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">New Password <span class="text-muted">(blank = keep current)</span></label>
                            <input type="password" class="form-control" id="edit-password" name="password" placeholder="Leave blank to keep">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <select class="form-control" id="edit-role" name="role">
                                <option value="technician">Technician</option>
                                <option value="security">Security</option>
                            </select>
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
                <button class="btn btn-light" data-bs-dismiss="modal" type="button">Cancel</button>
                <button class="btn btn-primary" type="button" id="btnUpdateStaff">
                    <i class="fe fe-save me-1"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="../assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
<script src="../assets/plugins/sweet-alert/sweetalert.min.js"></script>
<script>
$(function () {
    var csrfToken = $('#addStaffForm input[name="_token"]').val();
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': csrfToken } });

    function roleBadge(role) {
        var map = { technician: 'badge bg-info text-dark', security: 'badge bg-warning text-dark' };
        var cls = map[role] || 'badge bg-secondary';
        return '<span class="' + cls + '">' + (role ? role.charAt(0).toUpperCase() + role.slice(1) : '-') + '</span>';
    }

    function statusBadge(s) {
        return s === 'active'
            ? '<span class="badge bg-success">Active</span>'
            : '<span class="badge bg-secondary">Inactive</span>';
    }

    function formatDate(dt) {
        if (!dt) return '-';
        return new Date(dt).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    var table = $('#staff-datatable').DataTable({
        ajax: {
            url: "{{ route('staff.list') }}",
            dataSrc: 'data'
        },
        language: { emptyTable: 'No staff members found.' },
        columns: [
            { data: null, orderable: false, render: function (d, t, r, meta) { return meta.row + 1; } },
            { data: 'name' },
            { data: 'email' },
            { data: 'phone', defaultContent: '-', render: function (d) { return d || '-'; } },
            { data: 'role',       render: function (d) { return roleBadge(d); } },
            { data: 'status',     render: function (d) { return statusBadge(d); } },
            { data: 'created_at', render: function (d) { return formatDate(d); } },
            {
                data: null, orderable: false, className: 'text-center',
                render: function (d, t, r) {
                    var p = encodeURIComponent(JSON.stringify(r));
                    return '<button class="btn btn-sm btn-secondary btn-view-staff me-1" data-row="' + p + '" title="View"><i class="fe fe-eye"></i></button>' +
                           '<button class="btn btn-sm btn-info btn-edit-staff me-1" data-row="' + p + '" title="Edit"><i class="fe fe-edit-2"></i></button>' +
                           '<button class="btn btn-sm btn-danger btn-delete-staff" data-id="' + r.id + '" data-name="' + (r.name || '') + '" title="Delete"><i class="fe fe-trash-2"></i></button>';
                }
            }
        ],
        columnDefs: [{ orderable: false, targets: 7 }]
    });

    // ---- Filters ----
    $.fn.dataTable.ext.search.push(function (settings, data) {
        if (settings.nTable.id !== 'staff-datatable') return true;
        var role   = $('#filter-role').val();
        var status = $('#filter-status').val();
        var rowRole   = data[4].replace(/<[^>]+>/g, '').trim().toLowerCase();
        var rowStatus = data[5].replace(/<[^>]+>/g, '').trim().toLowerCase();
        if (role   && rowRole   !== role)   return false;
        if (status && rowStatus !== status) return false;
        return true;
    });

    $('#filter-role, #filter-status').on('change', function () { table.draw(); });
    $('#btn-clear-filters').on('click', function () {
        $('#filter-role, #filter-status').val('');
        table.draw();
    });

    // ---- View ----
    $('#staff-datatable').on('click', '.btn-view-staff', function () {
        var r = JSON.parse(decodeURIComponent($(this).data('row')));
        $('#view-name').text(r.name || '-');
        $('#view-email').text(r.email || '-');
        $('#view-phone').text(r.phone || '-');
        $('#view-role').html(roleBadge(r.role));
        $('#view-status').html(statusBadge(r.status));
        $('#view-joined').text(formatDate(r.created_at));
        $('#viewStaffModal').modal('show');
    });

    // ---- Open edit ----
    $('#staff-datatable').on('click', '.btn-edit-staff', function () {
        var r = JSON.parse(decodeURIComponent($(this).data('row')));
        $('#edit-id').val(r.id);
        $('#edit-name').val(r.name);
        $('#edit-email').val(r.email);
        $('#edit-phone').val(r.phone);
        $('#edit-password').val('');
        $('#edit-role').val(r.role);
        $('#edit-status').val(r.status);
        $('#editStaffModal').modal('show');
    });

    // ---- Create ----
    $('#btnSaveStaff').on('click', function () {
        var $btn  = $(this);
        var $form = $('#addStaffForm');
        if (!$form[0].checkValidity()) { $form[0].reportValidity(); return; }

        var data  = $form.serializeArray().reduce(function (o, f) { o[f.name] = f.value; return o; }, {});
        data.action = 'save';

        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving…');

        $.post('{{ route('staff.save') }}', data, function (res) {
            $btn.prop('disabled', false).html('<i class="fe fe-save me-1"></i> Add Staff');
            $('#addStaffModal').modal('hide');
            if (res.status) { $form[0].reset(); table.ajax.reload(null, false); swal('Saved!', res.message, 'success'); }
            else { swal('Failed', res.message, 'error'); }
        }).fail(function () {
            $btn.prop('disabled', false).html('<i class="fe fe-save me-1"></i> Add Staff');
            swal('Error', 'Could not reach the server.', 'error');
        });
    });

    // ---- Update ----
    $('#btnUpdateStaff').on('click', function () {
        var $btn  = $(this);
        var $form = $('#editStaffForm');
        var data  = $form.serializeArray().reduce(function (o, f) { o[f.name] = f.value; return o; }, {});
        data.action = 'update';

        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Updating…');

        $.post('{{ route('staff.save') }}', data, function (res) {
            $btn.prop('disabled', false).html('<i class="fe fe-save me-1"></i> Save Changes');
            $('#editStaffModal').modal('hide');
            if (res.status) { table.ajax.reload(null, false); swal('Updated!', res.message, 'success'); }
            else { swal('Failed', res.message, 'error'); }
        }).fail(function () {
            $btn.prop('disabled', false).html('<i class="fe fe-save me-1"></i> Save Changes');
            swal('Error', 'Could not reach the server.', 'error');
        });
    });

    // ---- Delete ----
    $('#staff-datatable').on('click', '.btn-delete-staff', function () {
        var $btn = $(this);
        var id   = $(this).data('id');
        var name = $(this).data('name');
        swal({
            title: 'Delete Staff?',
            text: '"' + name + '" will be permanently deleted.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d9534f',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }, function (confirmed) {
            if (!confirmed) return;
            $btn.prop('disabled', true);
            $.post('{{ route('staff.save') }}', { action: 'delete', id: id }, function (res) {
                if (res.status) { table.ajax.reload(null, false); swal('Deleted!', res.message, 'success'); }
                else { $btn.prop('disabled', false); swal('Failed', res.message, 'error'); }
            }).fail(function () {
                $btn.prop('disabled', false);
                swal('Error', 'Could not reach the server.', 'error');
            });
        });
    });
});
</script>
@endpush
