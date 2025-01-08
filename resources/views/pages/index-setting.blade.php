@extends('layouts.app')
@section('title', 'Setting IKU')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Daftar Setting IKU</h1>
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
                                <th>Indikator Kinerja Utama</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = $settings->firstItem(); @endphp
                            @foreach ($settings as $setting)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $setting->IndikatorKinerja->tahunKerja->th_tahun }}</td>
                                    <td>{{ $setting->IndikatorKinerja->ik_nama }}</td>
                                    <td>
                                        @if($setting->status == 0)
                                            <a class="btn btn-warning" href="{{ route('setting.index-detail', $setting->eval_id) }}"><i class="fa-solid fa-pen-to-square"></i> Isi/Ubah</a>
                                            
                                
                                        @else
                                            <a class="btn btn-success" href="{{ route('setting.show-setting', $setting->id_setting) }}"><i class="fa-solid fa-eye"></i> Lihat Data</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($settings->hasPages())
                    <div class="card-footer">
                        {{ $settings->links('pagination::bootstrap-5') }}
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
        title: 'Tambah Data Setting IKU',
        html: `
            <form id="form-add-setting">
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
                    <label for="ik_id" class="form-label">Indikator Kinerja Utama</label>
                    <select class="form-control" name="ik_id" required>
                        <option value="">Pilih IKU</option>
                        @foreach ($ikus as $iku)
                            <option value="{{ $iku->ik_id }}">{{ $iku->ik_nama }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        `,
        focusConfirm: false,
        preConfirm: () => {
            const ikId = document.querySelector('[name="ik_id"]').value;
            const thId = document.querySelector('[name="th_id"]').value;

            if (!ikId || !thId) {
                Swal.showValidationMessage('Harap pilih IKU dan Tahun');
                return false;
            }
            return { ik_id: ikId, th_id: thId };
        },
        showCancelButton: true,
        confirmButtonText: 'Tambah Data',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
    }).then((result) => {
        if (result.isConfirmed) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            fetch("{{ route('setting.store') }}", {
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