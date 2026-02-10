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
    
    /* --- Carousel & Ken Burns Effect --- */
    #announcementCarousel { 
        border-radius: var(--border-radius-lg); 
        overflow: hidden; 
        background: #000;
    }

    .carousel-item { 
        height: 500px; 
        transition: transform 1.2s cubic-bezier(0.7, 0, 0.3, 1);
    }

    /* Efek gambar bergerak pelan (Ken Burns) */
    .carousel-item.active .carousel-img {
        animation: kenburns 10s ease-out both;
    }

    @keyframes kenburns {
        0% { transform: scale(1); }
        100% { transform: scale(1.1); }
    }

    .carousel-img {
        height: 100%;
        width: 100%;
        object-fit: cover;
        opacity: 0.7; /* Membuat teks lebih menonjol */
    }

    /* --- Cinematic Overlay --- */
    .carousel-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(to bottom, 
            rgba(0,0,0,0.1) 0%, 
            rgba(0,0,0,0.4) 50%, 
            rgba(0,0,0,0.9) 100%);
        display: flex;
        align-items: flex-end; /* Teks di bawah agar cinematic */
        padding: 60px;
        text-align: left; /* Rata kiri lebih modern untuk slider besar */
    }

    /* --- Glassmorphism Badge --- */
    .badge-glass {
        display: inline-block;
        padding: 8px 20px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 50px;
        color: #fff;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        font-weight: 600;
    }

    /* --- Custom Navigation Circles --- */
    .custom-nav {
        width: 10%; /* Area klik lebih luas */
        opacity: 0;
        transition: all 0.4s ease;
    }

    #announcementCarousel:hover .custom-nav {
        opacity: 1;
    }

    .nav-circle {
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(5px);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        transition: 0.3s;
    }

    .nav-circle:hover {
        background: var(--primary-color);
        transform: scale(1.1);
    }

    /* --- Animations --- */
    .title-animate {
        animation: fadeInUp 0.8s ease-out both;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
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

    #announcementCarousel .carousel-indicators {
        display: none !important;
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

        .carousel-item { height: 350px; }
        .carousel-overlay { 
            padding: 60px 20px 25px; 
            border-radius: 0 0 var(--border-radius-lg) var(--border-radius-lg);
        }
        .carousel-overlay h1 { font-size: 1.25rem !important; margin-bottom: 10px !important; }
        .carousel-overlay .btn { padding: 8px 20px; font-size: 0.85rem; }
        
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
                <div id="announcementCarousel" class="carousel slide carousel-fade shadow-lg" data-bs-ride="carousel" data-bs-interval="5000">
                    <div class="carousel-inner">
                        @foreach($sliderAnnouncements as $key => $slide)
                            <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                @if($slide->image)
                                    <img src="{{ Storage::url($slide->image) }}" class="d-block w-100 carousel-img" alt="{{ $slide->title }}">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100 bg-dark text-white-50">No Image</div>
                                @endif
                                
                                <div class="carousel-overlay">
                                    <div class="container-inner">
                                        <!-- Tambahkan class d-none (sembunyi di semua layar) -->
                                        <!-- Dan d-md-inline-block (muncul kembali mulai dari layar medium/desktop) -->
                                        <span class="badge bg-primary mb-2 px-3 py-2 rounded-pill text-uppercase ls-1 d-none d-md-inline-block" style="font-size: 0.7rem;">
                                            Pengumuman Utama
                                        </span>
                                        
                                        <h1 class="fw-bold text-white text-shadow-strong mb-3">
                                            {{ \Illuminate\Support\Str::limit($slide->title, 70) }}
                                        </h1>
                                        
                                        <button type="button" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $slide->id }}" class="btn btn-light rounded-pill fw-bold">
                                            Baca Selengkapnya
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Custom Navigation -->
                    {{-- <button class="carousel-control-prev custom-nav" type="button" data-bs-target="#announcementCarousel" data-bs-slide="prev">
                        <span class="nav-circle"><i class="bi bi-chevron-left"></i></span>
                    </button>
                    <button class="carousel-control-next custom-nav" type="button" data-bs-target="#announcementCarousel" data-bs-slide="next">
                        <span class="nav-circle"><i class="bi bi-chevron-right"></i></span>
                    </button> --}}
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
                        <h6 class="card-title fw-bold mb-2">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $announcement->id }}" class="text-decoration-none text-dark stretched-link">
                                {{ \Illuminate\Support\Str::limit($announcement->title, 50) }}
                            </a>
                        </h6>
                        <p class="card-text text-muted small flex-grow-1">
                            {{ \Illuminate\Support\Str::limit(strip_tags($announcement->summary ?? $announcement->content), 70) }}
                        </p>
                        <div class="mt-3 pt-2 border-top">
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