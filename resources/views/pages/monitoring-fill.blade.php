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
                            <thead class="thead-light">
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
                                            <button onclick="showMonitoringModal('{{ $rencana->rk_nama }}', '{{ $periodeMonitoring->pmo_id }}', '{{ $rencana->rk_id }}')" 
                                                class="btn btn-{{ $rencana->is_submitted ? 'success' : 'warning' }} btn-sm">
                                                <i class="fa-solid {{ $rencana->is_submitted ? 'fa-eye' : 'fa-pen-to-square' }}"></i> 
                                                {{ $rencana->is_submitted ? 'Lihat Data' : 'Isi Monitoring' }}
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
    function showMonitoringModal(rencanaKerjaNama, pmo, rk) {
        fetch(`/monitoring/${pmo}/${rk}/getData`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                const capaian = data[0]?.mtg_capaian || '';
                const kondisi = data[0]?.mtg_kondisi || '';
                const kendala = data[0]?.mtg_kendala || '';
                const tindakLanjut = data[0]?.mtg_tindak_lanjut || '';
                const tindakLanjutTanggal = data[0]?.mtg_tindak_lanjut_tanggal || '';
                const bukti = data[0]?.mtg_bukti || null;

                let fileBuktiHTML = bukti ? `
                    <div class="form-group">
                        <p><strong>Bukti Terunggah</strong></p>
                        <a href="/storage/${bukti}" target="_blank" class="btn btn-success btn-sm">
                            <i class="fa-solid fa-eye"></i> Lihat Bukti
                        </a>
                    </div>` : '';

                const realisasi = data[1].filter(rl => rl.rk_id === rk);

                let viewRealisasi = '';
                realisasi.forEach((rl, index) => {
                    viewRealisasi += `
                        <tr>
                            <td>${index + 1}</td>
                            <td class="text-wrap">${rl.rkr_deskripsi}</td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: ${rl.rkr_capaian}%" aria-valuenow="${rl.rkr_capaian}" aria-valuemin="0" aria-valuemax="100">
                                        ${rl.rkr_capaian}%
                                    </div>
                                </div>
                            </td>
                            <td>${rl.rkr_tanggal ? new Date(rl.rkr_tanggal).toLocaleDateString('id-ID') : 'N/A'}</td>
                            <td class="text-wrap">
                                ${rl.rkr_url ? `<a href="${rl.rkr_url}" target="_blank">${rl.rkr_url}</a>` : 'Tidak Ada URL'}
                            </td>
                            <td>
                                ${rl.rkr_file ? `<a class="btn btn-success btn-sm" href="/storage/${rl.rkr_file}" target="_blank">Lihat Dokumen</a>` : 'Tidak Ada Dokumen'}
                            </td>
                        </tr>`;
                });

                Swal.fire({
                    title: `Isi Monitoring untuk ${rencanaKerjaNama}`,
                    width: '90%',
                    html: `
                        <form id="monitoringForm" action="{{ route('monitoring.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="pmo_id" value="${pmo}">
                            <input type="hidden" name="rk_id" value="${rk}">
                            <div class="form-group text-left">
                                <label for="mtg_capaian">Capaian</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fa-solid fa-percent"></i>
                                        </div>
                                    </div>
                                    <input type="number" name="mtg_capaian" class="form-control" value="${capaian}" required>
                                </div>
                            </div>
                            <div class="form-group text-left">
                                <label for="mtg_kondisi">Kondisi</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fa-solid fa-info-circle"></i>
                                        </div>
                                    </div>
                                    <input type="text" name="mtg_kondisi" class="form-control" value="${kondisi}" required>
                                </div>
                            </div>
                            <div class="form-group text-left">
                                <label for="mtg_kendala">Kendala</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fa-solid fa-exclamation-triangle"></i>
                                        </div>
                                    </div>
                                    <input type="text" name="mtg_kendala" class="form-control" value="${kendala}">
                                </div> 
                            </div>
                            <div class="form-group text-left">
                                <label for="mtg_tindak_lanjut">Tindak Lanjut</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fa-solid fa-tasks"></i>
                                        </div>
                                    </div>
                                    <input type="text" name="mtg_tindak_lanjut" class="form-control" value="${tindakLanjut}">
                                </div>
                            </div>
                            <div class="form-group text-left">
                                <label for="mtg_tindak_lanjut_tanggal">Tanggal Tindak Lanjut</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fa-solid fa-calendar"></i>
                                        </div>
                                    </div>
                                    <input type="date" name="mtg_tindak_lanjut_tanggal" class="form-control" value="${tindakLanjutTanggal}">
                                </div> 
                            </div>
                            <div class="form-group text-left">
                                <label for="mtg_bukti">Bukti</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fa-solid fa-file-upload"></i>
                                        </div>
                                    </div>
                                    <input class="form-control" type="file" name="mtg_bukti" />
                                </div>
                            </div>
                            
                            <div class="form-group">
                                ${fileBuktiHTML}
                            </div>
                        </form>

                        <div class="mt-4">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Data Realisasi ${rencanaKerjaNama}</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Deskripsi Realisasi</th>
                                                    <th>Capaian</th>
                                                    <th>Tanggal Realisasi</th>
                                                    <th>URL</th>
                                                    <th>Dokumen</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${viewRealisasi}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Submit',
                    cancelButtonText: 'Kembali',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-danger'
                    },
                    preConfirm: () => {
                        document.querySelector("#monitoringForm").submit();
                    }
                });
            })
            .catch(error => {
                console.error('Terjadi kesalahan:', error);
                Swal.fire('Error', 'Terjadi kesalahan saat memuat data', 'error');
            });
    }
</script>
@endpush