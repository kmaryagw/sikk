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

    <!-- CSS TRANSISI HALUS (Tanpa Loading Spinner) -->
    <style>
        /* 1. Kondisi Awal: Transparan & Turun Sedikit */
        #app {
            opacity: 0;
            transform: translateY(20px); /* Geser ke bawah 20px */
            transition: opacity 0.6s ease-out, transform 0.6s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        /* 2. Kondisi Akhir (Saat kelas 'loaded' ditambahkan): Muncul & Naik */
        body.loaded #app {
            opacity: 1;
            transform: translateY(0); /* Kembali ke posisi asal */
        }
    </style>
</head>

<body>

    <!-- MAIN APP -->
    <div id="app">
        <div class="main-wrapper">
            @include('components.header')
            @include('components.sidebar')
            
            <!-- Content -->
            @yield('main')

            <!-- SweetAlert Global -->
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

    <!-- SCRIPT TRANSISI & LOGOUT -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // 1. Trigger Transisi Masuk (Fade In + Slide Up)
            // setTimeout kecil (10ms) memastikan browser merender state awal (opacity 0) dulu
            setTimeout(() => {
                document.body.classList.add('loaded');
            }, 10);

            // 2. Fix untuk Tombol Back Browser (BFCache)
            // Agar saat di-back, halaman tetap muncul (tidak transparan)
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    document.body.classList.add('loaded');
                }
            });

            // 3. Logic Logout dengan SweetAlert
            const logoutLink = document.getElementById('logout-link');
            if (logoutLink) {
                logoutLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Yakin ingin keluar?',
                        text: "Sesi Anda akan berakhir!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ED3500',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Keluar!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const logoutForm = document.getElementById('logout-form');
                            if(logoutForm) logoutForm.submit();
                        }
                    });
                });
            }
        });
    </script>

</body>
</html>