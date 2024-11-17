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
                                            @if($rencana->is_submitted)
                                                <button onclick="showMonitoringModal('{{ $rencana->rk_nama }}', '{{ $periodeMonitoring->pmo_id }}', '{{ $rencana->rk_id }}')" 
                                                    class="btn btn-success btn-sm">
                                                    <i class="fa-solid fa-eye"></i> Lihat Data
                                                </button>
                                            @else
                                                <button onclick="showMonitoringModal('{{ $rencana->rk_nama }}', '{{ $periodeMonitoring->pmo_id }}', '{{ $rencana->rk_id }}')" 
                                                    class="btn btn-warning btn-sm">
                                                    <i class="fa-solid fa-pen-to-square"></i> Isi Monitoring
                                                </button>
                                            @endif
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
            .then(response => response.json())
            .then(data => {
                const capaian = data?.mtg_capaian || '';
                const kondisi = data?.mtg_kondisi || '';
                const kendala = data?.mtg_kendala || '';
                const tindakLanjut = data?.mtg_tindak_lanjut || '';
                const tindakLanjutTanggal = data?.mtg_tindak_lanjut_tanggal || '';
                const bukti = data?.mtg_bukti || null;

                let fileBuktiHTML = '';
                if (bukti) {
                    fileBuktiHTML = `<div class="mb-3"><p><strong>Bukti Terunggah:</strong> <a href="/storage/${bukti}" target="_blank">Lihat Bukti</a></p></div>`;
                }

                Swal.fire({
                    title: `Isi Monitoring untuk ${rencanaKerjaNama}`,
                    width: '75%',
                    html: `
                        <form id="monitoringForm" action="{{ route('monitoring.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="pmo_id" value="${pmo}">
                            <input type="hidden" name="rk_id" value="${rk}">
                            <div class="form-group text-left">
                                <label for="mtg_capaian">Capaian</label>
                                <input type="number" name="mtg_capaian" class="form-control" value="${capaian}" required>
                            </div>
                            <div class="form-group text-left">
                                <label for="mtg_kondisi">Kondisi</label>
                                <input type="text" name="mtg_kondisi" class="form-control" value="${kondisi}" required>
                            </div>
                            <div class="form-group text-left">
                                <label for="mtg_kendala">Kendala</label>
                                <input type="text" name="mtg_kendala" class="form-control" value="${kendala}">
                            </div>
                            <div class="form-group text-left">
                                <label for="mtg_tindak_lanjut">Tindak Lanjut</label>
                                <input type="text" name="mtg_tindak_lanjut" class="form-control" value="${tindakLanjut}">
                            </div>
                            <div class="form-group text-left">
                                <label for="mtg_tindak_lanjut_tanggal">Tanggal Tindak Lanjut</label>
                                <input type="date" name="mtg_tindak_lanjut_tanggal" class="form-control" value="${tindakLanjutTanggal}">
                            </div>
                            <div class="form-group text-left">
                                <label for="mtg_bukti">Bukti</label>
                                <input type="file" name="mtg_bukti" class="form-control">
                            </div>
                            ${fileBuktiHTML}
                        </form>

                        <div class="mt-4">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Data Realisasi</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-hover text-center">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Deskripsi</th>
                                                    <th>Capaian</th>
                                                    <th>Tanggal</th>
                                                    <th>URL</th>
                                                    <th>File</th>
                                                    
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $no = 1; @endphp
                                                @foreach ($realisasi as $item)
                                                    <tr>
                                                        <td>{{ $no++ }}</td>
                                                        <td class="text-wrap">{{ $item->rkr_deskripsi }}</td>
                                                        <td>
                                                            <div class="progress" style="height: 20px;">
                                                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $item->rkr_capaian }}%;" aria-valuenow="{{ $item->rkr_capaian }}" aria-valuemin="0" aria-valuemax="100">
                                                                    {{ $item->rkr_capaian }}%
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>{{ \Carbon\Carbon::parse($item->rkr_tanggal)->format('d-m-Y') }}</td>
                                                        <td class="text-wrap">
                                                            @if($item->rkr_url)
                                                                <a href="{{ $item->rkr_url }}" target="_blank">{{ $item->rkr_url }}</a>
                                                            @else
                                                                Tidak Ada URL
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($item->rkr_file)
                                                                <a class="btn btn-success btn-sm" href="{{ asset('storage/' . $item->rkr_file) }}" target="_blank">Lihat Dokumen</a>
                                                            @else
                                                                Tidak Ada Dokumen
                                                            @endif
                                                        </td>
                                                        
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <td>
                                                            <a href="{{ route('realisasirenja.create', ['rk_id' => $item->rk_id]) }}" class="btn btn-warning btn-sm">
                                                                <i class="fa-solid fa-pen-to-square"></i> Isi Realisasi
                                                            </a>
                                                        </td>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Batal',
                    preConfirm: () => {
                        document.getElementById('monitoringForm').submit();
                    }
                });
            })
            .catch(error => console.error('Error:', error));
    }
</script>
@endpush
