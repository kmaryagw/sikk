@extends('layouts.app')
@section('title', 'Lihat Monitoring')

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <!-- Mengambil nama rencana kerja dari elemen pertama koleksi -->
            <h1>Lihat Data Monitoring 
                
            </h1>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Tahun : <span class="badge badge-primary">{{ $periodeMonitoring->tahunKerja->th_tahun }}</span></h4>
                    <h4>
                        Periode :
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
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr class="text-center">
                                <th>Nama Rencana Kerja</th>
                                <th>Unit Kerja</th>
                                <th>Capaian</th>
                                <th>Kondisi</th>
                                <th>Kendala</th>
                                <th>Tindak Lanjut</th>
                                <th>Bukti</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rencanaKerja as $rencana)
                                <tr class="text-center align-middle">
                                    <td class="px-3 py-3">{{ $rencana->rk_nama }}</td>
                                    <td class="px-3 py-3">{{ $rencana->unitKerja->unit_nama ?? 'Tidak Ada' }}</td>
                        
                                    @if ($rencana->is_monitored)
                                        @php $monitoring = $rencana->monitoring->first(); @endphp
                                        <td class="px-3 py-4">{{ $monitoring->mtg_capaian }}</td>
                                        <td class="px-4 py-4">{{ $monitoring->mtg_kondisi }}</td>
                                        <td class="px-4 py-4">{{ $monitoring->mtg_kendala }}</td>
                                        <td class="px-4 py-4">{{ $monitoring->mtg_tindak_lanjut }}</td>
                                        <td class="px-4 py-4">
                                            @if ($monitoring->mtg_bukti)
                                                <a href="{{ Storage::url($monitoring->mtg_bukti) }}"
                                                   target="_blank"
                                                   class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i> Lihat Bukti
                                                </a>
                                            @else
                                                <span class="text-muted">Tidak ada bukti</span>
                                            @endif
                                        </td>
                                    @else
                                        <td colspan="6" class="px-3 py-3 text-warning font-weight-bold">
                                            Belum melakukan monitoring
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>                        
                    </table>
                    <div class="card-footer text-right">
                        <a class="btn btn-danger" href="{{ route('monitoring.index') }}">
                            <i class="fa-solid fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
