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
                
                    @if (Auth::user()->role == 'admin' || Auth::user()->role == 'prodi' || Auth::user()->role == 'unit kerja')
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
                            <select class="form-control" name="prodi_id">
                                <option value="">Semua Program Studi</option>
                                @foreach ($prodis as $prodi)
                                    <option value="{{ $prodi->prodi_id }}" {{ request('prodi_id') == $prodi->prodi_id ? 'selected' : '' }}>
                                        {{ $prodi->nama_prodi }}
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
                    @endif
                
                    <div class="col-auto">
                        <button class="btn btn-info"><i class="fa-solid fa-search"></i> Cari</button>
                    </div>
                    <div class="col-auto dropdown">
                        <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-file-excel"></i> Export Excel
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('export-excel.renja') }}">Semua Program Studi</a></li>
                            <li><a class="dropdown-item" href="{{ route('export-excel.renja.if') }}">Informatika</a></li>
                            <li><a class="dropdown-item" href="{{ route('export-excel.renja.rsk') }}">Rekayasa Sistem Komputer</a></li>
                            <li><a class="dropdown-item" href="{{ route('export-excel.renja.bd') }}">Bisnis Digital</a></li>
                            <li><a class="dropdown-item" href="{{ route('export-excel.renja.dkv') }}">Desain Komunikasi Visual</a></li>
                        </ul>
                    </div>                                       
                    <div class="col-auto dropdown">
                        <button class="btn btn-danger dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-file-pdf"></i> Export PDF
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('export-pdf.renja') }}">Semua Data</a></li>
                            <li><a class="dropdown-item" href="{{ route('export-pdf.renja.if') }}">Informatika</a></li>
                            <li><a class="dropdown-item" href="{{ route('export-pdf.renja.rsk') }}">Rekayasa Sistem Komputer</a></li>
                            <li><a class="dropdown-item" href="{{ route('export-pdf.renja.bd') }}">Bisnis Digital</a></li>
                            <li><a class="dropdown-item" href="{{ route('export-pdf.renja.dkv') }}">Desain Komunikasi Visual</a></li>

                        </ul>
                    </div>
                </form>                
            </div>

            <div class="table-responsive text-center">
                <table class="table table-hover table-bordered table-striped m-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tahun</th>
                            <th>Prodi</th>
                            <th>Standar</th> 
                            <th>Program Kerja</th>
                            <th>Unit Kerja</th>
                            <th>Periode Monev</th>
                            <th>Anggaran</th> 
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = $rencanaKerjas->firstItem(); @endphp
                        @foreach ($rencanaKerjas as $rencanaKerja)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $rencanaKerja->tahunKerja->th_tahun ?? '-' }}</td>
                                <td>
                                    @if($rencanaKerja->programStudis->isNotEmpty())
                                        <ul class="list-unstyled">
                                            @foreach ($rencanaKerja->programStudis as $prodi)
                                                <li>{{ $prodi->nama_prodi }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-muted">Tidak ada Program Studi</span>
                                    @endif
                                </td>                                                                
                                <td>{{ $rencanaKerja->standar->std_nama  ?? '-' }} - {{ $rencanaKerja->standar->std_deskripsi  ?? '-' }}</td> <!-- ✅ Standar -->
                                <td>{{ $rencanaKerja->rk_nama }}</td>
                                <td>{{ $rencanaKerja->UnitKerja->unit_nama ?? '-' }}</td>
                                <td>
                                    @if($rencanaKerja->periodes->isNotEmpty())
                                    @foreach ($rencanaKerja->periodes as $periode)
                                    <span class="badge badge-info">{{ $periode->pm_nama }}</span>
                                    @endforeach
                                    @else
                                    <span class="text-muted">Tidak ada periode</span>
                                    @endif
                                </td>
                                <td>
                                    @if($rencanaKerja->anggaran !== null)
                                        Rp {{ number_format($rencanaKerja->anggaran, 0, ',', '.') }}
                                    @else
                                        <span class="text-muted">Tidak ada</span>
                                    @endif
                                </td> <!-- ✅ Anggaran -->
                            </tr>
                        @endforeach
                        @if ($rencanaKerjas->isNotEmpty())
                            <tr>
                                <td colspan="7" class="text-right font-weight-bold">Total Anggaran:</td>
                                <td class="font-weight-bold text-success">
                                    Rp {{ number_format($totalAnggaran, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                        @if ($rencanaKerjas->isEmpty())
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

    <!-- Tambahan untuk mengaktifkan dropdown -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush
