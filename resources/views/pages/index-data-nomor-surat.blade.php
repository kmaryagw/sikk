@extends('layouts.app')
@section('title', 'Data Nomor Surat')

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
            background-color: white;
            color: rgb(0, 0, 0);
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
            border-left: 4px solid #ef6767;
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
                        <div class="card mb-4 shadow-sm">
                            <div class="card-body p-3">
                                <form class="row g-3 align-items-center" method="GET" action="{{ route('datanomorsurat.index') }}">
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text bg-light border-0"><i class="fa-solid fa-search"></i></div>
                                            </div>
                                            <input class="form-control bg-light border-0" name="q" value="{{ $q }}" placeholder="Cari Nomor Surat, Perihal..." />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                    </div>
                                    <div class="col-md-2 text-right">
                                        <button class="btn btn-info w-100 shadow-sm"><i class="fa-solid fa-search"></i> Cari</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table-modern">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="15%">Tanggal & Unit</th>
                                        <th width="15%">Nomor Surat</th>
                                        <th width="25%">Perihal</th>
                                        <th width="10%">Status</th>
                                        <th width="10%" class="text-center">Revisi</th>
                                        <th width="10%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = $dataSurats->firstItem(); @endphp
                                    @forelse($dataSurats as $surat)
                                    <tr>
                                        <td class="text-center">{{ $no++ }}</td>
                                        <td>
                                            <div class="fw-bold">{{ \Carbon\Carbon::parse($surat->sn_tanggal)->format('d M Y') }}</div>
                                            <small class="text-muted">{{ $surat->unitKerja->unit_nama ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <span class="d-block fw-bold text-primary">{{ $surat->sn_nomor ?? 'Belum Valid' }}</span>
                                        </td>
                                        <td>
                                            {{ Str::limit($surat->sn_perihal, 40) }}
                                        </td>
                                        <td>
                                            @if($surat->sn_status == 'draft')
                                                <span class="badge badge-info badge-styled">Draft</span>
                                            @elseif($surat->sn_status == 'ajukan')
                                                <span class="badge badge-warning badge-styled">Menunggu</span>
                                            @elseif($surat->sn_status == 'revisi')
                                                <span class="badge badge-danger badge-styled">Revisi</span>
                                            @else
                                                <span class="badge badge-success badge-styled">Valid</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($surat->sn_revisi)
                                                {{-- Tombol Pop-up Revisi --}}
                                                <button class="btn btn-danger btn-icon" 
                                                        title="Lihat Catatan Revisi"
                                                        data-revisi="{{ $surat->sn_revisi }}"
                                                        data-unit="{{ $surat->unitKerja->unit_nama ?? '-' }}"
                                                        data-nomor="{{ $surat->sn_nomor ?? 'Belum Valid' }}"
                                                        onclick="showRevisiNote(this)">
                                                    <i class="fa-solid fa-comment-dots"></i>
                                                </button>
                                            @else
                                                <i class="fa-solid fa-circle-check"></i>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-info btn-icon" 
                                                title="Lihat Detail Lengkap"
                                                type="button"
                                                data-surat="{{ json_encode($surat) }}"
                                                data-jabatan="{{ $surat->organisasiJabatan->oj_nama ?? '' }}"
                                                data-lingkup="{{ $surat->lingkup->skl_nama ?? '' }} ({{ $surat->lingkup->perihal->skp_nama ?? '' }})"
                                                onclick="showDetail(this)"> {{-- Kirim 'this' (elemen tombol ini) ke fungsi JS --}}
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <img src="{{ asset('img/no-data.svg') }}" alt="No Data" style="height: 100px; opacity: 0.5;">
                                            <p class="mt-3 text-muted">Tidak ada data surat yang ditemukan.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($dataSurats->hasPages())
                            <div class="mt-4 float-right">
                                {{ $dataSurats->links('pagination::bootstrap-4') }}
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
    <!-- JS Libraries -->
    <script src="{{ asset('library/summernote/dist/summernote-bs4.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // --- Logic Tampilkan Detail ---
        function showDetail(button) {
        let data = JSON.parse(button.getAttribute('data-surat'));
        let jabatanNama = button.getAttribute('data-jabatan');
        let lingkupNama = button.getAttribute('data-lingkup');

        // Debugging 
        // console.log("Detail clicked", data); 

        // 2. Mapping Data ke Modal (Sama seperti sebelumnya)
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

        $('#modalDetail').appendTo("body").modal('show');
    }

    // --- Logic Pop-up Revisi (SweetAlert) ---
    function showRevisiNote(button) {
        // 1. Ambil data dari atribut tombol
        var note = button.getAttribute('data-revisi');
        var unit = button.getAttribute('data-unit');
        var nomor = button.getAttribute('data-nomor');

        // 2. Tampilkan SweetAlert
        Swal.fire({
            title: 'Catatan Revisi',
            // Susun HTML agar Rata Tengah dan menampilkan detail
            html: `
                <div class="text-center">
                    <p class="mb-1 text-muted small">Unit : <strong>${unit}</strong></p>
                    <p class="mb-3 text-muted small">No. Surat : <strong>${nomor}</strong></p>
                    <hr>
                    <p class="mt-3 font-weight-bold" style="font-size: 1.1em;">"${note}"</p>
                </div>
            `,
            icon: 'warning',
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#fc544b',
            showClass: {
                popup: 'animate__animated animate__fadeInDown'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp'
            }
        });
    }
</script>
@endpush