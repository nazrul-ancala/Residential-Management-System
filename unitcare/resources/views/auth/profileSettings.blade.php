@extends('master_page.master_page')
@section('page_title', 'Account Settings')

@push('styles')
<link href="../assets/plugins/sweet-alert/sweetalert.css" rel="stylesheet">
@endpush

@section('content')
<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Account Settings</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard_utama') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('profile.show') }}">My Profile</a></li>
            <li class="breadcrumb-item active" aria-current="page">Account Settings</li>
        </ol>
    </div>
</div>

<div class="row row-sm justify-content-center">
    <div class="col-lg-8 col-xl-6">
        <div class="card custom-card">
            <div class="card-header">
                <div class="card-title">Change Password</div>
            </div>
            <div class="card-body">

                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Current Password <span class="text-danger">*</span></label>
                        <input type="password" name="current_password"
                               class="form-control @error('current_password') is-invalid @enderror"
                               autocomplete="current-password">
                        @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">New Password <span class="text-danger">*</span></label>
                        <input type="password" name="new_password"
                               class="form-control @error('new_password') is-invalid @enderror"
                               autocomplete="new-password" minlength="8">
                        @error('new_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Minimum 8 characters.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Confirm New Password <span class="text-danger">*</span></label>
                        <input type="password" name="new_password_confirmation"
                               class="form-control" autocomplete="new-password">
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-icon-text">
                            <i class="fe fe-lock me-2"></i> Change Password
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
@if(session('success'))
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: '{{ session('success') }}',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        confirmButtonColor: '#6259ca',
    });
@endif
</script>
@endpush
