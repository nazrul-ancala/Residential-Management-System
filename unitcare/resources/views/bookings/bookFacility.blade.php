@extends('master_page.master_page')
@section('page_title', 'Book a Facility')

@push('styles')
<style>
.slot-grid table { width: 100%; border-collapse: separate; border-spacing: 4px; }
.slot-grid th { font-size: .75rem; color: #888; font-weight: 600; text-align: center; padding: 4px 6px; }
.slot-grid td { text-align: center; padding: 0; }
.slot-cell {
    display: block;
    padding: 8px 4px;
    border: 1.5px solid #e8e8f7;
    border-radius: 6px;
    font-size: .78rem;
    cursor: pointer;
    transition: background .15s, border-color .15s;
    user-select: none;
    background: #fff;
}
.slot-cell.available:hover {
    border-color: #6259ca;
    background: #f0f0fc;
}
.slot-cell.selected {
    border-color: #6259ca !important;
    background: #6259ca !important;
    color: #fff !important;
}
.slot-cell.full {
    background: #f8f9fa;
    color: #bbb;
    cursor: not-allowed;
    border-color: #e8e8f7;
    text-decoration: line-through;
}
.slot-row-label {
    font-size: .75rem;
    font-weight: 600;
    color: #555;
    text-align: right;
    padding-right: 8px;
    white-space: nowrap;
}
</style>
@endpush

@section('content')

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Book a Facility</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard_utama') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Book Facility</li>
        </ol>
    </div>
    <div class="d-flex">
        <a href="{{ route('myBookings') }}" class="btn btn-light btn-icon-text">
            <i class="fe fe-calendar me-2"></i> My Bookings
        </a>
    </div>
</div>

<div class="row row-sm">
    {{-- Booking Form --}}
    <div class="col-lg-7">
        <div class="card custom-card">
            <div class="card-header">
                <h6 class="card-title mb-0"><i class="fe fe-calendar me-2 text-primary"></i>Booking Details</h6>
            </div>
            <div class="card-body">
                <form id="bookingForm">
                    @csrf
                    <input type="hidden" name="booking_date" id="hiddenDate">
                    <input type="hidden" name="start_time"   id="hiddenStart">
                    <input type="hidden" name="end_time"     id="hiddenEnd">

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Facility <span class="text-danger">*</span></label>
                            <select class="form-control" name="facility_id" id="facilitySelect" required>
                                <option value="" disabled selected>Select a facility…</option>
                                @foreach($facilities as $fac)
                                <option value="{{ $fac['id'] }}"
                                        data-desc="{{ $fac['description'] ?? '' }}"
                                        data-hours="{{ $fac['max_booking_hours'] ?? '' }}"
                                        data-capacity="{{ $fac['capacity'] ?? '' }}">
                                    {{ $fac['name'] }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Slot grid (shown after selecting facility) --}}
                        <div class="col-12 d-none" id="slotGridSection">
                            <label class="form-label">Select Date &amp; Time Slot <span class="text-danger">*</span></label>
                            <p class="text-muted tx-12 mb-2">
                                <i class="fe fe-info me-1"></i>
                                <span class="slot-cell available d-inline-block px-2 py-0 me-1" style="font-size:.7rem;border-radius:4px;">Open</span> = available &nbsp;
                                <span class="slot-cell full d-inline-block px-2 py-0 me-1" style="font-size:.7rem;border-radius:4px;">Full</span> = taken
                            </p>
                            <div id="slotLoadingSpinner" class="text-center py-3 d-none">
                                <span class="spinner-border spinner-border-sm text-primary"></span>
                                <span class="ms-2 text-muted tx-13">Checking availability…</span>
                            </div>
                            <div class="slot-grid border rounded p-3" id="slotGridWrap">
                                <table id="slotTable">
                                    <thead>
                                        <tr>
                                            <th style="width:80px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="slot-row-label">Morning<br><span style="font-weight:400;color:#aaa;">6AM–12PM</span></td>
                                        </tr>
                                        <tr>
                                            <td class="slot-row-label">Afternoon<br><span style="font-weight:400;color:#aaa;">12PM–6PM</span></td>
                                        </tr>
                                        <tr>
                                            <td class="slot-row-label">Evening<br><span style="font-weight:400;color:#aaa;">6PM–10PM</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p class="text-muted tx-12 mt-2 mb-0" id="selectedSlotLabel"></p>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Purpose <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="purpose" rows="2" maxlength="500"
                                      placeholder="Brief purpose of booking…" required></textarea>
                            <div class="text-end tx-12 text-muted mt-1">
                                <span id="purposeCharCount">0</span>/500
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. of Attendees</label>
                            <input type="number" class="form-control" name="attendees" min="1" placeholder="e.g. 10">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary w-100 btn-icon-text" id="submitBookingBtn">
                            <i class="fe fe-send me-2"></i> Submit Booking Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Facility Info --}}
    <div class="col-lg-5">
        <div class="card custom-card">
            <div class="card-header">
                <h6 class="card-title mb-0"><i class="fe fe-info me-2 text-info"></i>Facility Information</h6>
            </div>
            <div class="card-body">
                <p class="text-muted tx-13" id="facilityPrompt">Select a facility to view its details.</p>
                <div id="facilityDetails" class="d-none">
                    <table class="table table-borderless tx-13 mb-0">
                        <tr><th width="50%">Facility</th><td id="fac-name" class="fw-semibold">—</td></tr>
                        <tr><th>Description</th><td id="fac-desc">—</td></tr>
                        <tr><th>Max Booking Hours</th><td id="fac-hours">—</td></tr>
                        <tr><th>Max Capacity</th><td id="fac-capacity">—</td></tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="card custom-card">
            <div class="card-header">
                <h6 class="card-title mb-0"><i class="fe fe-alert-circle me-2 text-warning"></i>Booking Rules</h6>
            </div>
            <div class="card-body">
                <ul class="text-muted tx-13 ps-3 mb-0">
                    <li>Bookings are subject to admin approval.</li>
                    <li>Cancel at least 24 hours before the booking date.</li>
                    <li>Each resident may have max 2 active bookings.</li>
                    <li>No double-booking of the same time slot.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
var slotMap = [
    { key: 'morning',   start: '06:00:00', end: '12:00:00', label: 'Morning (6AM–12PM)'    },
    { key: 'afternoon', start: '12:00:00', end: '18:00:00', label: 'Afternoon (12PM–6PM)'  },
    { key: 'evening',   start: '18:00:00', end: '22:00:00', label: 'Evening (6PM–10PM)'    },
];

function slotOverlaps(bookingStart, bookingEnd, slotStart, slotEnd) {
    return bookingStart < slotEnd && bookingEnd > slotStart;
}

function dateStr(d) {
    // Local YYYY-MM-DD (avoids toISOString UTC off-by-one in +tz zones)
    var y = d.getFullYear();
    var m = ('0' + (d.getMonth() + 1)).slice(-2);
    var day = ('0' + d.getDate()).slice(-2);
    return y + '-' + m + '-' + day;
}

function getDays() {
    var today = new Date(), days = [];
    for (var i = 0; i < 5; i++) {
        var d = new Date(today);
        d.setDate(today.getDate() + i);
        days.push(d);
    }
    return days;
}

var bookedSlots = [];   // raw availability from API
var selectedDate = null, selectedStart = null, selectedEnd = null;

function buildGrid() {
    var days = getDays();

    // Header
    var headerRow = $('#slotTable thead tr');
    headerRow.find('th:not(:first)').remove();
    days.forEach(function (d) {
        var label = d.toLocaleDateString('en-MY', { weekday: 'short', day: 'numeric', month: 'short' });
        headerRow.append('<th>' + label + '</th>');
    });

    // Body
    $('#slotTable tbody tr').each(function (rowIdx) {
        var $row = $(this);              // capture row — `this` is NOT the row inside forEach
        var slot = slotMap[rowIdx];
        $row.find('td:not(:first)').remove();

        days.forEach(function (d) {
            var dStr = dateStr(d);
            var isTaken = bookedSlots.some(function (b) {
                return b.booking_date === dStr &&
                       slotOverlaps(b.start_time, b.end_time, slot.start, slot.end);
            });
            var isPast   = new Date(dStr + 'T' + slot.start) <= new Date();
            var blocked  = isTaken || isPast;

            var span = $('<span class="slot-cell ' + (blocked ? 'full' : 'available') + '">' +
                         (blocked ? '✕' : '✓') + '</span>');

            if (!blocked) {
                span.attr('data-date', dStr)
                    .attr('data-start', slot.start)
                    .attr('data-end', slot.end)
                    .attr('data-label', slot.label);
            } else if (isPast && !isTaken) {
                span.attr('title', 'This slot has already passed');
            }
            $row.append($('<td></td>').append(span));
        });
    });
}

function loadAvailability(facilityId) {
    var days  = getDays();
    var dfrom = dateStr(days[0]);
    var dto   = dateStr(days[days.length - 1]);

    $('#slotGridWrap').addClass('d-none');
    $('#slotLoadingSpinner').removeClass('d-none');

    $.ajax({
        url:      "{{ route('bookings.availability') }}",
        method:   'GET',
        data:     { facility_id: facilityId, date_from: dfrom, date_to: dto },
        dataType: 'json',
        complete: function (jqXHR) {
            var res = jqXHR.responseJSON;
            bookedSlots = (res && res.data) ? res.data : [];
            try {
                buildGrid();
            } catch (e) {
                console.error('buildGrid error:', e);
            }
            $('#slotLoadingSpinner').addClass('d-none');
            $('#slotGridWrap').removeClass('d-none');
        },
    });
}

$(function () {
    // Delegated click — works for all current and future slot cells
    $('#slotTable').on('click', '.slot-cell.available', function () {
        $('.slot-cell.selected').removeClass('selected').html('✓');
        $(this).addClass('selected').html('✓');
        selectedDate  = $(this).data('date');
        selectedStart = $(this).data('start');
        selectedEnd   = $(this).data('end');
        $('#hiddenDate').val(selectedDate);
        $('#hiddenStart').val(selectedStart);
        $('#hiddenEnd').val(selectedEnd);
        $('#selectedSlotLabel').html(
            '<i class="fe fe-check-circle me-1 text-success"></i>' +
            'Selected: <strong>' + $(this).data('label') + '</strong> on <strong>' + selectedDate + '</strong>'
        );
    });

    $('[name=purpose]').on('input', function () {
        $('#purposeCharCount').text($(this).val().length);
    });

    $('#facilitySelect').on('change', function () {
        var opt = $(this).find(':selected');
        var cap = opt.data('capacity');
        $('#fac-name').text(opt.text());
        $('#fac-desc').text(opt.data('desc') || '—');
        $('#fac-hours').text(opt.data('hours') ? opt.data('hours') + ' hour(s) max' : '—');
        $('#fac-capacity').text(cap ? cap + ' persons' : '—');
        // Set attendees max from capacity (if defined)
        if (cap) { $('[name=attendees]').attr('max', cap); }
        else     { $('[name=attendees]').removeAttr('max'); }
        $('#facilityDetails').removeClass('d-none');
        $('#facilityPrompt').addClass('d-none');

        $('#slotGridSection').removeClass('d-none');
        selectedDate = null; selectedStart = null; selectedEnd = null;
        $('#hiddenDate').val(''); $('#hiddenStart').val(''); $('#hiddenEnd').val('');
        $('#selectedSlotLabel').text('');

        loadAvailability($(this).val());
    });

    $('#bookingForm').on('submit', function (e) {
        e.preventDefault();

        if (!$('#facilitySelect').val()) {
            Swal.fire({ icon: 'warning', title: 'Select a facility', confirmButtonColor: '#6259ca' });
            return;
        }
        if (!selectedDate || !selectedStart) {
            Swal.fire({ icon: 'warning', title: 'Select a time slot', text: 'Please click a slot from the grid.', confirmButtonColor: '#6259ca' });
            return;
        }

        var purpose = $('[name=purpose]').val();
        if (!purpose || purpose.trim().length === 0) {
            Swal.fire({ icon: 'warning', title: 'Purpose required', text: 'Please enter a purpose for your booking.', confirmButtonColor: '#6259ca' });
            return;
        }
        if (purpose.length > 500) {
            Swal.fire({ icon: 'warning', title: 'Purpose too long', text: 'Please keep the purpose under 500 characters.', confirmButtonColor: '#6259ca' });
            return;
        }

        var cap       = parseInt($('#facilitySelect').find(':selected').data('capacity'), 10);
        var attendees = parseInt($('[name=attendees]').val(), 10);
        if (cap && attendees && attendees > cap) {
            Swal.fire({ icon: 'warning', title: 'Too many attendees', text: 'This facility holds a maximum of ' + cap + ' persons.', confirmButtonColor: '#6259ca' });
            return;
        }

        var $btn = $('#submitBookingBtn');
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Submitting…');

        $.post({
            url:         "{{ route('booking.store') }}",
            data:        JSON.stringify({
                facility_id:  $('#facilitySelect').val(),
                booking_date: selectedDate,
                start_time:   selectedStart,
                end_time:     selectedEnd,
                purpose:      purpose.trim(),
                attendees:    $('[name=attendees]').val() || null,
            }),
            contentType: 'application/json',
            success: function (res) {
                if (res.status) {
                    Swal.fire({
                        icon: 'success', title: 'Booking Submitted!',
                        text: res.message,
                        confirmButtonColor: '#6259ca',
                    }).then(function () {
                        window.location.href = "{{ route('myBookings') }}";
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Failed', text: res.message || 'Could not submit booking.', confirmButtonColor: '#6259ca' });
                }
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Server Error', text: 'Could not reach the server.', confirmButtonColor: '#6259ca' });
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="fe fe-send me-2"></i> Submit Booking Request');
            },
        });
    });
});
</script>
@endpush
