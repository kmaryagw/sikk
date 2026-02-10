@extends('layouts.app')
@section('title','SPMI')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                @if (Auth::user()->role == 'admin' || Auth::user()->role == 'fakultas' || Auth::user()->role == 'prodi')
                <h1>Daftar Monitoring Indikator Kinerja Per Program Studi</h1>
                @else
                <h1>Daftar Capaian Indikator Kinerja Per Program Studi</h1>
                @endif
            </div>
            <div class="card mb-3">
                <div class="card-header">
                    <form class="row g-2 align-items-center" method="GET" action="{{ route('monitoringiku.index') }}">
                        <div class="col-auto">
                            <input class="form-control" name="q" value="{{ $q }}" placeholder="Pencarian..." />
                        </div>
                        <div class="col-auto">
                            <select class="form-control" name="th_id" onchange="this.form.submit()">
                                <option value=""> Semua Tahun </option>
                                @foreach ($tahuns as $tahun)
                                    <option value="{{ $tahun->th_id }}" {{ request('th_id') == $tahun->th_id ? 'selected' : '' }}>
                                        {{ $tahun->th_tahun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-info"><i class="fa-solid fa-search"></i> Cari</button>
                            <a href="{{ route('monitoringiku.index') }}" class="btn btn-danger ml-3">
                                <i class="fa-solid fa-rotate"></i> Reset
                            </a>
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
                            @php $no = $monitoringikus->firstItem(); @endphp
                            @foreach ($monitoringikus as $monitoringiku)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    {{-- <td>{{ $monitoringiku->targetIndikator->tahunKerja->th_tahun }}</td> --}}
                                    <td>{{ $monitoringiku->tahunKerja->th_tahun }}</td>
                                    <td>{{ $monitoringiku->targetIndikator->prodi->nama_prodi }}</td>
                                    <td>
                                        @php
                                            $user = Auth::user();
                                            $isAdmin = $user->role === 'admin' || $user->role === 'fakultas'|| $user->role === 'prodi';
                                            $unitId = $user->unit_id;

                                            $isFinalForUnit = $monitoringiku->isFinalForUnit($unitId);
                                            
                                            $isYearLocked = $monitoringiku->targetIndikator->tahunKerja->th_is_editable == 0;
                                        @endphp

                                        @if($isYearLocked)
                                            <a class="btn btn-secondary" href="{{ route('monitoringiku.show-monitoringiku', $monitoringiku->mti_id) }}">
                                                <i class="fa-solid fa-lock"></i> Data Terkunci (Lihat)
                                            </a>
                                        @else

                                            @if($monitoringiku->status == 0 && $isAdmin)
                                                <a class="btn btn-primary" href="{{ route('monitoringiku.index-detail', $monitoringiku->mti_id) }}">
                                                    <i class="fa-solid fa-pen-to-square"></i> Lihat Monitoring
                                                </a>

                                            @elseif($monitoringiku->status == 0 && !$isAdmin)
                                                
                                                @if($isFinalForUnit)
                                                    <a class="btn btn-success" href="{{ route('monitoringiku.show-monitoringiku', $monitoringiku->mti_id) }}">
                                                        <i class="fa-solid fa-eye"></i> Lihat Data
                                                    </a>
                                                
                                                @elseif($monitoringiku->isCompleteForCurrentUnit()) 
                                                    <a class="btn btn-warning" href="{{ route('monitoringiku.index-detail', $monitoringiku->mti_id) }}">
                                                        <i class="fa-solid fa-pen-to-square"></i> Isi/Ubah
                                                    </a>

                                                    <button class="btn btn-primary finalBtn" data-id="{{ $monitoringiku->mti_id }}">
                                                        <i class="fa-solid fa-lock"></i> Final
                                                    </button>

                                                @else
                                                    <a class="btn btn-warning" href="{{ route('monitoringiku.index-detail', $monitoringiku->mti_id) }}">
                                                        <i class="fa-solid fa-pen-to-square"></i> Isi/Ubah
                                                    </a>

                                                    <button class="btn btn-secondary" disabled>
                                                        <i class="fa-solid fa-lock"></i> Final
                                                    </button>
                                                @endif

                                            @else
                                                <a class="btn btn-success" href="{{ route('monitoringiku.show-monitoringiku', $monitoringiku->mti_id) }}">
                                                    <i class="fa-solid fa-eye"></i> Lihat Data
                                                </a>
                                                @if($isAdmin)
                                                    <button class="btn btn-danger cancelFinalBtn" data-id="{{ $monitoringiku->mti_id }}">
                                                        <i class="fa-solid fa-unlock"></i> Batalkan Final
                                                    </button>
                                                @endif
                                            @endif

                                        @endif 
                                    </td>
                                    {{-- Debugging td --}}
                                    {{-- <td>
                                        @if(!$isAdmin)
                                            <small class="d-block text-muted">
                                                Lengkap: {{ $monitoringiku->isCompleteForCurrentUnit() ? 'Ya' : 'Tidak' }} <br>
                                                Sudah Final: {{ $isFinalForUnit ? 'Ya' : 'Tidak' }}
                                            </small>
                                        @endif
                                    </td> --}}
                                    {{-- Debugging td --}}
                                    
                                </tr>
                            @endforeach
                            
                            @if ($monitoringikus->isEmpty())
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @if ($monitoringikus->hasPages())
                    <div class="card-footer">
                        {{ $monitoringikus->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    {{-- ✅ Load Library dari CDN agar tidak error 404 --}}
    {{-- <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // ✅ Pastikan tombol "Tambah Data" ada dulu
        const showModalBtn = document.getElementById("showModalBtn");
        if (showModalBtn) {
            showModalBtn.addEventListener("click", function () {
                Swal.fire({
                    title: 'Tambah Data Monitoring IKU',
                    html: `
                        <form id="form-add-monitoring-iku">
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

                        fetch("{{ route('monitoringiku.store') }}", {
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
        }

        // ✅ Event tombol Finalisasi
        $(document).on('click', '.finalBtn', function () {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Finalisasi Unit?',
                text: 'Pastikan semua indikator sudah benar.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Finalisasi',
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/monitoringiku/${id}/finalize-unit`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    })
                    .then(res => res.json())
                    .then(data => {
                        Swal.fire(data.success ? 'Berhasil' : 'Gagal', data.message, data.success ? 'success' : 'error')
                            .then(() => location.reload());
                    });
                }
            });
        });

        $(document).on('click', '.cancelFinalBtn', function () {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Batalkan Finalisasi?',
                text: 'Data unit ini akan bisa diubah kembali.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Batalkan',
                confirmButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/monitoringiku/${id}/cancel-finalize-unit`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    })
                    .then(res => res.json())
                    .then(data => {
                        Swal.fire(data.success ? 'Berhasil' : 'Gagal', data.message, data.success ? 'success' : 'error')
                            .then(() => location.reload());
                    });
                }
            });
        });
    </script>
@endpush