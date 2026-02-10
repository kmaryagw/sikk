<!-- Import font Poppins -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<nav class="navbar-instiki">
    <div class="navbar-container">
        <!-- Logo & Brand -->
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ asset('img/instiki-putih.webp') }}" alt="Instiki Logo">
            <div class="brand-text">
                <span class="brand-sub">SISTEM PENJAMINAN MUTU</span>
                <span class="brand-main">INSTIKI</span>
            </div>
        </a>

        <!-- Toggle Button (Mobile) -->
        <button class="navbar-toggle" id="navbarToggle" aria-label="Toggle navigation">
            <span class="hamburger"></span>
        </button>

        <!-- Menu -->
        <ul class="navbar-menu" id="navbarMenu">
            <li><a href="{{ route('login') }}" class="btn-login-mobile">Login</a></li>
        </ul>
    </div>
</nav>


<style>
    /* GLOBAL FONT */
    body {
        font-family: 'Poppins', sans-serif;
        margin: 0;
    }

    /* NAVBAR BASE */
    .navbar-instiki {
        background-color: #f34e4e;
        color: white;
        position: sticky;
        top: 0;
        width: 100%;
        z-index: 1000;
        transition: all 0.3s ease;
        height: 70px; /* Tinggi tetap agar rapi */
        display: flex;
        align-items: center;
    }

    .navbar-instiki.scrolled {
        background-color: #c80000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        height: 60px;
    }

    /* Container */
    .navbar-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 20px;
        width: 100%;
        max-width: 1400px;
        margin: auto;
    }

    /* Brand Styling */
    .navbar-brand {
        display: flex;
        align-items: center;
        text-decoration: none;
        color: white;
        gap: 12px;
    }
    
    .navbar-brand img {
        height: 45px;
        width: auto;
        transition: 0.3s;
    }

    .brand-text {
        display: flex;
        flex-direction: column;
        line-height: 1.2;
    }

    .brand-sub {
        font-size: 10px;
        font-weight: 400;
        letter-spacing: 1px;
    }

    .brand-main {
        font-size: 16px;
        font-weight: 700;
    }

    /* Menu Desktop */
    .navbar-menu {
        list-style: none;
        display: flex;
        gap: 30px;
        margin: 0;
        padding: 0;
    }

    .navbar-menu li a {
        color: white;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        padding: 8px 16px;
        border-radius: 8px;
        transition: 0.3s;
    }

    .navbar-menu li a:hover {
        background: rgba(255,255,255,0.2);
    }

    /* Toggle Button (Hamburger) */
    .navbar-toggle {
        display: none;
        width: 30px;
        height: 30px;
        padding: 0;
        background: none;
        border: none;
        cursor: pointer;
        position: relative;
    }

    .hamburger, .hamburger::before, .hamburger::after {
        content: '';
        display: block;
        background: white;
        height: 3px;
        width: 100%;
        border-radius: 3px;
        transition: all 0.3s ease;
        position: absolute;
    }
    .hamburger { top: 13px; }
    .hamburger::before { top: -8px; }
    .hamburger::after { top: 8px; }

    /* Hamburger Animation when Active */
    .navbar-toggle.active .hamburger { background: transparent; }
    .navbar-toggle.active .hamburger::before { transform: rotate(45deg); top: 0; }
    .navbar-toggle.active .hamburger::after { transform: rotate(-45deg); top: 0; }

    /* RESPONSIVE IPHONE X & MOBILE */
    @media (max-width: 768px) {
        .navbar-instiki { height: 65px; }

        .navbar-toggle { display: block; }

        .brand-sub { font-size: 8px; }
        .brand-main { font-size: 13px; }
        .navbar-brand img { height: 35px; }

        .navbar-menu {
            position: fixed;
            top: 65px; /* Sesuai tinggi navbar */
            left: 0;
            right: 0;
            background-color: #c80000;
            flex-direction: column;
            gap: 0;
            width: 100%;
            height: 0;
            overflow: hidden;
            transition: height 0.3s ease;
            box-shadow: 0 10px 15px rgba(0,0,0,0.1);
        }

        .navbar-menu.show {
            height: auto;
            padding: 15px 0;
        }

        .navbar-menu li {
            width: 100%;
            text-align: center;
        }

        .navbar-menu li a {
            display: block;
            padding: 15px;
            font-size: 16px;
            border-radius: 0;
        }

        .btn-login-mobile {
            background: rgba(255,255,255,0.1);
            margin: 0 20px;
            border-radius: 10px !important;
        }
    }
</style>

<script>
    const toggle = document.getElementById('navbarToggle');
    const menu = document.getElementById('navbarMenu');
    const navbar = document.querySelector('.navbar-instiki');

    // Toggle menu on mobile
    toggle.addEventListener('click', () => {
        menu.classList.toggle('show');
        toggle.classList.toggle('active'); // Untuk animasi hamburger
    });

    // Close menu when clicking a link (mobile)
    document.querySelectorAll('.navbar-menu a').forEach(link => {
        link.addEventListener('click', () => {
            menu.classList.remove('show');
            toggle.classList.remove('active');
        });
    });

    // Scroll effect
    document.addEventListener('scroll', () => {
        if (window.scrollY > 30) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
</script>
