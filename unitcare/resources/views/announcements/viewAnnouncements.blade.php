@extends('master_page.master_page')
@section('page_title', 'Announcements')

@section('content')

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Announcements</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard_utama') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Announcements</li>
        </ol>
    </div>
</div>

{{-- Type filter chips --}}
<div class="row row-sm mg-b-20">
    <div class="col-12">
        <div class="d-flex flex-wrap gap-2" id="typeFilters">
            <button class="btn btn-primary btn-sm filter-btn active" data-type="all">All <span class="filter-count ms-1"></span></button>
            <button class="btn btn-outline-secondary btn-sm filter-btn" data-type="general">General <span class="filter-count ms-1"></span></button>
            <button class="btn btn-outline-danger btn-sm filter-btn" data-type="emergency">Emergency <span class="filter-count ms-1"></span></button>
            <button class="btn btn-outline-info btn-sm filter-btn" data-type="event">Event <span class="filter-count ms-1"></span></button>
            <button class="btn btn-outline-warning btn-sm filter-btn" data-type="maintenance">Maintenance <span class="filter-count ms-1"></span></button>
        </div>
    </div>
</div>

{{-- Announcement cards --}}
<div class="row row-sm" id="announcementsContainer">
    <div class="col-12 text-center py-4" id="ann-loading">
        <span class="spinner-border text-primary"></span>
    </div>
</div>
<div class="row row-sm d-none" id="ann-empty">
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-body text-center py-5">
                <i class="fe fe-bell-off" style="font-size:3rem;color:#ccc;"></i>
                <p class="text-muted mt-3 mb-0">No announcements at this time.</p>
            </div>
        </div>
    </div>
</div>

{{-- Detail Modal --}}
<div class="modal fade" id="annDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ann-detail-title">—</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <img id="ann-detail-img" src="" alt="" class="img-fluid rounded mb-3 w-100" style="display:none;max-height:300px;object-fit:cover;">
                <div class="d-flex align-items-center mb-3">
                    <span class="badge me-2" id="ann-detail-type">—</span>
                    <small class="text-muted" id="ann-detail-date">—</small>
                </div>
                <p id="ann-detail-content" class="text-muted" style="white-space:pre-wrap;">—</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
var typeConfig = {
    general:     { cls: 'bg-primary',           label: 'General',     btnCls: 'btn-outline-secondary' },
    emergency:   { cls: 'bg-danger',            label: 'Emergency',   btnCls: 'btn-outline-danger'    },
    event:       { cls: 'bg-info',              label: 'Event',       btnCls: 'btn-outline-info'      },
    maintenance: { cls: 'bg-warning text-dark', label: 'Maintenance', btnCls: 'btn-outline-warning'   },
};

function buildCard(r) {
    var tc      = typeConfig[r.type] || { cls: 'bg-secondary', label: r.type };
    var dateStr = r.published_at ? r.published_at.slice(0, 10) : '';
    var preview = (r.content || '').length > 100 ? r.content.slice(0, 100) + '…' : r.content;

    var thumbHtml = '';
    if (r.image_path) {
        thumbHtml = '<img src="' + r.image_path + '" alt="" '
            + 'style="width:64px;height:64px;object-fit:cover;border-radius:8px;flex-shrink:0;margin-left:10px;">';
    }

    return '<div class="col-lg-4 col-md-6 mb-4 announcement-card" data-type="' + r.type + '">'
        + '<div class="card custom-card h-100" style="border-top:3px solid ' + typeAccent(r.type) + ';">'
        + '<div class="card-body d-flex flex-column">'
        + '<div class="d-flex align-items-start justify-content-between mb-2">'
        + '<span class="badge ' + tc.cls + '">' + tc.label + '</span>'
        + '<div class="d-flex align-items-center">'
        + '<small class="text-muted">' + dateStr + '</small>'
        + thumbHtml
        + '</div>'
        + '</div>'
        + '<h6 class="fw-semibold mb-2">' + $('<div>').text(r.title).html() + '</h6>'
        + '<p class="text-muted tx-13 mb-3 flex-grow-1">' + $('<div>').text(preview).html() + '</p>'
        + '<button class="btn btn-sm btn-outline-primary btn-read-more align-self-start" data-id="' + r.id + '">Read More</button>'
        + '</div></div></div>';
}

function typeAccent(type) {
    var map = { general: '#4a90d9', emergency: '#dc3545', event: '#17a2b8', maintenance: '#ffc107' };
    return map[type] || '#6259ca';
}

$(function () {
    var allAnn = [];

    $.get("{{ route('viewAnnouncements.list') }}", function (res) {
        allAnn = res.data || [];
        $('#ann-loading').remove();

        if (allAnn.length === 0) {
            $('#ann-empty').removeClass('d-none');
            return;
        }

        // Sort: emergency first, then by date desc
        allAnn.sort(function (a, b) {
            if (a.type === 'emergency' && b.type !== 'emergency') return -1;
            if (b.type === 'emergency' && a.type !== 'emergency') return  1;
            return (b.published_at || '').localeCompare(a.published_at || '');
        });

        // Build cards
        var html = '';
        allAnn.forEach(function (r) { html += buildCard(r); });
        $('#announcementsContainer').html(html);

        // Count badges
        var counts = { all: allAnn.length, general: 0, emergency: 0, event: 0, maintenance: 0 };
        allAnn.forEach(function (r) { if (counts[r.type] !== undefined) counts[r.type]++; });
        $('.filter-btn').each(function () {
            var t = $(this).data('type');
            var n = counts[t] !== undefined ? counts[t] : 0;
            $(this).find('.filter-count').text(n > 0 ? '(' + n + ')' : '');
        });
    }).fail(function () {
        $('#ann-loading').remove();
        $('#ann-empty').removeClass('d-none');
    });

    // Filter chips
    $(document).on('click', '.filter-btn', function () {
        $('.filter-btn').each(function () {
            var t  = $(this).data('type');
            var oc = t === 'all' ? '' : (typeConfig[t] ? typeConfig[t].btnCls : 'btn-outline-secondary');
            $(this).removeClass('active btn-primary btn-danger btn-info btn-warning btn-secondary btn-outline-secondary btn-outline-danger btn-outline-info btn-outline-warning');
            if (oc) $(this).addClass(oc);
        });

        $(this).removeClass('btn-outline-secondary btn-outline-danger btn-outline-info btn-outline-warning');
        $(this).addClass('active');

        var type = $(this).data('type');
        if (type === 'all') {
            $(this).addClass('btn-primary');
            $('.announcement-card').show();
        } else {
            var tc = typeConfig[type];
            if (tc) $(this).addClass(tc.cls.split(' ')[0].replace('bg-', 'btn-'));
            $('.announcement-card').hide();
            $('.announcement-card[data-type="' + type + '"]').show();
        }
    });

    // Read More
    $(document).on('click', '.btn-read-more', function () {
        var id = $(this).data('id');
        var r  = allAnn.find(function (x) { return x.id == id; });
        if (!r) return;
        var tc = typeConfig[r.type] || { cls: 'bg-secondary', label: r.type };
        $('#ann-detail-title').text(r.title);
        $('#ann-detail-type').attr('class', 'badge me-2 ' + tc.cls).text(tc.label);
        $('#ann-detail-date').text(r.published_at ? r.published_at.slice(0, 10) : '');
        $('#ann-detail-content').text(r.content);
        if (r.image_path) {
            $('#ann-detail-img').attr('src', r.image_path).show();
        } else {
            $('#ann-detail-img').hide().attr('src', '');
        }
        $('#annDetailModal').modal('show');
    });
});
</script>
@endpush
