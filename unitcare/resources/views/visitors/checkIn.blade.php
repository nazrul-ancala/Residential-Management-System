@extends('master_page.master_page')
@section('page_title', 'Visitor Check-In / Out')

@push('styles')
<style>
.checkin-search-bar {
    border: 2px solid #e8e8f7;
    border-radius: 8px;
    transition: border-color 0.2s;
}
.checkin-search-bar:focus-within {
    border-color: #6259ca;
}
.checkin-search-bar input {
    border: none;
    box-shadow: none !important;
    font-size: 1rem;
    padding: 0.75rem 1rem;
}
.checkin-search-bar button {
    border-radius: 0 6px 6px 0;
    padding: 0 1.4rem;
}
.visitor-result-card .avatar-initials {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: #f0f0f8;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    font-weight: 700;
    color: #6259ca;
    flex-shrink: 0;
}
</style>
@endpush

@section('content')

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Visitor Check-In / Check-Out</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard_utama') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Check-In / Out</li>
        </ol>
    </div>
    <div class="d-flex">
        <a href="{{ route('todayVisitors') }}" class="btn btn-light btn-icon-text">
            <i class="fe fe-list me-2"></i> Today's Visitors
        </a>
    </div>
</div>

{{-- Search bar --}}
<div class="row row-sm mg-b-20">
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-body py-4">
                <p class="text-muted tx-13 mb-3 text-center">Enter the visitor's name, IC / Passport, or QR code to look them up.</p>
                <div class="d-flex checkin-search-bar mx-auto" style="max-width:600px;">
                    <input type="text" class="form-control" id="lookupInput"
                           placeholder="Visitor name, IC / Passport, or QR code…"
                           autocomplete="off">
                    <button class="btn btn-primary" id="lookupBtn">
                        <i class="fe fe-search me-1"></i> Search
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- QR scan (collapsed placeholder) --}}
<div class="row row-sm mg-b-20">
    <div class="col-12">
        <div class="accordion" id="qrAccordion">
            <div class="accordion-item border rounded" style="background:transparent;">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed bg-transparent tx-13 text-muted fw-semibold py-2 px-3"
                            type="button" data-bs-toggle="collapse" data-bs-target="#qrPanel">
                        <i class="fe fe-camera me-2"></i> Or scan QR code
                    </button>
                </h2>
                <div id="qrPanel" class="accordion-collapse collapse" data-bs-parent="#qrAccordion">
                    <div class="accordion-body text-center py-3">
                        <div id="qr-reader" style="width:100%;max-width:420px;margin:0 auto;"></div>
                        <p class="text-muted tx-12 mt-2 mb-0" id="qr-scan-status">Camera activates when panel opens.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Result cards --}}
<div id="resultsContainer"></div>

{{-- Not found --}}
<div class="row row-sm d-none" id="notFoundRow">
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-body text-center py-4 text-muted">
                <i class="fe fe-user-x d-block mb-2" style="font-size:2rem;opacity:.35;"></i>
                <p class="mb-0">No visitor found matching that search.</p>
                <p class="tx-12 mb-0">Try a different name, IC, or QR code.</p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
$(function () {
    function getInitials(name) {
        return (name || '').split(' ').slice(0, 2).map(function (w) { return w[0] ? w[0].toUpperCase() : ''; }).join('');
    }

    function statusBadge(s) {
        var map = {
            'pending':    '<span class="badge bg-secondary">Pending</span>',
            'checked_in': '<span class="badge bg-primary">Checked-In</span>',
            'completed':  '<span class="badge bg-success">Completed</span>',
            'cancelled':  '<span class="badge bg-dark">Cancelled</span>',
        };
        return map[s] || '<span class="badge bg-light text-dark">' + s + '</span>';
    }

    function actionButtons(visitor) {
        var btns = '';
        if (visitor.status === 'pending') {
            btns = '<button class="btn btn-primary w-100 btn-icon-text btn-checkin" data-id="' + visitor.id + '">'
                 + '<i class="fe fe-log-in me-2"></i> Check In</button>';
        } else if (visitor.status === 'checked_in') {
            btns = '<button class="btn btn-outline-secondary w-100 btn-icon-text btn-checkout" data-id="' + visitor.id + '">'
                 + '<i class="fe fe-log-out me-2"></i> Check Out</button>';
        } else {
            btns = '<span class="text-muted tx-13">No action available</span>';
        }
        return btns;
    }

    function buildCard(v) {
        return '<div class="row row-sm mb-3" id="visitor-card-' + v.id + '">'
            + '<div class="col-12">'
            + '<div class="card custom-card visitor-result-card">'
            + '<div class="card-body">'
            + '<div class="d-flex align-items-start gap-3">'
            + '<div class="avatar-initials">' + getInitials(v.name) + '</div>'
            + '<div class="flex-grow-1">'
            + '<div class="d-flex align-items-center justify-content-between flex-wrap gap-2">'
            + '<div><h5 class="mb-0 fw-semibold">' + v.name + '</h5><span class="text-muted tx-13">' + (v.ic_passport || '—') + '</span></div>'
            + '<div id="badge-' + v.id + '">' + statusBadge(v.status) + '</div>'
            + '</div>'
            + '<hr class="my-3">'
            + '<div class="row g-2 tx-13">'
            + '<div class="col-sm-4"><span class="text-muted">Host:</span> <span class="fw-semibold">' + (v.resident_name || '—') + '</span></div>'
            + '<div class="col-sm-4"><span class="text-muted">Unit:</span> <span class="fw-semibold">' + ((v.block_name || '') + (v.unit_number ? '-' + v.unit_number : '')) + '</span></div>'
            + '<div class="col-sm-4"><span class="text-muted">Visit:</span> <span class="fw-semibold">' + (v.visit_date || '') + ' ' + (v.visit_time || '') + '</span></div>'
            + '</div>'
            + '</div></div>'
            + '<div class="mt-3" id="action-' + v.id + '">' + actionButtons(v) + '</div>'
            + '</div></div></div></div>';
    }

    function doSearch() {
        var q = $('#lookupInput').val().trim();
        if (!q) return;

        $('#lookupBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.get("{{ route('visitor.search') }}", { q: q }, function (res) {
            var rows = res.data || [];
            $('#notFoundRow').addClass('d-none');
            $('#resultsContainer').empty();

            if (rows.length === 0) {
                $('#notFoundRow').removeClass('d-none');
            } else {
                rows.forEach(function (v) {
                    $('#resultsContainer').append(buildCard(v));
                });
            }
        }).always(function () {
            $('#lookupBtn').prop('disabled', false).html('<i class="fe fe-search me-1"></i> Search');
        });
    }

    $('#lookupBtn').on('click', doSearch);
    $('#lookupInput').on('keypress', function (e) { if (e.which === 13) doSearch(); });

    function processAction(action, id) {
        var $area = $('#action-' + id);
        $area.html('<span class="text-muted tx-13"><span class="spinner-border spinner-border-sm me-1"></span>Processing…</span>');

        $.post({
            url: "{{ route('visitor.checkin') }}",
            data: JSON.stringify({ action: action, id: id }),
            contentType: 'application/json',
            success: function (res) {
                if (res.status) {
                    var newStatus = (action === 'checkin') ? 'checked_in' : 'completed';
                    $('#badge-' + id).html(statusBadge(newStatus));
                    $area.html(actionButtons({ id: id, status: newStatus }));
                } else {
                    $area.html('<span class="text-danger tx-13">' + (res.message || 'Failed.') + '</span>');
                }
            },
            error: function () {
                $area.html('<span class="text-danger tx-13">Could not reach the server.</span>');
            }
        });
    }

    $(document).on('click', '.btn-checkin', function () {
        processAction('checkin', $(this).data('id'));
    });
    $(document).on('click', '.btn-checkout', function () {
        processAction('checkout', $(this).data('id'));
    });

    // QR Scanner
    var html5QrCode = null;

    $('#qrPanel').on('show.bs.collapse', function () {
        html5QrCode = new Html5Qrcode('qr-reader');
        html5QrCode.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: { width: 250, height: 250 } },
            function (decodedText) {
                html5QrCode.stop().then(function () {
                    html5QrCode = null;
                    $('#lookupInput').val(decodedText);
                    $('#qrPanel').collapse('hide');
                    doSearch();
                });
            }
        ).catch(function (err) {
            $('#qr-scan-status').text('Camera unavailable: ' + err);
        });
    });

    $('#qrPanel').on('hide.bs.collapse', function () {
        if (html5QrCode) {
            html5QrCode.stop().catch(function () {});
            html5QrCode = null;
        }
    });
});
</script>
@endpush
