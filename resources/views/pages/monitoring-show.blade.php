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
                    <h4 class="mb-0">Tahun: <span class="badge badge-primary">{{ $periodeMonitoring->tahunKerja->th_tahun }}</span></h4>
                    <h4 class="mb-0">Periode: <span class="badge badge-info">{{ $periodeMonitoring->periodeMonev->pm_nama }}</span></h4>
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
                                <tr class="text-center">
                                    <td>{{ $rencana->rk_nama }}</td>
                                    <td>{{ $rencana->unitKerja->unit_nama ?? 'Tidak Ada' }}</td>
                                    @if ($rencana->is_monitored)
                                        <td>{{ $rencana->monitoring->first()->mtg_capaian }}</td>
                                        <td>{{ $rencana->monitoring->first()->mtg_kondisi }}</td>
                                        <td>{{ $rencana->monitoring->first()->mtg_kendala }}</td>
                                        <td>{{ $rencana->monitoring->first()->mtg_tindak_lanjut }}</td>
                                        <td>
                                            @if ($rencana->monitoring->first()->mtg_bukti)
                                                <a href="{{ Storage::url($rencana->monitoring->first()->mtg_bukti) }}" target="_blank" class="btn btn-info btn-sm">Lihat Bukti</a>
                                            @else
                                                Tidak ada bukti
                                            @endif
                                        </td>
                                    @else
                                        <td colspan="6" class="text-center text-warning">Belum melakukan monitoring</td>
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
