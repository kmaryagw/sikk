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
                <h4>Periode: <span class="badge badge-success">{{ $periodeMonitoring->periodeMonev->pm_nama }}</span></h4>
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
                                            <button onclick="showMonitoringModal('{{ $rencana->rk_nama }}', '{{ route('realisasi.store') }}', '{{ route('monitoring.store', ['rk_id' => $rencana->rk_id, 'pmo_id' => $periodeMonitoring->pmo_id]) }}')" 
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
        function showMonitoringModal(rencanaKerjaNama, realisasiUrl, monitoringUrl) {
            Swal.fire({
                title: `Isi Monitoring untuk ${rencanaKerjaNama}`,
                html: `
                    <form id="monitoringForm" action="${monitoringUrl}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="monitoring_data">Data Monitoring:</label>
                            <input type="text" name="monitoring_data" class="form-control" required>
                        </div>
                    </form>
                    <form id="realisasiForm" action="${realisasiUrl}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="realisasi_data">Data Realisasi:</label>
                            <input type="text" name="realisasi_data" class="form-control" required>
                        </div>
                    </form>
                `,
                showCancelButton: true,
                confirmButtonText: 'Submit',
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    document.getElementById('monitoringForm').submit();
                    document.getElementById('realisasiForm').submit();
                }
            });
        }
    </script>
@endpush
