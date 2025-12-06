@extends('layouts.app')

@section('title', 'Laporan IKU/IKT')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/circular-progress-bar.css') }}">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

    <style>
        /* CSS UTAMA: Rata Tengah Vertikal & Horizontal */
        .table td, .table th {
            vertical-align: middle !important; /* Rata tengah vertikal */
            text-align: center !important;     /* Rata tengah horizontal */
        }

        /* PENGECUALIAN: Agar kolom Indikator yang panjang tetap rapi rata kiri */
        .table td.text-left {
            text-align: left !important;
        }

        /* Style tambahan untuk Header DataTables agar ikon sorting pas */
        table.dataTable thead .sorting:before, 
        table.dataTable thead .sorting_asc:before, 
        table.dataTable thead .sorting_desc:before, 
        table.dataTable thead .sorting:after, 
        table.dataTable thead .sorting_asc:after, 
        table.dataTable thead .sorting_desc:after {
            bottom: 0.8em; 
        }
        
        /* Menghilangkan border bawah pada info datatables */
        .dataTables_info {
            padding: 1rem;
            font-weight: 600;
        }
    </style>
@endpush

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Laporan Monitoring Indikator Kinerja Utama/Tambahan</h1>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <form class="row g-2 align-items-center">
                    <div class="col-auto">
                        <input class="form-control" name="q" value="{{ request('q') }}" placeholder="Pencarian..." />
                    </div>
                
                    <div class="col-auto">
                        <select class="form-control" name="tahun">
                            <option value="">Pilih Tahun</option>
                            @foreach ($tahuns as $tahun)
                                <option value="{{ $tahun->th_id }}" {{ request('tahun') == $tahun->th_id ? 'selected' : '' }}>
                                    {{ $tahun->th_tahun }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-auto">
                        <select class="form-control" name="unit">
                            <option value="">Semua Unit Kerja</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->unit_id }}" {{ request('unit') == $unit->unit_id ? 'selected' : '' }}>
                                    {{ $unit->unit_nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-auto">
                        <select class="form-control" name="prodi">
                            <option value="">Semua Prodi</option>
                            @foreach ($prodis as $prodi)
                                <option value="{{ $prodi->prodi_id }}" 
                                    {{ request('prodi') == $prodi->prodi_id ? 'selected' : '' }}>
                                    {{ $prodi->nama_prodi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                
                    <div class="col-auto">
                        <button class="btn btn-info"><i class="fa-solid fa-search"></i> Cari</button>
                    </div>
                    @php
                        $query = http_build_query([
                            'tahun' => request('tahun'),
                            'prodi' => request('prodi'),
                            'unit' => request('unit'),
                            'q' => request('q'),
                        ]);
                    @endphp

                    <div class="col-auto">
                        <a class="btn btn-success" href="{{ route('export-excel.iku') . '?' . $query }}" target="_blank">
                            <i class="fa-solid fa-file-excel"></i> Export Excel
                        </a>
                    </div>

                    <div class="col-auto">
                        <a class="btn btn-danger" href="{{ route('export-pdf.iku') . '?' . $query }}" target="_blank">
                            <i class="fa-solid fa-file-pdf"></i> Export PDF
                        </a>
                    </div>                   
                </form>                
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped m-0" id="table-laporan">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Tahun</th>
                            <th>Prodi</th>
                            <th style="width: 30%;">Indikator Kinerja</th>
                            <th style="width: 5%;">Target</th>
                            <th style="width: 15%;">Capaian</th> 
                            <th>Status</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @foreach ($target_capaians as $targetcapaian)
                        <tr>
                            <td style="padding: 1rem;">{{ $loop->iteration }}</td>
                            <td>{{ $targetcapaian->th_tahun }}</td>
                            <td>
                                {{-- Badge warna prodi --}}
                                <span class="badge">
                                    {{ $targetcapaian->nama_prodi }}
                                </span>
                            </td>
                            <td class="text-left" style="padding: 1.5rem;">
                                {{ $targetcapaian->ik_kode }} - {{ $targetcapaian->ik_nama }}
                            </td>                            

                            {{-- Target Capaian --}}
                            <td>
                                @php
                                    $ketercapaian = strtolower(optional($targetcapaian->indikatorKinerja)->ik_ketercapaian ?? '');
                                    $targetRaw = trim($targetcapaian->ti_target);
                                    $numericValue = (float) str_replace('%', '', $targetRaw);
                                    $progressColor = $numericValue == 0 ? '#dc3545' : '#28a745';
                                @endphp

                                @if ($ketercapaian === 'persentase' && is_numeric($numericValue))
                                    <div class="d-flex justify-content-center">
                                        <div class="ring-progress-wrapper">
                                            <div class="ring-progress" style="--value: {{ $numericValue }}; --progress-color: {{ $progressColor }};">
                                                <div class="ring-inner">
                                                    <span class="ring-text">{{ $numericValue }}%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @elseif ($ketercapaian === 'nilai' && is_numeric($targetRaw))
                                    <span class="badge badge-primary">{{ $targetRaw }}</span>
                                @elseif (in_array(strtolower($targetRaw), ['ada', 'tidak']))
                                    @if (strtolower($targetRaw) === 'ada')
                                        <span class="text-success"><i class="fa-solid fa-check-circle"></i> Ada</span>
                                    @else
                                        <span class="text-danger"><i class="fa-solid fa-times-circle"></i> Tidak</span>
                                    @endif
                                
                                @elseif ($ketercapaian === 'rasio')
                                    @php
                                        $formattedRasio = $targetRaw; // Default tampilkan apa adanya
                                        $cleaned = preg_replace('/\s*/', '', $targetRaw);
                                        if (preg_match('/^\d+:\d+$/', $cleaned)) {
                                            [$left, $right] = explode(':', $cleaned);
                                            $formattedRasio = $left . ' : ' . $right;
                                        }
                                    @endphp
                                    <span class="badge badge-info"><i class="fa-solid fa-balance-scale"></i>  {{ $formattedRasio }}</span>
                                @else
                                    {{ $targetRaw }}
                                @endif
                            </td>                          

                            {{-- Capaian --}}
                            <td>
                                @php
                                    $capaian = trim(optional($targetcapaian->monitoringDetail)->mtid_capaian ?? '');
                                    $ketercapaian = strtolower(optional($targetcapaian->indikatorKinerja)->ik_ketercapaian ?? '');
                                    $numericValue = (float) str_replace('%', '', $capaian);
                                    $progressColor = $numericValue == 0 ? '#dc3545' : '#28a745';
                                @endphp
                            

                                @if ($ketercapaian === 'persentase' && is_numeric($numericValue))
                                    <div class="d-flex justify-content-center">
                                        <div class="ring-progress-wrapper">
                                            <div class="ring-progress" style="--value: {{ $numericValue }}; --progress-color: {{ $progressColor }};">
                                                <div class="ring-inner">
                                                    <span class="ring-text">{{ $numericValue }}%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @elseif ($ketercapaian === 'nilai' && is_numeric($capaian))
                                    <span class="badge badge-primary"> {{ $capaian }}</span>
                                @elseif ($ketercapaian === 'rasio')
                                    @php
                                        $formattedRasio = $capaian;
                                        $cleaned = preg_replace('/\s*/', '', $capaian);
                                        if (preg_match('/^\d+:\d+$/', $cleaned)) {
                                            [$left, $right] = explode(':', $cleaned);
                                            $formattedRasio = $left . ' : ' . $right;
                                        }
                                    @endphp
                                    <span class="badge badge-info"><i class="fa-solid fa-balance-scale"></i>  {{ $formattedRasio }}</span>
                                @elseif ($ketercapaian === 'ketersediaan')
                                    @if (strtolower($capaian) === 'ada')
                                        <span class="text-success"><i class="fa-solid fa-check-circle"></i> Ada</span>
                                    @elseif (strtolower($capaian) === 'draft')
                                        <span class="text-warning"><i class="fa-solid fa-info-circle"></i> Draft</span>
                                    @else
                                        <span class="text-danger"><i class="fa-solid fa-times-circle"></i> Tidak Terlaksana</span>
                                    @endif
                                @elseif (!empty($capaian))
                                    <span class="badge badge-primary">{{ $capaian }}</span>
                                @else
                                    <span class="text-danger"><i class="fa-solid fa-times-circle"></i> Belum ada Capaian</span>
                                @endif
                            </td>                           

                            <td>
                                @php
                                    $capaian = optional($targetcapaian->monitoringDetail)->mtid_capaian ?? null;
                                    $target = $targetcapaian->ti_target;
                                    $jenis = optional($targetcapaian->indikatorKinerja)->ik_ketercapaian;
                                    
                                    $status = function_exists('hitungStatus') ? hitungStatus($capaian, $target, $jenis) : '-';
                                @endphp
                            

                                @if ($status === 'tercapai')
                                    <span class="text-success"><i class="fa-solid fa-check-circle"></i> Tercapai</span>
                                @elseif ($status === 'terlampaui')
                                    <span class="text-primary"><i class="fa-solid fa-arrow-up"></i> Terlampaui</span>
                                @elseif ($status === 'tidak tercapai')
                                    <span class="text-warning"><i class="fa-solid fa-info-circle"></i> Tidak Tercapai</span>
                                @elseif ($status === 'tidak terlaksana')
                                    <span class="text-danger"><i class="fa-solid fa-times-circle"></i> Tidak Terlaksana</span>
                                @else
                                    <span>Belum ada Status</span>
                                @endif
                            </td>    
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                @if ($target_capaians->isEmpty())
                    <div class="alert alert-light text-center mt-3">
                        Tidak ada data
                        @if (request('prodi'))
                            untuk <strong>Program Studi {{ $prodis->firstWhere('prodi_id', request('prodi'))?->nama_prodi ?? '' }}</strong>
                        @endif
                        @if (request('tahun'))
                            di Tahun <strong>{{ $tahuns->firstWhere('th_id', request('tahun'))?->th_tahun ?? '' }}</strong>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('library/simpleweather/jquery.simpleWeather.min.js') }}"></script>
    <script src="{{ asset('library/chart.js/dist/Chart.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
    <script src="{{ asset('library/summernote/dist/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('library/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/index-0.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            @if(!$target_capaians->isEmpty())
                $('#table-laporan').DataTable({
                    "paging": false,        
                    "searching": false,     
                    "ordering": true,       
                    "info": true,           
                    "autoWidth": false,     
                    "order": [[ 3, 'asc' ]] 
                });
            @endif
        });
    </script>
@endpush