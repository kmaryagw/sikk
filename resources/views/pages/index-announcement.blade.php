@extends('layouts.announcement')

@section('title', 'Pengumuman')

@section('main')
<div class="container-fluid px-4 py-5">
    
    {{-- Row atas --}}
    <div class="row g-3 mb-4">

        {{-- Pengumuman Utama --}}
        <div class="col-12 col-lg-8">
            @if($mainAnnouncement)
                <div class="card shadow-sm border-0 h-100">
                    <div class="ratio ratio-16x9">
                        @if($mainAnnouncement->image)
                            <img src="{{ Storage::url($mainAnnouncement->image) }}" 
                                class="card-img-top object-fit-cover rounded-top" 
                                alt="{{ $mainAnnouncement->title }}"
                                style="max-height: 300px; object-fit: cover; width: 100%;">
                        @endif
                    </div>
                    <div class="card-body">
                        <span class="badge bg-success mb-2">PENGUMUMAN</span>
                        <h3 class="fw-bold mb-2">{{ $mainAnnouncement->title }}</h3>
                        <small class="text-muted">
                            {{ \Carbon\Carbon::parse($mainAnnouncement->date)->translatedFormat('d F Y') }}
                        </small>
                        <p class="mt-3 text-secondary">
                            {{ \Illuminate\Support\Str::limit($mainAnnouncement->summary, 150) }}
                        </p>
                        <a href="{{ route('announcement.show', $mainAnnouncement->id) }}" 
                           class="btn btn-primary btn-sm mt-2">Baca Selengkapnya</a>
                    </div>
                </div>
            @else
                <div class="alert alert-info">Belum ada pengumuman utama.</div>
            @endif
        </div>

        {{-- List pengumuman lainnya --}}
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-light fw-bold">
                    Pengumuman Lainnya
                </div>
                <ul class="list-group list-group-flush">
                    @forelse($otherAnnouncements->take(5) as $announcement)
                        <li class="list-group-item list-hover">
                            <small class="text-muted d-block mb-1">
                                {{ \Carbon\Carbon::parse($announcement->date)->translatedFormat('d F Y') }}
                            </small>
                            <a href="{{ route('announcement.show', $announcement->id) }}" 
                               class="fw-semibold text-decoration-none text-dark">
                                {{ $announcement->title }}
                            </a>
                        </li>
                    @empty
                        <li class="list-group-item text-muted text-center">
                            Belum ada pengumuman lain.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

    </div>

    {{-- Grid bawah --}}
    <div class="row g-4">
        @forelse($allAnnouncements as $announcement)
            <div class="col-12 col-sm-6 col-lg-3 mb-4">
                <div class="card shadow-sm border-0 h-100 list-hover">
                    <div class="ratio ratio-16x9">
                        @if($announcement->image)
                            <img src="{{ asset('storage/' . $announcement->image) }}" 
                                 class="card-img-top object-fit-cover" 
                                 alt="{{ $announcement->title }}">
                        @endif
                    </div>
                    <div class="card-body">
                        <h6 class="fw-bold mb-1">{{ $announcement->title }}</h6>
                        <small class="text-muted">
                            {{ \Carbon\Carbon::parse($announcement->date)->translatedFormat('d F Y') }}
                        </small>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center text-muted">Belum ada pengumuman tersedia.</p>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $allAnnouncements->links() }}
    </div>

</div>
@endsection
