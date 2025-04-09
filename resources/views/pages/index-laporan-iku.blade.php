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
                    <div class="col-auto">
                        <a href="{{ route('export-excel.iku') }}" class="btn btn-success">
                            <i class="fa-solid fa-file-excel"></i> Export Excel
                        </a>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('export-pdf.iku') }}" class="btn btn-danger">
                            <i class="fa-solid fa-file-pdf"></i> Export PDF
                        </a>
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
                            <th>Keterangan</th>
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
                                    $targetValue = trim($targetcapaian->ti_target);
                                    $numericValue = (float) str_replace('%', '', $targetValue);
                                    $progressColor = $numericValue == 0 ? '#dc3545' : '#28a745'; // Bisa diatur dinamis
                                @endphp
                            
                                @if ($ketercapaian === 'persentase' && is_numeric($numericValue))
                                    <div class="ring-progress-wrapper">
                                        <div class="ring-progress" style="--value: {{ $numericValue }}; --progress-color: {{ $progressColor }};">
                                            <div class="ring-inner">
                                                <span class="ring-text">{{ $numericValue }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                @elseif ($ketercapaian === 'nilai' && is_numeric($targetValue))
                                    <span class="badge badge-primary">{{ $targetValue }}</span>
                                @elseif (in_array(strtolower($targetValue), ['ada', 'tidak']))
                                    @if (strtolower($targetValue) === 'ada')
                                        <span class="text-success"><i class="fa-solid fa-check-circle"></i> Ada</span>
                                    @else
                                        <span class="text-danger"><i class="fa-solid fa-times-circle"></i> Tidak</span>
                                    @endif
                                
                                @elseif ($ketercapaian === 'rasio')
                                    <span class="badge badge-info"><i class="fa-solid fa-balance-scale"></i> {{ $targetValue }}</span> 
                                @else
                                    {{ $targetValue }}
                                @endif
                            </td>
                            

                            {{-- Capaian --}}
                            <td>
                                @php
                                    $capaian = optional($targetcapaian->monitoringDetail)->mtid_capaian;
                                    $ketercapaian = optional($targetcapaian->indikatorKinerja)->ik_ketercapaian;
                                    $numericValue = (float) str_replace('%', '', $capaian);
                                    $progressColor = $numericValue == 0 ? '#dc3545' : '#28a745'; // merah jika 0, hijau jika ada nilai
                                @endphp

                                @if(strpos($capaian, '%') !== false)
                                    <div class="ring-progress-wrapper">
                                        <div class="ring-progress" style="--value: {{ $numericValue }}; --progress-color: {{ $progressColor }};">
                                            <div class="ring-inner">
                                                <span class="ring-text">{{ $numericValue }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                @elseif(is_numeric($capaian) && $ketercapaian == 'nilai')
                                    <span class="badge badge-primary"><i class="fa-solid fa-circle"></i> {{ $capaian }}</span>
                                @elseif(preg_match('/^\d+:\d+$/', $capaian))
                                    <span class="badge badge-info"><i class="fa-solid fa-balance-scale"></i> {{ $capaian }}</span>
                                @elseif(strtolower($capaian) === 'ada')
                                    <span class="text-success"><i class="fa-solid fa-check-circle"></i> Ada</span>
                                @elseif(strtolower($capaian) === 'draft')
                                    <span class="text-warning"><i class="fa-solid fa-info-circle"></i> Draft</span>
                                @elseif(!empty($capaian))
                                    <span class="badge badge-primary">{{ $capaian }}</span>
                                @else
                                    <span class="text-danger"><i class="fa-solid fa-times-circle"></i> Belum ada Capaian</span>
                                @endif
                            </td>

                            <td>
                                @php
                                    $status = strtolower(optional($targetcapaian->monitoringDetail)->mtid_status ?? '');
                                @endphp
                            
                                @if ($status === 'tercapai')
                                    <span class="text-success"><i class="fa-solid fa-check-circle"></i> Tercapai</span>
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
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data</td>
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

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/index-0.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush