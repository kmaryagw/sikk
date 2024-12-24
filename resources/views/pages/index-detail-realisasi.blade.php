@extends('layouts.app')
@section('title', 'Detail Realisasi Renja')

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

                @foreach ($rencanaKerja as $rencana)  <!-- Iterasi koleksi rencanaKerja -->
                    <div class="table-responsive text-center">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nama Rencana Kerja</th>
                                    <th>Unit Kerja</th>
                                    <th>Tahun</th>
                                    <th>Indikator Kinerja</th>
                                    <th>Periode Monev</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $rencana->rk_nama }}</td>
                                    <td>{{ $rencana->UnitKerja->unit_nama ?? '-' }}</td>
                                    <td>{{ $rencana->tahunKerja->th_tahun ?? '-' }}</td>
                                    <td>
                                        @if($rencana->targetindikators->isNotEmpty())
                                            @foreach ($rencana->targetindikators as $iku)
                                                <span class="badge badge-success">{{ $iku->indikatorKinerja->ik_nama }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">Tidak ada Indikator Kinerja</span>
                                        @endif
                                    </td> 
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
                @if (Auth::user()->role == 'admin')
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
                                <th>Deskripsi</th>
                                <th>Capaian</th>
                                <th>Tanggal</th>
                                <th>Url</th>
                                <th>File</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach ($realisasi as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $item->rkr_deskripsi }}</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar" style="width: {{ $item->rkr_capaian }}%;" aria-valuenow="{{ $item->rkr_capaian }}" aria-valuemin="0" aria-valuemax="100">{{ $item->rkr_capaian }}%</div>
                                        </div>
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
                                            <a href="{{ $item->rkr_url }}" target="_blank">{{ $item->rkr_url }}</a>
                                        @else
                                            Tidak Ada URL
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->rkr_file)
                                            <a class="btn btn-success" href="{{ asset('storage/' . $item->rkr_file) }}" target="_blank"><i class="fa-solid fa-eye"></i> Lihat Dokumen</a><br>
                                        @else
                                            Tidak Ada Dokumen
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
