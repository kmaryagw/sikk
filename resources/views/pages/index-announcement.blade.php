@extends('layouts.announcement')

@section('title', 'Pengumuman')

@section('main')

<style>
    body {
        background-color: #f8f9fa;
    }

    .text-shadow-strong { text-shadow: 0 2px 4px rgba(0,0,0,0.7); }

    .card-modern {
        border: none;
        background: #ffffff;
        transition: all 0.3s ease;
        box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        border-radius: 1rem;
        overflow: hidden;
    }
    
    .card-modern:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }
    
    .img-wrapper {
        overflow: hidden;
        border-radius: 1rem 1rem 0 0;
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

    .list-group-modern .list-group-item {
        border: none; border-bottom: 1px solid #f0f0f0; padding: 1rem 1.25rem; transition: background-color 0.2s;
    }
    .list-group-modern .list-group-item:hover { background-color: #f8fbfd; }
    .timeline-date { font-size: 0.75rem; font-weight: 600; letter-spacing: 0.5px; color: #adb5bd; text-transform: uppercase; }

    .carousel-item { height: 500px; background-color: #212529; }
    @media (max-width: 768px) { .carousel-item { height: 350px; } }
    .carousel-item img { height: 100%; width: 100%; object-fit: cover; opacity: 0.85; }
    .carousel-overlay {
        position: absolute; bottom: 0; left: 0; right: 0;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.8) 0%, rgba(0,0,0,0.6) 60%, rgba(0,0,0,0) 100%);
        padding: 80px 50px 50px; border-radius: 0 0 50px 50px;
    }

    .date-badge-floating {
        position: absolute; top: 15px; right: 15px;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 12px; padding: 8px 14px; text-align: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15); z-index: 2; line-height: 1;
    }
    .date-badge-day { font-size: 1.2rem; font-weight: 800; color: #212529; display: block; }
    .date-badge-month { font-size: 0.75rem; font-weight: 700; color: #dc3545; text-transform: uppercase; letter-spacing: 1px; }

    .modal-fancy .modal-content {
        border: none; border-radius: 24px; overflow: hidden;
    }
    .modal-hero-container {
        position: relative; height: 350px; width: 100%; background-color: #f0f0f0;
    }
    .modal-hero-img {
        width: 100%; height: 100%; object-fit: cover;
    }
    .btn-close-floating {
        position: absolute; top: 20px; right: 20px;
        background-color: rgba(255,255,255,0.8); backdrop-filter: blur(5px);
        width: 40px; height: 40px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        border: none; transition: all 0.2s; z-index: 10;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    .btn-close-floating:hover { background-color: white; transform: rotate(90deg); }
    
    .modal-body-content { padding: 40px; }
    .modal-meta-badge {
        display: inline-flex; align-items: center;
        background: #f8f9fa; padding: 6px 16px; border-radius: 50px;
        font-size: 0.85rem; color: #6c757d; font-weight: 600; margin-bottom: 20px;
    }
    .modal-title-custom { font-size: 2rem; font-weight: 800; line-height: 1.2; letter-spacing: -0.5px; margin-bottom: 1.5rem; color: #1a1e21; }
    .modal-text { font-size: 1.05rem; line-height: 1.8; color: #495057; text-align: justify; }

    .modal.fade .modal-dialog {
        transform: scale(0.9); transition: transform 0.3s ease-out;
    }
    .modal.show .modal-dialog { transform: scale(1); }

    /* carousel */
    /* Membuat scrollbar di dalam modal lebih tipis dan rapi */
    .modal-dialog-scrollable .modal-body::-webkit-scrollbar {
        width: 6px;
    }
    .modal-dialog-scrollable .modal-body::-webkit-scrollbar-track {
        background: #f1f1f1; 
    }
    .modal-dialog-scrollable .modal-body::-webkit-scrollbar-thumb {
        background: #ccc; 
        border-radius: 3px;
    }
    .modal-dialog-scrollable .modal-body::-webkit-scrollbar-thumb:hover {
        background: #aaa; 
    }

    /* Mengatur jarak antar paragraf di dalam konten agar nyaman dibaca */
    .announcement-content p {
        margin-bottom: 1.5rem;
    }

    /* Letter Spacing untuk meta data */
    .ls-1 {
        letter-spacing: 1px;
    }

</style>

<div class="container-fluid px-4 px-xl-5 py-5 fade-in-up">
    
    <div class="row g-4 mb-5">
        <div class="col-lg-12 col-xl-9">
            @if($sliderAnnouncements->count() > 0)
                <div id="announcementCarousel" class="carousel slide carousel-fade shadow-lg rounded-4 overflow-hidden" data-bs-ride="carousel" data-bs-interval="6000">
                    <div class="carousel-indicators">
                        @foreach($sliderAnnouncements as $key => $slide)
                            <button type="button" data-bs-target="#announcementCarousel" data-bs-slide-to="{{ $key }}" class="{{ $key == 0 ? 'active' : '' }}"></button>
                        @endforeach
                    </div>
                    <div class="carousel-inner">
                        @foreach($sliderAnnouncements as $key => $slide)
                            <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                @if($slide->image)
                                    <img src="{{ Storage::url($slide->image) }}" alt="{{ $slide->title }}">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100 bg-secondary text-white"><i class="bi bi-image fs-1"></i></div>
                                @endif
                                <div class="carousel-caption text-center p-0">
                                    <div class="carousel-overlay">
                                        <span class="badge bg-danger mb-3 px-3 py-2 rounded-pill fw-normal text-uppercase ls-1">Pengumuman Utama</span>
                                        <h1 class="fw-bold text-white text-shadow-strong display-5 mb-3">{{ $slide->title }}</h1>
                                        <p class="d-none d-lg-block text-white-50 fs-5 mb-4 col-lg-10 mx-auto">
                                            {{ \Illuminate\Support\Str::limit($slide->summary ?? strip_tags($slide->content), 150) }}
                                        </p>
                                        <button type="button" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $slide->id }}" class="btn btn-light rounded-pill px-4 py-2 fw-bold shadow-sm">
                                            Baca Selengkapnya <i class="bi bi-arrow-right ms-1"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#announcementCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
                    <button class="carousel-control-next" type="button" data-bs-target="#announcementCarousel" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
                </div>
                @foreach($sliderAnnouncements as $item)
                <div class="modal fade" id="modalDetail{{ $item->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $item->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                            
                            <div class="position-relative p-0">
                                @if($item->image)
                                    <img src="{{ Storage::url($item->image) }}" 
                                        class="w-100 object-fit-cover" 
                                        style="height: 350px; display: block;" 
                                        alt="{{ $item->title }}">
                                    
                                    <div class="position-absolute top-0 start-0 w-100 h-100" 
                                        style="background: linear-gradient(to bottom, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0) 30%); pointer-events: none;">
                                    </div>
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 150px;">
                                        <i class="bi bi-card-image text-muted display-4"></i>
                                    </div>
                                @endif

                                {{-- <button type="button" 
                                        class="btn btn-light btn-sm rounded-circle shadow-sm position-absolute top-0 end-0 m-3 d-flex align-items-center justify-content-center"
                                        style="width: 35px; height: 35px; z-index: 10;" 
                                        data-bs-dismiss="modal" 
                                        aria-label="Close">
                                    <i class="bi bi-x-lg text-dark"></i>
                                </button> --}}
                            </div>

                            <div class="modal-body p-4 p-md-5">
                                
                                <div class="d-flex align-items-center mb-3 text-muted small text-uppercase fw-bold ls-1">
                                    <span class="text-primary me-3 mr-2">
                                        <i class="bi bi-megaphone-fill me-1"></i> Pengumuman
                                    </span>
                                    <span>
                                        <i class="bi bi-calendar3 me-1"></i> 
                                        {{ \Carbon\Carbon::parse($item->date)->translatedFormat('d F Y') }}
                                    </span>
                                </div>

                                <h2 class="fw-bold text-dark mb-4 lh-sm" id="modalLabel{{ $item->id }}">
                                    {{ $item->title }}
                                </h2>

                                <hr class="border-secondary opacity-10 mb-4">

                                <div class="announcement-content text-dark fs-6 lh-lg" style="text-align: justify;">
                                    {!! $item->summary ?? $item->content !!}
                                </div>
                            </div>

                            <div class="modal-footer border-0 bg-light px-4 py-3 justify-content-between">
                                <span class="text-muted small fst-italic">
                                    Diterbitkan oleh Admin Akademik
                                </span>
                                <button type="button" class="btn btn-outline-dark rounded-pill px-4 fw-bold btn-sm" data-bs-dismiss="modal">
                                    Tutup
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="alert alert-light shadow-sm py-5 text-center rounded-4 border-0"><h5 class="text-muted">Belum ada pengumuman utama.</h5></div>
            @endif
        </div>

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
                            {{-- Trigger Modal Sidebar --}}
                            <a href="#" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $announcement->id }}" class="list-group-item text-decoration-none">
                                <div class="mb-1"><span class="timeline-date">{{ \Carbon\Carbon::parse($announcement->date)->translatedFormat('d F Y') }}</span></div>
                                <h6 class="mb-1 fw-bold text-dark lh-base" style="font-size: 0.95rem;">{{ \Illuminate\Support\Str::limit($announcement->title, 60) }}</h6>
                            </a>
                        @empty
                            <div class="p-4 text-center text-muted small">Tidak ada pengumuman tambahan.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex align-items-center mb-2 mt-0 px-1">
        <h3 class="fw-bold mb-0 text-dark">Semua Pengumuman</h3>
        {{-- <div class="ms-3 border-bottom flex-grow-1 border-secondary opacity-25"></div> --}}
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 row-cols-xl-5 g-4">
        @forelse($allAnnouncements as $announcement)
            <div class="col pt-4">
                <div class="card card-modern h-100 rounded-4">
                    {{-- Wrapper Gambar --}}
                    <div class="position-relative img-wrapper" style="height: 190px;">
                        @if($announcement->image)
                            <img src="{{ Storage::url($announcement->image) }}" class="img-cover" alt="{{ $announcement->title }}">
                        @else
                            <div class="bg-light h-100 w-100 d-flex align-items-center justify-content-center text-muted"><i class="bi bi-newspaper fs-1"></i></div>
                        @endif
                        <div class="date-badge-floating">
                            <span class="date-badge-day">{{ \Carbon\Carbon::parse($announcement->date)->format('d') }}</span>
                            <span class="date-badge-month">{{ \Carbon\Carbon::parse($announcement->date)->translatedFormat('M') }}</span>
                        </div>
                    </div>
                    
                    {{-- Content --}}
                    <div class="card-body d-flex flex-column p-3 pt-4">
                        <h6 class="card-title fw-bold mb-2 lh-sm">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $announcement->id }}" class="text-decoration-none text-dark stretched-link">
                                {{ \Illuminate\Support\Str::limit($announcement->title, 55) }}
                            </a>
                        </h6>
                        <p class="card-text text-muted small mb-3 flex-grow-1" style="line-height: 1.6;">
                            {{ \Illuminate\Support\Str::limit($announcement->summary, 75) }}
                        </p>
                        
                        <div class="d-flex align-items-center justify-content-between mt-auto pt-3 border-top border-light">
                            {{-- <small class="text-secondary" style="font-size: 0.75rem"><i class="bi bi-person-circle me-1"></i> Admin</small> --}}
                            <span class="text-primary fw-semibold" style="font-size: 0.75rem">
                                Baca Selengkapnya <i class="bi bi-box-arrow-in-up-right ms-1"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="py-5 text-center text-muted"><i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>Belum ada pengumuman tersedia.</div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-5 mb-5">
        {{ $allAnnouncements->links() }}
    </div>

</div>

@foreach($allAnnouncements as $announcement)
<div class="modal fade modal-fancy" id="modalDetail{{ $announcement->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content shadow-lg">
            
            {{-- Bagian Atas: HERO IMAGE --}}
            <div class="modal-hero-container">
                {{-- <button type="button" class="btn-close-floating" data-bs-dismiss="modal" aria-label="Close">
                    <i class="bi bi-x-lg text-dark"></i>
                </button> --}}

                @if($announcement->image)
                    <img src="{{ Storage::url($announcement->image) }}" class="modal-hero-img" alt="{{ $announcement->title }}">
                @else
                    <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-secondary text-white">
                        <div class="text-center">
                            <i class="bi bi-newspaper display-1 d-block mb-3 opacity-50"></i>
                            <span class="fs-5">Pengumuman</span>
                        </div>
                    </div>
                @endif
                
                <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 100px; background: linear-gradient(to top, #ffffff 0%, transparent 100%);"></div>
            </div>

            <div class="modal-body modal-body-content position-relative">
                
                <div class="modal-meta-badge">
                    <i class="bi bi-calendar-event me-2"></i> {{ \Carbon\Carbon::parse($announcement->date)->translatedFormat('l, d F Y') }}
                    {{-- <span class="mx-2">|</span> --}}
                    {{-- <i class="bi bi-person-circle me-2"></i> Admin --}}
                </div>

                {{-- Judul Besar --}}
                <h2 class="modal-title-custom">{{ $announcement->title }}</h2>

                {{-- Isi Berita --}}
                <div class="modal-text">
                    {!! $announcement->content ?? $announcement->summary !!}
                </div>

                <hr class="my-5 opacity-10">

                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-danger rounded-pill px-4 me-2 mr-3" data-bs-dismiss="modal">Tutup</button>
                    {{-- <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="bi bi-share-fill me-2"></i> Bagikan
                    </button> --}}
                </div>
            </div>

        </div>
    </div>
</div>
@endforeach

@endsection