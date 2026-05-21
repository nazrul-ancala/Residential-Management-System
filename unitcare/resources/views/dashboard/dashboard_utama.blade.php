@extends('master_page.master_page')

@section('page_title', 'Dashboard')

@section('content')
<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Dashboard</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Pages</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </div>
    <div class="d-flex">
        <div class="justify-content-center">
            <button type="button" class="btn btn-white btn-icon-text my-2 me-2">
                <i class="fe fe-download me-2"></i> Import
            </button>
            <button type="button" class="btn btn-white btn-icon-text my-2 me-2">
                <i class="fe fe-filter me-2"></i> Filter
            </button>
            <button type="button" class="btn btn-primary my-2 btn-icon-text">
                <i class="fe fe-download-cloud me-2"></i> Download Report
            </button>
        </div>
    </div>
</div>

<div class="row sidemenu-height">
    <div class="col-lg-12">
        <div class="card custom-card">
            <div class="card-body">
                Typing Some text here.....
            </div>
        </div>
    </div>
</div>
@endsection