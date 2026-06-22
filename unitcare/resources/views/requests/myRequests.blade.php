@extends('master_page.master_page')
@section('page_title', 'My Maintenance Requests')

@push('styles')
<link href="../assets/plugins/datatable/css/dataTables.bootstrap5.css" rel="stylesheet">
<link href="../assets/plugins/sweet-alert/sweetalert.css" rel="stylesheet">
@endpush

@section('content')

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">My Maintenance Requests</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard_utama') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">My Requests</li>
        </ol>
    </div>
    <div class="d-flex">
        <button type="button" class="btn btn-primary btn-icon-text" data-bs-toggle="modal" data-bs-target="#submitRequestModal">
            <i class="fe fe-plus-circle me-2"></i> Submit Request
        </button>
    </div>
</div>

<div class="row row-sm">
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="myrequests-datatable" class="table table-striped table-bordered text-nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Ticket No.</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Submitted</th>
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

{{-- ======================= SUBMIT REQUEST MODAL ======================= --}}
<div class="modal fade" id="submitRequestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fe fe-tool me-2"></i>Submit Maintenance Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="submitRequestForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-control" name="category" required>
                                <option value="" disabled selected>Select category…</option>
                                <option>Plumbing</option>
                                <option>Electrical</option>
                                <option>Cleaning</option>
                                <option>Internet</option>
                                <option>Structural</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Priority <span class="text-danger">*</span></label>
                            <select class="form-control" name="priority" required>
                                <option value="" disabled selected>Select priority…</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="description" rows="4"
                                placeholder="Describe the issue in detail…" required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Attachments <span class="text-muted">(optional)</span></label>
                            <input type="file" id="attachmentInput" name="attachments[]" multiple accept="image/*,.pdf" class="d-none">

                            <div id="attachmentDropzone"
                                 style="border:2px dashed #d0d0e8;border-radius:8px;padding:14px;cursor:pointer;transition:border-color .2s;"
                                 onclick="document.getElementById('attachmentInput').click()">
                                <div id="attachmentPlaceholder" class="text-center text-muted py-2" style="font-size:.85rem;">
                                    <i class="fe fe-upload-cloud" style="font-size:1.8rem;display:block;margin-bottom:4px;opacity:.4;"></i>
                                    Click or drag files here &nbsp;·&nbsp; Max 5 files, 5 MB each
                                    <div style="font-size:.75rem;margin-top:2px;">JPG · PNG · PDF</div>
                                </div>
                                <div id="attachmentPreviewList" class="row g-3" style="display:none;"></div>
                                <div id="attachmentAddMore" class="text-center mt-2" style="display:none;">
                                    <span style="font-size:.78rem;color:#6259ca;"><i class="fe fe-plus-circle me-1"></i>Add more files</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnSubmitRequest">
                    <i class="fe fe-send me-1"></i> Submit Request
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ======================= VIEW REQUEST MODAL (with timeline + discussion) ======================= --}}
<div class="modal fade" id="viewRequestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fe fe-file-text me-2"></i>Request Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <table class="table table-borderless tx-13 mb-0">
                            <tr><th width="45%">Ticket No.</th><td id="req-ticket">—</td></tr>
                            <tr><th>Category</th><td id="req-category">—</td></tr>
                            <tr><th>Priority</th><td id="req-priority">—</td></tr>
                            <tr><th>Status</th><td id="req-status">—</td></tr>
                            <tr><th>Submitted</th><td id="req-submitted">—</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold tx-13">Description</label>
                        <p class="text-muted tx-13 border rounded p-2" id="req-description">—</p>
                    </div>
                    <div class="col-12" id="req-attachment-wrap" style="display:none;">
                        <hr class="mt-0 mb-2">
                        <p class="fw-semibold tx-13 mb-2">Attachments</p>
                        <div id="req-attachment"></div>
                    </div>
                </div>

                <hr>
                <h6 class="tx-13 fw-semibold mb-3">Status Timeline</h6>
                <div id="req-timeline" style="position:relative;padding-left:2rem;">
                    <div style="position:absolute;left:.65rem;top:.5rem;bottom:.5rem;width:2px;background:#e8e8f7;"></div>
                </div>

                {{-- C6: Discussion section (resident side) --}}
                <hr class="my-3">
                <h6 class="tx-13 fw-semibold mb-2"><i class="fe fe-message-circle me-1"></i>Discussion</h6>
                <div id="req-comments-list" style="max-height:220px;overflow-y:auto;background:#f8f8fd;border-radius:6px;padding:12px;margin-bottom:8px;">
                    <div class="text-center text-muted py-3" id="req-comments-placeholder">
                        <span class="spinner-border spinner-border-sm"></span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <textarea class="form-control form-control-sm" id="req-comment-input" rows="2"
                        placeholder="Add a note…" style="resize:none;"></textarea>
                    <button class="btn btn-sm btn-primary align-self-end px-3" id="btnSendReqComment">
                        <i class="fe fe-send"></i>
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                {{-- A4: Cancel Request button (only shown when status = open) --}}
                <button type="button" class="btn btn-outline-danger me-auto" id="btnCancelRequest" style="display:none;">
                    <i class="fe fe-x-circle me-1"></i> Cancel Request
                </button>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
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
    var csrfToken = $('#submitRequestForm input[name="_token"]').val();
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': csrfToken } });

    var storageBase    = "{{ asset('storage') }}/";
    var currentReqId   = null;
    var myUserId       = parseInt("{{ Auth::id() }}");

    // ── Badge helpers ──────────────────────────────────────────────────────
    function priorityBadge(priority) {
        var map = { low: 'badge bg-secondary', medium: 'badge bg-info', high: 'badge bg-warning', urgent: 'badge bg-danger' };
        var cls = map[priority] || 'badge bg-light text-dark';
        return '<span class="' + cls + '">' + (priority ? priority.charAt(0).toUpperCase() + priority.slice(1) : '-') + '</span>';
    }

    function statusLabel(s) {
        if (!s) return '-';
        return s.split('_').map(function (w) { return w.charAt(0).toUpperCase() + w.slice(1); }).join(' ');
    }

    function statusBadge(s) {
        var map = { open: 'badge bg-primary', assigned: 'badge bg-info', in_progress: 'badge bg-warning', completed: 'badge bg-success', closed: 'badge bg-secondary' };
        var cls = map[s] || 'badge bg-light text-dark';
        return '<span class="' + cls + '">' + statusLabel(s) + '</span>';
    }

    // ── DataTable ──────────────────────────────────────────────────────────
    var table = $('#myrequests-datatable').DataTable({
        ajax: {
            url: "{{ route('myRequests.list') }}",
            dataSrc: 'data'
        },
        language: { emptyTable: '<div class="text-center py-4"><i class="fe fe-tool" style="font-size:2.2rem;opacity:.3;display:block;margin-bottom:.5rem;"></i><p class="text-muted mb-2">No maintenance requests yet.</p><button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#submitRequestModal">Submit a Request</button></div>' },
        columns: [
            { data: null, orderable: false, render: function (d, t, r, meta) { return meta.row + 1; } },
            { data: 'ticket_no' },
            { data: 'category', defaultContent: '-', render: function (d) { return d || '-'; } },
            { data: 'description', defaultContent: '-', render: function (d) {
                if (!d) return '-';
                return d.length > 60 ? d.substring(0, 60) + '…' : d;
            } },
            { data: 'priority', render: function (d) { return priorityBadge(d); } },
            { data: 'status', render: function (d) { return statusBadge(d); } },
            { data: 'created_at', defaultContent: '-', render: function (d) { return d ? d.substring(0, 16) : '-'; } },
            {
                data: null, orderable: false, className: 'text-center',
                render: function (d, t, r) {
                    var payload = encodeURIComponent(JSON.stringify(r));
                    return '<button class="btn btn-sm btn-secondary btn-view-request" data-row="' + payload + '" title="View">' +
                               '<i class="fe fe-eye"></i></button>';
                }
            }
        ],
        columnDefs: [{ orderable: false, targets: 7 }]
    });

    // ── Attachment display helper ──────────────────────────────────────────
    function renderAttachments(raw, base, wrapId, containerId) {
        if (!raw) { $('#' + wrapId).hide(); return; }
        var paths = [];
        try { paths = JSON.parse(raw); } catch (e) { paths = [raw]; }
        if (!Array.isArray(paths)) paths = [paths];
        paths = paths.filter(Boolean);
        if (!paths.length) { $('#' + wrapId).hide(); return; }

        var cols = paths.map(function (p) {
            var fname = p.split('/').pop();
            var url   = base + p;
            var isImg = /\.(jpe?g|png|gif|webp)$/i.test(fname);
            var media = isImg
                ? '<img class="pic-1 pos-relative rounded-5" src="' + url + '" alt="' + fname + '" style="object-fit:cover;width:100%;height:100%;">' +
                  '<span class="image-pic" style="font-size:.65rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:90%;display:inline-block;">' + fname + '</span>'
                : '<div style="height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;background:#f4f3ff;padding:8px;gap:4px;">' +
                  '<i class="fe fe-file-text" style="font-size:2rem;color:#6259ca;"></i>' +
                  '<span style="font-size:.65rem;color:#555;text-align:center;word-break:break-all;">' + fname + '</span></div>';
            return '<div class="col-lg-3 col-md-4 col-6">' +
                '<div class="card custom-card mb-0" style="overflow:hidden;">' +
                  '<div class="card-body p-2">' +
                    '<div class="attached-file-grid6">' +
                      '<div class="pro-img-box attached-file-image">' +
                        '<a href="' + url + '" target="_blank">' + media + '</a>' +
                        '<ul class="icons">' +
                          '<li class="me-1"><a href="' + url + '" download title="Download"><i class="fe fe-download"></i></a></li>' +
                          '<li class="me-1"><a href="' + url + '" target="_blank" title="Open"><i class="fe fe-share"></i></a></li>' +
                        '</ul>' +
                      '</div>' +
                    '</div>' +
                  '</div>' +
                '</div>' +
              '</div>';
        });

        $('#' + containerId).html('<div class="row g-2">' + cols.join('') + '</div>');
        $('#' + wrapId).show();
    }

    // ── Timeline ──────────────────────────────────────────────────────────
    var stages = [
        { key: 'open',        label: 'Open',        icon: 'fe-file-plus',    desc: 'Ticket submitted by resident.' },
        { key: 'assigned',    label: 'Assigned',     icon: 'fe-user-check',   desc: 'Assigned to maintenance team.' },
        { key: 'in_progress', label: 'In Progress',  icon: 'fe-settings',     desc: 'Work is currently underway.' },
        { key: 'completed',   label: 'Completed',    icon: 'fe-check-circle', desc: 'Issue resolved.' },
        { key: 'closed',      label: 'Closed',       icon: 'fe-archive',      desc: 'Ticket closed.' },
    ];

    function renderTimeline(currentStage) {
        var currentIdx = stages.findIndex(function (s) { return s.key === currentStage; });
        var html = '';
        stages.forEach(function (s, i) {
            var done    = i < currentIdx;
            var active  = i === currentIdx;
            var pending = i > currentIdx;
            var circleStyle = done
                ? 'background:#19b159;border-color:#19b159;color:#fff;'
                : active ? 'background:#6259ca;border-color:#6259ca;color:#fff;'
                          : 'background:#fff;border-color:#dee2e6;color:#ccc;';
            var labelColor = pending ? 'color:#aaa;' : '';
            var descColor  = pending ? 'color:#ccc;' : 'color:#888;';
            html += '<div style="position:relative;display:flex;align-items:flex-start;margin-bottom:1.1rem;">' +
                '<div style="width:28px;height:28px;border-radius:50%;border:2px solid;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-right:.9rem;' + circleStyle + '">' +
                '<i class="fe ' + (done ? 'fe-check' : s.icon) + '" style="font-size:.75rem;"></i></div>' +
                '<div><p style="margin:0;font-size:.82rem;font-weight:600;' + labelColor + '">' + s.label + '</p>' +
                '<p style="margin:0;font-size:.75rem;' + descColor + '">' + (active ? '<strong>Current stage</strong> — ' : '') + s.desc + '</p></div></div>';
        });
        $('#req-timeline').html(html);
    }

    // ── C6: comment rendering ─────────────────────────────────────────────
    function loadReqComments(ticketId) {
        var $list = $('#req-comments-list');
        $list.html('<div class="text-center text-muted py-3"><span class="spinner-border spinner-border-sm"></span></div>');

        $.get("{{ route('myRequests.comments') }}", { ticket_id: ticketId }, function (res) {
            renderReqComments(res.data || []);
        }).fail(function () {
            $list.html('<p class="text-center text-muted tx-13 mb-0">Failed to load notes.</p>');
        });
    }

    function renderReqComments(comments) {
        var $list = $('#req-comments-list');
        if (!comments.length) {
            $list.html('<p class="text-center text-muted tx-13 mb-0 py-2">No notes yet.</p>');
            return;
        }
        var html = '';
        comments.forEach(function (c) {
            var isOwn  = c.user_id == myUserId;
            var time   = c.created_at ? c.created_at.substring(0, 16) : '';
            var role   = c.user_role ? c.user_role.charAt(0).toUpperCase() + c.user_role.slice(1) : '';
            var bubble = isOwn ? 'background:#6259ca;color:#fff;' : 'background:#e8e7fb;color:#333;';
            html += '<div class="mb-2 d-flex ' + (isOwn ? 'justify-content-end' : 'justify-content-start') + '">' +
                '<div style="max-width:78%;">' +
                  '<div class="d-flex align-items-baseline gap-1 mb-1 ' + (isOwn ? 'justify-content-end' : '') + '">' +
                    '<span class="fw-semibold" style="font-size:.75rem;">' + (isOwn ? 'You' : c.user_name) + '</span>' +
                    (!isOwn && role ? '<span class="badge bg-secondary" style="font-size:.65rem;">' + role + '</span>' : '') +
                    '<span class="text-muted" style="font-size:.7rem;">' + time + '</span>' +
                  '</div>' +
                  '<div style="border-radius:8px;padding:6px 10px;font-size:.82rem;' + bubble + '">' + $('<div>').text(c.comment).html() + '</div>' +
                '</div>' +
              '</div>';
        });
        $list.html(html);
        $list.scrollTop($list[0].scrollHeight);
    }

    $('#btnSendReqComment').on('click', function () {
        var text = $.trim($('#req-comment-input').val());
        if (!text || !currentReqId) return;
        var $btn = $(this).prop('disabled', true);
        $.post("{{ route('myRequests.comment.save') }}", { ticket_id: currentReqId, comment: text }, function (res) {
            $btn.prop('disabled', false);
            if (res.status) {
                $('#req-comment-input').val('');
                loadReqComments(currentReqId);
            } else {
                swal('Error', res.message || 'Could not save note.', 'error');
            }
        }).fail(function () {
            $btn.prop('disabled', false);
            swal('Error', 'Could not reach the server.', 'error');
        });
    });

    $('#req-comment-input').on('keydown', function (e) {
        if (e.ctrlKey && e.key === 'Enter') $('#btnSendReqComment').trigger('click');
    });

    // ── View request (details + timeline + comments) ───────────────────────
    $('#myrequests-datatable').on('click', '.btn-view-request', function () {
        var r = JSON.parse(decodeURIComponent($(this).data('row')));
        currentReqId = r.id;

        $('#req-ticket').text(r.ticket_no || '-');
        $('#req-category').text(r.category || '-');
        $('#req-priority').html(priorityBadge(r.priority));
        $('#req-status').html(statusBadge(r.status));
        $('#req-submitted').text(r.created_at ? r.created_at.substring(0, 16) : '-');
        $('#req-description').text(r.description || '-');

        $('#req-attachment-wrap').hide();
        $('#req-attachment').empty();
        renderAttachments(r.attachment_path, storageBase, 'req-attachment-wrap', 'req-attachment');

        renderTimeline(r.status || 'open');

        // A4: show Cancel button only for open tickets
        $('#btnCancelRequest').toggle(r.status === 'open');

        loadReqComments(r.id);
        $('#viewRequestModal').modal('show');
    });

    // ── A4: Cancel Request ─────────────────────────────────────────────────
    $('#btnCancelRequest').on('click', function () {
        if (!currentReqId) return;
        swal({
            title: 'Cancel Request?',
            text: 'This will close your ticket and cannot be undone.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d9534f',
            confirmButtonText: 'Yes, cancel it',
            cancelButtonText: 'Keep open'
        }, function (confirmed) {
            if (!confirmed) return;
            $.post("{{ route('myRequests.save') }}", { action: 'update', id: currentReqId, status: 'closed' }, function (res) {
                $('#viewRequestModal').modal('hide');
                if (res.status) {
                    table.ajax.reload(null, false);
                    swal('Cancelled', res.message, 'success');
                } else {
                    swal('Failed', res.message, 'error');
                }
            }).fail(function () {
                swal('Error', 'Could not reach the server.', 'error');
            });
        });
    });

    // ── Multi-file picker ──────────────────────────────────────────────────
    var selectedFiles = [];

    function isImage(file) { return file.type.startsWith('image/'); }

    function renderPreviews() {
        var $list    = $('#attachmentPreviewList');
        var $ph      = $('#attachmentPlaceholder');
        var $addMore = $('#attachmentAddMore');
        $list.empty();

        if (!selectedFiles.length) {
            $list.hide(); $addMore.hide(); $ph.show(); return;
        }

        $ph.hide(); $list.show();
        $addMore.toggle(selectedFiles.length < 5);

        selectedFiles.forEach(function (file, idx) {
            var isImg  = isImage(file);
            var objUrl = isImg ? URL.createObjectURL(file) : null;
            var mediaSrc = isImg
                ? '<img class="pic-1 pos-relative rounded-5" src="' + objUrl + '" alt="' + file.name + '" style="object-fit:cover;width:100%;height:100%;">' +
                  '<span class="image-pic">' + file.name + '</span>'
                : '<div style="height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;background:#f1f1fb;gap:4px;">' +
                  '<i class="fe fe-file-text" style="font-size:2.2rem;color:#6259ca;"></i>' +
                  '<span style="font-size:.7rem;color:#555;text-align:center;padding:0 6px;word-break:break-all;">' + file.name + '</span></div>';

            var $col = $('<div class="col-xl-4 col-sm-6"></div>');
            $col.html(
                '<div class="card custom-card mb-0">' +
                  '<div class="card-body p-2">' +
                    '<div class="h-100 attached-file-grid6">' +
                      '<div class="pro-img-box attached-file-image" style="min-height:120px;">' +
                        '<a href="#">' + mediaSrc + '</a>' +
                        '<ul class="icons">' +
                          '<li class="me-1"><a href="#" class="btn-preview-file" data-idx="' + idx + '" title="Preview"><i class="fe fe-download"></i></a></li>' +
                          '<li class="me-1"><a href="#" class="btn-remove-file" data-idx="' + idx + '" title="Remove"><i class="fe fe-trash"></i></a></li>' +
                        '</ul>' +
                      '</div>' +
                    '</div>' +
                  '</div>' +
                '</div>'
            );
            $list.append($col);
        });
    }

    $('#attachmentDropzone').on('dragover', function (e) {
        e.preventDefault();
        $(this).css('border-color', '#6259ca');
    }).on('dragleave drop', function (e) {
        e.preventDefault();
        $(this).css('border-color', '#d0d0e8');
        if (e.type === 'drop') addFiles(e.originalEvent.dataTransfer.files);
    });

    $('#attachmentInput').on('change', function () {
        addFiles(this.files);
        this.value = '';
    });

    function addFiles(fileList) {
        var remaining = 5 - selectedFiles.length;
        if (remaining <= 0) { swal('Limit reached', 'Maximum 5 files allowed.', 'warning'); return; }

        var added = 0, skipped = [];
        Array.from(fileList).forEach(function (f) {
            if (added >= remaining) { skipped.push(f.name); return; }
            if (f.size > 5 * 1024 * 1024) { skipped.push(f.name + ' (exceeds 5 MB)'); return; }
            if (!['image/jpeg', 'image/png', 'application/pdf'].includes(f.type)) { skipped.push(f.name + ' (unsupported type)'); return; }
            selectedFiles.push(f); added++;
        });
        if (skipped.length) swal('Some files skipped', skipped.join('\n'), 'warning');
        renderPreviews();
    }

    $(document).on('click', '.btn-remove-file', function (e) {
        e.preventDefault(); e.stopPropagation();
        selectedFiles.splice(parseInt($(this).data('idx')), 1);
        renderPreviews();
    });
    $(document).on('click', '.btn-preview-file', function (e) {
        e.preventDefault(); e.stopPropagation();
        var file = selectedFiles[parseInt($(this).data('idx'))];
        if (file) window.open(URL.createObjectURL(file), '_blank');
    });

    $('#submitRequestModal').on('hidden.bs.modal', function () {
        selectedFiles = []; renderPreviews();
    });

    // ── Submit new request ─────────────────────────────────────────────────
    $('#btnSubmitRequest').on('click', function () {
        var $btn  = $(this);
        var $form = $('#submitRequestForm');
        if (!$form[0].checkValidity()) { $form[0].reportValidity(); return; }

        var formData = new FormData($form[0]);
        formData.delete('attachments[]');
        selectedFiles.forEach(function (f) { formData.append('attachments[]', f); });

        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Submitting…');

        $.ajax({
            url: "{{ route('myRequests.save') }}",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false
        }).done(function (res) {
            $btn.prop('disabled', false).html('<i class="fe fe-send me-1"></i> Submit Request');
            $('#submitRequestModal').modal('hide');
            if (res.status) {
                $form[0].reset(); selectedFiles = []; renderPreviews();
                table.ajax.reload(null, false);
                swal('Submitted!', res.message, 'success');
            } else {
                swal('Failed', res.message, 'error');
            }
        }).fail(function (xhr) {
            $btn.prop('disabled', false).html('<i class="fe fe-send me-1"></i> Submit Request');
            var msg = 'Could not reach the server.';
            if (xhr.status === 422 && xhr.responseJSON) {
                var errs = xhr.responseJSON.errors;
                msg = errs ? Object.values(errs).map(function (e) { return e[0]; }).join('\n') : (xhr.responseJSON.message || msg);
            }
            swal('Error', msg, 'error');
        });
    });
});
</script>
@endpush
