{{-- resources/views/pages/detail-monitoring.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Monitoring')

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Detail Monitoring</h1>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>Tahun: <span class="badge badge-primary">{{ $periodemonitoring->tahunKerja->th_tahun }}</span></h4>
                <h4>Periode: <span class="badge badge-info">{{ $periodemonitoring->periodes->first()->pm_nama }}</span></h4>
            </div>

            <div class="card-body">
                @if($programKerjas->isEmpty())
                    <p class="text-center text-muted">Tidak ada program kerja yang tersedia untuk periode ini.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Program Kerja</th>
                                    <th>Unit Kerja</th>
                                    <th>Capaian</th>
                                    <th>Kondisi</th>
                                    <th>Kendala</th>
                                    <th>Tindak Lanjut</th>
                                    <th>Tanggal Tindak Lanjut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach ($programKerjas as $program)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $program->rk_nama }}</td>
                                        <td>{{ $program->unitKerja->unit_nama }}</td>
                                        
                                        @if($program->monitoring)
                                            <td>{{ $program->monitoring->mtg_capaian }}%</td>
                                            <td>{{ $program->monitoring->mtg_kondisi }}</td>
                                            <td>{{ $program->monitoring->mtg_kendala }}</td>
                                            <td>{{ $program->monitoring->mtg_tindak_lanjut }}</td>
                                            <td>{{ \Carbon\Carbon::parse($program->monitoring->mtg_tindak_lanjut_tanggal)->format('d-m-Y') }}</td>
                                        @else
                                            <td colspan="5" class="text-muted">Tidak ada data monitoring</td>
                                        @endif
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