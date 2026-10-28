@extends('layouts.announcement')

@section('title','SPMI')

@section('main')

<style>
    :root {
        --primary-color: #0d6efd;
        --border-radius-lg: 1.25rem;
        --border-radius-md: 1rem;
    }

    body { background-color: #f8f9fa; color: #333; }

    .text-shadow-strong { text-shadow: 0 2px 10px rgba(0,0,0,0.8); }
    .ls-1 { letter-spacing: 0.5px; }
    
    /* ====================================================================
       CINEMATIC MOTION SUITE FOR ANNOUNCEMENT CAROUSEL
       ==================================================================== */
    #announcementCarousel { 
        border-radius: var(--border-radius-lg); 
        overflow: hidden; 
        background: #090d16;
        position: relative;
    }

    /* 1. Cinematic Cross-Depth Transition (Pengganti Fade Biasa) */
    .carousel-fade .carousel-item { 
        height: 500px; 
        opacity: 0;
        transition: opacity 0.95s cubic-bezier(0.16, 1, 0.3, 1), transform 1.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
        transform: scale(1.06) !important;
        pointer-events: none;
    }

    .carousel-fade .carousel-item.active {
        opacity: 1;
        transform: scale(1) !important;
        pointer-events: auto;
        z-index: 2;
    }

    /* 2. Ken Burns 2.0 Continuous Breathing Pan & Zoom */
    .carousel-img {
        height: 100%;
        width: 100%;
        object-fit: cover;
        opacity: 0.78;
        filter: brightness(0.92) contrast(1.05);
        transform-origin: center center;
    }

    .carousel-item.active .carousel-img {
        animation: cinematicKenBurns 9s cubic-bezier(0.25, 1, 0.5, 1) infinite alternate;
    }

    @keyframes cinematicKenBurns {
        0% { transform: scale(1) translate3d(0, 0, 0); }
        100% { transform: scale(1.08) translate3d(-1.5%, -1%, 0); }
    }

    /* 3. Cinematic Studio Vignette Gradient */
    .carousel-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, 
            rgba(9, 13, 22, 0.05) 0%, 
            rgba(9, 13, 22, 0.38) 40%, 
            rgba(9, 13, 22, 0.94) 100%);
        display: flex;
        align-items: flex-end;
        padding: 55px 60px 70px;
        text-align: left;
        z-index: 3;
    }

    /* 4. Choreographed Staggered Entry for Content */
    .carousel-item .carousel-badge-glass,
    .carousel-item .carousel-title-anim,
    .carousel-item .carousel-btn-anim {
        opacity: 0;
        will-change: transform, opacity;
    }

    /* Badge Stagger (Step 1: 0.15s) */
    .carousel-item.active .carousel-badge-glass {
        animation: badgeEntrance 0.75s cubic-bezier(0.16, 1, 0.3, 1) 0.15s both;
    }

    /* Title Stagger (Step 2: 0.3s dengan Optical Blur Fade) */
    .carousel-item.active .carousel-title-anim {
        animation: titleEntrance 0.85s cubic-bezier(0.16, 1, 0.3, 1) 0.3s both;
    }

    /* Button Stagger (Step 3: 0.45s) */
    .carousel-item.active .carousel-btn-anim {
        animation: btnEntrance 0.85s cubic-bezier(0.16, 1, 0.3, 1) 0.45s both;
    }

    @keyframes badgeEntrance {
        from {
            opacity: 0;
            transform: translateY(-22px) scale(0.92);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @keyframes titleEntrance {
        from {
            opacity: 0;
            transform: translateY(32px);
            filter: blur(6px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
            filter: blur(0);
        }
    }

    @keyframes btnEntrance {
        from {
            opacity: 0;
            transform: translateY(24px) scale(0.94);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* 5. Glowing Frosted Glass Badge with Live Pulsing Beacon */
    .carousel-badge-glass {
        display: inline-flex;
        align-items: center;
        padding: 7px 18px;
        background: rgba(13, 110, 253, 0.38);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 50px;
        color: #ffffff;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        box-shadow: 0 4px 20px rgba(13, 110, 253, 0.35);
    }

    .pulsing-beacon {
        width: 8px;
        height: 8px;
        background: #38bdf8;
        border-radius: 50%;
        position: relative;
        display: inline-block;
    }

    .pulsing-beacon::after {
        content: '';
        position: absolute;
        inset: -3px;
        border-radius: 50%;
        background: rgba(56, 189, 248, 0.6);
        animation: beaconPulse 2s infinite cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes beaconPulse {
        0% { transform: scale(0.8); opacity: 1; }
        100% { transform: scale(2.4); opacity: 0; }
    }

    /* 6. Typography & Text Shadow */
    .carousel-title-anim {
        font-size: 2.35rem;
        font-weight: 800;
        line-height: 1.25;
        letter-spacing: -0.5px;
        color: #ffffff;
        text-shadow: 0 4px 24px rgba(0, 0, 0, 0.85);
        max-width: 90%;
    }

    /* 7. Action Button with Shimmer & Spring Icon */
    .btn-modern-action {
        position: relative;
        overflow: hidden;
        background: #ffffff;
        color: #0f172a;
        border: none;
        padding: 12px 28px;
        font-size: 0.95rem;
        font-weight: 700;
        border-radius: 50px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.35);
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        display: inline-flex;
        align-items: center;
        text-decoration: none;
    }

    .btn-modern-action:hover {
        transform: translateY(-3px) scale(1.02);
        color: var(--primary-color);
        background: #ffffff;
        box-shadow: 0 14px 32px rgba(13, 110, 253, 0.4);
    }

    .btn-modern-action .btn-arrow {
        transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .btn-modern-action:hover .btn-arrow {
        transform: translateX(5px);
    }

    .btn-modern-action::before {
        content: '';
        position: absolute;
        top: 0; left: -100%;
        width: 60%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.7), transparent);
        transform: skewX(-20deg);
        transition: 0.7s;
    }

    .btn-modern-action:hover::before {
        left: 160%;
    }

    /* 8. Timer Progress Bar Indicators (Story / Netflix Style) */
    .custom-indicators {
        position: absolute;
        bottom: 25px;
        left: 60px;
        right: auto;
        margin: 0;
        display: flex !important;
        gap: 8px;
        z-index: 10;
    }

    .custom-indicators button {
        width: 32px !important;
        height: 5px !important;
        padding: 0;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.3) !important;
        border: none !important;
        transition: width 0.4s cubic-bezier(0.16, 1, 0.3, 1), background 0.4s ease !important;
        position: relative;
        overflow: hidden;
        opacity: 1 !important;
    }

    .custom-indicators button.active {
        width: 60px !important;
        background: rgba(255, 255, 255, 0.25) !important;
    }

    .custom-indicators button.active .indicator-progress {
        position: absolute;
        top: 0; left: 0; bottom: 0;
        width: 100%;
        background: #ffffff;
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.9);
        animation: fillProgressBar 5s linear forwards;
    }

    #announcementCarousel:hover .indicator-progress {
        animation-play-state: paused;
    }

    @keyframes fillProgressBar {
        from { width: 0%; }
        to { width: 100%; }
    }

    /* 9. Floating Frosted Navigation Controls */
    .custom-nav {
        width: 70px;
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        z-index: 10;
        border: none;
        background: transparent;
    }

    #announcementCarousel:hover .custom-nav {
        opacity: 1;
    }

    .nav-circle {
        width: 46px;
        height: 46px;
        background: rgba(15, 23, 42, 0.5);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.22);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 1.05rem;
        transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
    }

    .custom-nav:hover .nav-circle {
        background: var(--primary-color);
        transform: scale(1.12);
        box-shadow: 0 10px 28px rgba(13, 110, 253, 0.5);
        border-color: rgba(255, 255, 255, 0.5);
    }

    .card-modern {
        border: none !important;
        background: #ffffff;
        transition: all 0.3s ease;
        box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        border-radius: 1rem;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .card-modern:hover {
        transform: translateY(-8px); 
        box-shadow: 0 15px 30px rgba(0,0,0,0.12);
    }
    
    .img-wrapper {
        overflow: hidden;
        border-radius: 1rem 1rem 0 0;
        position: relative;
        height: 200px; 
    }

    .img-cover {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    .card-modern:hover .img-cover {
        transform: scale(1.1);
    }

    .card-body {
        position: relative;
        z-index: 2;
        background: #fff;
    }

    .row.gy-4 > .col {
        padding-top: 10px;
        padding-bottom: 10px;
    }

    /* rapihin deskripsis di card announcement */
    .card-title {
        display: block;
        min-height: 1.2em; 
        margin-bottom: 0.75rem;
    }

    .card-text {
        line-height: 1.6;
        display: -webkit-box;
        -webkit-line-clamp: 4; 
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .card-body.d-flex.flex-column {
        display: flex;
        flex-direction: column;
    }

    .mt-auto {
        margin-top: auto !important;
    }
        
    @media (min-width: 992px) {
        .card-modern:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
    }

    .img-wrapper { height: 200px; border-radius: var(--border-radius-md) var(--border-radius-md) 0 0; overflow: hidden; position: relative; }
    .img-cover { width: 100%; height: 100%; object-fit: cover; }

    .date-badge-floating {
        position: absolute; top: 12px; left: 12px;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 10px; padding: 6px 10px; text-align: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1); z-index: 2;
    }
    .date-badge-day { font-size: 1.1rem; font-weight: 800; color: #212529; display: block; line-height: 1; }
    .date-badge-month { font-size: 0.65rem; font-weight: 700; color: var(--primary-color); text-transform: uppercase; }

    .list-group-modern .list-group-item {
        border: none; border-bottom: 1px solid #f1f3f5; padding: 1.25rem; background: transparent;
    }
    .list-group-modern .list-group-item:last-child { border-bottom: none; }

    .modal-content { border: none; border-radius: 20px; }
    .modal-hero-container { height: 300px; position: relative; }
    .modal-hero-img { width: 100%; height: 100%; object-fit: cover; }
    .modal-body-content { padding: 30px; }
    .modal-title-custom { font-size: 1.75rem; font-weight: 800; color: #1a1e21; margin-bottom: 1rem; line-height: 1.3; }

    /* --- MOBILE RESPONSIVE (iPhone X etc) --- */
    @media (max-width: 767.98px) {
        body::-webkit-scrollbar { display: none; }
        body { -ms-overflow-style: none; scrollbar-width: none; }

        .container-fluid { 
            padding-left: 1.25rem !important; 
            padding-right: 1.25rem !important; 
        }
        
        #announcementCarousel { 
            margin-bottom: 2.5rem !important; 
            border-radius: var(--border-radius-lg); 
            overflow: hidden; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        #announcementCarousel .carousel-item, 
        #announcementCarousel img {
            border-radius: var(--border-radius-lg); 
        }

        .carousel-item { height: 350px !important; }
        .carousel-overlay { 
            padding: 30px 20px 28px !important; 
            border-radius: 0 0 var(--border-radius-lg) var(--border-radius-lg);
        }
        .carousel-title-anim { font-size: 1.35rem !important; margin-bottom: 12px !important; }
        .btn-modern-action { padding: 9px 20px !important; font-size: 0.85rem !important; }
        .carousel-badge-glass { font-size: 0.65rem !important; padding: 5px 12px !important; margin-bottom: 8px !important; }
        .custom-indicators { left: 20px !important; bottom: 15px !important; }
        .custom-indicators button { width: 22px !important; }
        .custom-indicators button.active { width: 40px !important; }
        .custom-nav { display: none !important; }
        
        .modal-hero-container { height: 200px; }
        .modal-body-content { padding: 20px; }
        .modal-title-custom { font-size: 1.3rem; }
        .modal-text { font-size: 0.95rem; line-height: 1.7; }
        
        .section-title { font-size: 1.2rem !important; }

        .list-group-modern .list-group-item { padding: 1.25rem 0; }
    }

</style>

<div class="container-fluid py-4 py-md-5">
    
    <div class="row g-4 mb-5">
        <div class="col-lg-8 col-xl-9">
            @if($sliderAnnouncements->count() > 0)
                <div id="announcementCarousel" class="carousel slide carousel-fade shadow-lg" data-bs-ride="carousel" data-bs-interval="5000" data-bs-pause="hover">
                    
                    {{-- Timer Progress Bar Indicators --}}
                    @if($sliderAnnouncements->count() > 1)
                        <div class="carousel-indicators custom-indicators">
                            @foreach($sliderAnnouncements as $key => $slide)
                                <button type="button" 
                                        data-bs-target="#announcementCarousel" 
                                        data-bs-slide-to="{{ $key }}" 
                                        class="{{ $key == 0 ? 'active' : '' }}" 
                                        aria-current="{{ $key == 0 ? 'true' : 'false' }}" 
                                        aria-label="Slide {{ $key + 1 }}">
                                    <span class="indicator-progress"></span>
                                </button>
                            @endforeach
                        </div>
                    @endif

                    <div class="carousel-inner">
                        @foreach($sliderAnnouncements as $key => $slide)
                            <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                @if($slide->image)
                                    <img src="{{ Storage::url($slide->image) }}" class="d-block w-100 carousel-img" alt="{{ $slide->title }}">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100 bg-dark text-white-50">No Image</div>
                                @endif
                                
                                <div class="carousel-overlay">
                                    <div class="container-inner w-100">
                                        <div class="carousel-badge-glass mb-3">
                                            <span>Pengumuman Utama</span>
                                        </div>
                                        
                                        <h1 class="fw-bold text-white carousel-title-anim mb-3">
                                            {{ \Illuminate\Support\Str::limit($slide->title, 70) }}
                                        </h1>
                                        
                                        <div class="carousel-btn-wrapper">
                                            <button type="button" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $slide->id }}" class="btn btn-modern-action carousel-btn-anim">
                                                <span>Baca Selengkapnya</span>
                                                <i class="fa-solid fa-arrow-right ms-2 btn-arrow"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    {{-- Floating Frosted Navigation Controls --}}
                    @if($sliderAnnouncements->count() > 1)
                        <button class="carousel-control-prev custom-nav" type="button" data-bs-target="#announcementCarousel" data-bs-slide="prev" aria-label="Sebelumnya">
                            <span class="nav-circle"><i class="fa-solid fa-chevron-left"></i></span>
                        </button>
                        <button class="carousel-control-next custom-nav" type="button" data-bs-target="#announcementCarousel" data-bs-slide="next" aria-label="Selanjutnya">
                            <span class="nav-circle"><i class="fa-solid fa-chevron-right"></i></span>
                        </button>
                    @endif
                </div>
            @endif
        </div>

        <!-- Sidebar: Pengumuman Terbaru -->
        <div class="col-lg-4 col-xl-3">
            <div class="card card-modern h-100 border-0 shadow-sm">
                <div class="card-header bg-white pt-4 pb-2 border-0 px-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Terbaru</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush list-group-modern">
                        @forelse($otherAnnouncements as $announcement)
                            <a href="#" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $announcement->id }}" class="list-group-item px-4">
                                <small class="text-muted d-block mb-1">{{ \Carbon\Carbon::parse($announcement->date)->translatedFormat('d M Y') }}</small>
                                <h6 class="mb-0 fw-bold text-dark lh-base" style="font-size: 0.9rem;">{{ \Illuminate\Support\Str::limit($announcement->title, 55) }}</h6>
                            </a>
                        @empty
                            <div class="p-4 text-center text-muted small">Tidak ada pengumuman.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section: Semua Pengumuman -->
    <div class="mb-4">
        <h4 class="fw-bold text-dark section-title">Semua Pengumuman</h4>
        <div style="width: 50px; height: 4px; background: var(--primary-color); border-radius: 10px;"></div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-5 gy-4 gx-3 gx-md-4">
        @forelse($allAnnouncements as $announcement)
            <div class="col">
                <div class="card card-modern h-100 shadow-sm"> 
                    <div class="img-wrapper">
                        @if($announcement->image)
                            <img src="{{ Storage::url($announcement->image) }}" class="img-cover" alt="{{ $announcement->title }}">
                        @else
                            <div class="bg-light h-100 w-100 d-flex align-items-center justify-content-center"><i class="bi bi-newspaper fs-2 text-muted"></i></div>
                        @endif
                        <div class="date-badge-floating">
                            <span class="date-badge-day">{{ \Carbon\Carbon::parse($announcement->date)->format('d') }}</span>
                            <span class="date-badge-month">{{ \Carbon\Carbon::parse($announcement->date)->translatedFormat('M') }}</span>
                        </div>
                    </div>
                    
                    <div class="card-body d-flex flex-column p-3">
                        <h6 class="card-title fw-bold mb-2 lh-base">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $announcement->id }}" class="text-decoration-none text-dark stretched-link">
                                {{ $announcement->title }}
                            </a>
                        </h6>

                        <p class="card-text text-muted small flex-grow-1 mb-3">
                            {{ \Illuminate\Support\Str::limit(strip_tags($announcement->summary ?? $announcement->content), 160) }}
                        </p>

                        <div class="mt-auto pt-2 border-top">
                            <span class="text-primary fw-bold" style="font-size: 0.75rem">
                                Selengkapnya <i class="bi bi-arrow-right ms-1"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 w-100 text-center py-5">
                <p class="text-muted">Belum ada pengumuman tersedia.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-5">
        {{ $allAnnouncements->links() }}
    </div>

</div>

<!-- Global Modals -->
@php
    $combinedAnnouncements = $allAnnouncements->merge($sliderAnnouncements)->unique('id');
@endphp

@foreach($combinedAnnouncements as $ann)
<div class="modal fade" id="modalDetail{{ $ann->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-hero-container">
                @if($ann->image)
                    <img src="{{ Storage::url($ann->image) }}" class="modal-hero-img">
                @else
                    <div class="w-100 h-100 bg-secondary d-flex align-items-center justify-content-center text-white">Pengumuman</div>
                @endif
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3 bg-white p-2 rounded-circle shadow-sm" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body modal-body-content">
                <div class="mb-2 text-primary fw-bold" style="font-size: 0.8rem; letter-spacing: 1px;">
                    <i class="bi bi-calendar3 me-2"></i> {{ \Carbon\Carbon::parse($ann->date)->translatedFormat('l, d F Y') }}
                </div>
                <h2 class="modal-title-custom">{{ $ann->title }}</h2>
                <hr class="my-4 opacity-50">
                <div class="modal-text text-dark announcement-content">
                    {!! $ann->content ?? $ann->summary !!}
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const carousel = document.getElementById('announcementCarousel');
        if (carousel) {
            carousel.addEventListener('slide.bs.carousel', function() {
                const progressBars = carousel.querySelectorAll('.indicator-progress');
                progressBars.forEach(function(bar) {
                    bar.style.animation = 'none';
                    bar.offsetHeight; /* trigger reflow */
                    bar.style.animation = '';
                });
            });
        }
    });
</script>
@endpush