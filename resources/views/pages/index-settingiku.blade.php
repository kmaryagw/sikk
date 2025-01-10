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
                <h1>Setting IKU</h1>
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
                                <th>Indikator Kinerja</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = $settings->firstItem(); @endphp
                            @foreach ($settings as $setting)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $setting->tahunKerja->th_tahun }}</td>
                                    <td>{{ $setting->indikatorKinerja->ik_nama }}</td>
                                    <td>
                                        <form action="{{ route('settingiku.destroy', $setting->id_setting) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger" onclick="return confirm('Hapus setting ini?')"><i class="fa-solid fa-trash"></i> Hapus</button>
                                        </form>
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
                title: 'Tambah Setting IKU',
                html: `
                    <form id="form-add-settingiku">
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
                            <label for="ik_id" class="form-label">Indikator Kinerja</label>
                            <select class="form-control" name="ik_id" required>
                                <option value="">Pilih Indikator Kinerja</option>
                                @foreach ($indikatorKinerjas as $indikator)
                                    <option value="{{ $indikator->ik_id }}">{{ $indikator->ik_nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                `,
                focusConfirm: false,
                preConfirm: () => {
                    const indikatorKinerjaId = document.querySelector('[name="ik_id"]').value;
                    const thId = document.querySelector('[name="th_id"]').value;

                    if (!indikatorKinerjaId || !thId) {
                        Swal.showValidationMessage('Harap pilih Indikator Kinerja dan Tahun');
                        return false;
                    }
                    return { ik_id: indikatorKinerjaId, th_id: thId };
                },
                showCancelButton: true,
                confirmButtonText: 'Tambah Data',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                    fetch("{{ route('settingiku.store') }}", {
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(result.value)
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
    </script>
@endpush
