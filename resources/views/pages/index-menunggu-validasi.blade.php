@extends('layouts.app')
@section('title', 'Menunggu Validasi')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
    
    <style>
        /* Custom Table Styling */
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
            margin: 0 2px;
            transition: all 0.3s;
        }
        .btn-icon:hover { transform: scale(1.1); }
        
        /* Modal Detail Styling */
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
                <h1>Validasi Surat</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <!-- Filter Card -->
                        <div class="card mb-4 shadow-sm">
                            <div class="card-body p-3">
                                <form class="row g-3 align-items-center" method="GET" action="{{ route('menungguvalidasi.index') }}">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text bg-light border-0"><i class="fa-solid fa-search"></i></div>
                                            </div>
                                            <input class="form-control bg-light border-0" name="q" value="{{ $q }}" placeholder="Cari Nomor atau Perihal..." />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="date" class="form-control bg-light border-0" name="tanggal" value="{{ request('tanggal') }}" />
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-control bg-light border-0" name="unit">
                                            <option value="">-- Semua Unit Kerja --</option>
                                            @foreach ($units as $u)
                                                <option value="{{ $u->unit_id }}" {{ request('unit') == $u->unit_id ? 'selected' : '' }}>
                                                    {{ $u->unit_nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2 text-right">
                                        <button class="btn btn-primary w-100 shadow-sm"><i class="fa-solid fa-filter"></i> Filter</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Data Table -->
                        <div class="table-responsive">
                            <table class="table-modern">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="15%">Tanggal & Unit</th>
                                        <th width="20%">Nomor Surat</th>
                                        <th width="30%">Perihal</th>
                                        <th width="10%">Status</th>
                                        <th width="20%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = $ajukans->firstItem(); @endphp
                                    @forelse($ajukans as $ajukan)
                                    <tr>
                                        <td class="text-center">{{ $no++ }}</td>
                                        <td>
                                            <div class="fw-bold">{{ \Carbon\Carbon::parse($ajukan->sn_tanggal)->format('d M Y') }}</div>
                                            <small class="text-muted">{{ $ajukan->unitKerja->unit_nama ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <span class="d-block fw-bold text-primary">{{ $ajukan->sn_nomor ?? 'Belum Digenerate' }}</span>
                                        </td>
                                        <td>
                                            {{ Str::limit($ajukan->sn_perihal, 50) }}
                                        </td>
                                        <td>
                                            @if($ajukan->sn_status == 'draft')
                                                <span class="badge badge-info badge-styled">Draft</span>
                                            @elseif($ajukan->sn_status == 'ajukan')
                                                <span class="badge badge-warning badge-styled">Menunggu</span>
                                            @elseif($ajukan->sn_status == 'revisi')
                                                <span class="badge badge-danger badge-styled">Revisi</span>
                                            @else
                                                <span class="badge badge-success badge-styled">Valid</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-info btn-icon" 
                                                title="Lihat Detail"
                                                type="button"
                                                onclick='showDetail(@json($ajukan), "{{ $ajukan->organisasiJabatan->oj_nama ?? '' }}", "{{ $ajukan->lingkup->skl_nama ?? '' }}")'>
                                                <i class="fa-solid fa-eye"></i>
                                            </button>

                                            @if($ajukan->sn_status == 'ajukan')
                                                <button class="btn btn-success btn-icon" title="Validasi" onclick="validasiSurat('{{ $ajukan->sn_id }}')">
                                                    <i class="fa-solid fa-check"></i>
                                                </button>
                                                <button class="btn btn-danger btn-icon" title="Minta Revisi" onclick="openModalRevisi('{{ $ajukan->sn_id }}')">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>                                        
                                            @else
                                                <button class="btn btn-secondary btn-icon" disabled title="Sudah Valid">
                                                    <i class="fa-solid fa-lock"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <p class="mt-3 text-muted">Tidak ada data surat yang ditemukan.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($ajukans->hasPages())
                            <div class="mt-4 float-right">
                                {{ $ajukans->links('pagination::bootstrap-4') }}
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
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fa-solid fa-file-alt mr-2"></i> Detail Surat</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-group">
                                    <span class="detail-label">Nomor Surat</span>
                                    <span class="detail-value" id="d_nomor"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-group">
                                    <span class="detail-label">Tanggal Surat</span>
                                    <span class="detail-value" id="d_tanggal"></span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="detail-group">
                                    <span class="detail-label">Perihal</span>
                                    <span class="detail-value" id="d_perihal"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-group">
                                    <span class="detail-label">Organisasi Jabatan</span>
                                    <span class="detail-value" id="d_jabatan"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-group">
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
                            <div class="col-12" id="box_revisi" style="display:none;">
                                <div class="detail-group" style="background-color: #ffe8e8; border-left-color: #fc544b;">
                                    <span class="detail-label text-danger">Riwayat Revisi Terakhir</span>
                                    <p class="detail-value text-danger mb-0" id="d_revisi"></p>
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

        <!-- Modal Revisi -->
        <div class="modal fade" id="modalRevisi" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="fa-solid fa-triangle-exclamation mr-2"></i> Input Revisi</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formRevisi" class="no-loader"> 
                            @csrf
                            <input type="hidden" id="sn_id_revisi">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Alasan / Catatan Revisi</label>
                                <textarea class="form-control" id="sn_revisi_input" name="sn_revisi" rows="5" placeholder="Tuliskan detail yang perlu diperbaiki..." required></textarea>
                            </div>
                            <div class="d-block text-right">
                                <button type="submit" class="btn btn-danger btn-lg shadow">Kirim Revisi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection


@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // --- Logic Tampilkan Detail ---
    function showDetail(data, jabatanNama, lingkupNama) {
        // Isi Data ke Element Modal
        $('#d_nomor').text(data.sn_nomor ? data.sn_nomor : 'Belum Valid');
        $('#d_tanggal').text(data.sn_tanggal);
        $('#d_perihal').text(data.sn_perihal);
        $('#d_keterangan').text(data.sn_keterangan ? data.sn_keterangan : '-');
        
        $('#d_jabatan').text(jabatanNama);
        $('#d_lingkup').text(lingkupNama);

        if(data.sn_revisi) {
            $('#box_revisi').show();
            $('#d_revisi').text(data.sn_revisi);
        } else {
            $('#box_revisi').hide();
        }

        // Pindahkan ke body agar z-index aman
        $('#modalDetail').appendTo("body").modal('show');
    }

    // --- Logic Revisi Modal ---
    function openModalRevisi(id) {
        $("#sn_id_revisi").val(id);
        $("#sn_revisi_input").val(''); 
        
        // Pindahkan ke body agar z-index aman
        $('#modalRevisi').appendTo("body").modal('show');
    }

    // --- Logic Validasi ---
    function validasiSurat(id) {
        Swal.fire({
            title: 'Validasi Surat?',
            text: "Pastikan data sudah benar. Status akan berubah menjadi Valid.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#47c363',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Validasi!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...', 
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading() }
                });

                fetch(`/menungguvalidasi/${id}/valid`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) { throw new Error(response.statusText); }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal!', 'Terjadi kesalahan saat validasi.', 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'Koneksi bermasalah atau Server Error.', 'error');
                });
            }
        });
    }

    // --- Logic Submit Revisi (PERBAIKAN UTAMA DISINI) ---
    $("#formRevisi").on("submit", function(event) {
        event.preventDefault(); // Mencegah form reload halaman secara default
        
        let id = $("#sn_id_revisi").val();
        let sn_revisi = $("#sn_revisi_input").val();
        let csrfToken = $('input[name="_token"]').val();

        // 1. Tutup modal terlebih dahulu
        $('#modalRevisi').modal('hide');
        
        // 2. Tampilkan Loading
        Swal.fire({
            title: 'Mengirim Revisi...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading() }
        });

        // 3. Lakukan Fetch
        fetch(`/menungguvalidasi/${id}/revisi`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ sn_revisi: sn_revisi }),
        })
        .then(response => {
            // Cek apakah server mengembalikan error (misal 500 atau 404)
            if (!response.ok) {
                throw new Error('Respon Server Error: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            // Jika JSON berhasil diparsing
            if (data.success) {
                Swal.fire({
                    title: "Terkirim!", 
                    text: data.message, 
                    icon: "success"
                }).then(() => {
                    location.reload(); // Reload halaman setelah user klik OK
                });
            } else {
                Swal.fire("Gagal!", data.message || "Terjadi kesalahan.", "error");
            }
        })
        .catch(error => {
            // Menangkap error koneksi atau error parsing JSON
            console.error('Error:', error);
            Swal.fire({
                title: "Gagal!",
                text: "Terjadi kesalahan pada sistem. Silakan coba lagi.",
                icon: "error"
            });
        });
    });
</script>
@endpush