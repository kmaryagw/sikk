<!-- Import font Poppins -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<nav class="navbar-instiki">
    <div class="navbar-container">
        <!-- Logo & Brand -->
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ asset('img/instiki-putih.webp') }}" alt="Instiki Logo" height="50">
            <span>SISTEM INFORMASI PENJAMINAN MUTU INSTIKI</span>
        </a>

        <!-- Toggle Button (Mobile) -->
        <button class="navbar-toggle" id="navbarToggle" aria-label="Toggle navigation">
            ☰
        </button>

        <!-- Menu -->
        <ul class="navbar-menu" id="navbarMenu">
            <li><a href="{{ route('login') }}">Login</a></li>
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
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    /* Scroll effect */
    .navbar-instiki.scrolled {
        background-color: #a00000;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }

    /* Container */
    .navbar-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 20px;
        max-width: 1850px;
        margin: auto;
    }

    /* Brand */
    .navbar-brand {
        display: flex;
        align-items: center;
        text-decoration: none;
        color: white;
        font-weight: 600;
        font-size: 15px;
    }
    .navbar-brand img {
        margin-right: 10px;
    }
    .navbar-brand span {
        white-space: nowrap;
    }

    /* Menu */
    .navbar-menu {
        list-style: none;
        display: flex;
        gap: 24px;
        margin: 0;
        padding: 0;
    }
    .navbar-menu li a {
        color: white;
        text-decoration: none;
        font-weight: 500;
        font-size: 14px;
        transition: color 0.3s ease;
    }
    .navbar-menu li a:hover {
        color: #ffdede;
    }

    /* Toggle (Mobile) */
    .navbar-toggle {
        display: none;
        font-size: 22px;
        background: none;
        border: none;
        color: white;
        cursor: pointer;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .navbar-toggle {
            display: block;
        }
        .navbar-menu {
            display: none;
            flex-direction: column;
            background-color: #c80000;
            position: absolute;
            top: 60px;
            right: 0;
            width: 220px;
            padding: 10px 0;
            animation: slideDown 0.3s ease forwards;
        }
        .navbar-menu.show {
            display: flex;
        }
        .navbar-menu li {
            padding: 12px 20px;
        }
        .navbar-brand span {
            font-size: 13px;
        }
    }

    /* Animasi menu mobile */
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
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
    });

    // Scroll effect
    document.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
</script>
