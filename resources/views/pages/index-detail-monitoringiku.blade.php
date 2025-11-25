@extends('layouts.app')
@section('title', 'Detail Monitoring Indikator Kinerja')

@push('style')
    <!-- CSS Libraries -->
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

    </style>
@endpush

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="mb-0">Detail Monitoring Indikator Kinerja</h1>
                    <a class="btn btn-danger" href="{{ route('monitoringiku.index') }}">
                        <i class="fa-solid fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Prodi : <span class="badge badge-info">{{ $Monitoringiku->targetIndikator->prodi->nama_prodi }}</span> Tahun : <span class="badge badge-primary">{{ $Monitoringiku->targetIndikator->tahunKerja->th_tahun }}</span></h4>
                <form action="{{ route('monitoringiku.index-detail', $Monitoringiku->mti_id) }}" method="GET">
                    <div class="form-row align-items-center">
                        @if (Auth::user()->role == 'admin' || Auth::user()->role == 'fakultas')
                        <div class="col-auto">
                            <select name="unit_kerja" class="form-control form-control-sm">
                                <option value="">Semua Unit Kerja</option>
                                @foreach($unitKerjas as $unit)
                                    <option value="{{ $unit->unit_id }}" 
                                        {{ $unitKerjaFilter == $unit->unit_id ? 'selected' : '' }}>
                                        {{ $unit->unit_nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="col-auto">
                            <input class="form-control form-control-sm" 
                                name="q" 
                                value="{{ $q }}" 
                                placeholder="Pencarian..." />
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-info btn-sm">
                                <i class="fa-solid fa-search"></i> Cari
                            </button>
                        </div>
                    </div>
                </form>

                @if (Auth::user()->role == 'admin')
                <div class="dropdown">
                    <button class="btn btn-success btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-file-excel"></i> Export Excel
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('monitoringiku.export-detail', ['mti_id' => $Monitoringiku->mti_id, 'type' => 'penetapan']) }}">Penetapan</a></li>
                        <li><a class="dropdown-item" href="{{ route('monitoringiku.export-detail', ['mti_id' => $Monitoringiku->mti_id, 'type' => 'pelaksanaan']) }}">Pelaksanaan</a></li>
                        <li><a class="dropdown-item" href="{{ route('monitoringiku.export-detail', ['mti_id' => $Monitoringiku->mti_id, 'type' => 'evaluasi']) }}">Evaluasi</a></li>
                        <li><a class="dropdown-item" href="{{ route('monitoringiku.export-detail', ['mti_id' => $Monitoringiku->mti_id, 'type' => 'pengendalian']) }}">Pengendalian</a></li>
                        <li><a class="dropdown-item" href="{{ route('monitoringiku.export-detail', ['mti_id' => $Monitoringiku->mti_id, 'type' => 'peningkatan']) }}">Peningkatan</a></li>
                    </ul>
                </div>
                @endif

                @if (Auth::user()->role == 'unit kerja' || Auth::user()->role == 'admin')
                    <a class="btn btn-primary" href="{{ route('monitoringiku.create-detail', ['mti_id' => $Monitoringiku->mti_id]) }}">
                        <i class="fa-solid fa-plus"></i> Isi Monitoring
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
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th style="width: 30%;">Indikator Kinerja</th>
                            <th style="width: 5%;">Baseline</th>
                            <th>Target</th>
                            @if (Auth::user()->role == 'admin'|| Auth::user()->role == 'fakultas')
                                <th>Capaian</th>
                                <th style="width: 10%;">URL</th>
                                <th>Status</th>
                                <th style="width: 9%;">Keterangan</th>
                                <th style="width: 15%;">Evaluasi</th>
                                <th style="width: 15%;">Tindak Lanjut</th>
                                <th style="width: 15%;">Peningkatan</th>
                                <th style="width: 10%;">Aksi</th>
                            @else
                                <th>Capaian</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th>URL</th>
                                <th style="width: 10%;">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach ($targetIndikators as $target)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td class="text-justify" style="padding: 2rem;">
                                        {{-- {{ $target->indikatorKinerja->ik_kode }} - {{ $target->indikatorKinerja->ik_nama }} --}}
                                        {{ ($target->has('indikatorKinerja') ?  $target->indikatorKinerja->ik_kode : "") }} - {{ ($target->has('indikatorKinerja') ?  $target->indikatorKinerja->ik_nama : "") }}
                                    </td>
                                    {{-- @php
                                        dd($target->indikatorKinerja); 
                                    @endphp --}}
                                    <td>
                                        @php
                                            $ketercapaian = strtolower(optional($target->indikatorKinerja)->ik_ketercapaian ?? '');

                                            // Ambil baseline dari relasi
                                            $baselineRaw = trim((string) (optional($target->baselineTahun)->baseline ?? '0'));

                                            // Untuk angka (nilai/persentase), hilangkan % dan spasi
                                            $cleanNum = str_replace(['%', ' '], '', $baselineRaw);
                                            $baselineValue = is_numeric($cleanNum) ? (float) $cleanNum : null;

                                            // Warna progress bar: merah kalau 0, hijau kalau > 0
                                            $progressColor = ($baselineValue !== null && $baselineValue == 0) ? '#dc3545' : '#28a745';
                                        @endphp
                                        {{-- Persentase --}}
                                        @if ($ketercapaian === 'persentase' && $baselineValue !== null)
                                            <div class="ring-progress-wrapper">
                                                <div class="ring-progress" style="--value: {{ $baselineValue }}; --progress-color: {{ $progressColor }};">
                                                    <div class="ring-inner">
                                                        <span class="ring-text">{{ $baselineValue }}%</span>
                                                    </div>
                                                </div>
                                            </div>

                                        {{-- Nilai --}}
                                        @elseif ($ketercapaian === 'nilai' && is_numeric($cleanNum))
                                            <span class="badge badge-primary">{{ $baselineRaw }}</span>

                                        {{-- Ada / Draft --}}
                                        @elseif (in_array(strtolower($baselineRaw), ['ada', 'draft']))
                                            @if (strtolower($baselineRaw) === 'ada')
                                                <span class="text-success"><i class="fa-solid fa-check-circle"></i> Ada</span>
                                            @else
                                                <span class="text-warning"><i class="fa-solid fa-info-circle"></i> Draft</span>
                                            @endif

                                        {{-- Rasio --}}
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

                                        {{-- Default --}}
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

                                    {{-- layout kolom admin mulai dari capaian --}}
                                    @if (Auth::user()->role == 'admin' || Auth::user()->role == 'fakultas')
                                    <td>
                                        @php
                                            $capaian = optional($target->monitoringDetail)->mtid_capaian;
                                            $ketercapaian = optional($target->indikatorKinerja)->ik_ketercapaian;
                                            $numericValue = (float) str_replace('%', '', $capaian);
                                            $progressColor = $numericValue == 0 ? '#dc3545' : '#28a745';
                                        @endphp

                                        @if (strpos($capaian, '%') !== false || $ketercapaian === 'persentase'){{-- Jika ada "%" berarti persentase --}}
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
                                            <a href="{{ $target->monitoringDetail->mtid_url }}" target="_blank" class="btn btn-sm btn-success"><i class="fa fa-eye"></i> Lihat</a>
                                        @else
                                            <span class="text-danger"><i class="fa-solid fa-times-circle"></i></span>
                                        @endif
                                    </td>   
                                    <td>
                                        @php
                                            $status = strtolower(optional($target->monitoringDetail)->mtid_status ?? ' ');
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
                                    <td>
                                        @if(optional($target->monitoringDetail)->mtid_keterangan)
                                            <!-- Tombol buka modal -->
                                            <button type="button" 
                                                    class="btn btn-info btn-sm btn-lihat-keterangan"
                                                    data-keterangan="{{ $target->monitoringDetail->mtid_keterangan }}"
                                                    data-indikator="{{ optional($target->indikatorKinerja)->ik_nama }}">
                                                <i class="fa fa-eye"></i> Lihat
                                            </button>
                                        @else
                                            <span class="text-muted">Belum ada keterangan</span>
                                        @endif
                                    </td>
                                    {{-- Tambahan untuk ADMIN --}}
                                    <td >
                                        @if(optional($target->monitoringDetail)->mtid_evaluasi)
                                            <span class="text-success">
                                                {{ optional($target->monitoringDetail)->mtid_evaluasi }}
                                            </span>
                                        @else
                                            <span class="text-muted">Belum ada evaluasi</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(optional($target->monitoringDetail)->mtid_tindaklanjut)
                                            <span class="text-success">
                                                {{ optional($target->monitoringDetail)->mtid_tindaklanjut }}
                                            </span>
                                        @else
                                            <span class="text-muted">Belum ada tindak lanjut</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(optional($target->monitoringDetail)->mtid_peningkatan)
                                            <span class="text-success">
                                                {{ optional($target->monitoringDetail)->mtid_peningkatan }}
                                            </span>
                                        @else
                                            <span class="text-muted">Belum ada peningkatan</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('monitoringiku.edit-detail', ['mti_id' => $Monitoringiku->mti_id, 'ti_id' => $target->ti_id]) }}" class="btn btn-warning btn-sm">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </a>
                                    </td>
                                    {{-- Tambahan untuk ADMIN --}}

                                    {{-- layout kolom bukan admin mulai dari capaian  --}}
                                    @else
                                    <td>
                                        @php
                                            $capaian = optional($target->monitoringDetail)->mtid_capaian;
                                            $ketercapaian = optional($target->indikatorKinerja)->ik_ketercapaian;
                                            $numericValue = (float) str_replace('%', '', $capaian);
                                            $progressColor = $numericValue == 0 ? '#dc3545' : '#28a745';
                                        @endphp

                                        @if (strpos($capaian, '%') !== false || $ketercapaian === 'persentase'){{-- Jika ada "%" berarti persentase --}}
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
                                            <span class="text-danger"><i class="fa-solid fa-times-circle"></i> Belum ada Capaian</span>
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
                                            <span class="text-danger"><i class="fa-solid fa-times-circle"></i> Tidak Terlaksana</span>
                                        @else
                                            <span class="text-danger"><i class="fa-solid fa-times-circle"></i></span>

                                        @endif
                                    </td>         
                                    <td>
                                        @if(optional($target->monitoringDetail)->mtid_keterangan)
                                            <!-- Tombol buka modal -->
                                            <button type="button" 
                                                    class="btn btn-info btn-sm btn-lihat-keterangan"
                                                    data-keterangan="{{ $target->monitoringDetail->mtid_keterangan }}"
                                                    data-indikator="{{ optional($target->indikatorKinerja)->ik_nama }}">
                                                <i class="fa fa-eye"></i> Lihat
                                            </button>
                                        @else
                                            <span class="text-muted">Belum ada keterangan</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($target->monitoringDetail->mtid_url) && $target->monitoringDetail->mtid_url)
                                            <a href="{{ $target->monitoringDetail->mtid_url }}" target="_blank" class="btn btn-sm btn-success">Lihat URL</a>
                                        @else
                                            Belum Ada URL
                                        @endif
                                    </td> 
                                    <td>
                                        <a href="{{ route('monitoringiku.edit-detail', ['mti_id' => $Monitoringiku->mti_id, 'ti_id' => $target->ti_id]) }}" class="btn btn-warning btn-sm">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </a>
                                    </td>
                                    @endif
                                    {{-- layout kolom bukan admin mulai dari capaian  --}}

                                    {{-- @if (Auth::user()->role == 'unit kerja')
                                    <td class="text-center">
                                        <a href="{{ route('monitoringiku.edit-detail', ['mti_id' => $Monitoringiku->mti_id, 'ti_id' => $target->ti_id]) }}" class="btn btn-sm btn-warning"><i class="fa-solid fa-pen-to-square"></i> Isi/Ubah</a>                                      
                                    </td>
                                    @endif --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!-- Modal Keterangan Global -->
                    <div class="modal fade" 
                        id="keteranganModal" 
                        tabindex="-1" 
                        aria-hidden="true" 
                        data-bs-backdrop="false">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        Keterangan - <span id="modalIndikatorNama"></span>
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true"></span>
                                    </button>
                                </div>
                                <div class="modal-body" id="modalKeteranganContent" style="white-space: pre-line; text-align:left;">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" id="copyKeteranganBtn">Salin</button>
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>        
    </section>
</div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

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
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const btns = document.querySelectorAll('.btn-lihat-keterangan');
        const modalTitle = document.getElementById('modalIndikatorNama');
        const modalBody = document.getElementById('modalKeteranganContent');

        const keteranganModal = new bootstrap.Modal(document.getElementById('keteranganModal'));

        btns.forEach(btn => {
            btn.addEventListener('click', function () {
                modalTitle.textContent = this.getAttribute('data-indikator') || '-';
                modalBody.innerText = this.getAttribute('data-keterangan') || 'Belum ada keterangan';
                keteranganModal.show();
            });
        });
    });

    document.getElementById('copyKeteranganBtn').addEventListener('click', function() {
        const text = document.getElementById('modalKeteranganContent').innerText;
        navigator.clipboard.writeText(text).then(() => {
            if (window.Swal) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Keterangan disalin',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inisialisasi DataTables hanya dengan fitur sorting
            $('.table').DataTable({
                "paging": false,        // Menonaktifkan fitur pagination
                "searching": false,     // Menonaktifkan fitur pencarian
                "ordering": true,       // Mengaktifkan fitur sorting
                "order": [[1, 'asc']],  // Mengurutkan berdasarkan kolom "Indikator Kinerja" (indeks 1)
                "lengthChange": false,  // Menonaktifkan perubahan jumlah data per halaman
                info: true,
                infoCallback: function(settings, start, end, max, total, pre) {
                    return `
                        <span class="badge bg-primary text-light px-3 py-2 m-3">
                            Total Data yang Tampil : ${total}
                        </span>
                    `;
                }
            });

            const btns = document.querySelectorAll('.btn-lihat-keterangan');
            const modalTitle = document.getElementById('modalIndikatorNama');
            const modalBody = document.getElementById('modalKeteranganContent');

            const keteranganModal = new bootstrap.Modal(document.getElementById('keteranganModal'));

            btns.forEach(btn => {
                btn.addEventListener('click', function () {
                    modalTitle.textContent = this.getAttribute('data-indikator') || '-';
                    modalBody.innerText = this.getAttribute('data-keterangan') || 'Belum ada keterangan';
                    keteranganModal.show();
                });
            });
        });

        document.getElementById('copyKeteranganBtn').addEventListener('click', function() {
            const text = document.getElementById('modalKeteranganContent').innerText;
            navigator.clipboard.writeText(text).then(() => {
                if (window.Swal) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Keterangan disalin',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            });
        });
    </script>

@endpush


    
