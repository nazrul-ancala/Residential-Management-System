@extends('master_page.master_page')
@section('page_title', 'Log Parcel')

@push('styles')
<style>
/* ── Resident lookup ─────────────────────────────── */
.resident-found-card {
    border: 2px solid #19b159;
    border-radius: 8px;
    background: #f0fff6;
    padding: .85rem 1rem;
    display: flex;
    align-items: center;
    gap: .75rem;
}
.resident-found-card .res-avatar {
    width: 42px; height: 42px;
    border-radius: 50%;
    background: #d4f1e2;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 1.1rem; color: #19b159;
    flex-shrink: 0;
}

/* ── Courier pill buttons ────────────────────────── */
.courier-btn-group { display: flex; flex-wrap: wrap; gap: .5rem; }
.courier-btn {
    padding: .42rem .9rem;
    border-radius: 50px;
    border: 2px solid #dee2e6;
    background: #fff;
    font-size: .82rem;
    font-weight: 600;
    cursor: pointer;
    transition: all .18s;
    white-space: nowrap;
    color: #555;
}
.courier-btn:hover  { border-color: #6259ca; color: #6259ca; }
.courier-btn.active { border-color: #6259ca; background: #6259ca; color: #fff; }

/* ── Parcel type tiles ───────────────────────────── */
.type-tile-group { display: grid; grid-template-columns: 1fr 1fr; gap: .6rem; }
.type-tile {
    border: 2px solid #dee2e6;
    border-radius: 10px;
    padding: .85rem .5rem;
    text-align: center;
    cursor: pointer;
    transition: all .18s;
    background: #fff;
    user-select: none;
}
.type-tile:hover  { border-color: #6259ca; }
.type-tile.active { border-color: #6259ca; background: #f0eeff; }
.type-tile .tile-icon  { font-size: 1.6rem; line-height: 1; display: block; margin-bottom: .3rem; }
.type-tile .tile-label { font-size: .78rem; font-weight: 600; color: #444; }
.type-tile.active .tile-label { color: #6259ca; }

/* ── Photo section ───────────────────────────────── */
.photo-thumb {
    max-height: 120px; border-radius: 8px;
    border: 1px solid #dee2e6; margin-top: .5rem;
}

/* ── Section heading ─────────────────────────────── */
.field-section-label {
    font-size: .7rem; font-weight: 700; letter-spacing: .12em;
    text-transform: uppercase; color: #888; margin-bottom: .6rem;
}
</style>
@endpush

@section('content')

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Log New Parcel</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard_utama') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Log Parcel</li>
        </ol>
    </div>
    <div class="d-flex">
        <a href="{{ route('parcels.pending') }}" class="btn btn-light btn-icon-text">
            <i class="fe fe-clock me-2"></i> Pending Pickups
        </a>
    </div>
</div>

<div class="row row-sm justify-content-center">
    <div class="col-lg-7">

        {{-- ══ SUCCESS CARD (hidden until submit) ══ --}}
        <div id="successCard" class="card custom-card d-none">
            <div class="card-body text-center py-5">
                <div class="mb-3" style="font-size:3rem;line-height:1;">✅</div>
                <h5 class="fw-bold mb-1">Parcel Logged</h5>
                <p class="text-muted tx-13 mb-1" id="sc-resident">—</p>
                <p class="text-muted tx-13 mb-4" id="sc-detail">—</p>
                <div class="d-flex justify-content-center gap-2">
                    <button class="btn btn-primary btn-icon-text" id="btnLogAnother">
                        <i class="fe fe-plus me-2"></i> Log Another Parcel
                    </button>
                    <a href="{{ route('parcels.pending') }}" class="btn btn-light btn-icon-text">
                        <i class="fe fe-list me-2"></i> View Pending
                    </a>
                </div>
            </div>
        </div>

        {{-- ══ LOG FORM ══ --}}
        <div id="logFormCard" class="card custom-card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fe fe-package me-2"></i>Parcel Details</h5>
            </div>
            <div class="card-body">
                <form id="logParcelForm" novalidate>
                    @csrf
                    <input type="hidden" id="residentId" name="resident_id">
                    <input type="hidden" id="courierVal" name="courier">
                    <input type="hidden" id="parcelTypeVal" name="parcel_type" value="small_box">

                    {{-- ── 1. RESIDENT LOOKUP ── --}}
                    <div class="mb-4">
                        <p class="field-section-label">1. Who is this parcel for?</p>

                        {{-- Step A: Block tap buttons --}}
                        <div id="blockSelectionStep">
                            <p class="tx-12 text-muted mb-2">Select block:</p>
                            <div class="courier-btn-group" id="blockBtns">
                                <span class="text-muted tx-12"><span class="spinner-border spinner-border-sm me-1"></span> Loading…</span>
                            </div>
                            <div class="mt-2">
                                <a href="javascript:void(0)" id="nameSearchToggle" class="text-muted tx-12">
                                    <i class="fe fe-search me-1"></i> Search by name instead
                                </a>
                                <input type="text" class="form-control form-control-sm mt-1" id="nameSearchInput"
                                       placeholder="Type resident name…" autocomplete="off" style="display:none;">
                                <div id="nameSearchResults" class="list-group mt-1" style="display:none;max-height:180px;overflow-y:auto;"></div>
                            </div>
                        </div>

                        {{-- Step B: Unit tap buttons (shown after block selected) --}}
                        <div id="unitSelectionStep" style="display:none;">
                            <div class="d-flex align-items-center mb-2">
                                <p class="tx-12 text-muted mb-0">
                                    Block <span id="selectedBlockLabel" class="fw-bold text-primary"></span> — select unit:
                                </p>
                                <a href="javascript:void(0)" id="btnChangeBlock" class="text-muted tx-12 ms-auto">
                                    <i class="fe fe-arrow-left me-1"></i> Change Block
                                </a>
                            </div>
                            <div class="courier-btn-group" id="unitBtns"></div>
                        </div>

                        {{-- Resident confirmed card --}}
                        <div id="residentResult" style="display:none;">
                            <div class="resident-found-card">
                                <div class="res-avatar" id="resAvatarInitials">?</div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold tx-14" id="resName">—</div>
                                    <div class="text-muted tx-12" id="resUnit">—</div>
                                    <div class="text-muted tx-12" id="resPhone">—</div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnChangeResident">Change</button>
                            </div>
                        </div>
                    </div>

                    {{-- ── 2. COURIER ── --}}
                    <div class="mb-4">
                        <p class="field-section-label">2. Courier</p>
                        <div class="courier-btn-group" id="courierBtns">
                            <button type="button" class="courier-btn" data-val="PosLaju">PosLaju</button>
                            <button type="button" class="courier-btn" data-val="J&T Express">J&T Express</button>
                            <button type="button" class="courier-btn" data-val="DHL">DHL</button>
                            <button type="button" class="courier-btn" data-val="Shopee Express">Shopee Express</button>
                            <button type="button" class="courier-btn" data-val="Lazada Logistics">Lazada</button>
                            <button type="button" class="courier-btn" data-val="GDex">GDex</button>
                            <button type="button" class="courier-btn" data-val="City-Link">City-Link</button>
                            <button type="button" class="courier-btn" data-val="__other__">Other…</button>
                        </div>
                        <input type="text" class="form-control form-control-sm mt-2" id="courierOtherInput"
                               placeholder="Enter courier name" maxlength="100" style="display:none;max-width:260px;">
                    </div>

                    {{-- ── 3. PARCEL TYPE ── --}}
                    <div class="mb-4">
                        <p class="field-section-label">3. Parcel Type</p>
                        <div class="type-tile-group" id="typeTiles">
                            <div class="type-tile" data-val="letter">
                                <span class="tile-icon">📄</span>
                                <span class="tile-label">Letter / Doc</span>
                            </div>
                            <div class="type-tile active" data-val="small_box">
                                <span class="tile-icon">📦</span>
                                <span class="tile-label">Small Box</span>
                            </div>
                            <div class="type-tile" data-val="large_box">
                                <span class="tile-icon">🗳️</span>
                                <span class="tile-label">Large Box</span>
                            </div>
                            <div class="type-tile" data-val="fragile">
                                <span class="tile-icon">⚠️</span>
                                <span class="tile-label">Fragile</span>
                            </div>
                        </div>
                    </div>

                    {{-- ── OPTIONAL: More Details ── --}}
                    <div class="mb-4">
                        <div class="accordion" id="moreDetailsAccordion">
                            <div class="accordion-item border rounded" style="background:transparent;">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed bg-transparent tx-13 fw-semibold py-2 px-3 text-muted"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#moreDetailsPanel">
                                        <i class="fe fe-plus-circle me-2"></i> Add tracking number, notes, or photo (optional)
                                    </button>
                                </h2>
                                <div id="moreDetailsPanel" class="accordion-collapse collapse">
                                    <div class="accordion-body pt-3">

                                        <div class="mb-3">
                                            <label class="form-label tx-13 fw-semibold">Tracking Number</label>
                                            <input type="text" class="form-control" name="tracking_no"
                                                   placeholder="e.g. EY123456789MY" maxlength="100">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label tx-13 fw-semibold">Notes</label>
                                            <textarea class="form-control" name="notes" rows="2"
                                                      placeholder="e.g. Slightly dented, left at counter…" maxlength="1000"></textarea>
                                        </div>

                                        {{-- Photo: Camera capture + file fallback --}}
                                        <div class="mb-2">
                                            <label class="form-label tx-13 fw-semibold">Photo</label>
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnOpenCamera">
                                                    <i class="fe fe-camera me-1"></i> Take Photo
                                                </button>
                                                <span class="text-muted tx-12">or</span>
                                                <label class="btn btn-outline-secondary btn-sm mb-0" style="cursor:pointer;">
                                                    <i class="fe fe-upload me-1"></i> Upload File
                                                    <input type="file" id="photoFileInput" accept="image/jpeg,image/png" style="display:none;">
                                                </label>
                                            </div>
                                            <div id="photoThumbWrap" style="display:none;" class="mt-2">
                                                <img id="photoThumb" src="" alt="Parcel photo" class="photo-thumb">
                                                <br>
                                                <a href="javascript:void(0)" id="btnRemovePhoto" class="text-danger tx-12 mt-1 d-inline-block">
                                                    <i class="fe fe-x me-1"></i> Remove
                                                </a>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-icon-text" id="submitBtn">
                            <i class="fe fe-save me-2"></i> Log Parcel
                        </button>
                        <a href="{{ route('parcels.pending') }}" class="btn btn-light">Cancel</a>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>

{{-- ══ CAMERA MODAL ══ --}}
<div class="modal fade" id="cameraModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title tx-14"><i class="fe fe-camera me-2"></i>Take Photo</h5>
                <button type="button" class="btn-close" id="btnCloseCamera"></button>
            </div>
            <div class="modal-body text-center p-2">
                <video id="camFeed" autoplay playsinline muted
                       style="width:100%;border-radius:6px;background:#000;max-height:260px;object-fit:cover;"></video>
                <canvas id="camCanvas" style="display:none;"></canvas>
                <p class="text-danger tx-12 mt-2 mb-0 d-none" id="camError">Camera unavailable. Please upload a file instead.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-primary" id="btnCapture">
                    <i class="fe fe-aperture me-1"></i> Capture
                </button>
                <button type="button" class="btn btn-light" id="btnCloseCameraAlt">Cancel</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(function () {

    /* ═══════════════════════════════════════════
       Resident data (loaded once, filtered locally)
    ════════════════════════════════════════════ */
    var allResidents     = [];
    var selectedResident = null;
    var selectedBlock    = null;

    function getInitials(name) {
        return (name || '').split(' ').slice(0, 2).map(function (w) { return w[0] ? w[0].toUpperCase() : ''; }).join('');
    }

    // ── Render block buttons ──────────────────────────
    function renderBlockBtns() {
        var blocks = [];
        allResidents.forEach(function (r) {
            if (r.block && blocks.indexOf(r.block) === -1) blocks.push(r.block);
        });
        blocks.sort();

        var $container = $('#blockBtns').empty();
        if (blocks.length === 0) {
            $container.html('<span class="text-muted tx-12">No residents found in system.</span>');
            return;
        }
        blocks.forEach(function (b) {
            $('<button type="button" class="courier-btn block-btn"></button>')
                .text(b)
                .attr('data-block', b)
                .appendTo($container);
        });
    }

    // ── On block tap → show unit buttons ─────────────
    $(document).on('click', '.block-btn', function () {
        selectedBlock = $(this).attr('data-block');
        $('#selectedBlockLabel').text(selectedBlock);

        var units = [];
        allResidents.forEach(function (r) {
            if (r.block === selectedBlock && r.unit_no && units.indexOf(r.unit_no) === -1) {
                units.push(r.unit_no);
            }
        });
        // Natural sort: numeric parts first
        units.sort(function (a, b) {
            return a.localeCompare(b, undefined, { numeric: true, sensitivity: 'base' });
        });

        var $unitContainer = $('#unitBtns').empty();
        units.forEach(function (u) {
            $('<button type="button" class="courier-btn unit-btn"></button>')
                .text(u)
                .attr('data-unit', u)
                .appendTo($unitContainer);
        });

        $('#blockSelectionStep').hide();
        $('#unitSelectionStep').show();
        $('#nameSearchInput').hide().val('');
        $('#nameSearchResults').hide();
    });

    // ── On unit tap → show resident card ─────────────
    $(document).on('click', '.unit-btn', function () {
        var unit  = $(this).attr('data-unit');
        var found = allResidents.filter(function (r) {
            return r.block === selectedBlock && r.unit_no === unit;
        });

        if (found.length > 0) {
            showResident(found[0]);
        }
    });

    $('#btnChangeBlock').on('click', function () {
        selectedBlock = null;
        $('#unitSelectionStep').hide();
        $('#blockSelectionStep').show();
    });

    // ── Show confirmed resident card ──────────────────
    function showResident(r) {
        selectedResident = r;
        $('#residentId').val(r.id);
        $('#resAvatarInitials').text(getInitials(r.name));
        $('#resName').text(r.name);
        $('#resUnit').text((r.block || '') + ', Unit ' + (r.unit_no || '?'));
        $('#resPhone').text(r.phone || '');
        $('#blockSelectionStep').hide();
        $('#unitSelectionStep').hide();
        $('#nameSearchInput').val('').hide();
        $('#nameSearchResults').hide();
        $('#residentResult').show();
    }

    // ── Change resident → back to block step ─────────
    function clearResident() {
        selectedResident = null;
        selectedBlock    = null;
        $('#residentId').val('');
        $('#residentResult').hide();
        $('#unitSelectionStep').hide();
        $('#blockBtns .courier-btn').removeClass('active');
        $('#blockSelectionStep').show();
        $('#nameSearchToggle').show();
    }

    $('#btnChangeResident').on('click', clearResident);

    // ── Name search fallback ──────────────────────────
    $('#nameSearchToggle').on('click', function () {
        $(this).hide();
        $('#nameSearchInput').show().focus();
    });

    $('#nameSearchInput').on('input', function () {
        var q = $(this).val().trim().toLowerCase();
        $('#nameSearchResults').empty().hide();
        if (q.length < 2) return;

        var matches = allResidents.filter(function (r) {
            return (r.name || '').toLowerCase().includes(q);
        }).slice(0, 8);

        if (matches.length === 0) {
            $('#nameSearchResults').show().append('<div class="list-group-item text-muted tx-12">No match found.</div>');
            return;
        }
        matches.forEach(function (r) {
            var item = $('<a href="javascript:void(0)" class="list-group-item list-group-item-action tx-13"></a>')
                .text(r.name + ' — ' + (r.block || '') + ', Unit ' + (r.unit_no || '?'))
                .on('click', function () {
                    showResident(r);
                    $('#nameSearchInput').val('').hide();
                    $('#nameSearchResults').hide();
                    $('#nameSearchToggle').show();
                });
            $('#nameSearchResults').append(item);
        });
        $('#nameSearchResults').show();
    });

    // ── Load residents & render blocks ────────────────
    $.get("{{ route('parcels.log.residents') }}", function (res) {
        allResidents = res.data || [];
        renderBlockBtns();
    });

    /* ═══════════════════════════════════════════
       Courier pill buttons
    ════════════════════════════════════════════ */
    $(document).on('click', '.courier-btn', function () {
        $('.courier-btn').removeClass('active');
        $(this).addClass('active');
        var val = $(this).data('val');
        if (val === '__other__') {
            $('#courierOtherInput').show().focus();
            $('#courierVal').val('');
        } else {
            $('#courierOtherInput').hide().val('');
            $('#courierVal').val(val);
        }
    });

    $('#courierOtherInput').on('input', function () {
        $('#courierVal').val($(this).val().trim());
    });

    /* ═══════════════════════════════════════════
       Parcel type tiles
    ════════════════════════════════════════════ */
    $(document).on('click', '.type-tile', function () {
        $('.type-tile').removeClass('active');
        $(this).addClass('active');
        $('#parcelTypeVal').val($(this).data('val'));
    });

    /* ═══════════════════════════════════════════
       Photo: camera capture
    ════════════════════════════════════════════ */
    var camStream = null;
    var photoBlob  = null;

    function stopCamera() {
        if (camStream) {
            camStream.getTracks().forEach(function (t) { t.stop(); });
            camStream = null;
        }
        $('#camFeed')[0].srcObject = null;
    }

    $('#btnOpenCamera').on('click', function () {
        $('#camError').addClass('d-none');
        $('#cameraModal').modal('show');
        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
            .then(function (stream) {
                camStream = stream;
                $('#camFeed')[0].srcObject = stream;
            })
            .catch(function () {
                $('#camError').removeClass('d-none');
                $('#btnCapture').prop('disabled', true);
            });
    });

    function closeCamera() {
        stopCamera();
        $('#cameraModal').modal('hide');
        $('#btnCapture').prop('disabled', false);
    }

    $('#btnCloseCamera, #btnCloseCameraAlt').on('click', closeCamera);

    $('#btnCapture').on('click', function () {
        var video  = $('#camFeed')[0];
        var canvas = $('#camCanvas')[0];
        canvas.width  = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        canvas.toBlob(function (blob) {
            photoBlob = blob;
            var url   = URL.createObjectURL(blob);
            $('#photoThumb').attr('src', url);
            $('#photoThumbWrap').show();
            closeCamera();
        }, 'image/jpeg', 0.85);
    });

    // File upload fallback
    $('#photoFileInput').on('change', function () {
        var file = this.files[0];
        if (!file) return;
        photoBlob = null;
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#photoThumb').attr('src', e.target.result);
            $('#photoThumbWrap').show();
        };
        reader.readAsDataURL(file);
    });

    $('#btnRemovePhoto').on('click', function () {
        photoBlob = null;
        $('#photoFileInput').val('');
        $('#photoThumb').attr('src', '');
        $('#photoThumbWrap').hide();
    });

    /* ═══════════════════════════════════════════
       Form submit
    ════════════════════════════════════════════ */
    $('#logParcelForm').on('submit', function (e) {
        e.preventDefault();

        if (!$('#residentId').val()) {
            Swal.fire({ icon: 'warning', title: 'Missing Resident', text: 'Please find and select a resident first.', confirmButtonColor: '#6259ca' });
            return;
        }
        if (!$('#courierVal').val()) {
            Swal.fire({ icon: 'warning', title: 'Missing Courier', text: 'Please select a courier.', confirmButtonColor: '#6259ca' });
            return;
        }

        var formData = new FormData(this);

        // Attach photo (blob from camera or file input)
        if (photoBlob) {
            formData.append('photo', photoBlob, 'parcel_photo.jpg');
        } else if ($('#photoFileInput')[0].files.length > 0) {
            formData.append('photo', $('#photoFileInput')[0].files[0]);
        }

        $('#submitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving…');

        $.ajax({
            url:         "{{ route('parcels.log.save') }}",
            type:        'POST',
            data:        formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.status) {
                    var r       = selectedResident || {};
                    var courier = $('#courierVal').val();
                    var type    = { letter: 'Letter/Doc', small_box: 'Small Box', large_box: 'Large Box', fragile: 'Fragile' }[$('#parcelTypeVal').val()] || '';

                    $('#sc-resident').text((r.name || '') + ' — Block ' + (r.block || '?') + ', Unit ' + (r.unit_no || '?'));
                    $('#sc-detail').text('Courier: ' + courier + '  ·  Type: ' + type);

                    $('#logFormCard').addClass('d-none');
                    $('#successCard').removeClass('d-none');
                } else {
                    Swal.fire({ icon: 'error', title: 'Failed', text: res.message, confirmButtonColor: '#6259ca' });
                }
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Server Error', text: 'Could not reach the server.', confirmButtonColor: '#6259ca' });
            },
            complete: function () {
                $('#submitBtn').prop('disabled', false).html('<i class="fe fe-save me-2"></i> Log Parcel');
            }
        });
    });

    /* ═══════════════════════════════════════════
       Log Another — reset form in-place
    ════════════════════════════════════════════ */
    $('#btnLogAnother').on('click', function () {
        // Reset resident
        clearResident();

        // Reset courier (only courier-btn that are NOT block-btn or unit-btn)
        $('#courierBtns .courier-btn').removeClass('active');
        $('#courierVal').val('');
        $('#courierOtherInput').hide().val('');

        // Reset parcel type to default
        $('.type-tile').removeClass('active');
        $('[data-val="small_box"]').addClass('active');
        $('#parcelTypeVal').val('small_box');

        // Reset optional fields
        $('input[name="tracking_no"]').val('');
        $('textarea[name="notes"]').val('');
        photoBlob = null;
        $('#photoFileInput').val('');
        $('#photoThumb').attr('src', '');
        $('#photoThumbWrap').hide();
        $('#moreDetailsPanel').removeClass('show');

        // Show form, hide success card
        $('#successCard').addClass('d-none');
        $('#logFormCard').removeClass('d-none');
    });

});
</script>
@endpush
