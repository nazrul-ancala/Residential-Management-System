@extends('master_page.master_page')
@section('page_title', 'Register Visitor')

@push('styles')
<style>
@media print {
    @page { margin: 1cm; }
    * { visibility: hidden !important; }
    #printable-pass { display: block !important; }
    #printable-pass,
    #printable-pass * { visibility: visible !important; }
    #printable-pass {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
    }
}
.wizard-steps {
    display: flex;
    align-items: center;
    margin-bottom: 2rem;
}
.wizard-step {
    display: flex;
    align-items: center;
    flex: 1;
}
.wizard-step:last-child { flex: 0; }
.step-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 2px solid #dee2e6;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: .85rem;
    background: #fff;
    color: #999;
    flex-shrink: 0;
    transition: all .25s;
}
.step-label {
    margin-left: .5rem;
    font-size: .8rem;
    color: #999;
    white-space: nowrap;
    transition: color .25s;
}
.step-line {
    flex: 1;
    height: 2px;
    background: #dee2e6;
    margin: 0 .75rem;
    transition: background .25s;
}
.wizard-step.active .step-circle {
    border-color: #6259ca;
    background: #6259ca;
    color: #fff;
}
.wizard-step.active .step-label { color: #6259ca; font-weight: 600; }
.wizard-step.done .step-circle {
    border-color: #19b159;
    background: #19b159;
    color: #fff;
}
.wizard-step.done .step-label { color: #19b159; }
.wizard-step.done + .step-line,
.wizard-step.active + .step-line { background: #6259ca; }

.review-row {
    display: flex;
    justify-content: space-between;
    padding: .45rem 0;
    border-bottom: 1px solid #f0f0f0;
    font-size: .875rem;
}
.review-row:last-child { border-bottom: none; }
.review-row .label { color: #888; }
.review-row .value { font-weight: 600; }
</style>
@endpush

@section('content')

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Register a Visitor</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard_utama') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Register Visitor</li>
        </ol>
    </div>
    <div class="d-flex">
        <a href="{{ route('myVisitors') }}" class="btn btn-light btn-icon-text">
            <i class="fe fe-list me-2"></i> My Visitors
        </a>
    </div>
</div>

<div class="row row-sm justify-content-center">
    <div class="col-lg-8">
        <div class="card custom-card">
            <div class="card-body">

                {{-- Step indicator --}}
                <div class="wizard-steps" id="wizardSteps">
                    <div class="wizard-step active" data-step="1">
                        <div class="step-circle">1</div>
                        <span class="step-label">Visitor Info</span>
                    </div>
                    <div class="step-line"></div>
                    <div class="wizard-step" data-step="2">
                        <div class="step-circle">2</div>
                        <span class="step-label">Visit Details</span>
                    </div>
                    <div class="step-line"></div>
                    <div class="wizard-step" data-step="3">
                        <div class="step-circle">3</div>
                        <span class="step-label">Review &amp; Pass</span>
                    </div>
                </div>

                <form id="visitorRegForm" novalidate>
                    @csrf

                    {{-- ── Step 1: Visitor Info ── --}}
                    <div class="wizard-panel" id="step1">
                        <h6 class="fw-semibold mb-4 tx-14">Step 1 — Visitor Details</h6>

                        @if(auth()->user()->role === 'admin')
                        <div class="row g-3 mb-1">
                            <div class="col-12">
                                <label class="form-label">Registering on behalf of <span class="text-danger">*</span></label>
                                <select class="form-control" id="f_resident" name="resident_id" required>
                                    <option value="">— Select Resident —</option>
                                </select>
                                <div class="form-text text-muted">Admin only: select the resident this visitor belongs to.</div>
                            </div>
                        </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Visitor Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="f_name" name="visitor_name"
                                       placeholder="e.g. Siti binti Harun" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">IC / Passport No. <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="f_ic" name="ic_passport"
                                       placeholder="e.g. 900101-01-1234" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Vehicle No. <span class="text-muted">(optional)</span></label>
                                <input type="text" class="form-control" id="f_vehicle" name="vehicle_number"
                                       placeholder="e.g. WXY 1234">
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-primary" id="toStep2">
                                Next: Visit Details <i class="fe fe-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    {{-- ── Step 2: Visit Details ── --}}
                    <div class="wizard-panel d-none" id="step2">
                        <h6 class="fw-semibold mb-4 tx-14">Step 2 — Visit Details</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Visit Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="f_date" name="visit_date" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Visit Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="f_time" name="visit_time" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Purpose of Visit <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="f_purpose" name="purpose" rows="3"
                                          placeholder="Brief description of the visit…" required></textarea>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-light" id="backToStep1">
                                <i class="fe fe-arrow-left me-1"></i> Back
                            </button>
                            <button type="button" class="btn btn-primary" id="toStep3">
                                Next: Review <i class="fe fe-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    {{-- ── Step 3: Review & Generate ── --}}
                    <div class="wizard-panel d-none" id="step3">
                        <h6 class="fw-semibold mb-4 tx-14">Step 3 — Review &amp; Generate Pass</h6>

                        <div class="border rounded p-3 mb-4" id="reviewSummary">
                            @if(auth()->user()->role === 'admin')
                            <div class="review-row"><span class="label">Resident</span><span class="value" id="rev-resident">—</span></div>
                            @endif
                            <div class="review-row"><span class="label">Visitor Name</span><span class="value" id="rev-name">—</span></div>
                            <div class="review-row"><span class="label">IC / Passport</span><span class="value" id="rev-ic">—</span></div>
                            <div class="review-row"><span class="label">Vehicle No.</span><span class="value" id="rev-vehicle">—</span></div>
                            <div class="review-row"><span class="label">Visit Date</span><span class="value" id="rev-date">—</span></div>
                            <div class="review-row"><span class="label">Visit Time</span><span class="value" id="rev-time">—</span></div>
                            <div class="review-row"><span class="label">Purpose</span><span class="value" id="rev-purpose">—</span></div>
                        </div>

                        {{-- QR result (shown after generate) --}}
                        <div id="qrResultArea" class="d-none text-center mb-4">
                            <div class="border rounded p-3 d-inline-block mb-3" style="background:#f8f9fa;">
                                <div id="qrCodeDisplay" style="display:inline-block;"></div>
                            </div>
                            <p class="fw-semibold mb-0" id="qr-name-display">—</p>
                            <p class="text-muted tx-12 mb-3" id="qr-datetime-display">—</p>
                            <p class="text-muted tx-12 mb-3">Share this pass code with your visitor.</p>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="printPass()">
                                <i class="fe fe-printer me-1"></i> Print Pass
                            </button>
                        </div>

                        <div id="step3Actions" class="d-flex justify-content-between mt-2">
                            <button type="button" class="btn btn-light" id="backToStep2">
                                <i class="fe fe-arrow-left me-1"></i> Back
                            </button>
                            <button type="submit" class="btn btn-primary btn-icon-text" id="generateBtn">
                                <i class="fe fe-check-circle me-2"></i> Generate Visitor Pass
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- Reminders sidebar --}}
    <div class="col-lg-4">
        <div class="card custom-card">
            <div class="card-header">
                <h6 class="card-title mb-0"><i class="fe fe-info me-2 text-info"></i>How It Works</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="d-flex mb-3">
                        <span class="avatar avatar-sm bg-primary-transparent text-primary rounded-circle me-3 flex-shrink-0 tx-12 fw-bold">1</span>
                        <div><strong class="tx-13">Fill visitor details</strong><p class="text-muted tx-12 mb-0">Enter the visitor's name and IC/passport number.</p></div>
                    </li>
                    <li class="d-flex mb-3">
                        <span class="avatar avatar-sm bg-primary-transparent text-primary rounded-circle me-3 flex-shrink-0 tx-12 fw-bold">2</span>
                        <div><strong class="tx-13">Set visit schedule</strong><p class="text-muted tx-12 mb-0">Choose the date, time, and purpose of visit.</p></div>
                    </li>
                    <li class="d-flex mb-3">
                        <span class="avatar avatar-sm bg-primary-transparent text-primary rounded-circle me-3 flex-shrink-0 tx-12 fw-bold">3</span>
                        <div><strong class="tx-13">Generate &amp; share pass</strong><p class="text-muted tx-12 mb-0">A QR pass code is created. Share it with your visitor.</p></div>
                    </li>
                    <li class="d-flex">
                        <span class="avatar avatar-sm bg-primary-transparent text-primary rounded-circle me-3 flex-shrink-0 tx-12 fw-bold">4</span>
                        <div><strong class="tx-13">Security scan at gate</strong><p class="text-muted tx-12 mb-0">Guard scans QR code upon your visitor's arrival.</p></div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card custom-card">
            <div class="card-header">
                <h6 class="card-title mb-0"><i class="fe fe-alert-circle me-2 text-warning"></i>Reminders</h6>
            </div>
            <div class="card-body">
                <ul class="text-muted tx-13 ps-3 mb-0">
                    <li>Register at least 30 minutes before arrival.</li>
                    <li>Each pass is valid for the selected date only.</li>
                    <li>Ensure the IC/passport number is accurate.</li>
                    <li>Cancel a pass from My Visitors if plans change.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- Hidden print-only pass card --}}
<div id="printable-pass" style="display:none;">
    <div style="font-family:sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:2rem;box-sizing:border-box;">
        <div style="width:100%;max-width:480px;border:2px solid #6259ca;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.12);">
            <div style="background:#6259ca;color:#fff;padding:1.4rem 1.8rem;text-align:center;">
                <div style="font-size:1.5rem;font-weight:800;letter-spacing:.08em;">UNITCARE</div>
                <div style="font-size:.8rem;letter-spacing:.18em;opacity:.85;margin-top:.2rem;">VISITOR ACCESS PASS</div>
            </div>
            <div style="background:#f8f8ff;padding:1.8rem 1.8rem 1rem;text-align:center;border-bottom:1px solid #e5e5f0;">
                <div id="pp-code" style="display:inline-block;border:6px solid #fff;box-shadow:0 2px 10px rgba(0,0,0,.1);border-radius:6px;"></div>
                <div id="pp-qrtext" style="font-family:monospace;font-size:.85rem;color:#6259ca;font-weight:700;margin-top:.6rem;letter-spacing:.05em;"></div>
            </div>
            <div style="padding:1.2rem 1.8rem;border-bottom:1px solid #eef0f8;">
                <div style="font-size:.65rem;letter-spacing:.12em;color:#999;text-transform:uppercase;margin-bottom:.35rem;">Visitor</div>
                <div id="pp-name" style="font-size:1.25rem;font-weight:700;color:#1a1a2e;"></div>
                <div id="pp-ic"   style="font-size:.85rem;color:#666;margin-top:.2rem;"></div>
            </div>
            <div style="display:flex;padding:1rem 1.8rem;border-bottom:1px solid #eef0f8;gap:1rem;">
                <div style="flex:1;">
                    <div style="font-size:.65rem;letter-spacing:.12em;color:#999;text-transform:uppercase;margin-bottom:.25rem;">Visit Date</div>
                    <div id="pp-date" style="font-weight:600;color:#1a1a2e;"></div>
                </div>
                <div style="flex:1;">
                    <div style="font-size:.65rem;letter-spacing:.12em;color:#999;text-transform:uppercase;margin-bottom:.25rem;">Visit Time</div>
                    <div id="pp-time" style="font-weight:600;color:#1a1a2e;"></div>
                </div>
            </div>
            <div style="padding:1rem 1.8rem;border-bottom:1px solid #eef0f8;">
                <div style="font-size:.65rem;letter-spacing:.12em;color:#999;text-transform:uppercase;margin-bottom:.25rem;">Purpose of Visit</div>
                <div id="pp-purpose" style="font-size:.875rem;color:#333;"></div>
            </div>
            <div style="background:#f5f5fb;padding:.8rem 1.8rem;text-align:center;font-size:.75rem;color:#888;">
                Valid for selected date only &nbsp;&middot;&nbsp; Present to security guard upon arrival
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
var currentQrCode = '';

function generateQR(elementId, text, size) {
    size = size || 160;
    var el = document.getElementById(elementId);
    el.innerHTML = '';
    if (text) {
        new QRCode(el, { text: text, width: size, height: size,
                         colorDark: '#000000', colorLight: '#ffffff' });
    }
}

function printPass() {
    document.getElementById('pp-name').textContent    = $('#f_name').val();
    document.getElementById('pp-ic').textContent      = $('#f_ic').val();
    document.getElementById('pp-date').textContent    = $('#f_date').val();
    document.getElementById('pp-time').textContent    = $('#f_time').val();
    document.getElementById('pp-purpose').textContent = $('#f_purpose').val();
    document.getElementById('pp-qrtext').textContent  = currentQrCode;
    generateQR('pp-code', currentQrCode, 200);
    var origTitle = document.title;
    document.title = 'Visitor Pass - ' + currentQrCode;
    window.onafterprint = function () { document.title = origTitle; window.onafterprint = null; };
    setTimeout(function () { window.print(); }, 150);
}

$(function () {
    var currentStep = 1;
    var isAdmin = @json(auth()->user()->role === 'admin');

    $('#f_date').attr('min', new Date().toISOString().slice(0, 10));

    // Admin: load resident list
    if (isAdmin) {
        $.get("{{ route('visitor.residents') }}", function (res) {
            var opts = '<option value="">— Select Resident —</option>';
            (res.data || []).forEach(function (r) {
                opts += '<option value="' + r.id + '">' + r.name + ' (' + r.block + '-' + r.unit_no + ')</option>';
            });
            $('#f_resident').html(opts);
        });
    }

    function goTo(step) {
        $('.wizard-panel').addClass('d-none');
        $('#step' + step).removeClass('d-none');
        currentStep = step;

        $('.wizard-step').each(function () {
            var s = parseInt($(this).data('step'));
            $(this).removeClass('active done');
            if (s < step) $(this).addClass('done');
            else if (s === step) $(this).addClass('active');
        });
        $('.wizard-step.done .step-circle').each(function () {
            if (!$(this).find('i').length) {
                $(this).html('<i class="fe fe-check"></i>');
            }
        });
    }

    function validateStep(s) {
        if (s === 1) {
            if (isAdmin && !$('#f_resident').val()) {
                return 'Please select a resident before continuing.';
            }
            if (!$('#f_name').val().trim() || !$('#f_ic').val().trim()) {
                return 'Please fill in the visitor name and IC/Passport number.';
            }
            return null;
        }
        if (s === 2) {
            if (!$('#f_date').val() || !$('#f_time').val() || !$('#f_purpose').val().trim()) {
                return 'Please fill in the visit date, time, and purpose.';
            }
            var today = new Date(); today.setHours(0, 0, 0, 0);
            var visitDate = new Date($('#f_date').val()); visitDate.setHours(0, 0, 0, 0);
            if (visitDate < today) {
                return 'Visit date cannot be in the past. Please select today or a future date.';
            }
            var visitDT = new Date($('#f_date').val() + 'T' + $('#f_time').val());
            var minDT   = new Date(Date.now() + 30 * 60000);
            if (visitDT < minDT) {
                return 'Visitors must be registered at least 30 minutes before the visit time.';
            }
            return null;
        }
        return null;
    }

    function populateReview() {
        if (isAdmin) {
            $('#rev-resident').text($('#f_resident option:selected').text());
        }
        $('#rev-name').text($('#f_name').val() || '—');
        $('#rev-ic').text($('#f_ic').val() || '—');
        $('#rev-vehicle').text($('#f_vehicle').val() || '(none)');
        $('#rev-date').text($('#f_date').val() || '—');
        $('#rev-time').text($('#f_time').val() || '—');
        $('#rev-purpose').text($('#f_purpose').val() || '—');
    }

    $('#toStep2').on('click', function () {
        var err = validateStep(1);
        if (err) { Swal.fire({ icon: 'warning', title: 'Check your input', text: err, confirmButtonColor: '#6259ca' }); return; }
        goTo(2);
    });

    $('#toStep3').on('click', function () {
        var err = validateStep(2);
        if (err) { Swal.fire({ icon: 'warning', title: 'Check your input', text: err, confirmButtonColor: '#6259ca' }); return; }
        populateReview();
        goTo(3);
    });

    $('#backToStep1').on('click', function () { goTo(1); });
    $('#backToStep2').on('click', function () { goTo(2); });

    $('#visitorRegForm').on('submit', function (e) {
        e.preventDefault();

        var $btn = $('#generateBtn');
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving…');

        var payload = {
            visitor_name:   $('#f_name').val(),
            ic_passport:    $('#f_ic').val(),
            vehicle_number: $('#f_vehicle').val(),
            visit_date:     $('#f_date').val(),
            visit_time:     $('#f_time').val(),
            purpose:        $('#f_purpose').val(),
        };
        if (isAdmin) { payload.resident_id = $('#f_resident').val(); }

        $.post({
            url:  "{{ route('visitor.store') }}",
            data: JSON.stringify(payload),
            contentType: 'application/json',
            success: function (res) {
                if (res.status) {
                    var qrCode = (res.data && res.data.qr_code) ? res.data.qr_code : '';
                    currentQrCode = qrCode;
                    generateQR('qrCodeDisplay', qrCode, 160);
                    $('#qr-name-display').text($('#f_name').val());
                    $('#qr-datetime-display').text($('#f_date').val() + ' at ' + $('#f_time').val());
                    $('#qrResultArea').removeClass('d-none');
                    $btn.html('<i class="fe fe-check me-2"></i> Pass Generated');
                    $('#backToStep2').hide();
                } else {
                    Swal.fire({ icon: 'error', title: 'Failed', text: res.message || 'An unknown error occurred.', confirmButtonColor: '#6259ca' });
                    $btn.prop('disabled', false).html('<i class="fe fe-check-circle me-2"></i> Generate Visitor Pass');
                }
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Server Error', text: 'Could not reach the server. Please try again.', confirmButtonColor: '#6259ca' });
                $btn.prop('disabled', false).html('<i class="fe fe-check-circle me-2"></i> Generate Visitor Pass');
            }
        });
    });
});
</script>
@endpush
