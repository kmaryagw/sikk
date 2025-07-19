<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    
    <!-- Title untuk halaman -->
    <title>@yield('title') &mdash; Instiki</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('/img/instiki-logo.png') }}" type="image/png">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset('library/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
        integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    @stack('style')

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">

    <!-- Start GA (Google Analytics) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-94034622-3');
    </script>
    <!-- END GA -->

    {{-- <!-- CSS untuk Loading Screen -->
    <style>
        #loading-screen {
            position: fixed;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.2); /* Background gelap semi-transparan */
            backdrop-filter: blur(10px); /* Efek blur untuk tampilan lebih modern */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.3s ease-out, visibility 0.3s ease-out;
        }

        .fade-out {
            opacity: 0;
            visibility: hidden;
        }

        /* Animasi Spinner */
        .spinner {
            width: 70px;
            height: 70px;
            border: 6px solid rgba(255, 255, 255, 0.9);
            border-top: 6px solid #ff4747;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            box-shadow: 0 0 15px #ff4747;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Efek Dot Wave */
        .dot-wave {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 15px;
        }

        .dot {
            width: 12px;
            height: 12px;
            margin: 0 5px;
            background-color: #ff4747;
            border-radius: 50%;
            animation: wave 1.5s infinite ease-in-out;
        }

        .dot:nth-child(1) { animation-delay: 0s; }
        .dot:nth-child(2) { animation-delay: 0.2s; }
        .dot:nth-child(3) { animation-delay: 0.4s; }
        .dot:nth-child(4) { animation-delay: 0.6s; }

        @keyframes wave {
            0%, 100% { transform: translateY(0); opacity: 0.5; }
            50% { transform: translateY(-10px); opacity: 1; }
        }

        /* Teks loading */
        .loading-text {
            font-size: 18px;
            color: #ffffff;
            font-weight: bold;
            margin-top: 15px;
            text-shadow: 0 0 10px rgba(255, 71, 71, 0.8);
        }
    </style>
     --}}
</head>

<body>

    <!-- Loading Screen -->
    {{-- <div id="loading-screen">
        <div class="spinner"></div>
        <div class="loading-text">Loading...</div>
        <div class="dot-wave">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>
    </div> --}}

    <div id="app">
        <div class="main-wrapper">
            <!-- Header -->
            @include('components.header')

            <!-- Sidebar -->
            @include('components.sidebar')

            <!-- Content -->
            @yield('main')

            @include('sweetalert::alert')

            <!-- Footer -->
            @include('components.footer')
        </div>
    </div>

    {{-- <!-- Script untuk menghilangkan loading setelah halaman selesai dimuat -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let loadingScreen = document.getElementById("loading-screen");

            // Fungsi untuk menyembunyikan loading screen setelah halaman selesai dimuat
            function hideLoadingScreen() {
                loadingScreen.style.visibility = "hidden"; // Hilangkan loading screen
                loadingScreen.style.opacity = "1"; // Transisi smooth
            }

            // Fungsi untuk menampilkan loading screen sebelum berpindah halaman
            function showLoadingScreen() {
                loadingScreen.style.visibility = "visible";
                loadingScreen.style.opacity = "1";
            }

            // **Menghilangkan loading saat halaman selesai dimuat**
            window.onload = function () {
                hideLoadingScreen();
            };

            // **Fix Bug: Mencegah loading muncul saat tekan tombol "Back" pada browser**
            window.addEventListener("pageshow", function (event) {
                if (event.persisted) { // Halaman dimuat dari cache browser
                    hideLoadingScreen();
                }
            });

            // Event listener untuk semua link <a> agar loading muncul sebelum navigasi
            document.querySelectorAll("a").forEach(link => {
                link.addEventListener("click", function (event) {
                    let target = this.getAttribute("target");
                    let href = this.getAttribute("href");

                    // Cek apakah link menuju halaman lain atau hanya # (anchor)
                    if (href && href !== "#" && !href.startsWith("javascript:") && target !== "_blank") {
                        showLoadingScreen();
                    }
                });
            });

            // Event listener untuk semua form agar loading muncul sebelum submit
            document.querySelectorAll("form").forEach(form => {
                form.addEventListener("submit", function () {
                    showLoadingScreen();
                });
            });

            // Event listener untuk tombol submit agar loading muncul sebelum proses dijalankan
            document.querySelectorAll("button[type='submit'], input[type='submit']").forEach(button => {
                button.addEventListener("click", function () {
                    showLoadingScreen();
                });
            });

            // Event listener untuk tombol dengan atribut data-link agar loading muncul sebelum pindah halaman
            document.querySelectorAll("button[data-link]").forEach(button => {
                button.addEventListener("click", function () {
                    let link = this.getAttribute("data-link");
                    if (link) {
                        showLoadingScreen();
                        window.location.href = link;
                    }
                });
            });
        });
    </script> --}}


    
    

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
    
</body>

</html>
