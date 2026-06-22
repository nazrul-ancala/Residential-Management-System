@extends('master_page.master_page')
@section('page_title', 'My Tasks')

@push('styles')
<link href="../assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="../assets/plugins/sweet-alert/sweetalert.css" rel="stylesheet">
<style>
/* Discussion thread */
.chat-box { max-height: 320px; overflow-y: auto; display: flex; flex-direction: column; gap: 8px; padding: 4px 2px; }
.chat-msg { display: flex; flex-direction: column; max-width: 75%; }
.chat-msg.mine { align-self: flex-end; align-items: flex-end; }
.chat-msg.theirs { align-self: flex-start; align-items: flex-start; }
.chat-bubble { padding: 8px 12px; border-radius: 12px; font-size: .85rem; word-break: break-word; }
.chat-msg.mine   .chat-bubble { background: #6d28d9; color: #fff; border-bottom-right-radius: 2px; }
.chat-msg.theirs .chat-bubble { background: #ede9fe; color: #3b0764; border-bottom-left-radius: 2px; }
.chat-meta { font-size: .72rem; color: #9ca3af; margin-top: 2px; }
.chat-empty { text-align: center; color: #9ca3af; font-size: .85rem; padding: 24px 0; }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">My Tasks</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard_utama') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">My Tasks</li>
        </ol>
    </div>
</div>

<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card custom-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tasks-datatable" class="table table-striped table-bordered text-nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Ticket No.</th>
                                <th>Resident</th>
                                <th>Unit</th>
                                <th>Category</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>{{-- Populated via DataTables --}}</tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ======================= VIEW TASK MODAL ======================= --}}
<div class="modal fade" id="viewTaskModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fe fe-tool me-2"></i>Task Details — <span id="task-modal-ticket-no"></span></h6>
                <button class="btn-close" data-bs-dismiss="modal" type="button"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    {{-- Left: ticket info + status update --}}
                    <div class="col-md-5">
                        <table class="table table-borderless table-sm mb-0">
                            <tr><th style="width:40%">Resident</th><td id="task-resident"></td></tr>
                            <tr><th>Unit</th><td id="task-unit"></td></tr>
                            <tr><th>Category</th><td id="task-category"></td></tr>
                            <tr><th>Priority</th><td id="task-priority"></td></tr>
                            <tr><th>Status</th><td id="task-status-badge"></td></tr>
                            <tr><th>Date</th><td id="task-date"></td></tr>
                        </table>
                        <hr>
                        <div class="mb-1">
                            <label class="form-label fw-semibold">Update Status</label>
                            <select id="task-status-select" class="form-select form-select-sm">
                                <option value="open">Open</option>
                                <option value="assigned">Assigned</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                        <button class="btn btn-sm btn-primary w-100 mt-2" id="btnUpdateStatus">
                            <i class="fe fe-save me-1"></i> Save Status
                        </button>
                    </div>
                    {{-- Right: description + discussion --}}
                    <div class="col-md-7">
                        <label class="form-label fw-semibold">Description</label>
                        <p id="task-description" class="text-muted small mb-3" style="white-space:pre-wrap"></p>
                        <hr class="mt-0">
                        <label class="form-label fw-semibold">Discussion</label>
                        <div class="chat-box mb-2" id="task-chat-box">
                            <div class="chat-empty">No messages yet.</div>
                        </div>
                        <div class="input-group input-group-sm">
                            <textarea id="task-comment-input" class="form-control" rows="2"
                                placeholder="Type a message… (Ctrl+Enter to send)"></textarea>
                            <button class="btn btn-primary" id="btnSendTaskComment" type="button">
                                <i class="fe fe-send"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal" type="button">Close</button>
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
    var csrfToken = $('meta[name="csrf-token"]').attr('content') || '';
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': csrfToken } });

    var myUserId   = parseInt("{{ Auth::id() }}");
    var activeTaskId = null;

    // ── Helpers ────────────────────────────────────────────────────────────
    function priorityBadge(p) {
        var map = { high: 'danger', medium: 'warning text-dark', low: 'info text-dark' };
        return '<span class="badge bg-' + (map[p] || 'secondary') + '">' + (p ? p.charAt(0).toUpperCase() + p.slice(1) : '-') + '</span>';
    }

    function statusBadge(s) {
        var map = {
            open: 'secondary', assigned: 'primary',
            in_progress: 'warning text-dark', completed: 'success', closed: 'dark'
        };
        var label = s ? s.replace('_', ' ').replace(/\b\w/g, function(c){ return c.toUpperCase(); }) : '-';
        return '<span class="badge bg-' + (map[s] || 'secondary') + '">' + label + '</span>';
    }

    function formatDate(dt) {
        if (!dt) return '-';
        return new Date(dt).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    // ── DataTable ──────────────────────────────────────────────────────────
    var table = $('#tasks-datatable').DataTable({
        ajax: { url: "{{ route('myTasks.list') }}", dataSrc: 'data' },
        language: { emptyTable: 'No tasks assigned to you yet.' },
        order: [[7, 'desc']],
        columns: [
            { data: null, orderable: false, render: function (d, t, r, meta) { return meta.row + 1; } },
            { data: 'ticket_no' },
            { data: 'resident_name', defaultContent: '-' },
            { data: null, render: function (d) {
                return (d.block_name || '-') + ' / ' + (d.unit_number || '-');
            }},
            { data: 'category' },
            { data: 'priority', render: function (d) { return priorityBadge(d); } },
            { data: 'status',   render: function (d) { return statusBadge(d); } },
            { data: 'created_at', render: function (d) { return formatDate(d); } },
            { data: null, orderable: false, className: 'text-center',
              render: function (d, t, r) {
                return '<button class="btn btn-sm btn-primary btn-view-task" data-id="' + r.id + '" title="View">' +
                       '<i class="fe fe-eye"></i></button>';
              }
            }
        ]
    });

    // ── Open modal ─────────────────────────────────────────────────────────
    var rowCache = {};

    table.on('draw', function () {
        table.rows().every(function () {
            var d = this.data();
            rowCache[d.id] = d;
        });
    });

    $('#tasks-datatable').on('click', '.btn-view-task', function () {
        var id = $(this).data('id');
        var r  = rowCache[id];
        if (!r) return;

        activeTaskId = id;
        $('#task-modal-ticket-no').text(r.ticket_no);
        $('#task-resident').text(r.resident_name || '-');
        $('#task-unit').text((r.block_name || '-') + ' / ' + (r.unit_number || '-'));
        $('#task-category').text(r.category || '-');
        $('#task-priority').html(priorityBadge(r.priority));
        $('#task-status-badge').html(statusBadge(r.status));
        $('#task-date').text(formatDate(r.created_at));
        $('#task-description').text(r.description || '-');
        $('#task-status-select').val(r.status || 'open');
        loadTaskComments(id);
        $('#viewTaskModal').modal('show');
    });

    // ── Update status ───────────────────────────────────────────────────────
    $('#btnUpdateStatus').on('click', function () {
        var $btn   = $(this);
        var newSt  = $('#task-status-select').val();

        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>');

        $.post("{{ route('myTasks.save') }}", { id: activeTaskId, status: newSt }, function (res) {
            $btn.prop('disabled', false).html('<i class="fe fe-save me-1"></i> Save Status');
            if (res.status) {
                $('#task-status-badge').html(statusBadge(newSt));
                if (rowCache[activeTaskId]) rowCache[activeTaskId].status = newSt;
                table.ajax.reload(null, false);
                swal('Updated!', 'Status changed to ' + newSt.replace('_',' ') + '.', 'success');
            } else {
                swal('Failed', res.message || 'Could not update status.', 'error');
            }
        }).fail(function () {
            $btn.prop('disabled', false).html('<i class="fe fe-save me-1"></i> Save Status');
            swal('Error', 'Could not reach the server.', 'error');
        });
    });

    // ── Comments ────────────────────────────────────────────────────────────
    function loadTaskComments(ticketId) {
        $('#task-chat-box').html('<div class="chat-empty">Loading…</div>');
        $.get("{{ route('myTasks.comments') }}", { ticket_id: ticketId }, function (res) {
            renderTaskComments(res.data || []);
        });
    }

    function renderTaskComments(comments) {
        var $box = $('#task-chat-box');
        if (!comments.length) {
            $box.html('<div class="chat-empty">No messages yet.</div>');
            return;
        }
        var html = '';
        $.each(comments, function (i, c) {
            var mine  = parseInt(c.user_id) === myUserId;
            var label = mine ? 'You' : (c.user_name || 'Staff');
            var ts    = c.created_at ? new Date(c.created_at).toLocaleString('en-GB', {
                day:'2-digit', month:'short', hour:'2-digit', minute:'2-digit'
            }) : '';
            html += '<div class="chat-msg ' + (mine ? 'mine' : 'theirs') + '">' +
                    '<div class="chat-bubble">' + $('<span>').text(c.comment).html() + '</div>' +
                    '<div class="chat-meta">' + label + (ts ? ' · ' + ts : '') + '</div>' +
                    '</div>';
        });
        $box.html(html);
        $box.scrollTop($box[0].scrollHeight);
    }

    function sendTaskComment() {
        var text = $('#task-comment-input').val().trim();
        if (!text || !activeTaskId) return;
        $('#task-comment-input').val('').prop('disabled', true);

        $.post("{{ route('myTasks.comment.save') }}", { ticket_id: activeTaskId, comment: text }, function (res) {
            $('#task-comment-input').prop('disabled', false).focus();
            if (res.status) { loadTaskComments(activeTaskId); }
            else { swal('Failed', res.message || 'Could not send message.', 'error'); }
        }).fail(function () {
            $('#task-comment-input').prop('disabled', false);
            swal('Error', 'Could not reach the server.', 'error');
        });
    }

    $('#btnSendTaskComment').on('click', sendTaskComment);

    $('#task-comment-input').on('keydown', function (e) {
        if (e.ctrlKey && e.key === 'Enter') { e.preventDefault(); sendTaskComment(); }
    });

    // Clear state on modal close
    $('#viewTaskModal').on('hidden.bs.modal', function () {
        activeTaskId = null;
        $('#task-chat-box').html('<div class="chat-empty">No messages yet.</div>');
        $('#task-comment-input').val('');
    });
});
</script>
@endpush
