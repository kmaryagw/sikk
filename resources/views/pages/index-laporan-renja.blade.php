@extends('layouts.app')

@section('title', 'Laporan Rencana Kerja')

@push('style')
    <!-- Tambahkan CSS Libraries jika diperlukan -->
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Laporan Rencana Kerja</h1>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <form class="row g-2 align-items-center">
                    <div class="col-auto">
                        <input class="form-control" name="q" value="{{ request('q') }}" placeholder="Pencarian..." />
                    </div>
                
                    {{-- @if (Auth::user()->role == 'admin' || Auth::user()->role == 'prodi') --}}
                        <div class="col-auto">
                            <select class="form-control" name="unit_id">
                                <option value="">Semua Unit Kerja</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->unit_id }}" {{ request('unit_id') == $unit->unit_id ? 'selected' : '' }}>
                                        {{ $unit->unit_nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                
                        <div class="col-auto">
                            <select class="form-control" name="tahun">
                                <option value="">Pilih Tahun</option>
                                @foreach ($tahuns as $tahun)
                                    <option value="{{ $tahun->th_id }}" {{ request('tahun') == $tahun->th_id ? 'selected' : '' }}>
                                        {{ $tahun->th_tahun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    {{-- @endif --}}
                
                    <div class="col-auto">
                        <button class="btn btn-info"><i class="fa-solid fa-search"></i> Cari</button>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('export-excel.renja') }}" class="btn btn-success">
                            <i class="fa-solid fa-file-excel"></i> Export Excel
                        </a>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('export-pdf.renja') }}" class="btn btn-danger">
                            <i class="fa-solid fa-file-pdf"></i> Export PDF
                        </a>
                    </div>
                </form>                
            </div>

            <div class="table-responsive text-center">
                <table class="table table-hover table-bordered table-striped m-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Program Kerja</th>
                            <th>Unit Kerja</th>
                            <th>Tahun</th>
                            <th>Periode Monev</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = $rencanaKerjas->firstItem(); @endphp
                        @foreach ($rencanaKerjas as $rencanaKerja)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $rencanaKerja->rk_nama }}</td>
                                <td>{{ $rencanaKerja->UnitKerja->unit_nama ?? '-' }}</td>
                                <td>{{ $rencanaKerja->tahunKerja->th_tahun ?? '-' }}</td>
                                <td>
                                    @if($rencanaKerja->periodes->isNotEmpty())
                                        @foreach ($rencanaKerja->periodes as $periode)
                                            <span class="badge badge-info">{{ $periode->pm_nama }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">Tidak ada periode</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                        @if ($suratNomors->isEmpty())
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            @if ($rencanaKerjas->hasPages())
                <div class="card-footer">
                    {{ $rencanaKerjas->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </section>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('library/simpleweather/jquery.simpleWeather.min.js') }}"></script>
    <script src="{{ asset('library/chart.js/dist/Chart.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
    <script src="{{ asset('library/summernote/dist/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('library/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/index-0.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush