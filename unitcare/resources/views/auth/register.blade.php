<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">
    <meta name="description" content="Spruha - Admin Panel HTML Dashboard Template">

    <!-- Favicon -->
    <link rel="icon" href="../assets/img/brand/favicon.ico" type="image/x-icon" />

    <title>Register - UNitCAre</title>

    <link id="style" href="../assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="../assets/plugins/web-fonts/icons.css" rel="stylesheet" />
    <link href="../assets/plugins/web-fonts/font-awesome/font-awesome.min.css" rel="stylesheet">
    <link href="../assets/plugins/web-fonts/plugin.css" rel="stylesheet" />
    <link href="../assets/css/style.css" rel="stylesheet">

</head>

<body class="ltr main-body leftmenu error-1">

    <div id="global-loader">
        <img src="../assets/img/loader.svg" class="loader-img" alt="Loader">
    </div>

    <div class="page main-signin-wrapper">

        <div class="row signpages text-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="row row-sm">
                        <div class="col-lg-6 col-xl-5 d-none d-lg-block text-center bg-primary details">
                            <div class="mt-5 pt-4 p-2 pos-absolute">
                                <a href="{{ route('homepage') }}">
                                    <img src="../assets/img/brand/logo-light.png" class="d-lg-none header-brand-img text-start float-start mb-4 error-logo-light" alt="logo">
                                    <img src="../assets/img/brand/logo.png" class="d-lg-none header-brand-img text-start float-start mb-4 error-logo" alt="logo">
                                </a>
                                <div class="clearfix"></div>
                                <a href="{{ route('homepage') }}"><img src="../assets/img/brand/icon-light.png" class="ht-100 mb-0" alt="logo"></a>
                                <h5 class="mt-4 text-white">Create Your Account</h5>
                                <span class="tx-white-6 tx-13 mb-5 mt-xl-0">Register to submit and track your helpdesk tickets</span>
                            </div>
                        </div>
                        <div class="col-lg-6 col-xl-7 col-xs-12 col-sm-12 login_form">
                            <div class="main-container container-fluid">
                                <div class="row row-sm">
                                    <div class="card-body mt-2 mb-2">
                                        <a href="{{ route('homepage') }}"><img src="../assets/img/brand/logo.png" class="d-lg-none header-brand-img text-start float-start mb-4" alt="logo"></a>
                                        <div class="clearfix"></div>
                                        <form method="POST" action="{{ route('register.post') }}">
                                            @csrf
                                            <h5 class="text-start mb-2">Create an Account</h5>
                                            <p class="mb-4 text-muted tx-13 ms-0 text-start">Fill in your details to register</p>

                                            <div class="form-group text-start">
                                                <label>Name</label>
                                                <input class="form-control @error('name') is-invalid @enderror" placeholder="Enter your name" type="text" name="name" value="{{ old('name') }}" required>
                                                @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group text-start">
                                                <label>Email</label>
                                                <input class="form-control @error('email') is-invalid @enderror" placeholder="Enter your email" type="email" name="email" value="{{ old('email') }}" required>
                                                @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group text-start">
                                                <label>Password</label>
                                                <input class="form-control @error('password') is-invalid @enderror" placeholder="Create a password (min 8 chars)" type="password" name="password" required>
                                                @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group text-start">
                                                <label>Confirm Password</label>
                                                <input class="form-control" placeholder="Repeat your password" type="password" name="password_confirmation" required>
                                            </div>

                                            <button type="submit" class="btn ripple btn-main-primary btn-block">Create Account</button>
                                        </form>
                                        <div class="text-start mt-5 ms-0">
                                            <div>Already have an account? <a href="{{ route('login') }}">Sign In</a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="../assets/plugins/jquery/jquery.min.js"></script>
    <script src="../assets/plugins/bootstrap/js/popper.min.js"></script>
    <script src="../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="../assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="../assets/js/themeColors.js"></script>
    <script src="../assets/js/custom.js"></script>

</body>

</html>