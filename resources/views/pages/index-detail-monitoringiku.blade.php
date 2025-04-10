@extends('layouts.app')
@section('title', 'Detail Monitoring IKU')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/circular-progress-bar.css') }}">
@endpush

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="mb-0">Detail Program Studi</h1>
                    <a class="btn btn-danger" href="{{ route('monitoringiku.index') }}">
                        <i class="fa-solid fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Data Monitoring IKU dari Prodi : <span class="badge badge-info">{{ $Monitoringiku->targetIndikator->prodi->nama_prodi }}</span> Tahun : <span class="badge badge-primary">{{ $Monitoringiku->targetIndikator->tahunKerja->th_tahun }}</span></h4>
                @if (Auth::user()->role == 'admin' || Auth::user()->role == 'fakultas')
                    <a class="btn btn-primary" href="{{ route('monitoringiku.create-detail', ['mti_id' => $Monitoringiku->mti_id]) }}">
                        <i class="fa-solid fa-plus"></i> Isi/Ubah Semua
                    </a>
                @endif           
            </div>
        
            @if($targetIndikators->isEmpty())
                <div class="card-body text-center">
                    <p>Tidak ada target untuk prodi ini.</p>
                </div>
            @else
                <div class="table-responsive text-center">
                    <table class="table table-hover table-bordered table-striped m-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th style="width: 39%;">Indikator Kinerja</th>
                                <th>Baseline</th>
                                <th>Target</th>
                                <th>Keterangan Indikator</th>
                                <th>Capaian</th>
                                <th>Status</th>
                                <th>URL</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach ($targetIndikators as $target)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td style="padding: 2rem;">
                                        {{-- {{ $target->indikatorKinerja->ik_kode }} - {{ $target->indikatorKinerja->ik_nama }} --}}
                                        {{ ($target->has('indikatorKinerja') ?  $target->indikatorKinerja->ik_kode : "") }} - {{ ($target->has('indikatorKinerja') ?  $target->indikatorKinerja->ik_nama : "") }}
                                    </td>
                                    <td>
                                        @php
                                            $ketercapaian = strtolower(optional($target->indikatorKinerja)->ik_ketercapaian ?? '');
                                            $baselineRaw = trim($target->indikatorKinerja->ik_baseline);
                                            $baselineValue = (float) str_replace('%', '', $baselineRaw);
                                            $progressColor = $baselineValue == 0 ? '#dc3545' : '#28a745'; // Merah jika 0, hijau jika > 0
                                        @endphp
                                    
                                        @if ($ketercapaian === 'persentase' && is_numeric($baselineValue))
                                            <div class="ring-progress-wrapper">
                                                <div class="ring-progress" style="--value: {{ $baselineValue }}; --progress-color: {{ $progressColor }};">
                                                    <div class="ring-inner">
                                                        <span class="ring-text">{{ $baselineValue }}%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif ($ketercapaian === 'nilai' && is_numeric($baselineRaw))
                                            <span class="badge badge-primary">{{ $baselineRaw }}</span>
                                        @elseif (in_array(strtolower($baselineRaw), ['ada', 'draft']))
                                            @if (strtolower($baselineRaw) === 'ada')
                                                <span class="text-success"><i class="fa-solid fa-check-circle"></i> Ada</span>
                                            @else
                                                <span class="text-warning"><i class="fa-solid fa-info-circle"></i> Draft</span>
                                            @endif
                                        @elseif ($ketercapaian === 'rasio')
                                            <span class="badge badge-info"><i class="fa-solid fa-balance-scale"></i> {{ $baselineRaw }} </span>
                                        @else
                                            {{ $baselineRaw }}
                                        @endif
                                    </td>                                    
                                    <td>
                                        @php
                                            $ketercapaian = strtolower(optional($target->indikatorKinerja)->ik_ketercapaian ?? '');
                                            $targetValue = trim($target->ti_target);
                                            $numericValue = (float) str_replace('%', '', $targetValue);
                                            $progressColor = $numericValue == 0 ? '#dc3545' : '#28a745'; // Merah jika 0%, hijau jika > 0%
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
                                    <td>{{ $target->ti_keterangan }}</td>  
                                    {{-- <pre>{{ json_encode($target->monitoringDetail) }}</pre> --}}
                                    <td>
                                        @php
                                            $capaian = optional($target->monitoringDetail)->mtid_capaian;
                                            $ketercapaian = optional($target->indikatorKinerja)->ik_ketercapaian;
                                            $numericValue = (float) str_replace('%', '', $capaian);
                                            $progressColor = $numericValue == 0 ? '#dc3545' : '#28a745'; // Warna bisa kamu sesuaikan
                                        @endphp

                                        @if (strpos($capaian, '%') !== false) {{-- Jika ada "%" berarti persentase --}}
                                            <div class="ring-progress-wrapper">
                                                <div class="ring-progress" style="--value: {{ $numericValue }}; --progress-color: {{ $progressColor }};">
                                                    <div class="ring-inner">
                                                        <span class="ring-text">{{ $numericValue }}%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif(is_numeric($capaian) && $ketercapaian == 'nilai')
                                            <span class="badge badge-primary"><i class="fa-solid fa-circle"></i> {{ $capaian }}</span>
                                        @elseif(preg_match('/^\d+:\d+$/', $capaian)) {{-- Jika format rasio (misalnya 1:2) --}}
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
                                            $status = strtolower(optional($target->monitoringDetail)->mtid_status ?? '');
                                        @endphp
                                    
                                        @if($status === 'tercapai')
                                            <span class="text-success"><i class="fa-solid fa-check-circle"></i> Tercapai</span>
                                        @elseif($status === 'tidak tercapai')
                                            <span class="text-warning"><i class="fa-solid fa-info-circle"></i> Tidak Tercapai</span>
                                        @elseif($status === 'tidak terlaksana')
                                            <span class="text-danger"><i class="fa-solid fa-times-circle"></i> Tidak Terlaksana</span>
                                        @else
                                            <span>Belum ada Status</span>
                                        @endif
                                    </td>
                                    
                                    
                                    {{-- <td>
                                        @if($target->monitoringDetail->mtid_url ?? 'Belum ada Capaian')
                                            <a href="{{ $target->monitoringDetail->mtid_url ?? 'Belum ada Capaian'}}" target="_blank" class="btn btn-sm btn-success">Lihat URL</a>
                                        @else
                                            Belum Ada URL
                                        @endif
                                    </td>   --}}
                                    <td>
                                        @if(isset($target->monitoringDetail->mtid_url) && $target->monitoringDetail->mtid_url)
                                            <a href="{{ $target->monitoringDetail->mtid_url }}" target="_blank" class="btn btn-sm btn-success">Lihat URL</a>
                                        @else
                                            Belum Ada URL
                                        @endif
                                    </td>
                                    
                                                                     
                                    <td class="text-center">
                                        <a href="{{ route('monitoringiku.edit-detail', ['mti_id' => $Monitoringiku->mti_id, 'ti_id' => $target->ti_id]) }}" class="btn btn-sm btn-warning"><i class="fa-solid fa-pen-to-square"></i> Isi/Ubah</a>                                      
                                    </td>
                                    
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>        
    </section>
</div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(event, formid) {
            event.preventDefault();
            Swal.fire({
                title: 'Yakin menghapus Data ini?',
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
            });
        }
    </script>
@endpush