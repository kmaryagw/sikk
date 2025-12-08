<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    
    <!-- Title -->
    <title>@yield('title') &mdash; Instiki</title>
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('/img/instiki-logo.png') }}" type="image/png">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset('library/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @stack('style')

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', 'UA-94034622-3');
    </script>

    <!-- CRITICAL CSS: Loading Screen -->
    <style>
        /* 1. Preloader: Default STATE = AKTIF (Visible) */
        #preloader {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: #ffffff; /* Background solid putih agar menutupi proses rendering */
            z-index: 99999;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            /* Transisi hanya pada opacity agar smooth saat hilang */
            transition: opacity 0.5s ease-in-out; 
            opacity: 1;
            pointer-events: all;
        }

        /* Class untuk menghilangkan preloader */
        body.loaded #preloader {
            opacity: 0;
            pointer-events: none; /* Agar bisa diklik tembus */
        }

        /* 2. Spinner Animasi */
        .spinner-box { width: 50px; height: 50px; position: relative; }
        .spinner-circle {
            width: 100%; height: 100%;
            border: 3px solid rgba(103, 119, 239, 0.2); /* Warna muda */
            border-top-color: #6777ef; /* Warna utama Stisla */
            border-radius: 50%;
            animation: spin 0.6s linear infinite; /* Kecepatan putar lebih natural */
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        .loading-text {
            margin-top: 15px;
            font-size: 12px;
            font-weight: 700;
            color: #6777ef;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        /* 3. Konten Utama (#app) */
        /* Kita sembunyikan sedikit kontennya biar ada efek muncul */
        #app {
            opacity: 0;
            transform: translateY(15px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }

        /* Saat loaded, konten muncul */
        body.loaded #app {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>

<body>

    <!-- PRELOADER -->
    <!-- Ditaruh paling atas agar dirender duluan oleh browser -->
    <div id="preloader">
        <div class="spinner-box">
            <div class="spinner-circle"></div>
        </div>
        <div class="loading-text">Loading</div>
    </div>

    <!-- MAIN APP -->
    <div id="app">
        <div class="main-wrapper">
            @include('components.header')
            @include('components.sidebar')
            @yield('main')
            @include('sweetalert::alert')
            @include('components.footer')
        </div>
    </div>

    <!-- General JS Scripts -->
    <script src="{{ asset('library/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('library/popper.js/dist/umd/popper.js') }}"></script>
    <script src="{{ asset('library/tooltip.js/dist/umd/tooltip.js') }}"></script>
    <script src="{{ asset('library/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('library/jquery.nicescroll/dist/jquery.nicescroll.min.js') }}"></script>
    <script src="{{ asset('library/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('js/stisla.js') }}"></script>

    @stack('scripts')

    <!-- Template JS File -->
    <script src="{{ asset('js/scripts.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- LOGIC LOADING SUPER FAST & SYNC -->
    <script>
        (function() {
            // Helper: Hilangkan Loader
            function hideLoader() {
                document.body.classList.add('loaded');
            }
            
            // Helper: Munculkan Loader
            function showLoader() {
                document.body.classList.remove('loaded');
            }

            // 1. ENTRY PHASE (Saat halaman dibuka)
            // Gunakan DOMContentLoaded agar loader hilang begitu HTML siap (sebelum gambar berat dimuat)
            // Ini membuat website terasa lebih cepat/snappy
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', hideLoader);
            } else {
                hideLoader();
            }

            // Fallback: Jika DOMContentLoaded gagal, window.load akan menangkapnya
            window.addEventListener('load', hideLoader);

            // 2. BROWSER BACK BUTTON FIX (BFCache)
            // Browser modern menyimpan cache halaman. Saat back, halaman tidak di-reload.
            // Kita harus memaksa loader hilang jika user menekan tombol Back.
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    hideLoader();
                }
            });

            // 3. EXIT PHASE (Saat link diklik)
            // Mencegat semua klik link agar loader muncul SEBELUM browser berpindah halaman
            document.addEventListener('click', function(e) {
                // Cari elemen <a> terdekat dari yang diklik
                const anchor = e.target.closest('a');

                if (anchor) {
                    const href = anchor.getAttribute('href');
                    const target = anchor.getAttribute('target');

                    // Validasi link internal:
                    // - Ada href
                    // - Bukan anchor link (#)
                    // - Bukan javascript
                    // - Bukan target _blank
                    // - Bukan modifier key (Ctrl/Cmd + Click)
                    if (
                        href && 
                        href !== '#' && 
                        !href.startsWith('#') &&
                        !href.startsWith('javascript') && 
                        target !== '_blank' &&
                        !e.ctrlKey && 
                        !e.metaKey
                    ) {
                        // Tampilkan loader INSTAN saat klik
                        showLoader();
                    }
                }
            });

            // 4. FORM SUBMIT PHASE
            // Saat form disubmit (misal Login atau Simpan Data), munculkan loader
            document.addEventListener('submit', function(e) {
                const form = e.target;
                if (form.checkValidity()) {
                    showLoader();
                }
            });

        })();
    </script>

</body>
</html>