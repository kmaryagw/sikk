@extends('layouts.app')
@section('title', 'Target Capaian')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/circular-progress-bar.css') }}">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    
    <style>
        .table td, .table th {
            vertical-align: middle !important; 
            text-align: center !important;     
        }

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
                <h1>Daftar Target Indikator</h1>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <form class="row g-2 align-items-center">
                        
                        {{-- Filter Tahun --}}
                        @if (Auth::user()->role== 'admin' || Auth::user()->role == 'prodi')
                        <div class="col-auto">
                            <select class="form-control" name="tahun">
                                <option value="" disabled selected>Tahun</option>
                                @foreach ($tahun as $th)
                                    <option value="{{ $th->th_id }}" {{ request('tahun') == $th->th_id ? 'selected' : '' }}>
                                        {{ $th->th_tahun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        
                        {{-- Filter Unit Kerja (Hanya muncul jika variabel dikirim dari controller) --}}
                        <div class="col-auto">
                            <select class="form-control" name="unit_kerja">
                                <option value="">Semua Unit Kerja</option>
                                @foreach ($unitKerjas as $unit)
                                    <option value="{{ $unit->unit_id }}" 
                                        {{ request('unit_kerja') == $unit->unit_id ? 'selected' : '' }}>
                                        {{ $unit->unit_nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-auto">
                            <input class="form-control" name="q" value="{{ $q }}" placeholder="Pencarian..." />
                        </div>
                        
                        <div class="col-auto">
                            <button class="btn btn-info"><i class="fa-solid fa-search"></i> Cari</button>
                        </div>

                        @if (Auth::user()->role== 'admin' || Auth::user()->role == 'prodi')
                        <div class="col-auto">
                            <a class="btn btn-primary" href="{{ route('targetcapaianprodi.create') }}"><i class="fa-solid fa-plus"></i> Isi / Ubah Target</a>
                        </div>
                        @endif
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped m-0" id="table-target">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Tahun</th>
                                <th>Prodi</th>
                                <th style="width : 39%">Indikator Kinerja</th>
                                <th>Jenis</th>
                                <th>Nilai Baseline</th>
                                <th>Target</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($target_capaians as $targetcapaian)
                                <tr>
                                    {{-- Menggunakan $loop->iteration karena pagination dihapus --}}
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $targetcapaian->th_tahun }}</td>
                                    <td><span class="badge">{{ $targetcapaian->nama_prodi }}</span></td>
                                    <td style="padding : 1.5rem;" class="text-left">
                                        {{ $targetcapaian->ik_kode }} - {{ $targetcapaian->ik_nama }}
                                    </td>
                                    <td>
                                        @if (strtolower($targetcapaian->ik_jenis == 'IKU'))
                                            <span class="badge badge-success">IKU</span>
                                        @elseif (strtolower($targetcapaian->ik_jenis == 'IKT'))
                                            <span class="badge badge-primary">IKT</span>
                                        @elseif (strtolower($targetcapaian->ik_jenis == 'IKU/IKT'))
                                            <span class="badge badge-danger">IKU/IKT</span>
                                        @else
                                            <span class="badge badge-secondary">Tidak Diketahui</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            // --- LOGIC BASELINE ---
                                            $ketercapaian = strtolower((string) ($targetcapaian->indikatorKinerja->ik_ketercapaian ?? ''));
                                            $bt = trim((string) ($targetcapaian->baseline_tahun ?? ''));
                                            $baselineRaw = $bt !== '' ? $bt : '0';
                                            $cleanNum = str_replace(['%', ' '], '', $baselineRaw);
                                            $baselineValue = is_numeric($cleanNum) ? (float) $cleanNum : null;
                                            $progressColor = ($baselineValue !== null && $baselineValue == 0) ? '#dc3545' : '#28a745';
                                        @endphp

                                        {{-- Visualisasi Baseline --}}
                                        @if ($ketercapaian === 'persentase' && $baselineValue !== null)
                                            <div class="d-flex justify-content-center">
                                                <div class="ring-progress-wrapper">
                                                    <div class="ring-progress" style="--value: {{ $baselineValue }}; --progress-color: {{ $progressColor }};">
                                                        <div class="ring-inner">
                                                            <span class="ring-text">{{ $baselineValue }}%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif ($ketercapaian === 'nilai' && is_numeric($cleanNum))
                                            <span class="badge badge-primary">{{ $baselineRaw }}</span>
                                        @elseif (in_array(strtolower($baselineRaw), ['ada', 'draft']))
                                            @if (strtolower($baselineRaw) === 'ada')
                                                <span class="text-success"><i class="fa-solid fa-check-circle"></i> Ada</span>
                                            @else
                                                <span class="text-warning"><i class="fa-solid fa-info-circle"></i> Draft</span>
                                            @endif
                                        @elseif ($ketercapaian === 'rasio')
                                            @php
                                                $formattedRasio = $baselineRaw;
                                                $cleaned = preg_replace('/\s*/', '', $baselineRaw);
                                                if (preg_match('/^\d+:\d+$/', $cleaned)) {
                                                    [$a, $b] = explode(':', $cleaned);
                                                    $formattedRasio = "{$a} : {$b}";
                                                }
                                            @endphp
                                            <span class="badge badge-info"><i class="fa-solid fa-balance-scale"></i> {{ $formattedRasio }}</span>
                                        @else
                                            {{ $baselineRaw }}
                                        @endif
                                    </td>

                                    <td>
                                        @php
                                            // --- LOGIC TARGET ---
                                            $ketercapaian = strtolower($targetcapaian->indikatorKinerja->ik_ketercapaian ?? '');
                                            $targetRaw = trim($targetcapaian->ti_target ?? '');
                                            $numericValue = (float) str_replace('%', '', $targetRaw);
                                            $progressColor = $numericValue == 0 ? '#dc3545' : '#28a745';
                                        @endphp

                                        {{-- Visualisasi Target --}}
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
                                        @elseif (in_array(strtolower($targetRaw), ['ada', 'draft']))
                                            @if (strtolower($targetRaw) === 'ada')
                                                <span class="text-success"><i class="fa-solid fa-check-circle"></i> Ada</span>
                                            @else
                                                <span class="text-warning"><i class="fa-solid fa-info-circle"></i> Draft</span>
                                            @endif
                                        @elseif ($ketercapaian === 'rasio')
                                            @php
                                                $formattedRasio = $targetRaw;
                                                $cleaned = preg_replace('/\s*/', '', $targetRaw);
                                                if (preg_match('/^\d+:\d+$/', $cleaned)) {
                                                    [$left, $right] = explode(':', $cleaned);
                                                    $formattedRasio = $left . ' : ' . $right;
                                                }
                                            @endphp
                                            <span class="badge badge-info"><i class="fa-solid fa-balance-scale"></i> {{ $formattedRasio }} </span>
                                        @else
                                            {{ $targetRaw }}
                                        @endif
                                    </td>  
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            {{-- Tombol Edit --}}
                                            <a href="{{ route('targetcapaianprodi.edit', $targetcapaian->ti_id) }}" class="btn btn-warning btn-sm">
                                                <i class="fa-solid fa-pen-to-square"></i> Edit
                                            </a>
                                        </div>
                                    </td>                                                                                                         
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if ($target_capaians->isEmpty())
                        <div class="alert alert-light text-center mt-3">
                            Target capaian tahun {{ $tahunLabel }} tidak ditemukan atau belum diset.
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraries -->
    <script src="{{ asset('library/simpleweather/jquery.simpleWeather.min.js') }}"></script>
    <script src="{{ asset('library/chart.js/dist/Chart.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
    <script src="{{ asset('library/summernote/dist/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('library/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/index-0.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            @if(!$target_capaians->isEmpty())
                $('#table-target').DataTable({
                    "paging": false,        
                    "searching": false,     
                    "ordering": true,       
                    "info": true,           
                    "autoWidth": false,
                    "order": [[ 3, 'asc' ]] 
                });
            @endif
        });

        function confirmDelete(event, formid) {
            event.preventDefault();
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus data!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + formid).submit();
                }
            })
        }
    </script>
@endpush