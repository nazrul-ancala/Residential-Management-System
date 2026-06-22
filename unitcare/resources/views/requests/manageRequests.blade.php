@extends('master_page.master_page')
@section('page_title', 'Maintenance Requests')

@push('styles')
<link href="../assets/plugins/datatable/css/dataTables.bootstrap5.css" rel="stylesheet">
<link href="../assets/plugins/sweet-alert/sweetalert.css" rel="stylesheet">
@endpush

@section('content')

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Maintenance Requests</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard_utama') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">All Requests</li>
        </ol>
    </div>
</div>

{{-- A1: Stat cards --}}
<div class="row row-sm mg-b-20">
    <div class="col-6 col-md-3">
        <div class="card custom-card text-center p-3">
            <p class="tx-12 text-muted mb-1">Total</p>
            <h3 class="mb-0 fw-bold" id="stat-total">—</h3>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card custom-card text-center p-3">
            <p class="tx-12 text-muted mb-1">Open</p>
            <h3 class="mb-0 fw-bold text-primary" id="stat-open">—</h3>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card custom-card text-center p-3">
            <p class="tx-12 text-muted mb-1">In Progress</p>
            <h3 class="mb-0 fw-bold text-warning" id="stat-inprogress">—</h3>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card custom-card text-center p-3">
            <p class="tx-12 text-muted mb-1">Completed</p>
            <h3 class="mb-0 fw-bold text-success" id="stat-completed">—</h3>
        </div>
    </div>
</div>

{{-- Filter tabs (A2: added Closed) --}}
<div class="row row-sm mg-b-0">
    <div class="col-12">
        <ul class="nav nav-tabs mg-b-0" id="requestTabs" role="tablist">
            <li class="nav-item"><a class="nav-link active" href="#" data-status="">All</a></li>
            <li class="nav-item"><a class="nav-link" href="#" data-status="open">Open</a></li>
            <li class="nav-item"><a class="nav-link" href="#" data-status="assigned">Assigned</a></li>
            <li class="nav-item"><a class="nav-link" href="#" data-status="in_progress">In Progress</a></li>
            <li class="nav-item"><a class="nav-link" href="#" data-status="completed">Completed</a></li>
            <li class="nav-item"><a class="nav-link" href="#" data-status="closed">Closed</a></li>
        </ul>
    </div>
</div>



<div class="row row-sm">
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-body">
                {{-- D: Extra filter row --}}
                <div class="d-flex justify-content-end gap-2 mb-3">
                    <select id="filter-category" class="form-select form-select-sm w-auto">
                        <option value="">All Categories</option>
                        <option>Plumbing</option>
                        <option>Electrical</option>
                        <option>Cleaning</option>
                        <option>Internet</option>
                        <option>Structural</option>
                    </select>
                    <select id="filter-priority" class="form-select form-select-sm w-auto">
                        <option value="">All Priorities</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                    <button id="btn-reset-filters" class="btn btn-sm btn-light">Clear</button>
                </div>

                <div class="table-responsive">
                    <table id="allrequests-datatable" class="table table-striped table-bordered text-nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Ticket No.</th>
                                <th>Resident</th>
                                <th>Unit</th>
                                <th>Category</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Date</th>
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

{{-- ======================= VIEW & UPDATE STATUS MODAL ======================= --}}
<div class="modal fade" id="viewAdminRequestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fe fe-file-text me-2"></i>Request Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    {{-- Left: ticket info --}}
                    <div class="col-md-6">
                        <table class="table table-borderless tx-13 mb-0">
                            <tr>
                                <th width="40%">Ticket No.</th>
                                <td id="adm-ticket">—</td>
                            </tr>
                            <tr>
                                <th>Resident</th>
                                <td id="adm-resident">—</td>
                            </tr>
                            <tr>
                                <th>Unit</th>
                                <td id="adm-unit">—</td>
                            </tr>
                            <tr>
                                <th>Category</th>
                                <td id="adm-category">—</td>
                            </tr>
                            <tr>
                                <th>Priority</th>
                                <td id="adm-priority">—</td>
                            </tr>
                            <tr>
                                <th>Current Status</th>
                                <td id="adm-status">—</td>
                            </tr>
                            <tr>
                                <th>Assigned To</th>
                                <td id="adm-assigned">—</td>
                            </tr>
                        </table>
                    </div>
                    {{-- Right: description + controls --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold tx-13">Description</label>
                        <p class="text-muted tx-13 border rounded p-2" id="adm-description">—</p>

                        <label class="form-label fw-semibold tx-13 mt-2">Update Status</label>
                        <select class="form-control" id="adm-status-select">
                            <option value="open">Open</option>
                            <option value="assigned">Assigned</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="closed">Closed</option>
                        </select>

                        {{-- B2: Assign To dropdown --}}
                        <label class="form-label fw-semibold tx-13 mt-2">Assign To</label>
                        <select class="form-control" id="adm-assign-select">
                            <option value="">— Unassigned —</option>
                        </select>
                    </div>

                    {{-- Attachments --}}
                    <div class="col-12" id="adm-attachment-wrap" style="display:none;">
                        <hr class="mt-0 mb-2">
                        <p class="fw-semibold tx-13 mb-2">Attachments</p>
                        <div id="adm-attachment"></div>
                    </div>
                </div>

                {{-- C6: Discussion section --}}
                <hr class="my-3">
                <h6 class="tx-13 fw-semibold mb-2"><i class="fe fe-message-circle me-1"></i>Discussion</h6>
                <div id="adm-comments-list" style="max-height:220px;overflow-y:auto;background:#f8f8fd;border-radius:6px;padding:12px;margin-bottom:8px;">
                    <div class="text-center text-muted py-3" id="adm-comments-placeholder">
                        <span class="spinner-border spinner-border-sm"></span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <textarea class="form-control form-control-sm" id="adm-comment-input" rows="2"
                        placeholder="Add a note…" style="resize:none;"></textarea>
                    <button class="btn btn-sm btn-primary align-self-end px-3" id="btnSendAdmComment">
                        <i class="fe fe-send"></i>
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnUpdateStatus">
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
    $(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        var storageBase = "{{ asset('storage') }}/";
        var currentRequestId = null;
        var myUserId = parseInt("{{ Auth::id() }}");

        // ── Badge helpers ──────────────────────────────────────────────────────
        function priorityBadge(priority) {
            var map = {
                low: 'badge bg-secondary',
                medium: 'badge bg-info',
                high: 'badge bg-warning',
                urgent: 'badge bg-danger'
            };
            var cls = map[priority] || 'badge bg-light text-dark';
            return '<span class="' + cls + '">' + (priority ? priority.charAt(0).toUpperCase() + priority.slice(1) : '-') + '</span>';
        }

        function statusLabel(s) {
            if (!s) return '-';
            return s.split('_').map(function(w) {
                return w.charAt(0).toUpperCase() + w.slice(1);
            }).join(' ');
        }

        function statusBadge(s) {
            var map = {
                open: 'badge bg-primary',
                assigned: 'badge bg-info',
                in_progress: 'badge bg-warning',
                completed: 'badge bg-success',
                closed: 'badge bg-secondary'
            };
            var cls = map[s] || 'badge bg-light text-dark';
            return '<span class="' + cls + '">' + statusLabel(s) + '</span>';
        }

        // ── Attachment display helper ──────────────────────────────────────────
        function renderAttachments(raw, base, wrapId, containerId) {
            if (!raw) {
                $('#' + wrapId).hide();
                return;
            }
            var paths = [];
            try {
                paths = JSON.parse(raw);
            } catch (e) {
                paths = [raw];
            }
            if (!Array.isArray(paths)) paths = [paths];
            paths = paths.filter(Boolean);
            if (!paths.length) {
                $('#' + wrapId).hide();
                return;
            }

            var cols = paths.map(function(p) {
                var fname = p.split('/').pop();
                var url = base + p;
                var isImg = /\.(jpe?g|png|gif|webp)$/i.test(fname);
                var media = isImg ?
                    '<img class="pic-1 pos-relative rounded-5" src="' + url + '" alt="' + fname + '" style="object-fit:cover;width:100%;height:100%;">' +
                    '<span class="image-pic" style="font-size:.65rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:90%;display:inline-block;">' + fname + '</span>' :
                    '<div style="height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;background:#f4f3ff;padding:8px;gap:4px;">' +
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

        // ── DataTable ──────────────────────────────────────────────────────────
        var table = $('#allrequests-datatable').DataTable({
            ajax: {
                url: "{{ route('allRequests.list') }}",
                dataSrc: 'data'
            },
            language: {
                emptyTable: 'No maintenance requests found.'
            },
            columns: [{
                    data: null,
                    orderable: false,
                    render: function(d, t, r, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: 'ticket_no'
                },
                {
                    data: 'resident_name',
                    defaultContent: '-',
                    render: function(d) {
                        return d || '-';
                    }
                },
                {
                    data: null,
                    render: function(d, t, r) {
                        if (!r.unit_number) return '-';
                        return (r.block_name ? r.block_name + ' / ' : '') + r.unit_number;
                    }
                },
                {
                    data: 'category',
                    defaultContent: '-',
                    render: function(d) {
                        return d || '-';
                    }
                },
                {
                    data: 'priority',
                    render: function(d) {
                        return priorityBadge(d);
                    }
                },
                {
                    data: 'status',
                    render: function(d, type) {
                        return type === 'display' ? statusBadge(d) : d;
                    }
                },
                // A3: Assigned To column
                {
                    data: 'assigned_to_name',
                    defaultContent: '—',
                    render: function(d) {
                        return d || '—';
                    }
                },
                {
                    data: 'created_at',
                    defaultContent: '-',
                    render: function(d) {
                        return d ? d.substring(0, 16) : '-';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    className: 'text-center',
                    render: function(d, t, r) {
                        var payload = encodeURIComponent(JSON.stringify(r));
                        return '<button class="btn btn-sm btn-secondary btn-view-request" data-row="' + payload + '" title="View / Update">' +
                            '<i class="fe fe-eye"></i></button>';
                    }
                }
            ],
            columnDefs: [{
                orderable: false,
                targets: [0, 9]
            }],
            // A1: compute stat cards once table data loads
            initComplete: function() {
                updateStats();
            }
        });

        // A1: stat card updater (re-run whenever table redraws)
        function updateStats() {
            var all = table.data().toArray();
            var open = 0,
                inprog = 0,
                done = 0;
            all.forEach(function(r) {
                var s = (r.status || '').toLowerCase();
                if (s === 'open') open++;
                if (s === 'in_progress') inprog++;
                if (s === 'completed') done++;
            });
            $('#stat-total').text(all.length);
            $('#stat-open').text(open);
            $('#stat-inprogress').text(inprog);
            $('#stat-completed').text(done);
        }
        table.on('draw', updateStats);

        // ── Status filter tabs ─────────────────────────────────────────────────
        $('#requestTabs').on('click', '.nav-link', function(e) {
            e.preventDefault();
            $('#requestTabs .nav-link').removeClass('active');
            $(this).addClass('active');
            var status = $(this).data('status');
            table.column(6).search(status ? '^' + status + '$' : '', true, false).draw();
        });

        // ── D: Extra column filters (category + priority) ──────────────────────
        $('#filter-category').on('change', function() {
            table.column(4).search($(this).val(), false, false).draw();
        });
        $('#filter-priority').on('change', function() {
            var val = $(this).val();
            table.column(5).search(val ? '^' + val + '$' : '', true, false).draw();
        });
        $('#btn-reset-filters').on('click', function() {
            $('#filter-category').val('');
            $('#filter-priority').val('');
            table.column(4).search('', false, false);
            table.column(5).search('', true, false).draw();
        });

        // ── B2: Populate Assign-To dropdown once on page load ──────────────────
        $.get("{{ route('allRequests.staff') }}", function(res) {
            var opts = '<option value="">— Unassigned —</option>';
            (res.data || []).forEach(function(s) {
                opts += '<option value="' + s.id + '">' + s.name + ' (' + s.role + ')</option>';
            });
            $('#adm-assign-select').html(opts);
        });

        // ── Open view / update modal ───────────────────────────────────────────
        $('#allrequests-datatable').on('click', '.btn-view-request', function() {
            var r = JSON.parse(decodeURIComponent($(this).data('row')));
            currentRequestId = r.id;

            $('#adm-ticket').text(r.ticket_no || '-');
            $('#adm-resident').text(r.resident_name || '-');
            $('#adm-unit').text(r.unit_number ? (r.block_name ? r.block_name + ' / ' : '') + r.unit_number : '-');
            $('#adm-category').text(r.category || '-');
            $('#adm-priority').html(priorityBadge(r.priority));
            $('#adm-status').html(statusBadge(r.status));
            // A3: show assigned_to_name in modal
            $('#adm-assigned').text(r.assigned_to_name || '—');
            $('#adm-description').text(r.description || '-');
            $('#adm-status-select').val(r.status || 'open');
            // B2: pre-select current assigned_to
            $('#adm-assign-select').val(r.assigned_to || '');

            $('#adm-attachment-wrap').hide();
            $('#adm-attachment').empty();
            renderAttachments(r.attachment_path, storageBase, 'adm-attachment-wrap', 'adm-attachment');

            // C6: load comments
            loadComments(r.id);

            $('#viewAdminRequestModal').modal('show');
        });

        // ── C6: comment loading & rendering ───────────────────────────────────
        function loadComments(ticketId) {
            var $list = $('#adm-comments-list');
            $list.html('<div class="text-center text-muted py-3"><span class="spinner-border spinner-border-sm"></span></div>');

            $.get("{{ route('allRequests.comments') }}", {
                ticket_id: ticketId
            }, function(res) {
                renderComments(res.data || []);
            }).fail(function() {
                $list.html('<p class="text-center text-muted tx-13 mb-0">Failed to load notes.</p>');
            });
        }

        function renderComments(comments) {
            var $list = $('#adm-comments-list');
            if (!comments.length) {
                $list.html('<p class="text-center text-muted tx-13 mb-0 py-2">No notes yet.</p>');
                return;
            }
            var html = '';
            comments.forEach(function(c) {
                var isOwn = c.user_id == myUserId;
                var time = c.created_at ? c.created_at.substring(0, 16) : '';
                var role = c.user_role ? c.user_role.charAt(0).toUpperCase() + c.user_role.slice(1) : '';
                var bubble = isOwn ?
                    'background:#6259ca;color:#fff;' :
                    'background:#e8e7fb;color:#333;';

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

        $('#btnSendAdmComment').on('click', function() {
            var text = $.trim($('#adm-comment-input').val());
            if (!text || !currentRequestId) return;

            var $btn = $(this).prop('disabled', true);
            $.post("{{ route('allRequests.comment.save') }}", {
                ticket_id: currentRequestId,
                comment: text
            }, function(res) {
                $btn.prop('disabled', false);
                if (res.status) {
                    $('#adm-comment-input').val('');
                    loadComments(currentRequestId);
                } else {
                    swal('Error', res.message || 'Could not save note.', 'error');
                }
            }).fail(function() {
                $btn.prop('disabled', false);
                swal('Error', 'Could not reach the server.', 'error');
            });
        });

        // Allow Ctrl+Enter to send comment
        $('#adm-comment-input').on('keydown', function(e) {
            if (e.ctrlKey && e.key === 'Enter') $('#btnSendAdmComment').trigger('click');
        });

        // ── Update status + assign ─────────────────────────────────────────────
        $('#btnUpdateStatus').on('click', function() {
            if (!currentRequestId) return;

            var newStatus = $('#adm-status-select').val();
            var newAssign = $('#adm-assign-select').val();

            // Auto-advance to 'assigned' if staff is selected but status is still 'open'
            if (newAssign && newStatus === 'open') newStatus = 'assigned';

            var $btn = $(this);
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Updating…');

            $.post("{{ route('allRequests.save') }}", {
                action: 'update',
                id: currentRequestId,
                status: newStatus,
                assigned_to: newAssign
            }, function(res) {
                $btn.prop('disabled', false).html('<i class="fe fe-save me-1"></i> Update');
                $('#viewAdminRequestModal').modal('hide');
                if (res.status) {
                    table.ajax.reload(null, false);
                    swal('Updated!', res.message, 'success');
                } else {
                    swal('Failed', res.message, 'error');
                }
            }).fail(function() {
                $btn.prop('disabled', false).html('<i class="fe fe-save me-1"></i> Update');
                swal('Error', 'Could not reach the server.', 'error');
            });
        });
    });
</script>
@endpush