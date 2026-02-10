@extends('layouts.app')

@section('title','SPMI')

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Isi Monitoring</h1>
        </div>
        <div class="card">
            <div class="card-header">
                <h4>Tahun: <span class="badge badge-primary">{{ $periodeMonitoring->tahunKerja->th_tahun }}</span></h4>
                <h4>
                    Periode:
                    @if ($periodeMonitoring->periodeMonev && $periodeMonitoring->periodeMonev->isNotEmpty())
                        @foreach ($periodeMonitoring->periodeMonev as $periode)
                            <span class="badge badge-info">{{ $periode->pm_nama }}</span>
                        @endforeach
                    @else
                        <span class="text-muted">Tidak ada periode</span>
                    @endif
                </h4>
            </div>

            <div class="card-body">
                @if ($rencanaKerja->isEmpty())
                    <p class="text-center text-muted">Tidak ada rencana kerja yang tersedia untuk periode ini.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Rencana Kerja</th>
                                    <th>Unit Kerja</th>
                                    <th>Periode</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rencanaKerja as $index => $rencana)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $rencana->rk_nama }}</td>
                                        <td>{{ $rencana->unitKerja->unit_nama ?? 'N/A' }}</td>
                                        <td>
                                            @if ($rencana->periodes && $rencana->periodes->isNotEmpty())
                                                @foreach ($rencana->periodes as $periode)
                                                    <span class="badge badge-info">{{ $periode->pm_nama }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">Tidak ada periode</span>
                                            @endif
                                        </td>
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
    // Fungsi untuk menampilkan/menyembunyikan container berdasarkan status monitoring
    function viewq(val) {
    if (val === 'p') {
        document.getElementById('periodeContainer').style.display = 'block';
        document.getElementById('flagContainer').style.display = 'block';
    } else {
        document.getElementById('periodeContainer').style.display = 'none';
        document.getElementById('flagContainer').style.display = 'none';
    }
}


    // Fungsi untuk menampilkan modal SweetAlert untuk form monitoring
    function showMonitoringModal(rencanaKerjaNama, pmo, rk) {
    fetch(`/monitoring/${pmo}/${rk}/getData`)
        .then(response => response.json())
        .then(data => {
            const monitoring = data.monitoring || {};
            const capaian = monitoring.mtg_capaian || '';
            const kondisi = monitoring.mtg_kondisi || '';
            const kendala = monitoring.mtg_kendala || '';
            const tindakLanjut = monitoring.mtg_tindak_lanjut || '';
            const tindakLanjutTanggal = monitoring.mtg_tindak_lanjut_tanggal || '';
            const bukti = monitoring.mtg_bukti || null;
            const status = monitoring.mtg_status || '';
            const selectedPeriodes = monitoring.periodes ? monitoring.periodes.map(p => p.pm_id) : [];
            const flag = monitoring.mtg_flag ? '1' : '0';

            // Jika ada bukti yang sudah terunggah, tampilkan link untuk melihatnya
            let fileBuktiHTML = bukti ? `
                <div class="form-group">
                    <p><strong>Bukti Terunggah</strong></p>
                    <a href="/storage/${bukti}" target="_blank" class="btn btn-success btn-sm">
                        <i class="fa-solid fa-eye"></i> Lihat Bukti
                    </a>
                </div>` : '';

            // Filter data realisasi berdasarkan rk yang bersangkutan
            const realisasi = Array.isArray(data.realisasi) ? data.realisasi.filter(rl => rl.rk_id === rk) : [];
            let viewRealisasi = '';

            if (realisasi.length === 0) {
                viewRealisasi = `
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            <p>Belum ada data realisasi. Silakan lakukan pengisian realisasi.</p>
                            <div class="mt-2">
                                <a href="/realisasirenja/create?rk_id=${rk}" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-plus"></i> Isi Realisasi
                                </a>
                            </div>
                        </td>
                    </tr>`;
            } else {
                realisasi.forEach((rl, index) => {
                    viewRealisasi += `
                        <tr>
                            <td>${index + 1}</td>
                            <td class="text-wrap">${rl.rkr_deskripsi}</td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: ${rl.rkr_capaian}%;" 
                                        aria-valuenow="${rl.rkr_capaian}" aria-valuemin="0" aria-valuemax="100">
                                        ${rl.rkr_capaian}%
                                    </div>
                                </div>
                            </td>
                            <td>${rl.rkr_tanggal ? new Date(rl.rkr_tanggal).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) : 'N/A'}</td>
                            <td class="text-wrap">
                                ${rl.rkr_url ? `<a href="${rl.rkr_url}" target="_blank" class="btn btn-link">Lihat URL</a>` : 'Tidak Ada URL'}
                            </td>
                            <td>
                                ${rl.rkr_file ? `<a class="btn btn-success btn-sm" href="/storage/${rl.rkr_file}" target="_blank">
                                                <i class="fa-solid fa-eye"></i> Lihat Dokumen
                                            </a>` : 'Tidak Ada Dokumen'}
                            </td>
                        </tr>`;
                });
            }
            // --- Bagian Status (PHP ke JS) ---
            // Tentukan status-status yang tersedia
            const hideTindakLanjut = @json($hideTindakLanjut); // Konversi ke JSON
            console.log(hideTindakLanjut); // Cek di konsol browser

            const allowedStatuses = {
                'y': 'Tercapai',
                'n': 'Belum Tercapai',
                't': 'Tidak Terlaksana'
            };
            if (!hideTindakLanjut) {
                allowedStatuses['p'] = 'Perlu Tindak Lanjut';
            }


            // Bangun opsi select secara dinamis
            let statusOptions = '';
            for (const key in allowedStatuses) {
                statusOptions += `<option value="${key}" ${status === key ? 'selected' : ''}>${allowedStatuses[key]}</option>`;
            }
            // --- End Bagian Status ---

            // Bangun HTML untuk checkbox periode
            const periodeContainerHTML = `
                <div class="form-group text-left" id="periodeContainer" style="display: ${status === 'p' ? 'block' : 'none'};">
                    <label>Pilih Periode Monev</label>
                    <div>
                        @foreach ($periodes as $periode)
                            <div class="form-check form-check-inline">
                                <input type="checkbox" class="form-check-input" name="pm_id[]" value="{{ $periode->pm_id }}"
                                    ${selectedPeriodes.includes('{{ $periode->pm_id }}') ? 'checked' : ''}>
                                <label class="form-check-label">{{ $periode->pm_nama }}</label><br>
                            </div>
                        @endforeach
                    </div>
                </div>
            `;

            Swal.fire({
                title: `Isi Monitoring untuk ${rencanaKerjaNama}`,
                width: '90%',
                html: `
                    <form id="monitoringForm" action="{{ route('monitoring.store') }}" method="POST" enctype="multipart/form-data">
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
                            <label for="mtg_status">Status</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fa-solid fa-calendar"></i>
                                    </div>
                                </div>
                                <select onchange="viewq(this.value)" class="form-control" name="mtg_status" id="mtg_status" required>
                                    ${statusOptions}
                                </select>
                            </div>
                        </div>
                        ${periodeContainerHTML}
                        <div class="form-group text-left" id="flagContainer" style="display: none;">
                            <label for="mtg_flag">Tandai Monitoring</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fa-solid fa-flag"></i>
                                    </div>
                                </div>
                                <select name="mtg_flag" id="mtg_flag" class="form-control">
                                    <option value="0" ${flag === '0' ? 'selected' : ''}>Belum Ditandai</option>
                                    <option value="1" ${flag === '1' ? 'selected' : ''}>Sudah Ditandai</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group text-left">
                            <label for="mtg_bukti">Bukti</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fa-solid fa-file"></i>
                                    </div>
                                </div>
                                <input class="form-control" type="url" name="mtg_bukti" value="${bukti ?? ''}" />
                            </div>
                        </div>
                        ${fileBuktiHTML}
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
                    </form>
                `,
                showCancelButton: true,
                cancelButtonText: '<i class="fa-solid fa-times"></i> Batal',
                confirmButtonText: '<i class="fa-solid fa-save"></i> Simpan Monitoring',
                focusConfirm: false,
                preConfirm: () => {
                    const form = document.getElementById('monitoringForm');
                    const formData = new FormData(form);

                    return fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    })
                    .then(response => {
                        console.log({response});
                        if (!response.ok) {
                            return response.json().then(err => {
                                throw new Error(err.message || 'Gagal menyimpan data');
                            });
                        }
                        return response.json();
                    })
                    .catch(error => {
                        console.error({error});
                        Swal.showValidationMessage(`Request failed: ${error}`);
                    });
                }
            })
            .then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: result.value.message,
                }).then(() => {
                    location.reload(); // Reload halaman otomatis tanpa perlu klik OK di alert
                });
            }
        });

        })
        .catch(error => {
            console.error('Error fetching monitoring data:', error);
        });
}

// Fungsi untuk menampilkan/menyembunyikan container berdasarkan status monitoring
function viewq(val) {
    const periodeContainer = document.getElementById('periodeContainer');
    if (val === 'p') {
        periodeContainer.style.display = 'block';
    } else {
        periodeContainer.style.display = 'none';
    }
}

</script>
@endpush
