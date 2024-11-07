{{-- resources/views/pages/detail-monitoring.blade.php --}}
@extends('layouts.app')
@section('title', 'Detail Monitoring')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Detail Monitoring Periode: {{ $periodemonitoring->tahunKerja->th_tahun }} - {{ $periodemonitoring->periodes->first()->pm_nama }}</h1>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5>Program Kerja yang Masuk dalam Periode Ini</h5>
                </div>
                <div class="table-responsive text-center">
                    <table class="table table-hover table-bordered table-striped m-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Program Kerja</th>
                                <th>Periode Monev</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach ($programKerjas as $program)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $program->rk_nama }}</td>
                                    <td>
                                        @foreach ($periodemonitoring->periodes as $periode)
                                            <span class="badge badge-info">{{ $periode->pm_nama }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
@endsection