@extends('layouts.app')

@section('title', 'Isi Monitoring')

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Isi Monitoring</h1>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>Tahun: <span class="badge badge-primary">{{ $periodeMonitoring->tahunKerja->th_tahun }}</span></h4>
                <h4>Periode: <span class="badge badge-info">{{ $periodeMonitoring->periodeMonev->pm_nama }}</span></h4>
            </div>

            <div class="card-body">
                @if($rencanaKerja->isEmpty())
                    <p class="text-center text-muted">Tidak ada rencana kerja yang tersedia untuk periode ini.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Rencana Kerja</th>
                                    <th>Unit Kerja</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rencanaKerja as $index => $rencana)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $rencana->rk_nama }}</td>
                                        <td>{{ $rencana->unitKerja->unit_nama ?? 'N/A' }}</td>
                                        <td>
                                            <button onclick="showMonitoringModal('{{ $rencana->rk_nama }}', '{{ route('realisasirenja.store') }}', '{{ route('monitoring.store', ['rk_id' => $rencana->rk_id, 'pmo_id' => $periodeMonitoring->pmo_id]) }}', '{{ $realisasi->rkr_deskripsi ?? '' }}', '{{ $realisasi->rkr_capaian ?? '' }}')" 
                                               class="btn btn-warning btn-sm">
                                                <i class="fa-solid fa-pen-to-square"></i> Isi Monitoring
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="card-footer text-right">
                <a class="btn btn-danger" href="{{ route('monitoring.index') }}">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function showMonitoringModal(rencanaKerjaNama, realisasiUrl, monitoringUrl, realisasiPageUrl) {
        Swal.fire({
            title: `Isi Monitoring untuk ${rencanaKerjaNama}`,
            html: `
                <div class="row">
                    <!-- Form Monitoring -->
                    <div class="col-md-6">
                        <form id="monitoringForm" action="${monitoringUrl}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-check-circle"></i></span>
                                </div>
                                <input type="number" name="mtg_capaian" class="form-control" placeholder="Masukkan capaian (angka)" required>
                            </div>
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-tachometer-alt"></i></span>
                                </div>
                                <input type="text" name="mtg_kondisi" class="form-control" placeholder="Masukkan kondisi" required>
                            </div>
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-exclamation-triangle"></i></span>
                                </div>
                                <input type="text" name="mtg_kendala" class="form-control" placeholder="Masukkan kendala" required>
                            </div>
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-arrow-right"></i></span>
                                </div>
                                <input type="text" name="mtg_tindak_lanjut" class="form-control" placeholder="Masukkan tindak lanjut" required>
                            </div>
                            <!-- Form Tindak Lanjut Tanggal -->
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-calendar-check"></i></span>
                                </div>
                                <input type="date" name="mtg_tindak_lanjut_tanggal" class="form-control" required>
                            </div>
                            <!-- Form Bukti -->
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-paperclip"></i></span>
                                </div>
                                <input type="file" name="mtg_bukti" class="form-control">
                            </div>
                        </form>
                    </div>
                    
                    <!-- Form Realisasi -->
                    <div class="col-md-6">
                        <form id="realisasiForm" action="${realisasiUrl}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-pencil-alt"></i></span>
                                </div>
                                <input type="text" name="rkr_deskripsi" class="form-control" placeholder="Masukkan deskripsi realisasi" required>
                            </div>
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-trophy"></i></span>
                                </div>
                                <input type="number" name="rkr_capaian" class="form-control" value="{{ old('rkr_capaian') }}" placeholder="Masukkan capaian (angka)" required />
                            </div>
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                </div>
                                <input type="datetime-local" name="rkr_tanggal" class="form-control" required>
                            </div>
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-link"></i></span>
                                </div>
                                <input type="url" name="rkr_url" class="form-control" placeholder="Masukkan URL" required>
                            </div>
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-upload"></i></span>
                                </div>
                                <input type="file" name="rkr_file" class="form-control">
                            </div>
                        </form>
                        <a href="${realisasiPageUrl}" class="btn btn-info btn-sm mt-3" target="_blank">
                            <i class="fa fa-edit"></i> Isi Realisasi
                        </a>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="fa fa-check"></i> Submit Monitoring',
            cancelButtonText: '<i class="fa fa-times"></i> Batal',
            preConfirm: () => {
                // Men-submit form monitoring
                document.getElementById('monitoringForm').submit();
            },
            customClass: {
                popup: 'width-90',
                content: 'p-4',
            }
        });
    }
</script>

<style>
    .swal2-popup {
        width: 80% !important;
    }

    .swal2-content {
        padding: 20px;
    }

    .form-group input-group .input-group-prepend {
        background-color: #f1f1f1;
        border-right: 1px solid #ccc;
    }

    .form-group input-group .input-group-text {
        font-size: 1.2rem;
    }

    .form-group .form-control {
        border-left: none;
    }

    .swal2-confirm {
        background-color: #28a745;
        border-color: #28a745;
    }

    .swal2-cancel {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .swal2-cancel i, .swal2-confirm i {
        margin-right: 5px;
    }

    .btn-info i {
        margin-right: 5px;
    }
</style>


@endpush
