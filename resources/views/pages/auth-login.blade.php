@extends('layouts.auth')

@section('title', 'Login')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/bootstrap-social/bootstrap-social.css') }}">

    <style>
        .was-validated .form-control:valid, 
        .form-control.is-valid {
            border-color: #ced4da !important; 
            background-image: none !important;
        }
    </style>
@endpush

@section('main')
    <div class="login-bg">
        <div class="card card-danger">
            <div class="card-header ">
                <h4>Login</h4>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('login.action') }}" class="needs-validation" novalidate="">
                    @csrf
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input id="username" type="text" class="form-control" name="username" tabindex="1" required autofocus>
                        <div class="invalid-feedback">Please fill in your Username</div>
                    </div>

                    <div class="form-group">
                        <div class="d-block">
                            <label for="password" class="control-label">Password</label>
                        </div>
                        <input id="password" type="password" class="form-control" name="password" tabindex="2" required>
                        <div class="invalid-feedback">please fill in your password</div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-danger btn-lg btn-block" tabindex="4">Login</button>
                    </div>
                </form>

                @if (session('alert.config'))
                    <script>
                        Swal.fire({!! session('alert.config') !!});
                    </script>
                @endif

            </div>
        </div>
    </div>
@endsection
