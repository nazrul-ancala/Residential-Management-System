@extends('master_page.master_page')
@section('page_title', 'My Profile')

@push('styles')
<link href="../assets/plugins/sweet-alert/sweetalert.css" rel="stylesheet">
@endpush

@section('content')

@php
    $user     = auth()->user();
    $words    = explode(' ', trim($user->name));
    $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
@endphp

<!-- Page Header -->
<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Profile</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard_utama') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Profile</li>
        </ol>
    </div>
</div>

<!-- Tabbed Content -->
<div class="row row-sm">
    <div class="col-xl-12">
        <div class="card custom-card mg-b-20">
            <div class="card-body">
                <div class="panel panel-primary tabs-style-3">

                    <!-- Tab Nav -->
                    <div class="tab-menu-heading">
                        <div class="tabs-menu">
                            <ul class="nav panel-tabs">
                                <li>
                                    <a href="#about"
                                       class="{{ $activeTab === 'about' ? 'active' : '' }}"
                                       data-bs-toggle="tab">
                                        <i class="fe fe-user me-1"></i> About
                                    </a>
                                </li>
                                <li>
                                    <a href="#edit"
                                       class="{{ $activeTab === 'edit' ? 'active' : '' }}"
                                       data-bs-toggle="tab">
                                        <i class="fe fe-edit-2 me-1"></i> Edit Profile
                                    </a>
                                </li>
                                <li>
                                    <a href="#settings"
                                       class="{{ $activeTab === 'settings' ? 'active' : '' }}"
                                       data-bs-toggle="tab">
                                        <i class="fe fe-settings me-1"></i> Account Settings
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Tab Content -->
                    <div class="panel-body tabs-menu-body">
                        <div class="tab-content">

                            {{-- About --}}
                            <div class="tab-pane {{ $activeTab === 'about' ? 'active show' : '' }}" id="about">
                                <div class="card-body p-0 border rounded-10">

                                    <div class="p-4">
                                        <h4 class="tx-15 text-uppercase mb-3">Personal Information</h4>
                                        <div class="d-sm-flex flex-wrap">
                                            <div class="mg-sm-r-20 mg-b-10">
                                                <div class="main-profile-contact-list">
                                                    <div class="media">
                                                        <div class="media-icon bg-primary-transparent text-primary">
                                                            <i class="fe fe-mail"></i>
                                                        </div>
                                                        <div class="media-body">
                                                            <span>Email</span>
                                                            <div>{{ $user->email }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mg-sm-r-20 mg-b-10">
                                                <div class="main-profile-contact-list">
                                                    <div class="media">
                                                        <div class="media-icon bg-success-transparent text-success">
                                                            <i class="icon ion-md-phone-portrait"></i>
                                                        </div>
                                                        <div class="media-body">
                                                            <span>Phone</span>
                                                            <div>{{ $user->phone ?: '—' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mg-sm-r-20 mg-b-10">
                                                <div class="main-profile-contact-list">
                                                    <div class="media">
                                                        <div class="media-icon bg-info-transparent text-info">
                                                            <i class="fe fe-shield"></i>
                                                        </div>
                                                        <div class="media-body">
                                                            <span>Role</span>
                                                            <div>{{ ucfirst($user->role) }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mg-sm-r-20 mg-b-10">
                                                <div class="main-profile-contact-list">
                                                    <div class="media">
                                                        <div class="media-icon bg-warning-transparent text-warning">
                                                            <i class="fe fe-calendar"></i>
                                                        </div>
                                                        <div class="media-body">
                                                            <span>Member Since</span>
                                                            <div>{{ $user->created_at?->format('d M Y') ?? '—' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if($user->role === 'resident')
                                    <div class="border-top"></div>
                                    <div class="p-4">
                                        <label class="main-content-label tx-13 mg-b-20">Residence Details</label>
                                        <div class="d-sm-flex">
                                            <div class="mg-sm-r-20 mg-b-10">
                                                <div class="main-profile-contact-list">
                                                    <div class="media">
                                                        <div class="media-icon bg-primary-transparent text-primary">
                                                            <i class="fe fe-home"></i>
                                                        </div>
                                                        <div class="media-body">
                                                            <span>Block</span>
                                                            <div>{{ $user->block ?: '—' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mg-sm-r-20 mg-b-10">
                                                <div class="main-profile-contact-list">
                                                    <div class="media">
                                                        <div class="media-icon bg-danger-transparent text-danger">
                                                            <i class="fe fe-key"></i>
                                                        </div>
                                                        <div class="media-body">
                                                            <span>Unit No.</span>
                                                            <div>{{ $user->unit_no ?: '—' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="border-top"></div>
                                    <div class="p-4">
                                        <label class="main-content-label tx-13 mg-b-20">Quick Actions</label>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-icon-text">
                                                <i class="fe fe-edit-2 me-2"></i> Edit Profile
                                            </a>
                                            <a href="{{ route('profile.settings') }}" class="btn btn-secondary btn-icon-text">
                                                <i class="fe fe-lock me-2"></i> Change Password
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            {{-- Edit Profile --}}
                            <div class="tab-pane {{ $activeTab === 'edit' ? 'active show' : '' }}" id="edit">
                                <div class="card-body border">

                                    @if($errors->any() && $activeTab === 'edit')
                                    <div class="alert alert-danger mg-b-20">
                                        <ul class="mb-0">
                                            @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif

                                    <div class="mb-4 main-content-label">Personal Information</div>

                                    <form method="POST" action="{{ route('profile.update') }}">
                                        @csrf

                                        <div class="form-group">
                                            <div class="row row-sm">
                                                <div class="col-md-3">
                                                    <label class="form-label">Full Name</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input type="text" name="name"
                                                           class="form-control @error('name') is-invalid @enderror"
                                                           value="{{ old('name', $user->name) }}"
                                                           required maxlength="255" placeholder="Full name">
                                                    @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="row row-sm">
                                                <div class="col-md-3">
                                                    <label class="form-label">Email</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input type="email" class="form-control bg-light"
                                                           value="{{ $user->email }}" readonly>
                                                    <small class="text-muted">Email cannot be changed. Contact admin if needed.</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="row row-sm">
                                                <div class="col-md-3">
                                                    <label class="form-label">Phone</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input type="text" name="phone"
                                                           class="form-control @error('phone') is-invalid @enderror"
                                                           value="{{ old('phone', $user->phone) }}"
                                                           maxlength="20" placeholder="e.g. 0123456789">
                                                    @error('phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        @if($user->role === 'resident')
                                        <div class="mb-4 main-content-label">
                                            Residence Details <small class="text-muted fw-normal">(managed by admin)</small>
                                        </div>
                                        <div class="form-group">
                                            <div class="row row-sm">
                                                <div class="col-md-3"><label class="form-label">Block</label></div>
                                                <div class="col-md-9">
                                                    <input type="text" class="form-control bg-light"
                                                           value="{{ $user->block ?: '—' }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row row-sm">
                                                <div class="col-md-3"><label class="form-label">Unit No.</label></div>
                                                <div class="col-md-9">
                                                    <input type="text" class="form-control bg-light"
                                                           value="{{ $user->unit_no ?: '—' }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        <div class="form-group mb-0">
                                            <div class="row row-sm">
                                                <div class="col-md-3"></div>
                                                <div class="col-md-9 d-flex gap-2">
                                                    <button type="submit" class="btn btn-primary btn-icon-text">
                                                        <i class="fe fe-save me-2"></i> Save Changes
                                                    </button>
                                                    <a href="{{ route('profile.show') }}" class="btn btn-secondary btn-icon-text">
                                                        <i class="fe fe-x me-2"></i> Cancel
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                </div>
                            </div>

                            {{-- Account Settings --}}
                            <div class="tab-pane {{ $activeTab === 'settings' ? 'active show' : '' }}" id="settings">
                                <div class="card-body border">

                                    <div class="mb-4 main-content-label">Account</div>

                                    <div class="form-group">
                                        <div class="row row-sm">
                                            <div class="col-md-3"><label class="form-label">User Name</label></div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control bg-light"
                                                       value="{{ $user->name }}" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row row-sm">
                                            <div class="col-md-3"><label class="form-label">Email</label></div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control bg-light"
                                                       value="{{ $user->email }}" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-4 main-content-label">Security Settings</div>

                                    @if($errors->any() && $activeTab === 'settings')
                                    <div class="alert alert-danger mg-b-20">
                                        <ul class="mb-0">
                                            @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif

                                    <form method="POST" action="{{ route('profile.password') }}">
                                        @csrf

                                        <div class="form-group">
                                            <div class="row row-sm">
                                                <div class="col-md-3">
                                                    <label class="form-label">Current Password</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input type="password" name="current_password"
                                                           class="form-control @error('current_password') is-invalid @enderror"
                                                           autocomplete="current-password">
                                                    @error('current_password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="row row-sm">
                                                <div class="col-md-3">
                                                    <label class="form-label">New Password</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input type="password" name="new_password"
                                                           class="form-control @error('new_password') is-invalid @enderror"
                                                           autocomplete="new-password">
                                                    @error('new_password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="text-muted">Minimum 8 characters.</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="row row-sm">
                                                <div class="col-md-3">
                                                    <label class="form-label">Confirm Password</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input type="password" name="new_password_confirmation"
                                                           class="form-control" autocomplete="new-password">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group mb-0">
                                            <div class="row row-sm">
                                                <div class="col-md-3"></div>
                                                <div class="col-md-9">
                                                    <button type="submit" class="btn btn-primary btn-icon-text">
                                                        <i class="fe fe-lock me-2"></i> Change Password
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="../assets/plugins/sweet-alert/sweetalert.min.js"></script>
@if(session('success'))
<script>
Swal.fire({
    toast: true,
    position: 'top-end',
    icon: 'success',
    title: "{{ session('success') }}",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    confirmButtonColor: '#6259ca',
});
</script>
@endif
@endpush
