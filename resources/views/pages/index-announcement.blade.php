@extends('layouts.announcement')

@section('title', 'Pengumuman')

@section('main')
<div class="container-fluid px-4 py-5">
    
    {{-- Row atas --}}
    <div class="row g-3 mb-4">

        {{-- Pengumuman Utama --}}
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="ratio ratio-16x9">
                    {{-- <img src="https://via.placeholder.com/800x400" class="card-img-top object-fit-cover rounded-top" alt="Pengumuman Utama"> --}}
                </div>
                <div class="card-body">
                    <span class="badge bg-success mb-5">PENGUMUMAN</span>
                    <h3 class="fw-bold mb-2">Judul Pengumuman Utama</h3>
                    <small class="text-muted">20 Agustus 2025</small>
                    <p class="mt-3 text-secondary">
                        Ringkasan singkat pengumuman utama yang akan menarik perhatian pembaca.
                    </p>
                    <a href="#" class="btn btn-primary btn-sm mt-2">Baca Selengkapnya</a>
                </div>
            </div>
        </div>

        {{-- List pengumuman --}}
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-light fw-bold">
                    Pengumuman Lainnya
                </div>
                <ul class="list-group list-group-flush">
                    @foreach ([
                        ['date' => '19 Agustus 2025', 'title' => 'Judul Pengumuman Kedua'],
                        ['date' => '15 Agustus 2025', 'title' => 'Judul Pengumuman Ketiga'],
                        ['date' => '10 Agustus 2025', 'title' => 'Judul Pengumuman Keempat']
                    ] as $item)
                        <li class="list-group-item list-hover">
                            <small class="text-muted d-block mb-1">{{ $item['date'] }}</small>
                            <a href="#" class="fw-semibold text-decoration-none text-dark">
                                {{ $item['title'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

    </div>

    {{-- Grid bawah --}}
    <div class="row g-4">
        @foreach ([
            ['title' => 'Judul Pengumuman 1', 'date' => '05 Agustus 2025'],
            ['title' => 'Judul Pengumuman 2', 'date' => '02 Agustus 2025'],
            ['title' => 'Judul Pengumuman 3', 'date' => '29 Juli 2025'],
            ['title' => 'Judul Pengumuman 4', 'date' => '25 Juli 2025'],
            ['title' => 'Judul Pengumuman 5', 'date' => '30 Juli 2025'],
            ['title' => 'Judul Pengumuman 6', 'date' => '28 Juli 2025'],
            ['title' => 'Judul Pengumuman 7', 'date' => '05 Agustus 2025'],
            ['title' => 'Judul Pengumuman 8', 'date' => '02 Agustus 2025']
        ] as $item)
            <div class="col-12 col-sm-6 col-lg-3 mb-4">
                <div class="card shadow-sm border-0 h-100 list-hover">
                    <div class="ratio ratio-16x9">
                        {{-- <img src="h..." class="card-img-top object-fit-cover" alt="Pengumuman"> --}}
                    </div>
                    <div class="card-body">
                        <h6 class="fw-bold mb-1">{{ $item['title'] }}</h6>
                        <small class="text-muted">{{ $item['date'] }}</small>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection
