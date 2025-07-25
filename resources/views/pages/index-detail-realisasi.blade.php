@extends('layouts.app')
@section('title', 'Detail Realisasi Renja')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('css/circular-progress-bar.css') }}">
@endpush

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="mb-0">Detail Rencana Kerja</h1>
                    <a class="btn btn-danger" href="{{ route('realisasirenja.index') }}">
                        <i class="fa-solid fa-arrow-left"></i> Kembali
                    </a>
                </div>

                @foreach ($rencanaKerja as $rencana)
                    <div class="table-responsive text-center">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Tahun</th>
                                    <th>Program Studi</th>
                                    <th style="width: 50%;">Indikator Kinerja</th>
                                    <th>Nama Rencana Kerja</th>
                                    <th>Unit Kerja</th>
                                    <th>Periode Monev</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $rencana->tahunKerja->th_tahun ?? '-' }}</td>
                                    <td>
                                        @if($rencana->programStudis->isNotEmpty())
                                            <ul class="list-unstyled">
                                                @foreach ($rencana->programStudis as $prodi)
                                                    <li class="my-2">{{ $prodi->nama_prodi }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-muted">Tidak ada Program Studi</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($rencana->targetindikators->isNotEmpty())
                                            <ul class="list-unstyled">
                                                @foreach ($rencana->targetindikators as $iku)
                                                    <li class="my-2" >{{ $iku->indikatorKinerja->ik_kode }} - {{ $iku->indikatorKinerja->ik_nama }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-muted">Tidak ada Indikator Kinerja</span>
                                        @endif
                                    </td>
                                    <td>{{ $rencana->rk_nama }}</td>
                                    <td>{{ $rencana->UnitKerja->unit_nama ?? '-' }}</td> 
                                    <td>
                                        @if($rencana->periodes->isNotEmpty())
                                            @foreach ($rencana->periodes as $periode)
                                                <span class="badge badge-info">{{ $periode->pm_nama }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">Tidak ada periode</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Data Realisasi</h4>
                @if (Auth::user()->role == 'unit kerja')
                    <a class="btn btn-primary" href="{{ route('realisasirenja.create', ['rk_id' => $rencana->rk_id]) }}">
                        <i class="fa-solid fa-plus"></i> Tambah Realisasi
                    </a>
                @endif
            </div>

            @if($realisasi->isEmpty())
                <div class="card-body text-center">
                    <p>Tidak ada data realisasi untuk rencana kerja ini.</p>
                </div>
            @else
                <div class="table-responsive text-center">
                    <table class="table table-hover table-bordered table-striped m-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th style="width: 50%;">Deskripsi</th>
                                <th>Capaian</th>
                                <th>Tanggal</th>
                                <th>Url</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach ($realisasi as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td style="padding: 1rem;">{{ $item->rkr_deskripsi }}</td>
                                    <td>
                                        @php
                                            $capaianRaw = trim($item->rkr_capaian);
                                            $capaianValue = (float) str_replace('%', '', $capaianRaw);
                                            $progressColor = $capaianValue == 0 ? '#dc3545' : '#28a745';
                                        @endphp
                                    
                                        @if (is_numeric($capaianValue))
                                            <div class="ring-progress-wrapper">
                                                <div class="ring-progress" style="--value: {{ $capaianValue }}; --progress-color: {{ $progressColor }};">
                                                    <div class="ring-inner">
                                                        <span class="ring-text">{{ $capaianValue }}%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            {{ $capaianRaw }}
                                        @endif
                                    </td>
                                    
                                    <td>
                                        @if($item->rkr_tanggal instanceof \Carbon\Carbon)
                                            {{ $item->rkr_tanggal->format('d-m-Y') }}
                                        @else
                                            {{ $item->rkr_tanggal }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->rkr_url)
                                            <a href="{{ $item->rkr_url}}" target="_blank" class="btn btn-success">Lihat URL</a>
                                        @else
                                            Tidak Ada URL
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('realisasirenja.edit', $item->rkr_id) }}" class="btn btn-warning"><i class="fa-solid fa-pen-to-square"></i> Ubah</a>     
                                        <form id="delete-form-{{ $item->rkr_id }}" method="POST" class="d-inline" action="{{ route('realisasirenja.destroy', $item->rkr_id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger" onclick="confirmDelete(event, '{{ $item->rkr_id }}')"><i class="fa-solid fa-trash"></i> Hapus</button>
                                        </form>
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
            });
        }
    </script>
@endpush
