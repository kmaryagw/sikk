@extends('layouts.app')
@section('title', 'Daftar Nomor Surat')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
    
    <style>
        /* --- CSS Custom Table Modern --- */
        .table-modern {
            border-collapse: separate;
            border-spacing: 0 8px; 
            width: 100%;
        }
        
        .table-modern thead th {
            background-color: #ff5550;
            color: white;
            border: none;
            padding: 15px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        .table-modern thead th:first-child { border-radius: 8px 0 0 8px; }
        .table-modern thead th:last-child { border-radius: 0 8px 8px 0; }

        .table-modern tbody tr {
            background-color: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .table-modern tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            background-color: #fbfbfb;
        }

        .table-modern td {
            border: none;
            padding: 15px;
            vertical-align: middle;
            font-size: 14px;
            color: #495057;
        }
        
        .table-modern td:first-child { border-radius: 8px 0 0 8px; }
        .table-modern td:last-child { border-radius: 0 8px 8px 0; }

        /* --- Badge & Button Styles --- */
        .badge-styled {
            padding: 6px 12px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 11px;
            letter-spacing: 0.5px;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 3px;
            transition: all 0.2s;
            border: none;
        }
        .btn-icon:hover { transform: scale(1.15); box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        
        /* --- Modal Styles --- */
        .detail-group {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #6777ef;
        }
        .detail-label {
            font-weight: bold;
            color: #6c757d;
            font-size: 0.85rem;
            display: block;
            margin-bottom: 3px;
        }
        .detail-value {
            font-weight: 500;
            color: #343a40;
            font-size: 1rem;
        }
    </style>
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Daftar Surat</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <!-- Filter & Action Card -->
                        <div class="card mb-4 shadow-sm">
                            <div class="card-body p-3">
                                <form class="row g-2 align-items-center" method="GET" action="{{ route('nomorsurat.index') }}">
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text bg-light border-0"><i class="fa-solid fa-search text-muted"></i></div>
                                            </div>
                                            <input class="form-control bg-light border-0" name="q" value="{{ $q }}" placeholder="Cari Nomor, Perihal..." />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <button class="btn btn-info w-100 shadow-sm"><i class="fa-solid fa-search me-2"></i> Cari</button>
                                    </div>
                                    <div class="col-md-3 text-right">
                                        <a class="btn btn-primary w-100 shadow-sm" href="{{ route('nomorsurat.create') }}">
                                            <i class="fa-solid fa-plus me-2"></i> Tambah Surat
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Data Table -->
                        <div class="table-responsive">
                            <table class="table-modern">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">No</th>
                                        <th width="15%">Tanggal & Unit</th>
                                        <th width="20%">Nomor Surat</th>
                                        <th width="25%">Perihal</th>
                                        <th width="10%">Status</th>
                                        <th width="5%" class="text-center">Revisi</th>
                                        <th width="20%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = $suratNomors->firstItem(); @endphp
                                    @forelse($suratNomors as $surat)
                                    <tr>
                                        <td class="text-center">{{ $no++ }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($surat->sn_tanggal)->format('d M Y') }}</div>
                                            <small class="text-muted">{{ $surat->unitKerja->unit_nama ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <span class="d-block fw-bold text-primary" style="font-size: 14px;">
                                                {{ $surat->sn_nomor ?? 'Belum Valid' }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ Str::limit($surat->sn_perihal, 40) }}
                                        </td>
                                        <td>
                                            @if($surat->sn_status == 'draft')
                                                <span class="badge badge-warning badge-styled">Draft</span>
                                            @elseif($surat->sn_status == 'ajukan')
                                                <span class="badge badge-warning badge-styled">Menunggu validasi</span>
                                            @elseif($surat->sn_status == 'revisi')
                                                <span class="badge badge-danger badge-styled">Revisi</span>
                                            @else
                                                <span class="badge badge-success badge-styled">Valid</span>
                                            @endif
                                        </td>

                                        {{-- Kolom Revisi Pop-up --}}
                                        <td class="text-center">
                                            @if($surat->sn_revisi)
                                                <button class="btn btn-danger btn-icon" 
                                                        title="Lihat Catatan Revisi"
                                                        data-revisi="{{ $surat->sn_revisi }}"
                                                        data-unit="{{ $surat->unitKerja->unit_nama ?? '-' }}"
                                                        data-nomor="{{ $surat->sn_nomor ?? 'Belum Valid' }}"
                                                        onclick="showRevisiNote(this)">
                                                    <i class="fa-solid fa-comment-dots"></i>
                                                </button>
                                            @else
                                                <i class="fa-regular fa-circle-check"></i>
                                            @endif
                                        </td>

                                        {{-- Kolom Aksi --}}
                                        <td class="text-center">
                                            @php
                                                $oj_induk_lower = strtolower($surat->organisasiJabatan->parent->oj_nama ?? '');
                                                $sn_status_lower = strtolower($surat->sn_status ?? '');
                                                $memiliki_induk_rektor = $oj_induk_lower === 'rektor';
                                            @endphp

                                            {{-- 1. Tombol Detail (Selalu Muncul) --}}
                                            <button class="btn btn-info btn-icon" 
                                                title="Lihat Detail"
                                                type="button"
                                                data-surat="{{ json_encode($surat) }}"
                                                data-jabatan="{{ $surat->organisasiJabatan->oj_nama ?? '' }} ({{ $surat->organisasiJabatan->parent->oj_nama ?? '-' }})"
                                                data-lingkup="{{ $surat->lingkup->skl_nama ?? '' }} ({{ $surat->lingkup->perihal->skp_nama ?? '' }})"
                                                onclick="showDetail(this)">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>

                                            {{-- 2. Tombol Edit & Ajukan (Hanya jika Draft/Revisi) --}}
                                            @if (in_array($sn_status_lower, ['draft', 'revisi']))
                                                <a href="{{ route('nomorsurat.edit', $surat->sn_id) }}" 
                                                   class="btn btn-warning btn-icon text-white" 
                                                   title="Edit Data">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>

                                                @if ($memiliki_induk_rektor)
                                                    <button class="btn btn-success btn-icon" 
                                                            title="Ajukan Validasi" 
                                                            onclick="ajukanSurat('{{ $surat->sn_id }}')">
                                                        <i class="fa-solid fa-paper-plane"></i>
                                                    </button>
                                                @endif
                                            @endif

                                            {{-- 3. Tombol Hapus (Selalu Muncul) --}}
                                            <button class="btn btn-danger btn-icon" 
                                                    title="Hapus Surat" 
                                                    onclick="hapusSurat('{{ $surat->sn_id }}')">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <i class="fa-regular fa-circle-xmark fa-3x text-muted"></i>
                                            <p class="mt-3 text-muted">Tidak ada data surat.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($suratNomors->hasPages())
                            <div class="mt-4 float-right">
                                {{ $suratNomors->links('pagination::bootstrap-4') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <!-- Modal Detail Data -->
        <div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="fa-solid fa-file-alt mr-2"></i> Detail Surat</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-group" style="border-left-color: #fc544b;">
                                    <span class="detail-label">Nomor Surat</span>
                                    <span class="detail-value" id="d_nomor"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-group" style="border-left-color: #fc544b;">
                                    <span class="detail-label">Tanggal Surat</span>
                                    <span class="detail-value" id="d_tanggal"></span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="detail-group" style="border-left-color: #fc544b;">
                                    <span class="detail-label">Perihal</span>
                                    <span class="detail-value" id="d_perihal"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-group" style="border-left-color: #fc544b;">
                                    <span class="detail-label">Organisasi Jabatan</span>
                                    <span class="detail-value" id="d_jabatan"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-group" style="border-left-color: #fc544b;">
                                    <span class="detail-label">Lingkup</span>
                                    <span class="detail-value" id="d_lingkup"></span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="detail-group" style="border-left-color: #fc544b;">
                                    <span class="detail-label">Catatan / Keterangan</span>
                                    <p class="detail-value mb-0" id="d_keterangan"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-whitesmoke">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // --- 1. Logic Detail Modal ---
    function showDetail(button) {
        // Ambil data aman dari data-attribute
        let data = JSON.parse(button.getAttribute('data-surat'));
        let jabatanNama = button.getAttribute('data-jabatan');
        let lingkupNama = button.getAttribute('data-lingkup');

        // Isi Modal
        $('#d_nomor').text(data.sn_nomor ? data.sn_nomor : 'Belum Valid');
        $('#d_tanggal').text(data.sn_tanggal);
        $('#d_perihal').text(data.sn_perihal);
        $('#d_keterangan').text(data.sn_keterangan ? data.sn_keterangan : '-');
        $('#d_jabatan').text(jabatanNama);
        $('#d_lingkup').text(lingkupNama);

        // Fix Backdrop & Show
        $('#modalDetail').appendTo("body").modal('show');
    }

    // --- 2. Logic Pop-up Revisi ---
    function showRevisiNote(button) {
        var note = button.getAttribute('data-revisi');
        var unit = button.getAttribute('data-unit');
        var nomor = button.getAttribute('data-nomor');

        Swal.fire({
            title: 'Catatan Revisi',
            html: `
                <div class="text-center">
                    <p class="mb-1 text-muted small">Unit: <strong>${unit}</strong></p>
                    <p class="mb-3 text-muted small">No. Surat: <strong>${nomor}</strong></p>
                    <hr>
                    <p class="mt-3 font-weight-bold" style="font-size: 1.1em;">"${note}"</p>
                </div>
            `,
            icon: 'warning',
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#fc544b',
            showClass: { popup: 'animate__animated animate__fadeInDown' },
            hideClass: { popup: 'animate__animated animate__fadeOutUp' }
        });
    }

    // --- 3. Logic Hapus Surat ---
    function hapusSurat(id) {
        Swal.fire({
            title: 'Hapus Surat?',
            text: "Data yang dihapus tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Loading State
                Swal.fire({title: 'Menghapus...', didOpen: () => { Swal.showLoading() }});

                fetch(`/nomorsurat/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(response => response.json())
                  .then(data => {
                      Swal.fire('Terhapus!', 'Data berhasil dihapus.', 'success')
                          .then(() => location.reload());
                  }).catch(error => {
                      Swal.fire('Error!', 'Gagal menghapus data.', 'error');
                  });
            }
        });
    }

    // --- 4. Logic Ajukan Validasi ---
    function ajukanSurat(id) {
        Swal.fire({
            title: 'Ajukan Validasi?',
            text: "Data akan dikirim ke admin untuk divalidasi.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Ajukan!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({title: 'Memproses...', didOpen: () => { Swal.showLoading() }});

                fetch(`/nomorsurat/${id}/ajukan`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error');
                    }
                }).catch(error => {
                    Swal.fire('Error!', 'Koneksi bermasalah.', 'error');
                });
            }
        });
    }
</script>
@endpush