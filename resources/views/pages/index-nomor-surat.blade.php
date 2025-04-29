@extends('layouts.app')
@section('title', 'Nomor Surat')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush


@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Daftar Surat</h1>
            </div>
{{-- test --}}
            <div class="card mb-3">
                <div class="card-header">
                    <form class="row g-2 align-items-center" method="GET" action="{{ route('nomorsurat.index') }}">
                        <div class="col-auto">
                            <input class="form-control" name="q" value="{{ $q }}" placeholder="Pencarian..." />
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-info"><i class="fa-solid fa-search"></i> Cari</button>
                        </div>
                        <div class="col-auto">
                            <a class="btn btn-primary" href="{{ route('nomorsurat.create') }}"><i class="fa-solid fa-plus"></i> Tambah</a>
                        </div>
                    </form>
                </div>

                <div class="table-responsive text-center">
                    <table class="table table-hover table-bordered table-striped m-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nomor Surat</th>
                                <th>Organisasi Jabatan</th>
                                <th>Lingkup</th>
                                <th>Tanggal</th>
                                <th>Perihal</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th>Revisi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = $suratNomors->firstItem(); @endphp
                            @foreach($suratNomors as $surat)
                            <tr>
                                <td style="padding :3rem;">{{ $no++}}</td>
                                <td>{{ $surat->sn_nomor ?? 'Belum Valid '}}</td>
                                <td>{{ $surat->organisasiJabatan->oj_nama }} ({{ $surat->organisasiJabatan->parent->oj_nama ?? '-' }}, {{ $surat->organisasiJabatan->parent->parent->oj_nama ?? '-' }})</td>
                                <td>
                                    <div class="d-flex align-items-center text-truncate" style="max-width: 250px;">
                                        <span class="text-truncate" style="max-width: 220px;" 
                                              data-bs-toggle="tooltip" 
                                              data-bs-placement="top" 
                                              title="{{ $surat->lingkup->skl_nama }} ({{ $surat->lingkup->perihal->skp_nama ?? '' }}, {{ $surat->lingkup->perihal->fungsi->skf_nama ?? '' }})">
                                            {{ $surat->lingkup->skl_nama }} ({{ $surat->lingkup->perihal->skp_nama ?? '' }}, {{ $surat->lingkup->perihal->fungsi->skf_nama ?? '' }})
                                        </span>
                                        <i class="fa-solid fa-circle-info ms-2 text-primary" 
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="{{ $surat->lingkup->skl_nama }} ({{ $surat->lingkup->perihal->skp_nama ?? '' }}, {{ $surat->lingkup->perihal->fungsi->skf_nama ?? '' }})"
                                           style="cursor: pointer;">
                                        </i>
                                    </div>
                                </td>                                
                                <td>{{ $surat->sn_tanggal }}</td>
                                <td>{{ $surat->sn_perihal }}</td>
                                <td>{{ $surat->sn_keterangan }}</td>
                                <td>
                                    @if($surat->sn_status == 'draft')
                                        <span class="badge bg-info bg text-light"><i class="fa-solid fa-info-circle"></i> Draft</span>
                                    @elseif($surat->sn_status == 'ajukan')
                                        <span class="badge bg-warning text-light"><i class="fa-solid fa-clock"></i> Menunggu Validasi</span>
                                    @elseif($surat->sn_status == 'revisi')
                                        <span class="badge bg-danger text-light"><i class="fa-solid fa-clipboard-list"></i> Revisi</span>
                                    @else
                                        <span class="badge bg-success text-light"><i class="fa-solid fa-check-circle"></i> Valid</span>
                                    @endif
                                </td>
                                <td>{{ $surat->sn_revisi ?? '-'}}</td>
                                <td>
                                    @php
                                        $oj_nama_lower = strtolower($surat->organisasiJabatan->oj_nama);
                                        $oj_induk_lower = strtolower($surat->organisasiJabatan->parent->oj_nama ?? '');
                                        $sn_status_lower = strtolower($surat->sn_status);
                                        $is_rektor_senat = in_array($oj_nama_lower, ['rektor', 'senat akademik']) || in_array($oj_induk_lower, ['rektor', 'senat akademik']);
                                    @endphp
                                
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Aksi
                                        </button>
                                        <ul class="dropdown-menu">
                                            @if ($sn_status_lower == 'draft' || $sn_status_lower == 'revisi')
                                                @if ($is_rektor_senat)
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('nomorsurat.edit', $surat->sn_id) }}">
                                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <button class="dropdown-item text-danger" onclick="hapusSurat('{{ $surat->sn_id }}')">
                                                            <i class="fa-solid fa-trash"></i> Hapus
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button class="dropdown-item text-primary" onclick="ajukanSurat('{{ $surat->sn_id }}')">
                                                            <i class="fa-solid fa-paper-plane"></i> Ajukan
                                                        </button>
                                                    </li>
                                                @else
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('nomorsurat.edit', $surat->sn_id) }}">
                                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <button class="dropdown-item text-danger" onclick="hapusSurat('{{ $surat->sn_id }}')">
                                                            <i class="fa-solid fa-trash"></i> Hapus
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button class="dropdown-item text-success" onclick="validasiSurat('{{ $surat->sn_id }}')">
                                                            <i class="fa-solid fa-lock"></i> Valid
                                                        </button>
                                                    </li>
                                                @endif
                                            @elseif ($sn_status_lower == 'validasi')
                                                <li>
                                                    <button class="dropdown-item text-secondary" disabled>
                                                        <i class="fa-solid fa-check"></i> Sudah Valid
                                                    </button>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item text-danger" onclick="hapusSurat('{{ $surat->sn_id }}')">
                                                        <i class="fa-solid fa-trash"></i> Hapus
                                                    </button>
                                                </li>
                                            @elseif ($sn_status_lower == 'ajukan')
                                                <li>
                                                    <button class="dropdown-item text-secondary" disabled>
                                                        <i class="fa-solid fa-paper-plane"></i> Sudah Diajukan
                                                    </button>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item text-danger" onclick="hapusSurat('{{ $surat->sn_id }}')">
                                                        <i class="fa-solid fa-trash"></i> Hapus
                                                    </button>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>                                
                                
                            </tr>
                            @endforeach

                            @if ($suratNomors->isEmpty())
                                <tr>
                                    <td colspan="10" class="text-center">Tidak ada data</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @if ($suratNomors->hasPages())
                    <div class="card-footer">
                        {{ $suratNomors->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraries -->
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
    function validasiSurat(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang divalidasi tidak dapat dirubah!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Validasi!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/nomorsurat/${id}/validasi`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire(
                            'Berhasil!',
                            data.message,
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire(
                            'Gagal!',
                            'Terjadi kesalahan saat validasi.',
                            'error'
                        );
                    }
                }).catch(error => {
                    Swal.fire(
                        'Error!',
                        'Terjadi kesalahan dalam koneksi.',
                        'error'
                    );
                    console.error('Error:', error);
                });
            }
        });
    }

    function ajukanSurat(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang diajukan akan ditinjau oleh admin!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Ajukan!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/nomorsurat/${id}/ajukan`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire(
                            'Berhasil!',
                            data.message,
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire(
                            'Gagal!',
                            'Terjadi kesalahan saat validasi.',
                            'error'
                        );
                    }
                }).catch(error => {
                    Swal.fire(
                        'Error!',
                        'Terjadi kesalahan dalam koneksi.',
                        'error'
                    );
                    console.error('Error:', error);
                });
            }
        });
    }

    function hapusSurat(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang dihapus tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/nomorsurat/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(response => response.json())
                  .then(data => {
                      Swal.fire('Terhapus!', 'Data berhasil dihapus.', 'success').then(() => {
                          location.reload();
                      });
                  }).catch(error => {
                      Swal.fire('Error!', 'Data tidak dapat dihapus.', 'error');
                  });
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@endpush