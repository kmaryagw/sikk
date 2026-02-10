@extends('layouts.auth')

@section('title','SPMI')

@push('style')
    {{-- <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"> untuk fontnya --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        /* ==== GLOBAL VARIABLES ==== */
        :root {
            --primary-color: #e63946; /* Merah Profesional */
            --primary-hover: #d62828;
            --text-main: #1d3557;
            --text-muted: #6c757d;
            --bg-color: #f8f9fa;
            --input-bg: #f1f3f5;
            --input-focus-bg: #ffffff;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            margin: 0;
            overflow-x: hidden;
        }

        /* ==== LAYOUT SPLIT SCREEN ==== */
        .login-wrapper {
            display: flex;
            width: 100vw; 
            height: 100vh;
            
           
            position: fixed; 
            top: 0;
            left: 0;
            z-index: 9999; 
            background-color: white;
            overflow: hidden; 
        }

        .login-visual {
            flex: 1.2; 
            background-image: url("{{ asset('img/lab-instiki.jpg') }}");
            background-size: cover;
            background-position: center;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 50px;
            color: white;
            overflow: hidden;
        }

        .login-visual::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.4) 50%, rgba(230, 57, 70, 0.2) 100%);
            z-index: 1;
        }

        .visual-content {
            position: relative;
            z-index: 2;
            animation: fadeInUp 1s ease;
        }

        .visual-content h2 {
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 10px;
            line-height: 1.2;
        }

        .visual-content p {
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 500px;
        }

        .login-form-container {
            flex: 0.8;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
        }

        .login-form-wrapper {
            width: 100%;
            max-width: 400px;
            animation: fadeInRight 0.8s ease;
        }

        .brand-logo {
            font-size: 40px;
            color: var(--primary-color);
            margin-bottom: 20px;
            display: inline-block;
        }

        .form-title h4 {
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 5px;
        }

        .form-title p {
            color: var(--text-muted);
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 8px;
            display: block;
        }

        .input-box {
            position: relative;
        }

        .form-control-pro {
            width: 100%;
            padding: 14px 45px 14px 15px; 
            border: 2px solid var(--input-bg);
            background-color: var(--input-bg);
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-main);
            transition: all 0.3s ease;
            outline: none;
        }

        .form-control-pro:focus {
            background-color: var(--input-focus-bg);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(230, 57, 70, 0.1);
        }

        .input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1.1rem;
            transition: color 0.3s;
            cursor: pointer;
        }

        .form-control-pro:focus + .input-icon {
            color: var(--primary-color);
        }

        .btn-pro {
            width: 100%;
            padding: 14px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(230, 57, 70, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-pro:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(230, 57, 70, 0.3);
        }

        .btn-pro:active {
            transform: scale(0.98);
        }

        .loader {
            width: 18px;
            height: 18px;
            border: 2px solid #FFF;
            border-bottom-color: transparent;
            border-radius: 50%;
            display: none;
            box-sizing: border-box;
            animation: rotation 1s linear infinite;
        }
        @keyframes rotation { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        
        .loading .loader { display: inline-block; }
        .loading span { display: none; }

        .login-footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #aaa;
        }

        .invalid-tooltip-custom {
            font-size: 0.8rem;
            color: #dc3545;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* ==== RESPONSIVE (Mobile) ==== */
        @media (max-width: 992px) {
            .login-visual { display: none; } /* Hilangkan gambar di mobile */
            .login-form-container { flex: 1; padding: 20px; }
            .login-form-wrapper { max-width: 100%; padding: 0 20px; }
        }

        /* ==== SIMPLE BACK BUTTON ==== */
        .back-btn-minimal {
            position: absolute;
            top: 25px;
            left: 25px;
            width: 45px;
            height: 45px;
            background-color: var(--input-bg);
            color: var(--text-main);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            transition: all 0.3s ease;
            text-decoration: none;
            z-index: 10;
        }

        .back-btn-minimal:hover {
            background-color: var(--primary-color);
            color: white;
            transform: translateX(-3px);
            box-shadow: 0 4px 12px rgba(230, 57, 70, 0.3);
            text-decoration: none;
        }

        @media (max-width: 992px) {
            .back-btn-minimal {
                top: 20px;
                left: 20px;
                width: 40px;
                height: 40px;
            }
        }
    </style>
@endpush

@section('main')
<div class="login-wrapper">
    
    <!-- Bagian KIRI: Visual Branding -->
    <div class="login-visual">
        <div class="visual-content">
            <h2>Sistem Informasi<br>Monitoring Indikator Kinerja INSTIKI</h2>
            <p>Kelola indikator kinerja, pantau capaian, dan tingkatkan kualitas mutu pendidikan kampus INSTIKI.</p>
        </div>
    </div>

    <!-- Bagian KANAN: Form Login -->
    <div class="login-form-container">
        <a href="{{ url('/') }}" class="back-btn-minimal" title="Kembali ke Pengumuman">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div class="login-form-wrapper">
            
            <div class="text-center text-lg-left mb-4">
                <div class="brand-logo animate__animated animate__bounceIn">
                    <i class="fa-solid fa-chart-pie"></i>
                </div>
                <div class="form-title">
                    <h4>Selamat Datang Kembali</h4>
                    <p>Masukkan kredensial Anda untuk mengakses sistem.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('login.action') }}" class="needs-validation" novalidate id="loginForm">
                @csrf

                <!-- Username -->
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-box">
                        <input id="username" type="text" class="form-control-pro" name="username" required autofocus tabindex="1">
                        <i class="fa-regular fa-user input-icon"></i>
                    </div>
                    @error('username')
                        <div class="invalid-tooltip-custom">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <div class="d-flex justify-content-between">
                        <label for="password" class="form-label">Password</label>
                    </div>
                    <div class="input-box">
                        <input id="password" type="password" class="form-control-pro" name="password" required tabindex="2">
                        <i class="fa-regular fa-eye input-icon" id="togglePassword" title="Lihat Password"></i>
                    </div>
                    @error('password')
                        <div class="invalid-tooltip-custom">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Tombol Submit -->
                <button type="submit" class="btn-pro mt-4" tabindex="4" id="btnSubmit">
                    <span class="loader"></span>
                    <span>Masuk ke Dashboard <i class="fa-solid fa-arrow-right ml-2"></i></span>
                </button>

                <div class="login-footer">
                     &copy; 2024 {{-- {{ date('Y') }}--}} <strong>INSTIKI</strong>. Institut Bisnis dan Teknologi Indonesia.<br>
                    All Rights Reserved.
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Toggle Password
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        // 2. Loading State
        const form = document.getElementById('loginForm');
        const btn = document.getElementById('btnSubmit');

        form.addEventListener('submit', function(e) {
            if (form.checkValidity()) {
                btn.classList.add('loading');
                btn.disabled = true;
            }
        });

        // 3. SweetAlert Notifikasi
        @if (session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Akses Ditolak',
                text: '{{ session('error') }}',
                confirmButtonColor: '#e63946'
            });
        @endif
    });
</script>
@endsection