@extends('layouts.app')
@section('title', 'IKU/IKT')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/circular-progress-bar.css') }}"> 
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Daftar Indikator Kinerja Utama/Tambahan</h1>
            </div>

            @if(session('success'))
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: '{{ session("success") }}',
                    });
                </script>
            @endif

            @if(session('error'))
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: '{{ session("error") }}',
                    });
                </script>
            @endif

            <div class="card mb-3">
                <div class="card-header">
                    <form class="row g-2 align-items-center">                      
                        <div class="col-auto">
                            <input class="form-control form-control-sm" name="q" value="{{ $q }}" placeholder="Pencarian..." />
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-info btn-sm"><i class="fa-solid fa-search"></i> Cari</button>
                        </div>
                        @if (Auth::user()->role== 'admin')
                        <div class="col-auto">
                            <a class="btn btn-primary btn-sm" href="{{ route('indikatorkinerja.create') }}">
                                <i class="fa-solid fa-plus"></i> Tambah
                            </a>
                        </div>
                        <div class="col-auto">
                            <a class="btn btn-success btn-sm" href="{{ route('indikatorkinerja.template') }}">
                                <i class="fa-solid fa-file-download"></i> Download Template
                            </a>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#importModal">
                                <i class="fa-solid fa-file-upload"></i> Import Data
                            </button>
                        </div>
                        @endif
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped m-0 text-center">
                        <thead>
                            <tr>
                                <th style="width : 1%">No</th>
                                <th style="width : 8%">Kode IKU/T</th>
                                <th style="width : 30%">Nama IKU/T</th>
                                <th>Standar</th>
                                <th>Jenis</th>
                                <th>Ketercapaian</th>
                                <th>Baseline</th>
                                <th>Status</th>
                                @if (Auth::user()->role== 'admin')
                                <th>Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = $indikatorkinerjas->firstItem(); @endphp
                            @foreach ($indikatorkinerjas as $indikatorkinerja)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $indikatorkinerja->ik_kode }}</td>
                                    <td>{{ $indikatorkinerja->ik_nama }}</td>
                                    <td style="padding: 3rem;">{{ $indikatorkinerja->std_nama ?? '-' }}</td>
                                    <td>
                                        @if (strtolower($indikatorkinerja->ik_jenis) === 'iku')
                                            <span class="badge badge-success">IKU</span>
                                        @elseif (strtolower($indikatorkinerja->ik_jenis) === 'ikt')
                                            <span class="badge badge-primary">IKT</span>
                                        @elseif (strtolower($indikatorkinerja->ik_jenis) === 'ikt/iku')
                                            <span class="badge badge-danger">IKT/IKU</span>
                                        @else
                                            {{ $indikatorkinerja->ik_jenis }}
                                        @endif
                                    </td>                                    
                                    <td>{{ $indikatorkinerja->ik_ketercapaian }}</td>
                                    <td>
                                        @php
                                            $ketercapaian = strtolower($indikatorkinerja->ik_ketercapaian);
                                            $baselineRaw = trim($indikatorkinerja->baseline_tahun); // ganti dari ik_baseline ke baseline_tahun
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
                                            @php
                                                $formattedRasio = 'Format salah';
                                                $cleaned = preg_replace('/\s*/', '', $baselineRaw);
                                                if (preg_match('/^\d+:\d+$/', $cleaned)) {
                                                    $parts = explode(':', $cleaned);
                                                    if (count($parts) === 2) {
                                                        $formattedRasio = $parts[0] . ' : ' . $parts[1];
                                                    }
                                                }
                                            @endphp
                                            <span class="badge badge-info">
                                                <i class="fa-solid fa-balance-scale"></i> {{ $formattedRasio }}
                                            </span>
                                        @else
                                            {{ $baselineRaw ?? 'Belum Ada' }}
                                        @endif
                                    </td>
                                    <td>
                                        @if (strtolower($indikatorkinerja->ik_is_aktif) === 'y')
                                            <span class="text-success"><i class="fa-solid fa-check-circle"></i> Aktif</span>
                                        @else
                                            <span class="text-danger"><i class="fa-solid fa-times-circle"></i> Tidak</span>
                                        @endif
                                    </td> 
                                    @if (Auth::user()->role== 'admin')
                                    <td>
                                        <a class="btn btn-warning btn-sm mb-3 mt-3" href="{{ route('indikatorkinerja.edit', $indikatorkinerja->ik_id) }}">
                                            <i class="fa-solid fa-pen-to-square"></i> Ubah
                                        </a>
                                        <form id="delete-form-{{ $indikatorkinerja->ik_id }}" method="POST" class="d-inline" action="{{ route('indikatorkinerja.destroy', $indikatorkinerja->ik_id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm mb-3 mt-3" onclick="confirmDelete(event, '{{ $indikatorkinerja->ik_id }}')">
                                                <i class="fa-solid fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </td>                                    
                                    @endif
                                </tr>
                            @endforeach

                            @if ($indikatorkinerjas->isEmpty())
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada data</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                @if ($indikatorkinerjas->hasPages())
                    <div class="card-footer">
                        {{ $indikatorkinerjas->links('pagination::bootstrap-5') }}
                    </div>
        @endif
            </div>
        </section>
    </div>

    <!-- Modal Import -->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Data IKU/IKT</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('indikatorkinerja.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="file">Pilih File Excel (Format .xlsx)</label>
                            <input type="file" class="form-control-file" name="file" required accept=".xlsx">
                            <small class="form-text text-muted">Pastikan format sudah sesuai dengan template</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa-solid fa-times"></i> Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Import</button>
                    </div>
                </form>
            </div>
        </div>
        
    </div>
@endsection

@push('scripts')
    <!-- Load SweetAlert2 -->
    <script src="{{ asset('library/simpleweather/jquery.simpleWeather.min.js') }}"></script>
    <script src="{{ asset('library/chart.js/dist/Chart.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
    <script src="{{ asset('library/summernote/dist/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('library/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/index-0.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
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

    <!-- Alert untuk success dan error -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Sukses!',
                    text: '{{ session("success") }}',
                    confirmButtonText: 'OK'
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ session("error") }}',
                    confirmButtonText: 'OK'
                });
            @endif
        });
    </script>
@endpush