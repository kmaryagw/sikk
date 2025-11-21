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
                                    @php
                                        // Gabungkan semua data lingkup dalam satu string
                                        $lingkupText = trim($surat->lingkup->skl_nama . ' (' .
                                            ($surat->lingkup->perihal->skp_nama ?? '-') . ', ' .
                                            ($surat->lingkup->perihal->fungsi->skf_nama ?? '-') . ')');

                                        // Tentukan batas karakter (misalnya 40)
                                        $maxLength = 40;
                                        $isTooLong = strlen($lingkupText) > $maxLength;
                                    @endphp

                                    <div class="d-flex align-items-center" style="max-width: 250px;">
                                        @if($isTooLong)
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-info text-truncate"
                                                    style="max-width: 220px;"
                                                    onclick="showFullText('{{ addslashes($lingkupText) }}')">
                                                {{ Str::limit($lingkupText, $maxLength) }}
                                            </button>
                                        @else
                                            <span class="text-truncate" style="max-width: 220px;">
                                                {{ $lingkupText }}
                                            </span>
                                        @endif
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
                                <td class="text-center align-middle" style="white-space: nowrap;">
                                    @php
                                        $oj_nama_lower = strtolower($surat->organisasiJabatan->oj_nama ?? '');
                                        $oj_induk_lower = strtolower($surat->organisasiJabatan->parent->oj_nama ?? '');
                                        $sn_status_lower = strtolower($surat->sn_status ?? '');
                                        $memiliki_induk_rektor = $oj_induk_lower === 'rektor';
                                    @endphp

                                    <div class="dropdown d-inline-block" data-bs-display="static">
                                        <button 
                                            class="btn btn-sm btn-primary dropdown-toggle" 
                                            type="button" 
                                            data-bs-toggle="dropdown" 
                                            aria-expanded="false"
                                            style="min-width: 90px;">
                                            Aksi
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 fixed-dropdown" style="font-size: 14px; min-width: 160px;">
                                            
                                            {{-- === STATUS: DRAFT / REVISI === --}}
                                            @if (in_array($sn_status_lower, ['draft', 'revisi']))
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('nomorsurat.edit', $surat->sn_id) }}">
                                                        <i class="fa-solid fa-pen-to-square me-2 text-primary"></i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item text-danger" onclick="hapusSurat('{{ $surat->sn_id }}')">
                                                        <i class="fa-solid fa-trash me-2"></i> Hapus
                                                    </button>
                                                </li>

                                                @if ($memiliki_induk_rektor)
                                                    <li>
                                                        <button class="dropdown-item text-primary" onclick="ajukanSurat('{{ $surat->sn_id }}')">
                                                            <i class="fa-solid fa-paper-plane me-2"></i> Ajukan
                                                        </button>
                                                    </li>
                                                @endif

                                            {{-- === STATUS: AJUKAN === --}}
                                            @elseif ($sn_status_lower == 'ajukan')
                                                <li>
                                                    <button class="dropdown-item text-secondary" disabled>
                                                        <i class="fa-solid fa-paper-plane me-2"></i> Sudah Diajukan
                                                    </button>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item text-danger" onclick="hapusSurat('{{ $surat->sn_id }}')">
                                                        <i class="fa-solid fa-trash me-2"></i> Hapus
                                                    </button>
                                                </li>

                                            {{-- === STATUS: VALIDASI === --}}
                                            @elseif ($sn_status_lower == 'validasi')
                                                <li>
                                                    <button class="dropdown-item text-secondary" disabled>
                                                        <i class="fa-solid fa-check me-2"></i> Sudah Valid
                                                    </button>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item text-danger" onclick="hapusSurat('{{ $surat->sn_id }}')">
                                                        <i class="fa-solid fa-trash me-2"></i> Hapus
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function showFullText(text) {
    Swal.fire({
        title: '<strong>Keterangan</strong>',
        html: `
            <div class="text-start mt-3">${text}</div>
        `,
        position: 'top', // tampil di atas seperti contohmu
        showConfirmButton: false,
        showCloseButton: false,
        allowOutsideClick: true,
        showCancelButton: true,
        cancelButtonText: 'Tutup',
        cancelButtonColor: '#dc3545',
        confirmButtonText: 'Salin',
        confirmButtonColor: '#007bff',
        showConfirmButton: true,
        didOpen: () => {
            // styling tambahan agar manis
            const swalPopup = document.querySelector('.swal2-popup');
            if (swalPopup) {
                swalPopup.style.width = '600px';
                swalPopup.style.fontSize = '14px';
                swalPopup.style.textAlign = 'left';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            navigator.clipboard.writeText(text).then(() => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Keterangan disalin',
                    showConfirmButton: false,
                    timer: 1500
                });
            });
        }
    });
}
</script>
<style>
/* Pastikan dropdown muncul di atas semua dan tidak bikin scroll */
.fixed-dropdown {
    position: fixed !important;
    z-index: 9999;
}
.table-responsive {
    overflow: visible !important; /* supaya dropdown bisa tampil di luar tabel */
}
</style>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@endpush