@extends('layouts.app')
@section('title', 'Detail Evaluasi')

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="mb-0">Detail Program Studi</h1>
                    <a class="btn btn-danger" href="{{ route('evaluasi.index') }}">
                        <i class="fa-solid fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Data Evaluasi dari Prodi: <span class="badge badge-info">{{ $Evaluasi->targetIndikator->prodi->nama_prodi }}</span> Tahun: <span class="badge badge-primary">{{ $Evaluasi->targetIndikator->tahunKerja->th_tahun }}</span></h4>
                @if (Auth::user()->role == 'admin')
                    <a class="btn btn-primary" href="{{ route('evaluasi.create-detail', ['eval_id' => $Evaluasi->eval_id]) }}">
                        <i class="fa-solid fa-plus"></i> Tambah Evaluasi
                    </a>
                @endif
            </div>
        
            @if($Evaluasi->evaluasiDetails->isEmpty())
                <div class="card-body text-center">
                    <p>Tidak ada data evaluasi untuk prodi ini.</p>
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
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach ($Evaluasi->evaluasiDetails as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $item->targetIndikator->indikatorKinerja->ik_nama }}</td>
                                    <td>{{ $item->evald_target }}</td>
                                    <td>{{ $item->evald_keterangan }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('evaluasi.edit-detail', ['evald_id' => $item->evald_id]) }}" class="btn btn-warning"><i class="fa-solid fa-pen-to-square"></i> Ubah</a>     
                                        <form id="delete-form-{{ $item->evald_id }}" method="POST" 
                                            action="{{ route('evaluasi.destroy-detail', ['eval_id' => $Evaluasi->eval_id, 'evald_id' => $item->evald_id]) }}" 
                                            class="d-inline">
                                          @csrf
                                          @method('DELETE')
                                          <button class="btn btn-danger" type="submit" onclick="confirmDelete(event, '{{ $item->evald_id }}')">
                                              <i class="fa-solid fa-trash"></i> Hapus
                                          </button>
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