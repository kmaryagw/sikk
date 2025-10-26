@extends('layouts.auth')

@section('title', 'Login')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/bootstrap-social/bootstrap-social.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        /* ==== RESET VALIDASI BAWAAN ==== */
        .was-validated .form-control:valid,
        .form-control.is-valid {
            border-color: #ced4da !important;
            background-image: none !important;
        }

        /* ==== CARD STYLE ==== */
        .card.card-danger {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .card.card-danger:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .card-header h4 {
            font-weight: 600;
            color: #dc3545;
            text-align: center;
        }

        /* ==== INPUT FIELD ==== */
        .form-group {
            position: relative;
        }

        .form-group .form-control {
            border-radius: 50px;
            padding-left: 2.5rem;
            padding-right: 2.5rem;
            transition: all 0.2s ease-in-out;
            height: 45px;
        }

        .form-group .form-control:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        /* ==== INPUT WRAPPER & ICON ==== */
        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        /* Input field */
        .input-wrapper .form-control {
            width: 100%;
            height: 48px; /* sedikit lebih tinggi agar proporsional dengan icon */
            line-height: 1.5;
            border-radius: 50px;
            padding: 0 50px; /* kanan kiri */
            padding-left: 48px; /* ruang khusus kiri untuk icon */
            box-sizing: border-box;
            transition: all 0.2s ease-in-out;
        }

        /* Style icon umum */
        .input-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%); /* center vertikal sempurna */
            color: #888;
            font-size: 1.1rem; /* sedikit lebih kecil agar seimbang */
            transition: color 0.3s, transform 0.2s;
            line-height: 1; /* hindari tambahan ruang vertikal */
        }

        /* Icon di kiri */
        .input-icon.left {
            left: 18px;
            pointer-events: none;
        }

        /* Icon di kanan */
        .input-icon.right {
            right: 18px;
            cursor: pointer;
        }

        /* Fokus input ubah warna icon kiri */
        .input-wrapper .form-control:focus ~ .input-icon.left {
            color: #dc3545;
        }

        /* Hover & klik efek untuk icon kanan */
        .input-icon.right:hover {
            color: #dc3545;
        }

        .input-icon.right:active {
            transform: translateY(-50%) scale(0.9);
        }

        /* ==== BUTTON ==== */
        .btn-danger {
            border-radius: 50px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-danger:hover {
            background-color: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(220, 53, 69, 0.3);
        }

        .btn-danger i {
            font-size: 1rem;
        }

        /* ==== SWEETALERT OVERRIDE ==== */
        .swal2-popup {
            border-radius: 1rem !important;
        }

        /* ==== ANIMASI FADE IN HALUS ==== */
        .fade-in-card {
            opacity: 0;
            transform: translateY(10px);
            animation: fadeInSmooth 0.8s ease forwards;
        }

        @keyframes fadeInSmooth {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

    </style>
@endpush

@section('main')
    <div class="login-bg">
        {{-- Efek animasi fadeInDown dari Animate.css --}}
        {{-- <div class="card card-danger animate__animated animate__fadeInDown animate__faster"> --}}
            <div class="card card-danger fade-in-card">
            <div class="card-header">
                <h4><i class="fa-solid fa-right-to-bracket"></i> Login</h4>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('login.action') }}" class="needs-validation" novalidate="">
                    @csrf

                    {{-- Username Field --}}
                    <div class="form-group mb-4">
                        <label for="username">Username</label>
                        <div class="input-wrapper">
                            {{-- <span class="input-icon left">
                                <i class="fa-solid fa-user"></i>
                            </span> --}}
                            <input id="username"
                                type="text"
                                class="form-control"
                                name="username"
                                tabindex="1"
                                required
                                autofocus>
                        </div>
                        <div class="invalid-feedback">Please fill in your Username</div>
                    </div>

                    {{-- Password Field --}}
                    <div class="form-group mb-4">
                        <label for="password" class="control-label">Password</label>
                        <div class="input-wrapper">
                            {{-- <span class="input-icon left">
                                <i class="fa-solid fa-lock"></i>
                            </span> --}}
                            <input id="password"
                                type="password"
                                class="form-control"
                                name="password"
                                tabindex="2"
                                required>
                            <span class="input-icon right" id="togglePassword">
                                <i class="fa-solid fa-eye"></i>
                            </span>
                        </div>
                        <div class="invalid-feedback">Please fill in your password</div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="form-group">
                        <button type="submit" class="btn btn-danger btn-lg btn-block" tabindex="4">
                            <i class="fa-solid fa-sign-in-alt"></i> Login
                        </button>
                    </div>
                </form>

                {{-- Script Toggle Password --}}
                <script>
                    const togglePassword = document.getElementById('togglePassword');
                    const passwordInput = document.getElementById('password');

                    togglePassword.addEventListener('click', function () {
                        const type = passwordInput.type === 'password' ? 'text' : 'password';
                        passwordInput.type = type;

                        const icon = this.querySelector('i');
                        icon.classList.toggle('fa-eye');
                        icon.classList.toggle('fa-eye-slash');
                    });
                </script>

                @if (session('alert.config'))
                    <script>
                        Swal.fire({!! session('alert.config') !!});
                    </script>
                @endif

            </div>
        </div>
    </div>
@endsection
