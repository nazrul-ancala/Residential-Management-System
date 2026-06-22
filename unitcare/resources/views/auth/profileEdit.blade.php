@extends('master_page.master_page')
@section('page_title', 'Edit Profile')

@push('styles')
<link href="../assets/plugins/sweet-alert/sweetalert.css" rel="stylesheet">
@endpush

@section('content')
<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Edit Profile</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard_utama') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('profile.show') }}">My Profile</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Profile</li>
        </ol>
    </div>
</div>

<div class="row row-sm justify-content-center">
    <div class="col-lg-8 col-xl-6">
        <div class="card custom-card">
            <div class="card-header">
                <div class="card-title">Profile Information</div>
            </div>
            <div class="card-body">

                @if($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', auth()->user()->name) }}" required maxlength="255">
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" class="form-control bg-light" value="{{ auth()->user()->email }}" readonly>
                        <small class="text-muted">Email cannot be changed. Contact admin if needed.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Phone</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                               value="{{ old('phone', auth()->user()->phone) }}" maxlength="20"
                               placeholder="e.g. 0123456789">
                        @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if(auth()->user()->role === 'resident')
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Block</label>
                        <input type="text" class="form-control bg-light" value="{{ auth()->user()->block ?: '—' }}" readonly>
                        <small class="text-muted">Managed by admin.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Unit No.</label>
                        <input type="text" class="form-control bg-light" value="{{ auth()->user()->unit_no ?: '—' }}" readonly>
                    </div>
                    @endif

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-icon-text">
                            <i class="fe fe-save me-2"></i> Save Changes
                        </button>
                        <a href="{{ route('profile.show') }}" class="btn btn-secondary btn-icon-text">
                            <i class="fe fe-x me-2"></i> Cancel
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="../assets/plugins/sweet-alert/sweetalert.min.js"></script>
<script>
var _flash = {!! json_encode(session('success')) !!};
if (_flash) {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: _flash,
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        confirmButtonColor: '#6259ca',
    });
}
</script>
@endpush
