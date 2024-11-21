@extends('layouts.app')
@section('title', 'Detail Evaluasi')

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="mb-0">Detail Evaluasi</h1>
                    <a class="btn btn-danger" href="{{ route('evaluasi.index') }}">
                        <i class="fa-solid fa-arrow-left"></i> Kembali
                    </a>
                </div>

                @foreach ($Evaluasi as $evaluasi)
                    <div class="table-responsive text-center">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Tahun</th>
                                    <th>Prodi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $evaluasi->targetIndikator->tahunKerja->th_tahun }}</td>
                                    <td>{{ $evaluasi->targetIndikator->prodi->prodi_nama }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Data Evaluasi</h4>
                @if (Auth::user()->role == 'admin')
                    <a class="btn btn-primary" href="{{ route('evaluasi.create-detail', ['eval_id' => $evaluasi->eval_id]) }}">
                        <i class="fa-solid fa-plus"></i> Tambah Evalusi
                    </a>
                @endif
            </div>

            @if($evaluasi->evaluasiDetails->isEmpty())
                <div class="card-body text-center">
                    <p>Tidak ada data evaluasi untuk rencana kerja ini.</p>
                </div>
            @else
                <div class="table-responsive text-center">
                    <table class="table table-hover table-bordered table-striped m-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Indikator Kinerja</th>
                                <th>Target</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach ($evaluasis as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $item->targetIndikator->iku->ik_nama }}</td>
                                    <td>{{ $item->evald_target }}</td>
                                    <td>{{ $item->evald_keterangan }}</td>
                                    <td>
                                        <a href="#" class="btn btn-warning"><i class="fa-solid fa-pen-to-square"></i> Ubah</a>     
                                        <form id="#" method="POST" class="d-inline" action="#">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger" onclick="#"><i class="fa-solid fa-trash"></i> Hapus</button>
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
