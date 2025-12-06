@extends('layouts.announcement')

@section('title', 'Pengumuman')

@section('main')

<style>
    body {
        background-color: #f8f9fa;
    }

    .text-shadow-strong {
        text-shadow: 0 2px 4px rgba(0,0,0,0.7);
    }

    .card-modern {
        border: none;
        background: #ffffff;
        transition: all 0.3s ease;
        box-shadow: 0 2px 15px rgba(0,0,0,0.05);
    }
    
    .card-modern:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    .img-cover {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .card-modern:hover .img-cover {
        transform: scale(1.05);
    }

    .list-group-modern .list-group-item {
        border: none;
        border-bottom: 1px solid #f0f0f0;
        padding: 1rem 1.25rem;
        transition: background-color 0.2s;
    }
    .list-group-modern .list-group-item:last-child {
        border-bottom: none;
    }
    .list-group-modern .list-group-item:hover {
        background-color: #f8fbfd;
    }
    .timeline-date {
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        color: #adb5bd;
        text-transform: uppercase;
    }

    /* Date Badge Floating for Grid */
    .date-badge-floating {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 8px;
        padding: 6px 12px;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        z-index: 2;
        line-height: 1;
    }
    .date-badge-day { font-size: 1.1rem; font-weight: 800; color: #333; display: block; }
    .date-badge-month { font-size: 0.7rem; font-weight: 600; color: #dc3545; text-transform: uppercase; }

    /* Animation Fade Up */
    .fade-in-up { animation: fadeInUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1); }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .carousel-item {
        height: 500px; 
        background-color: #212529;
        transition: transform 1.2s cubic-bezier(0.25, 1, 0.5, 1);
    }
    
    @media (max-width: 768px) {
        .carousel-item { height: 350px; }
    }

    .carousel-item img {
        height: 100%;
        width: 100%;
        object-fit: cover;
        opacity: 0.85;
    }

    /* Agar saat digeser tidak ada jeda putih */
    .carousel-inner {
        overflow: hidden; 
    }

    .carousel-overlay {
        position: absolute;
        bottom: 0; left: 0; right: 0;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.7) 0%, rgba(0,0,0,0.6) 60%, rgba(0,0,0,0) 100%);
        padding: 80px 50px 50px;
        border-radius: 0 0 50px 50px;
    }
</style>

<div class="container-fluid px-4 px-xl-5 py-5 fade-in-up">
    
    <div class="row g-4 mb-5">
        
        {{-- CAROUSEL UTAMA (Grid 9) --}}
        <div class="col-lg-12 col-xl-9">
            @if($sliderAnnouncements->count() > 0)
                {{-- PENTING: Class 'carousel-fade' DIHAPUS agar efeknya kembali ke SLIDE (Geser) --}}
                <div id="announcementCarousel" class="carousel slide shadow-lg rounded-4 overflow-hidden" data-bs-ride="carousel" data-bs-interval="6000">
                    
                    <div class="carousel-indicators">
                        @foreach($sliderAnnouncements as $key => $slide)
                            <button type="button" data-bs-target="#announcementCarousel" data-bs-slide-to="{{ $key }}" 
                                class="{{ $key == 0 ? 'active' : '' }}"></button>
                        @endforeach
                    </div>

                    <div class="carousel-inner">
                        @foreach($sliderAnnouncements as $key => $slide)
                            <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                @if($slide->image)
                                    <img src="{{ Storage::url($slide->image) }}" alt="{{ $slide->title }}">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100 bg-secondary text-white">
                                        <i class="bi bi-image fs-1"></i>
                                    </div>
                                @endif
                                
                                <div class="carousel-caption text-center p-0">
                                    <div class="carousel-overlay">
                                        <span class="badge bg-danger mb-3 px-3 py-2 rounded-pill fw-normal text-uppercase ls-1">Pengumuman Utama</span>
                                        <h1 class="fw-bold text-white text-shadow-strong display-5 mb-3">{{ $slide->title }}</h1>
                                        
                                        <p class="d-none d-lg-block text-white-50 fs-5 mb-4 col-lg-10 mx-auto">
                                            {{ \Illuminate\Support\Str::limit($slide->summary ?? strip_tags($slide->content), 150) }}
                                        </p>
                                        
                                        <a href="{{ route('announcement.show', $slide->id) }}" class="btn btn-light rounded-pill px-4 py-2 fw-bold shadow-sm">
                                            Baca Selengkapnya <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button class="carousel-control-prev" type="button" data-bs-target="#announcementCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#announcementCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            @else
                <div class="alert alert-light shadow-sm py-5 text-center rounded-4 border-0">
                    <h5 class="text-muted">Belum ada pengumuman utama.</h5>
                </div>
            @endif
        </div>

        {{-- SIDEBAR TIMELINE (Grid 3) --}}
        <div class="col-lg-12 col-xl-3">
            <div class="card card-modern h-100 rounded-4 overflow-hidden">
                <div class="card-header bg-white pt-4 pb-3 border-0 px-4">
                    <div class="d-flex align-items-center mb-0">
                        <div class="bg-primary rounded-pill me-2 mr-2" style="width: 4px; height: 24px;"></div>
                        <h5 class="fw-bold mb-0 text-dark">Terbaru</h5>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush list-group-modern">
                        @forelse($otherAnnouncements as $announcement)
                            <a href="{{ route('announcement.show', $announcement->id) }}" class="list-group-item text-decoration-none">
                                <div class="mb-1">
                                    <span class="timeline-date">
                                        {{ \Carbon\Carbon::parse($announcement->date)->translatedFormat('d F Y') }}
                                    </span>
                                </div>
                                <h6 class="mb-1 fw-bold text-dark lh-base" style="font-size: 0.95rem;">
                                    {{ \Illuminate\Support\Str::limit($announcement->title, 60) }}
                                </h6>
                            </a>
                        @empty
                            <div class="p-4 text-center text-muted small">Tidak ada pengumuman tambahan.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BAGIAN BAWAH: GRID TITLE --}}
    <div class="d-flex align-items-center mb-4 mt-5 px-1">
        <h3 class="fw-bold mb-0 text-dark">Semua Pengumuman</h3>
        <div class="ms-3 border-bottom flex-grow-1 border-secondary opacity-25"></div>
    </div>

    {{-- BAGIAN BAWAH: GRID CARDS --}}
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 row-cols-xl-5 g-4">
        @forelse($allAnnouncements as $announcement)
            <div class="col pt-4">
                <div class="card card-modern h-100 rounded-4">
                    {{-- Gambar dengan Date Badge --}}
                    <div class="position-relative img-wrapper" style="height: 190px;">
                        @if($announcement->image)
                            <img src="{{ Storage::url($announcement->image) }}" 
                                class="img-cover"
                                alt="{{ $announcement->title }}">
                        @else
                            <div class="bg-light h-100 w-100 d-flex align-items-center justify-content-center text-muted">
                                <i class="bi bi-newspaper fs-1"></i>
                            </div>
                        @endif
                        
                        <div class="date-badge-floating">
                            <span class="date-badge-day">{{ \Carbon\Carbon::parse($announcement->date)->format('d') }}</span>
                            <span class="date-badge-month">{{ \Carbon\Carbon::parse($announcement->date)->translatedFormat('M') }}</span>
                        </div>
                    </div>
                    
                    {{-- Card Content --}}
                    <div class="card-body d-flex flex-column p-3 pt-4">
                        <h6 class="card-title fw-bold mb-2 lh-sm">
                            <a href="{{ route('announcement.show', $announcement->id) }}" class="text-decoration-none text-dark stretched-link">
                                {{ \Illuminate\Support\Str::limit($announcement->title, 55) }}
                            </a>
                        </h6>
                        <p class="card-text text-muted small mb-3 flex-grow-1" style="line-height: 1.6;">
                            {{ \Illuminate\Support\Str::limit($announcement->summary, 75) }}
                        </p>
                        
                        <div class="d-flex align-items-center justify-content-between mt-auto pt-3 border-top border-light">
                            <small class="text-secondary" style="font-size: 0.75rem">
                                <i class="bi bi-person-circle me-1"></i> Admin
                            </small>
                            <span class="text-primary fw-semibold" style="font-size: 0.75rem">
                                Baca <i class="bi bi-chevron-right" style="font-size: 0.7rem"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="py-5 text-center text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
                    Belum ada pengumuman tersedia.
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-center mt-5 mb-5">
        {{ $allAnnouncements->links() }}
    </div>

</div>

@endsection