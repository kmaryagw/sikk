@extends('layouts.app')
@section('title','SPMI')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/circular-progress-bar.css') }}">
    
    <style>
        .keterangan-content {
            white-space: pre-line;    
            text-align: left;
            word-break: break-word;   
            overflow-wrap: anywhere;
        }

        .table-responsive {
            max-height: 50rem;  
            overflow-y: auto;    
        }

        .table thead th {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #f8f9fa !important; 
        }

        .table td, .table th {
            text-align: center;         
            vertical-align: middle;     
        }

        .dataTables_info {
            padding: 1rem;
            font-weight: 600;
        }

        /* Menjamin modal selalu di atas backdrop */
        .modal {
            z-index: 1060 !important;
        }
        .modal-backdrop {
            z-index: 1050 !important;
        }
        
        /* Mempercantik tampilan konten modal */
        .keterangan-content {
            white-space: pre-line;
            font-size: 1rem;
            color: #444;
            line-height: 1.6;
        }

        .modal-content {
            border-radius: 12px;
        }

        .modal-header {
            border-radius: 12px 12px 0 0;
        }

        /* Animasi halus */
        .modal.fade .modal-dialog {
            transform: scale(0.9);
            transition: transform 0.2s ease-out;
        }
        .modal.show .modal-dialog {
            transform: scale(1);
        }
    </style>
@endpush

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    @if (Auth::user()->role == 'admin' || Auth::user()->role == 'fakultas' || Auth::user()->role == 'prodi')
                        <h1 class="mb-0">Detail Monitoring Indikator Kinerja</h1>
                    @else
                        <h1 class="mb-0">Detail Capaian Indikator Kinerja</h1>
                    @endif
                    
                    <div class="d-flex align-items-center"> 
                        @if (Auth::user()->role == 'admin' || Auth::user()->role == 'fakultas' || Auth::user()->role == 'prodi')
                            {{-- Tombol Export PDF --}}
                            <div class="dropdown mr-3">
                                <button class="btn btn-danger btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" target="_blank">
                                    <i class="fa-solid fa-file-pdf"></i> Export PDF
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" target="_blank" href="{{ route('monitoringiku.export-pdf-detail', ['mti_id' => $Monitoringiku->mti_id, 'type' => 'penetapan', 'unit_kerja' => request('unit_kerja'), 'q' => request('q')]) }}">Penetapan</a></li>
                                    <li><a class="dropdown-item" target="_blank" href="{{ route('monitoringiku.export-pdf-detail', ['mti_id' => $Monitoringiku->mti_id, 'type' => 'pelaksanaan', 'unit_kerja' => request('unit_kerja'), 'q' => request('q')]) }}">Pelaksanaan</a></li>
                                    <li><a class="dropdown-item" target="_blank" href="{{ route('monitoringiku.export-pdf-detail', ['mti_id' => $Monitoringiku->mti_id, 'type' => 'evaluasi', 'unit_kerja' => request('unit_kerja'), 'q' => request('q')]) }}">Evaluasi</a></li>
                                    <li><a class="dropdown-item" target="_blank" href="{{ route('monitoringiku.export-pdf-detail', ['mti_id' => $Monitoringiku->mti_id, 'type' => 'pengendalian', 'unit_kerja' => request('unit_kerja'), 'q' => request('q')]) }}">Pengendalian</a></li>
                                    <li><a class="dropdown-item" target="_blank" href="{{ route('monitoringiku.export-pdf-detail', ['mti_id' => $Monitoringiku->mti_id, 'type' => 'peningkatan', 'unit_kerja' => request('unit_kerja'), 'q' => request('q')]) }}">Peningkatan</a></li>
                                </ul>
                            </div>

                            {{-- Tombol Export Excel --}}
                            <div class="dropdown mr-3 no-loader">
                                <button class="btn btn-success btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-solid fa-file-excel"></i> Export Excel
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('monitoringiku.export-detail', ['mti_id' => $Monitoringiku->mti_id, 'type' => 'penetapan', 'unit_kerja' => request('unit_kerja')]) }}">Penetapan</a></li>
                                    <li><a class="dropdown-item" href="{{ route('monitoringiku.export-detail', ['mti_id' => $Monitoringiku->mti_id, 'type' => 'pelaksanaan', 'unit_kerja' => request('unit_kerja')]) }}">Pelaksanaan</a></li>
                                    <li><a class="dropdown-item" href="{{ route('monitoringiku.export-detail', ['mti_id' => $Monitoringiku->mti_id, 'type' => 'evaluasi', 'unit_kerja' => request('unit_kerja')]) }}">Evaluasi</a></li>
                                    <li><a class="dropdown-item" href="{{ route('monitoringiku.export-detail', ['mti_id' => $Monitoringiku->mti_id, 'type' => 'pengendalian', 'unit_kerja' => request('unit_kerja')]) }}">Pengendalian</a></li>
                                    <li><a class="dropdown-item" href="{{ route('monitoringiku.export-detail', ['mti_id' => $Monitoringiku->mti_id, 'type' => 'peningkatan', 'unit_kerja' => request('unit_kerja')]) }}">Peningkatan</a></li>
                                </ul>
                            </div>
                        @endif

                        {{-- Tombol Kembali --}}
                        <a class="btn btn-danger btn-sm" href="{{ route('monitoringiku.index') }}">
                            <i class="fa-solid fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Prodi : <span class="badge badge-info">{{ $Monitoringiku->targetIndikator->prodi->nama_prodi }}</span> Tahun : <span class="badge badge-primary">{{ $Monitoringiku->tahunKerja->th_tahun }}</span></h4>
                
                {{-- Form Pencarian (Mengarah ke route show) --}}
                <form action="{{ route('monitoringiku.show', $Monitoringiku->mti_id) }}" method="GET">
                    <div class="form-row align-items-center">
                        @if (Auth::user()->role == 'admin' || Auth::user()->role == 'fakultas' || Auth::user()->role == 'prodi')
                        <div class="col-auto">
                            <select name="unit_kerja" class="form-control form-control-sm">
                                <option value="">Semua Unit Kerja</option>
                                @foreach($unitKerjas as $unit)
                                    <option value="{{ $unit->unit_id }}" 
                                        {{ (isset($unitKerjaFilter) && $unitKerjaFilter == $unit->unit_id) ? 'selected' : '' }}>
                                        {{ $unit->unit_nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="col-auto">
                            <input class="form-control form-control-sm" 
                                name="q" 
                                value="{{ $q ?? '' }}" 
                                placeholder="Pencarian..." />
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-info btn-sm">
                                <i class="fa-solid fa-search"></i> Cari
                            </button>
                        </div>
                    </div>
                </form>
                {{-- Tombol "Isi Monitoring" DIHAPUS karena ini halaman view-only --}}
            </div>
        
            @if($targetIndikators->isEmpty())
                <div class="card-body text-center">
                    <p>Tidak ada target untuk prodi ini.</p>
                </div>
            @else
                <div class="table-responsive text-center">
                <table class="table table-hover table-bordered table-striped m-0">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th style="width: 25%;">Indikator Kinerja</th>
                            <th style="width: 5%;">Baseline</th>
                            <th>Target</th>
                            @if (Auth::user()->role == 'fakultas' || Auth::user()->role == 'prodi' || Auth::user()->role == 'admin')
                                <th>Capaian</th>
                                <th style="width: 10%;">URL</th>
                                <th>Status</th>
                                <th style="width: 15%;">Pelaksanaan</th>
                                <th style="width: 15%;">Evaluasi</th>
                                <th style="width: 15%;">Tindak Lanjut</th>
                                <th style="width: 15%;">Peningkatan</th>
                            @else
                                {{-- Unit Kerja View --}}
                                <th>Capaian</th>
                                <th>Status</th>
                                <th style="width: 25%;">Pelaksanaan</th>
                                <th>URL</th>
                            @endif
                            {{-- Kolom Aksi DIHAPUS --}}
                        </tr>
                    </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach ($targetIndikators as $target)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td class="text-justify" style="padding: 2rem;">
                                        {{ ($target->has('indikatorKinerja') ?  $target->indikatorKinerja->ik_kode : "") }} - {{ ($target->has('indikatorKinerja') ?  $target->indikatorKinerja->ik_nama : "") }}
                                    </td>
                                    <td>
                                        @php
                                            $ketercapaian = strtolower(optional($target->indikatorKinerja)->ik_ketercapaian ?? '');
                                            $baselineRaw = trim((string) (optional($target->baselineTahun)->baseline ?? '0'));
                                            $cleanNum = str_replace(['%', ' '], '', $baselineRaw);
                                            $baselineValue = is_numeric($cleanNum) ? (float) $cleanNum : null;
                                            $progressColor = ($baselineValue !== null && $baselineValue == 0) ? '#dc3545' : '#28a745';
                                        @endphp
                                        
                                        @if ($ketercapaian === 'persentase' && $baselineValue !== null)
                                            <div class="ring-progress-wrapper">
                                                <div class="ring-progress" style="--value: {{ $baselineValue }}; --progress-color: {{ $progressColor }};">
                                                    <div class="ring-inner">
                                                        <span class="ring-text">{{ $baselineValue }}%</span>
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
                                                $formattedRasio = '0:0';
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
                                            $ketercapaian = strtolower(optional($target->indikatorKinerja)->ik_ketercapaian ?? '');
                                            $targetValue = trim($target->ti_target);
                                            $numericValue = (float) str_replace('%', '', $targetValue);
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
                                        @elseif ($ketercapaian === 'nilai' && is_numeric($targetValue))
                                            <span class="badge badge-primary">{{ $targetValue }}</span>
                                        @elseif (in_array(strtolower($targetValue), ['ada', 'draft']))
                                            @if (strtolower($targetValue) === 'ada')
                                                <span class="text-success"><i class="fa-solid fa-check-circle"></i> Ada</span>
                                            @else
                                                <span class="text-warning"><i class="fa-solid fa-info-circle"></i> Draft</span>
                                            @endif
                                        @elseif ($ketercapaian === 'rasio')
                                            <span class="badge badge-info"><i class="fa-solid fa-balance-scale"></i> {{$targetValue}}</span>
                                        @else
                                            {{ $targetValue }}
                                        @endif
                                    </td>        

                                    {{-- Layout Kolom Admin/Fakultas/Prodi --}}
                                    @if (Auth::user()->role == 'admin' || Auth::user()->role == 'fakultas' || Auth::user()->role == 'prodi')
                                    <td>
                                        @php
                                            $capaian = optional($target->monitoringDetail)->mtid_capaian;
                                            $ketercapaian = optional($target->indikatorKinerja)->ik_ketercapaian;
                                            $numericValue = (float) str_replace('%', '', $capaian);
                                            $progressColor = $numericValue == 0 ? '#dc3545' : '#28a745';
                                        @endphp

                                        @if (strpos($capaian, '%') !== false || $ketercapaian === 'persentase')
                                            <div class="ring-progress-wrapper">
                                                <div class="ring-progress" style="--value: {{ $numericValue }}; --progress-color: {{ $progressColor }};">
                                                    <div class="ring-inner">
                                                        <span class="ring-text">{{ $numericValue }}%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif(is_numeric($capaian) && $ketercapaian == 'nilai')
                                            <span class="badge badge-primary"> {{ $capaian }}</span>
                                        @elseif(preg_match('/^\d+\s*:\s*\d+$/', $capaian))
                                            @php
                                                $cleanedRasio = preg_replace('/\s*/', '', $capaian);
                                                [$left, $right] = explode(':', $cleanedRasio);
                                                $formattedRasio = $left . ' : ' . $right;
                                            @endphp
                                            <span class="badge badge-info"><i class="fa-solid fa-balance-scale"></i> {{ $formattedRasio }}</span>
                                        @elseif(strtolower($capaian) === 'ada')
                                            <span class="text-success"><i class="fa-solid fa-check-circle"></i> Ada</span>
                                        @elseif(strtolower($capaian) === 'draft')
                                            <span class="text-warning"><i class="fa-solid fa-info-circle"></i> Draft</span>
                                        @elseif(!empty($capaian))
                                            <span class="badge badge-primary">{{ $capaian }}</span>
                                        @else
                                            <span class="text-danger"><i class="fa-solid fa-times-circle"></i></span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($target->monitoringDetail->mtid_url) && $target->monitoringDetail->mtid_url)
                                            <a href="{{ $target->monitoringDetail->mtid_url }}" target="_blank" class="btn btn-sm btn-success"><i class="fa fa-eye"></i> URL</a>
                                        @else
                                            <span class="text-danger"><i class="fa-solid fa-times-circle"></i></span>
                                        @endif
                                    </td>   
                                    <td>
                                        @php
                                            $status = strtolower(optional($target->monitoringDetail)->mtid_status ?? '');
                                        @endphp
                                    
                                        @if($status === 'tercapai')
                                            <span class="text-success"><i class="fa-solid fa-check-circle"></i> Tercapai</span>
                                        @elseif($status === 'terlampaui')
                                            <span class="text-primary"><i class="fa-solid fa-arrow-up"></i> Terlampaui</span>
                                        @elseif($status === 'tidak tercapai')
                                            <span class="text-warning"><i class="fa-solid fa-info-circle"></i> Tidak Tercapai</span>
                                        @elseif($status === 'tidak terlaksana')
                                            <span class="text-danger"></i> Tidak Terlaksana</span>
                                        @else
                                            <span class="text-danger"> Tidak Terlaksana</span>
                                        @endif
                                    </td>    
                                    
                                    {{-- 1. Kolom Pelaksanaan --}}     
                                    @php 
                                        $pelaksanaan = optional($target->monitoringDetail)->mtid_keterangan; 
                                    @endphp
                                    <td style="vertical-align: middle; text-align: {{ $pelaksanaan ? 'left' : 'center' }}; padding: 8px;">
                                        @if($pelaksanaan)
                                            <div style="line-height: 1.5;">{{ $pelaksanaan }}</div>
                                        @else
                                            <span style="color: #999; font-style: italic; font-size: 9pt;">(Belum ada pelaksanaan)</span>
                                        @endif
                                    </td>

                                    {{-- 2. Kolom Evaluasi --}}
                                    @php 
                                        $evaluasi = optional($target->monitoringDetail)->mtid_evaluasi; 
                                    @endphp
                                    <td style="vertical-align: middle; text-align: {{ $evaluasi ? 'left' : 'center' }}; padding: 8px;">
                                        @if($evaluasi)
                                            <div style="line-height: 1.5;">{{ $evaluasi }}</div>
                                        @else
                                            <span style="color: #999; font-style: italic; font-size: 9pt;">(Belum ada evaluasi)</span>
                                        @endif
                                    </td>

                                    {{-- 3. Kolom Tindak Lanjut --}}
                                    @php 
                                        $tindak_lanjut = optional($target->monitoringDetail)->mtid_tindaklanjut; 
                                    @endphp
                                    <td style="vertical-align: middle; text-align: {{ $tindak_lanjut ? 'left' : 'center' }}; padding: 8px;">
                                        @if($tindak_lanjut)
                                            <div style="line-height: 1.5;">{{ $tindak_lanjut }}</div>
                                        @else
                                            <span style="color: #999; font-style: italic; font-size: 9pt;">(Belum ada tindak lanjut)</span>
                                        @endif
                                    </td>

                                    {{-- 4. Kolom Peningkatan --}}
                                    @php 
                                        $peningkatan = optional($target->monitoringDetail)->mtid_peningkatan; 
                                    @endphp
                                    <td style="vertical-align: middle; text-align: {{ $peningkatan ? 'left' : 'center' }}; padding: 8px;">
                                        @if($peningkatan)
                                            <div style="line-height: 1.5;">{{ $peningkatan }}</div>
                                        @else
                                            <span style="color: #999; font-style: italic; font-size: 9pt;">(Belum ada peningkatan)</span>
                                        @endif
                                    </td>

                                    {{-- Layout kolom bukan admin (Unit Kerja / Others) --}}
                                    @else
                                    <td>
                                        @php
                                            $capaian = optional($target->monitoringDetail)->mtid_capaian;
                                            $ketercapaian = optional($target->indikatorKinerja)->ik_ketercapaian;
                                            $numericValue = (float) str_replace('%', '', $capaian);
                                            $progressColor = $numericValue == 0 ? '#dc3545' : '#28a745';
                                        @endphp

                                        @if (strpos($capaian, '%') !== false || $ketercapaian === 'persentase')
                                            <div class="ring-progress-wrapper">
                                                <div class="ring-progress" style="--value: {{ $numericValue }}; --progress-color: {{ $progressColor }};">
                                                    <div class="ring-inner">
                                                        <span class="ring-text">{{ $numericValue }}%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif(is_numeric($capaian) && $ketercapaian == 'nilai')
                                            <span class="badge badge-primary"> {{ $capaian }}</span>
                                        @elseif(preg_match('/^\d+\s*:\s*\d+$/', $capaian))
                                            @php
                                                $cleanedRasio = preg_replace('/\s*/', '', $capaian);
                                                [$left, $right] = explode(':', $cleanedRasio);
                                                $formattedRasio = $left . ' : ' . $right;
                                            @endphp
                                            <span class="badge badge-info"><i class="fa-solid fa-balance-scale"></i> {{ $formattedRasio }}</span>
                                        @elseif(strtolower($capaian) === 'ada')
                                            <span class="text-success"><i class="fa-solid fa-check-circle"></i> Ada</span>
                                        @elseif(strtolower($capaian) === 'draft')
                                            <span class="text-warning"><i class="fa-solid fa-info-circle"></i> Draft</span>
                                        @elseif(!empty($capaian))
                                            <span class="badge badge-primary">{{ $capaian }}</span>
                                        @else
                                            <span class="text-danger"><i class="fa-solid fa-times-circle"></i></span>
                                        @endif
                                    </td>  
                                    <td>
                                        @php
                                            $status = strtolower(optional($target->monitoringDetail)->mtid_status ?? '');
                                        @endphp
                                    
                                        @if($status === 'tercapai')
                                            <span class="text-success"><i class="fa-solid fa-check-circle"></i> Tercapai</span>
                                        @elseif($status === 'terlampaui')
                                            <span class="text-primary"><i class="fa-solid fa-arrow-up"></i> Terlampaui</span>
                                        @elseif($status === 'tidak tercapai')
                                            <span class="text-warning"><i class="fa-solid fa-info-circle"></i> Tidak Tercapai</span>
                                        @else
                                            <span class="text-danger"><i></i> Tidak Terlaksana</span>
                                        @endif
                                    </td>        
                                    @php 
                                        $pelaksanaan = optional($target->monitoringDetail)->mtid_keterangan; 
                                    @endphp
                                    <td style="vertical-align: middle; text-align: {{ $pelaksanaan ? 'left' : 'center' }}; padding: 8px;">
                                        @if($pelaksanaan)
                                            <div style="line-height: 1.5;">{{ $pelaksanaan }}</div>
                                        @else
                                            <span style="color: #999; font-style: italic; font-size: 9pt;">(Belum ada data pelaksanaan)</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($target->monitoringDetail->mtid_url) && $target->monitoringDetail->mtid_url)
                                            <a href="{{ $target->monitoringDetail->mtid_url }}" target="_blank" class="btn btn-sm btn-success">URL</a>
                                        @else
                                            Belum Ada URL
                                        @endif
                                    </td> 
                                    @endif
                                    
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>        
    </section>
</div>

{{-- <div class="modal fade" id="keteranganModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalTitle">
                    <i class="fas fa-info-circle mr-2"></i> Detail Informasi
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Indikator Kinerja :</small>
                    <h6 class="font-weight-bold text-dark">
                        <span id="modalIndikatorKode" class="badge badge-danger mr-2"></span> 
                        <span id="modalIndikatorNama"></span>
                    </h6>
                </div>
                <hr>
                <div class="p-3 bg-light rounded shadow-sm" style="min-height: 150px;">
                    <div id="modalKeteranganContent" class="keterangan-content"></div>
                </div>
            </div>
            <div class="modal-footer bg-whitesmoke">
                <button type="button" class="btn btn-primary" id="copyKeteranganBtn">
                    <i class="fas fa-copy"></i> Salin Data
                </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div> --}}
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#keteranganModal').appendTo("body");

            if ($.fn.DataTable.isDataTable('.table')) {
                $('.table').DataTable().destroy();
            }
            $('.table').DataTable({
                "paging": false,
                "searching": false,
                "ordering": true,
                "info": true, 
                "order": [[1, 'asc']],
                "language": {
                    "info": "Menampilkan _TOTAL_ indikator",
                    "infoEmpty": "Tidak ada data"
                }
            });

            $('.btn-lihat-detail, .btn-lihat-keterangan').on('click', function (e) {
                e.preventDefault();
                
                const jenis = $(this).data('jenis') || 'Detail';
                const indikator = $(this).data('indikator') || '-';
                const kode = $(this).data('kode') || '-'; 
                const isi = $(this).data('isi') || $(this).data('keterangan') || 'Tidak ada data.';

                $('#modalIndikatorKode').text(kode); 
                $('#modalIndikatorNama').text(indikator); 
                $('.modal-title').html(`<i class="fas fa-info-circle mr-2"></i> ${jenis}`);
                $('#modalKeteranganContent').text(isi);

                $('#keteranganModal').modal('show');
            });

            $('#copyKeteranganBtn').on('click', function() {
                const text = $('#modalKeteranganContent').text();
                navigator.clipboard.writeText(text).then(() => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Teks berhasil disalin',
                        showConfirmButton: false,
                        timer: 1500
                    });
                });
            });
        });
    </script>
@endpush