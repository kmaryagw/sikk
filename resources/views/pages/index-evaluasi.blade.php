@extends('layouts.app')
@section('title', 'Evaluasi')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Daftar Program Studi</h1>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <form class="row g-2 align-items-center">
                        <div class="col-auto">
                            <input class="form-control" name="q" value="{{ $q }}" placeholder="Pencarian..." />
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-info"><i class="fa-solid fa-search"></i> Cari</button>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-primary" id="showModalBtn">
                                <i class="fa-solid fa-plus"></i> Tambah
                            </button>
                        </div>
                    </form>
                </div>

                <div class="table-responsive text-center">
                    <table class="table table-hover table-bordered table-striped m-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tahun</th>
                                <th>Nama Prodi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = $evaluasis->firstItem(); @endphp
                            @foreach ($evaluasis as $evaluasi)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $evaluasi->targetIndikator->tahunKerja->th_tahun }}</td>
                                    <td>{{ $evaluasi->targetIndikator->prodi->nama_prodi }}</td>
                                    <td>
                                        @if($evaluasi->status == 0)
                                            <a class="btn btn-warning" href="{{ route('evaluasi.index-detail', $evaluasi->eval_id) }}"><i class="fa-solid fa-pen"></i> Isi/Ubah</a>
                                            @if($evaluasi->isFilled())
                                                <button class="btn btn-info finalBtn" data-id="{{ $evaluasi->eval_id }}"><i class="fa-solid fa-check"></i> Final</button>
                                            @else
                                                <button class="btn btn-secondary" disabled><i class="fa-solid fa-lock"></i> Final</button>
                                            @endif
                                        @else
                                            <a class="btn btn-success" href="{{ route('evaluasi.show-evaluasi', $evaluasi->eval_id) }}"><i class="fa-solid fa-eye"></i> Lihat Data</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($evaluasis->hasPages())
                    <div class="card-footer">
                        {{ $evaluasis->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('library/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('library/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Modal Tambah Data
        document.getElementById("showModalBtn").addEventListener("click", function () {
    Swal.fire({
        title: 'Tambah Data Evaluasi',
        html: `
            <form id="form-add-evaluasi">
                <div class="mb-3">
                    <label for="th_id" class="form-label">Tahun</label>
                    <select class="form-control" name="th_id" required>
                        <option value="">Pilih Tahun</option>
                        @foreach ($tahuns as $tahun)
                            <option value="{{ $tahun->th_id }}">{{ $tahun->th_tahun }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="prodi_id" class="form-label">Prodi</label>
                    <select class="form-control" name="prodi_id" required>
                        <option value="">Pilih Prodi</option>
                        @foreach ($prodis as $prodi)
                            <option value="{{ $prodi->prodi_id }}">{{ $prodi->nama_prodi }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        `,
        focusConfirm: false,
        preConfirm: () => {
            const prodiId = document.querySelector('[name="prodi_id"]').value;
            const thId = document.querySelector('[name="th_id"]').value;

            if (!prodiId || !thId) {
                Swal.showValidationMessage('Harap pilih Prodi dan Tahun');
                return false;
            }
            return { prodi_id: prodiId, th_id: thId };
        },
        showCancelButton: true,
        confirmButtonText: 'Tambah Data',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
    }).then((result) => {
        if (result.isConfirmed) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            fetch("{{ route('evaluasi.store') }}", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    prodi_id: result.value.prodi_id,
                    th_id: result.value.th_id
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Sukses', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Gagal', data.message, 'error');
                }
            })
            .catch(() => Swal.fire('Gagal', 'Terjadi kesalahan.', 'error'));
        }
    });
});

document.querySelectorAll('.finalBtn').forEach(button => {
    button.addEventListener('click', function () {
        const evaluasiId = this.getAttribute('data-id');

        Swal.fire({
            title: 'Finalisasi?',
            text: 'Pastikan data sudah benar. Data Final tidak akan bisa dirubah kembali',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, finalisasi!',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/evaluasi/final/${evaluasiId}`, {
                    method: 'POST',
                    headers: { 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Sukses', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', data.message, 'error');
                    }
                })
                .catch(() => Swal.fire('Gagal', 'Terjadi kesalahan.', 'error'));
            }
        });
    });
});
    </script>
@endpush