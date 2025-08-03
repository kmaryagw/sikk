@extends('layouts.app')

@section('title', 'Laporan IKU/IKT')

@push('style')
    <!-- Tambahkan CSS Libraries jika diperlukan -->
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/circular-progress-bar.css') }}">
@endpush

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Laporan Indikator Kinerja Utama/Tambahan</h1>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <form class="row g-2 align-items-center">
                    <div class="col-auto">
                        <input class="form-control" name="q" value="{{ request('q') }}" placeholder="Pencarian..." />
                    </div>
                
                    {{-- @if (Auth::user()->role == 'admin' || Auth::user()->role == 'prodi') --}}
                
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
                    {{-- @endif --}}

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
                    <div class="dropdown col-auto">
                        <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-file-excel"></i> Export Excel
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('export-excel.iku') . '?tahun=' . request('tahun') . '&prodi=' . request('prodi') . '&q=' . request('q') }}" target="_blank">Semua Prodi</a></li>
                            <li><a class="dropdown-item" href="{{ route('export-excel.iku.if')  . '?tahun=' . request('tahun') . '&prodi=' . request('prodi') . '&q=' . request('q')}}" target="_blank">Informatika</a></li>
                            <li><a class="dropdown-item" href="{{ route('export-excel.iku.rsk')  . '?tahun=' . request('tahun') . '&prodi=' . request('prodi') . '&q=' . request('q')}}" target="_blank">Rekayasa Sistem Komputer</a></li>
                            <li><a class="dropdown-item" href="{{ route('export-excel.iku.bd')  . '?tahun=' . request('tahun') . '&prodi=' . request('prodi') . '&q=' . request('q')}}" target="_blank">Bisnis Digital</a></li>
                            <li><a class="dropdown-item" href="{{ route('export-excel.iku.dkv')  . '?tahun=' . request('tahun') . '&prodi=' . request('prodi') . '&q=' . request('q')}}" target="_blank">Desain Komunikasi Visual</a></li>
                        </ul>
                    </div>                  
                    
                    @php
                        $query = http_build_query([
                            'tahun' => request('tahun'),
                            'q' => request('q'),
                        ]);
                    @endphp

                    <div class="col-auto">
                        <div class="dropdown">
                            <button class="btn btn-danger dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-file-pdf"></i> Export PDF
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('export-pdf.iku') . '?' . $query }}" target="_blank">Semua Prodi</a></li>
                                <li><a class="dropdown-item" href="{{ route('export-pdf.iku.if') . '?' . $query }}" target="_blank">Informatika</a></li>
                                <li><a class="dropdown-item" href="{{ route('export-pdf.iku.rsk') . '?' . $query }}" target="_blank">Rekayasa Sistem Komputer</a></li>
                                <li><a class="dropdown-item" href="{{ route('export-pdf.iku.bd') . '?' . $query }}" target="_blank">Bisnis Digital</a></li>
                                <li><a class="dropdown-item" href="{{ route('export-pdf.iku.dkv') . '?' . $query }}" target="_blank">Desain Komunikasi Visual</a></li>
                            </ul>
                        </div>
                    </div>                    
                </form>                
            </div>

            <div class="table-responsive text-center">
                <table class="table table-hover table-bordered table-striped m-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tahun</th>
                            <th>Prodi</th>
                            <th style="width: 30%;">Indikator Kinerja</th>
                            <th style="width: 10%;">Target Capaian</th>
                            <th style="width: 15%;">Capaian</th> 
                            <th>Status</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @php $no = $target_capaians->firstItem(); @endphp
                        @foreach ($target_capaians as $targetcapaian)
                        <tr>
                            <td style="padding: 3rem;">{{ $no++ }}</td>
                            <td>{{ $targetcapaian->th_tahun }}</td>
                            <td>{{ $targetcapaian->nama_prodi }}</td>
                            <td>{{ $targetcapaian->ik_nama }}</td>
                            
                            {{-- Target Capaian --}}
                            <td>
                                @php
                                    $ketercapaian = strtolower(optional($targetcapaian->indikatorKinerja)->ik_ketercapaian ?? '');
                                    $targetRaw = trim($targetcapaian->ti_target);
                                    $numericValue = (float) str_replace('%', '', $targetRaw);
                                    $progressColor = $numericValue == 0 ? '#dc3545' : '#28a745';
                                @endphp
                            
                                @if ($ketercapaian === 'persentase' && is_numeric($numericValue))
                                    <div class="ring-progress-wrapper">
                                        <div class="ring-progress" style="--value: {{ $numericValue }}; --progress-color: {{ $progressColor }};">
                                            <div class="ring-inner">
                                                <span class="ring-text">{{ $numericValue }}%</span>
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
                                        $formattedRasio = 'Format salah';
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
                                    <div class="ring-progress-wrapper">
                                        <div class="ring-progress" style="--value: {{ $numericValue }}; --progress-color: {{ $progressColor }};">
                                            <div class="ring-inner">
                                                <span class="ring-text">{{ $numericValue }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                @elseif ($ketercapaian === 'nilai' && is_numeric($capaian))
                                    <span class="badge badge-primary"> {{ $capaian }}</span>
                                @elseif ($ketercapaian === 'rasio')
                                    @php
                                        $formattedRasio = 'Format salah';
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
                                        <span class="text-danger"><i class="fa-solid fa-times-circle"></i> Tidak Valid</span>
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
                                    $status = hitungStatus($capaian, $target, $jenis);
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
                        @if ($target_capaians->isEmpty())
                            @php
                                $tahunText = $tahuns->firstWhere('th_id', request('tahun'))?->th_tahun ?? null;
                                $prodiText = $prodis->firstWhere('prodi_id', request('prodi'))?->nama_prodi ?? null;
                            @endphp
                            <tr>
                                <td colspan="12" class="text-center alert alert-danger m-0">
                                    Tidak ada data
                                    @if ($prodiText)
                                        untuk <strong>Program Studi {{ $prodiText }}</strong>
                                    @endif
                                    @if ($tahunText)
                                        di Tahun <strong>{{ $tahunText }}</strong>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            @if ($target_capaians->hasPages())
                <div class="card-footer">
                    {{ $target_capaians->links('pagination::bootstrap-5') }}
                </div>
            @endif
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

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/index-0.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush