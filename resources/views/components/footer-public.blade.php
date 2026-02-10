<footer class="main-footer">
    <div class="footer-container">
        <span>Copyright &copy; 2024</span>
        <span class="bullet"></span>
        <span class="brand-footer">INSTIKI</span>
    </div>
</footer>

<style>
    /* Footer Style */
    .main-footer {
        background-color: #ffffff; /* Warna putih bersih */
        color: #6c757d; /* Abu-abu profesional */
        padding: 25px 0;
        border-top: 1px solid #ececec;
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        width: 100%;
    }

    /* Kontainer footer */
    .footer-container {
        max-width: 1850px;
        margin: auto;
        padding: 0 20px;
        /* Default Desktop: Padding left mengikuti layout dashboard biasanya */
        padding-left: 5rem; 
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }

    /* Bullet kecil di tengah */
    .main-footer .bullet {
        display: inline-block;
        width: 5px;
        height: 5px;
        background-color: #adb5bd;
        border-radius: 50%;
        margin: 0 12px;
    }

    .brand-footer {
        font-weight: 600;
        color: #f34e4e; /* Senada dengan warna tema Navbar */
    }

    /* RESPONSIF MOBILE (iPhone X, dll) */
    @media (max-width: 768px) {
        .main-footer {
            padding: 20px 0;
            font-size: 12px;
        }

        .footer-container {
            padding-left: 0; /* Hapus padding besar di mobile */
            justify-content: center; /* Teks jadi rata tengah di mobile */
            flex-direction: row; /* Tetap satu baris agar ringkas */
        }

        .main-footer .bullet {
            margin: 0 8px;
        }
    }
</style>