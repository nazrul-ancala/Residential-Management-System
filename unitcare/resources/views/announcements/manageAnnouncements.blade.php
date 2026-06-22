@extends('master_page.master_page')
@section('page_title', 'Announcements')

@push('styles')
<link href="../assets/plugins/datatable/css/dataTables.bootstrap5.css" rel="stylesheet">
@endpush

@section('content')

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Announcements</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard_utama') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Manage Announcements</li>
        </ol>
    </div>
    <div class="d-flex">
        <button type="button" class="btn btn-primary btn-icon-text" data-bs-toggle="modal" data-bs-target="#createAnnouncementModal">
            <i class="fe fe-plus-circle me-2"></i> Create Announcement
        </button>
    </div>
</div>

<div class="row row-sm">
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="announcements-datatable" class="table table-striped table-bordered text-nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Published At</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Populated by DB later --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ======================= CREATE ANNOUNCEMENT MODAL ======================= --}}
<div class="modal fade" id="createAnnouncementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fe fe-bell me-2"></i>Create Announcement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createAnnouncementForm">
                    @csrf
                    <input type="hidden" id="edit-ann-id" value="">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" placeholder="Announcement title…" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type <span class="text-danger">*</span></label>
                            <select class="form-control" name="type" required>
                                <option value="" disabled selected>Select type…</option>
                                <option value="general">General Notice</option>
                                <option value="emergency">Emergency Notice</option>
                                <option value="event">Event Information</option>
                                <option value="maintenance">Maintenance Notice</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Publish Date</label>
                            <input type="date" class="form-control" name="publish_date">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Content <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="content" rows="5" placeholder="Write the announcement content…" required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Image <small class="text-muted">(optional, max 2 MB)</small></label>
                            <input type="file" class="form-control" id="ann-image-input" accept="image/*">
                            <div id="ann-image-preview" class="mt-2 d-flex align-items-center" style="display:none !important;">
                                <img id="ann-image-thumb" src="" alt="" style="max-height:110px;border-radius:6px;border:1px solid #dee2e6;">
                                <button type="button" class="btn btn-sm btn-outline-danger ms-2" id="ann-remove-image">
                                    <i class="fe fe-trash-2 me-1"></i>Remove
                                </button>
                            </div>
                            <input type="hidden" id="ann-image-path" value="">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-control" name="status">
                                <option value="draft" selected>Draft</option>
                                <option value="published">Published</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveAnnouncementBtn">
                    <i class="fe fe-send me-1"></i> <span id="saveBtnLabel">Save</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ======================= VIEW ANNOUNCEMENT MODAL ======================= --}}
<div class="modal fade" id="viewAnnouncementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ann-modal-title">—</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center mb-3">
                    <span class="badge me-2" id="ann-modal-type">—</span>
                    <small class="text-muted" id="ann-modal-date">—</small>
                </div>
                <p id="ann-modal-content" class="text-muted">—</p>
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
var annData = {};

var typeConfig = {
    general:     { cls: 'bg-primary',           label: 'General'     },
    emergency:   { cls: 'bg-danger',            label: 'Emergency'   },
    event:       { cls: 'bg-info',              label: 'Event'       },
    maintenance: { cls: 'bg-warning text-dark', label: 'Maintenance' },
};
var statusConfig = {
    draft:     { cls: 'bg-secondary',          label: 'Draft'     },
    scheduled: { cls: 'bg-warning text-dark',  label: 'Scheduled' },
    published: { cls: 'bg-success',            label: 'Published' },
};

function typeBadge(t) {
    var c = typeConfig[t] || { cls: 'bg-light text-dark', label: t };
    return '<span class="badge ' + c.cls + '">' + c.label + '</span>';
}
function statusBadge(s) {
    var c = statusConfig[s] || { cls: 'bg-light text-dark', label: s };
    return '<span class="badge ' + c.cls + '">' + c.label + '</span>';
}

$(function () {
    var dt = $('#announcements-datatable').DataTable({
        ajax: {
            url: "{{ route('manageAnnouncements.list') }}",
            dataSrc: function (json) {
                annData = {};
                (json.data || []).forEach(function (r) { annData[r.id] = r; });
                return json.data || [];
            },
        },
        columns: [
            { data: null, render: function (d, t, r, m) { return m.row + 1; } },
            { data: 'title' },
            { data: 'type',        render: function (d) { return typeBadge(d); } },
            { data: 'published_at', defaultContent: '—' },
            { data: 'status',      render: function (d) { return statusBadge(d); } },
            {
                data: null,
                render: function (d, t, r) {
                    return '<div class="text-center">'
                        + '<button class="btn btn-sm btn-outline-info me-1 btn-view-ann" data-id="' + r.id + '"><i class="fe fe-eye"></i></button>'
                        + '<button class="btn btn-sm btn-outline-primary me-1 btn-edit-ann" data-id="' + r.id + '"><i class="fe fe-edit-2"></i></button>'
                        + '<button class="btn btn-sm btn-outline-danger btn-delete-ann" data-id="' + r.id + '" data-title="' + (r.title || '').replace(/"/g, '&quot;') + '"><i class="fe fe-trash-2"></i></button>'
                        + '</div>';
                }
            },
        ],
        columnDefs: [{ orderable: false, targets: [0, 5] }],
        language: { emptyTable: 'No announcements yet.' },
    });

    // ── Image upload helpers ───────────────────────────────────────────────
    function showImagePreview(src) {
        $('#ann-image-thumb').attr('src', src);
        $('#ann-image-preview').css('display', 'flex');
    }
    function clearImagePreview() {
        $('#ann-image-path').val('');
        $('#ann-image-thumb').attr('src', '');
        $('#ann-image-preview').css('display', 'none');
        $('#ann-image-input').val('');
    }

    $('#ann-image-input').on('change', function () {
        var file = this.files[0];
        if (!file) return;
        var fd = new FormData();
        fd.append('image', file);
        fd.append('_token', $('meta[name="csrf-token"]').attr('content'));
        $.ajax({
            url:         "{{ route('announcement.uploadImage') }}",
            method:      'POST',
            data:        fd,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.status) {
                    $('#ann-image-path').val(res.path);
                    showImagePreview(res.path);
                } else {
                    Swal.fire({ icon: 'error', title: 'Upload Failed', text: 'Could not save the image.', confirmButtonColor: '#6259ca' });
                    clearImagePreview();
                }
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Upload Error', text: 'Image upload failed. Max size is 2 MB.', confirmButtonColor: '#6259ca' });
                clearImagePreview();
            },
        });
    });

    $('#ann-remove-image').on('click', function () { clearImagePreview(); });

    // ── Create button — reset modal to "create" mode ──────────────────────
    $('[data-bs-target="#createAnnouncementModal"]').on('click', function () {
        $('#edit-ann-id').val('');
        $('#createAnnouncementForm')[0].reset();
        clearImagePreview();
        $('#createAnnouncementModal .modal-title').html('<i class="fe fe-bell me-2"></i>Create Announcement');
        $('#saveBtnLabel').text('Save');
    });

    // ── View ──────────────────────────────────────────────────────────────
    $(document).on('click', '.btn-view-ann', function () {
        var r = annData[$(this).data('id')];
        if (!r) return;
        var tc = typeConfig[r.type] || { cls: 'bg-light text-dark', label: r.type };
        $('#ann-modal-title').text(r.title);
        $('#ann-modal-type').attr('class', 'badge me-2 ' + tc.cls).text(tc.label);
        $('#ann-modal-date').text(r.published_at || 'Not yet published');
        $('#ann-modal-content').text(r.content);
        $('#viewAnnouncementModal').modal('show');
    });

    // ── Edit ──────────────────────────────────────────────────────────────
    $(document).on('click', '.btn-edit-ann', function () {
        var r = annData[$(this).data('id')];
        if (!r) return;
        $('#edit-ann-id').val(r.id);
        $('#createAnnouncementForm [name=title]').val(r.title);
        $('#createAnnouncementForm [name=type]').val(r.type);
        $('#createAnnouncementForm [name=content]').val(r.content);
        $('#createAnnouncementForm [name=publish_date]').val(r.published_at ? r.published_at.slice(0, 10) : '');
        $('#createAnnouncementForm [name=status]').val(r.status === 'draft' ? 'draft' : 'published');
        if (r.image_path) {
            $('#ann-image-path').val(r.image_path);
            showImagePreview(r.image_path);
        } else {
            clearImagePreview();
        }
        $('#createAnnouncementModal .modal-title').html('<i class="fe fe-edit-2 me-2"></i>Edit Announcement');
        $('#saveBtnLabel').text('Update');
        $('#createAnnouncementModal').modal('show');
    });

    // ── Save / Update ─────────────────────────────────────────────────────
    $('#saveAnnouncementBtn').on('click', function () {
        var title   = $('#createAnnouncementForm [name=title]').val().trim();
        var type    = $('#createAnnouncementForm [name=type]').val();
        var content = $('#createAnnouncementForm [name=content]').val().trim();
        var status  = $('#createAnnouncementForm [name=status]').val();
        var pubDate = $('#createAnnouncementForm [name=publish_date]').val();
        var editId  = $('#edit-ann-id').val();

        if (!title || !type || !content) {
            Swal.fire({ icon: 'warning', title: 'Check your input', text: 'Title, type, and content are required.', confirmButtonColor: '#6259ca' });
            return;
        }

        var publishedAt = null;
        if (status === 'published') {
            publishedAt = pubDate ? pubDate + ' 00:00:00' : new Date().toISOString().slice(0, 10) + ' 00:00:00';
        }

        var payload = {
            action:       editId ? 'update' : 'save',
            id:           editId || null,
            title:        title,
            type:         type,
            content:      content,
            published_at: publishedAt,
            image_path:   $('#ann-image-path').val() || null,
        };

        var $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving…');

        $.post({
            url:         "{{ route('announcement.store') }}",
            data:        JSON.stringify(payload),
            contentType: 'application/json',
            success: function (res) {
                if (res.status) {
                    $('#createAnnouncementModal').modal('hide');
                    dt.ajax.reload(null, false);
                    Swal.fire({ icon: 'success', title: 'Done', text: res.message, confirmButtonColor: '#6259ca', timer: 2000, showConfirmButton: false });
                } else {
                    Swal.fire({ icon: 'error', title: 'Failed', text: res.message || 'Unknown error.', confirmButtonColor: '#6259ca' });
                }
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Server Error', text: 'Could not reach the server.', confirmButtonColor: '#6259ca' });
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="fe fe-send me-1"></i> <span id="saveBtnLabel">' + (editId ? 'Update' : 'Save') + '</span>');
            },
        });
    });

    // ── Delete ────────────────────────────────────────────────────────────
    $(document).on('click', '.btn-delete-ann', function () {
        var id    = $(this).data('id');
        var title = $(this).data('title');
        Swal.fire({
            icon:              'warning',
            title:             'Delete Announcement?',
            text:              'Delete "' + title + '"? This cannot be undone.',
            showCancelButton:  true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText:  'Cancel',
            confirmButtonColor:'#dc3545',
            cancelButtonColor: '#6c757d',
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.post({
                url:         "{{ route('announcement.store') }}",
                data:        JSON.stringify({ action: 'delete', id: id }),
                contentType: 'application/json',
                success: function (res) {
                    if (res.status) {
                        dt.ajax.reload(null, false);
                        Swal.fire({ icon: 'success', title: 'Deleted', text: res.message, confirmButtonColor: '#6259ca', timer: 1800, showConfirmButton: false });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Failed', text: res.message || 'Unknown error.', confirmButtonColor: '#6259ca' });
                    }
                },
                error: function () {
                    Swal.fire({ icon: 'error', title: 'Server Error', text: 'Could not reach the server.', confirmButtonColor: '#6259ca' });
                },
            });
        });
    });
});
</script>
@endpush
