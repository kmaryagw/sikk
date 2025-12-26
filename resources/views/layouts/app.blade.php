<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    
    <title>@yield('title') &mdash; Instiki</title>
    <link rel="icon" href="{{ asset('/img/instiki-logo.png') }}" type="image/png">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{ asset('library/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @stack('style')

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">

    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', 'UA-94034622-3');
    </script>

    <style>
        /* CSS TRANSISI */
        #app {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease-out, transform 0.6s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        /* --- PERBAIKAN PENTING DI SINI --- */
        /* Gunakan 'transform: none' bukan 'translateY(0)' agar position:fixed Modal kembali normal */
        body.loaded #app {
            opacity: 1;
            transform: none; 
        }
    </style>
</head>

<body>
    <div id="app">
        <div class="main-wrapper">
            @include('components.header')
            @include('components.sidebar')
            
            @yield('main')

            @include('sweetalert::alert')

            @include('components.footer')
        </div>
    </div>

    <script src="{{ asset('library/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('library/popper.js/dist/umd/popper.js') }}"></script>
    <script src="{{ asset('library/tooltip.js/dist/umd/tooltip.js') }}"></script>
    <script src="{{ asset('library/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('library/jquery.nicescroll/dist/jquery.nicescroll.min.js') }}"></script>
    <script src="{{ asset('library/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('js/stisla.js') }}"></script>

    @stack('scripts')

    <script src="{{ asset('js/scripts.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // 1. Trigger Transisi Masuk
            setTimeout(() => {
                document.body.classList.add('loaded');
                
                // --- PERBAIKAN PENTING DI SINI ---
                // Paksa hapus style transform setelah animasi selesai (700ms)
                // Ini memastikan browser merender ulang z-index Modal dengan benar
                setTimeout(() => {
                    var appElement = document.getElementById('app');
                    if(appElement) {
                        appElement.style.transform = 'none';
                        appElement.style.transition = 'none'; // Matikan transisi agar ringan
                    }
                }, 700);
            }, 10);

            // 2. Fix BFCache
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    document.body.classList.add('loaded');
                }
            });

            // 3. Fix Backdrop Global (Jaga-jaga)
            if (typeof $ !== 'undefined') {
                $('.modal').on('hidden.bs.modal', function () {
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');
                });
            }

            // 4. Logout Logic
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