@extends('layouts.app')

@section('title', 'Dashboard')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet"
        href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Dashboard</h1>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-12">
                    <div class="card shadow-sm p-3 text-center text-danger">
                        @foreach ($tahuns as $tahun)
                            @if ($tahun->th_is_aktif === 'y')
                                <h5 class="mb-0"><i class="fa-solid fa-calendar-alt"></i> Tahun Pelaksanaan: <strong>{{ $tahun->th_tahun }}</strong></h5>
                            @endif
                        @endforeach
                    </div>
                </div>                               
                
                <div class="col-lg-6 col-md-6 col-12">

                    {{-- IKU - ADMIN --}}
                    @if (Auth::user()->role == 'admin')
                    <div class="card shadow-sm mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Set IKU</h4>
                            <a class="btn btn-primary" href="{{ route('targetcapaian.index') }}">
                                <i class="fa-solid fa-eye"></i> Lihat Detail
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered text-center">
                                    <thead>
                                        <tr>
                                            <th>Nama Program Studi</th>
                                            <th>Jumlah IKU</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($jumlahiku as $prodi)
                                            <tr>
                                                <td>{{ $prodi->nama_prodi }}</td>
                                                <td>{{ $prodi->target_indikator_count }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-muted">Tidak ada data tersedia</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                
                    {{-- IKU - PRODI & FAKULTAS --}}
                    @if (Auth::user()->role == 'prodi' || Auth::user()->role == 'fakultas')
                    <div class="card shadow-sm mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Set IKU</h4>
                            <a class="btn btn-primary" href="{{ route('targetcapaian.index') }}">
                                <i class="fa-solid fa-eye"></i> Lihat Detail
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered text-center">
                                    <thead>
                                        <tr>
                                            <th>Nama Program Studi</th>
                                            <th>Jumlah IKU</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($jumlahiku as $prodi)
                                            <tr>
                                                <td>{{ $prodi->nama_prodi }}</td>
                                                <td>{{ $prodi->target_indikator_count }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-muted">Tidak ada data tersedia</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                
                    {{-- RENCANA KERJA --}}
                    @if (Auth::user()->role == 'admin')
                    <div class="card shadow-sm mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Rencana Kerja</h4>
                            <a class="btn btn-primary" href="{{ route('programkerja.index') }}">
                                <i class="fa-solid fa-eye"></i> Lihat Detail
                            </a>
                        </div>
                        <div class="card-body">
                            <!-- Total Renja -->
                            @if (Auth::user()->role == 'admin')
                            <div class="text-center">
                                {{-- <h5>Total Renja: <span class="badge bg-primary text-light">{{ $totalrenja }}</span></h5> --}}
                            </div>
                             <!-- Pie Chart -->
                             {{-- <div class="row justify-content-center mb-3">
                                <div class="col-md-8 col-lg-7">
                                    <div class="rounded-4">
                                        <div >
                                            <div class="text-center">
                                                <h5 class="fw-bold text-dark">Rencana Kerja</h5>
                                                <p class="text-muted small">Berdasarkan Unit Kerja</p>
                                            </div>
                                            <div class="d-flex justify-content-center align-items-center" style="height: 308px;">
                                                <canvas id="pieRenja" style="width: 60rem; height: 60rem;"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                            <!-- Pie Chart -->
                            @endif
                            <!-- Tabel Unit Kerja -->
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered text-center">
                                    <thead>
                                        <tr>
                                            <th>Nama Unit Kerja</th>
                                            <th>Jumlah Renja</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($unitKerjarenja as $unit)
                                            <tr>
                                                <td>{{ $unit->unit_nama }}</td>
                                                <td>{{ $unit->rencana_kerja_count }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-muted">Tidak ada data tersedia</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                
                </div>
                

                <div class="col-lg-6 col-md-6 col-12">
                    {{-- IKT PRODI FAKULTAS--}}
                    @if (Auth::user()->role == 'prodi' || Auth::user()->role == 'fakultas')
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h4 class="mb-0">Set IKT</h4>
                            <div class="card-header-action">
                                <a class="btn btn-primary" href="{{ route('targetcapaian.index') }}"><i class="fa-solid fa-eye"></i> Lihat Detail </a>
                            </div>
                        </div>
                        {{-- <div class="mt-4 text-center">
                            <h5>Total IKT: <span class="badge bg-primary text-light">{{ $totalikt }}</span></h5>
                        </div> --}}
                        <!-- Bar Chart Jumlah IKT -->
                        {{-- <div class="row justify-content-center">
                            <div class="col-md-8 col-lg-7">
                                <div class="rounded-4">
                                    <div class="card-body px-4 py-4">
                                        <div class="text-center mb-3">
                                            <h5 class="fw-bold text-dark mb-1">Distribusi IKT per Program Studi</h5>
                                            <p class="text-muted small mb-0">Jumlah indikator kinerja tambahan</p>
                                        </div>
                                        <div class="d-flex justify-content-center align-items-center" style="height: 400px;">
                                            <canvas id="barIKT" style="width: 60rem; height: 60rem;"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>--}}
                        <!-- Bar Chart Jumlah IKT -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead class="text-center">
                                        <tr>
                                            <th>Nama Program Studi</th>
                                            <th style="text-align: center;">Jumlah IKT</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                        @forelse ($jumlahikt as $prodi)
                                            <tr>
                                                <td>{{ $prodi->nama_prodi }}</td>
                                                <td style="text-align: center;">{{ $prodi->target_indikator_count }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">Tidak ada data tersedia</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                    {{-- IKT PRODI FAKULTAS--}}

                    {{-- IKT Admin --}}
                    @if (Auth::user()->role == 'admin')
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h4 class="mb-0">Set IKT</h4>
                            <div class="card-header-action">
                                <a class="btn btn-primary" href="{{ route('targetcapaian.index') }}"><i class="fa-solid fa-eye"></i> Lihat Detail </a>
                            </div>
                        </div>
                        {{-- <div class="mt-4 text-center">
                            <h5>Total IKT: <span class="badge bg-primary text-light">{{ $totalikt }}</span></h5>
                        </div> --}}
                        <!-- Column Chart Jumlah IKT -->                        
                        {{-- <div class="mb-4">
                            <canvas id="iktColumnChart" height="250"></canvas>
                        </div> --}}
                        <!-- Column Chart Jumlah IKT -->                        
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead class="text-center">
                                        <tr>
                                            <th>Nama Program Studi</th>
                                            <th style="text-align: center;">Jumlah IKT</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                        @forelse ($jumlahikt as $prodi)
                                            <tr>
                                                <td>{{ $prodi->nama_prodi }}</td>
                                                <td style="text-align: center;">{{ $prodi->target_indikator_count }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">Tidak ada data tersedia</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif     
                    
                    @if(Auth::user()->role == 'admin')
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h4>Realisasi</h4>
                                <div class="card-header-action">
                                    <a class="btn btn-primary" href="{{ route('realisasirenja.index') }}"><i class="fa-solid fa-eye"></i> Lihat Detail </a>
                                </div>
                            </div>
                            <div class="card-body">
                            {{-- Chart section --}}
                                {{-- <div class="row">
                                    @foreach ($realisasi as $r)
                                        <div class="col-md-6">
                                            <div class="card shadow-sm">
                                                <div class="card-body">
                                                    <h6 class="card-title">{{ $r->unit_nama }}</h6>
                                                    <canvas id="chart-{{ $loop->index }}" height="120"></canvas>
                                                    <!-- Menampilkan jumlah nilai di bawah chart -->
                                                    <div id="value-{{ $loop->index }}" class="mt-3 text-center">
                                                        <span class="value-text" style="font-size: 16px; font-weight: bold;">
                                                           Renja: {{ $r->jumlah_renja }} | Realisasi: {{ $r->jumlah_realisasi }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div> --}}
                                {{-- Chart section --}}
                                {{-- Table moved above chart --}}
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th class="text-center">Unit Kerja</th>
                                                <th class="text-center">Jumlah Renja</th>
                                                <th class="text-center">Jumlah Realisasi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($realisasi as $r)
                                            <tr>
                                                <td class="text-center">{{ $r->unit_nama }}</td>
                                                <td class="text-center">{{ $r->jumlah_renja }}</td>
                                                <td class="text-center">{{ $r->jumlah_realisasi }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">Tidak ada data tersedia</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                {{-- Table moved above chart --}}
                    
                            </div>                         
                        </div>
                    @endif
                </div>
                    
                <div class="col-lg-12 col-md-12 col-12">
                    @if (Auth::user()->role == 'unit kerja')
                    <div class="card shadow-sm mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Rencana Kerja</h4>
                            <a class="btn btn-primary" href="{{ route('programkerja.index') }}">
                                <i class="fa-solid fa-eye"></i> Lihat Detail
                            </a>
                        </div>
                        <div class="card-body">
                            <!-- Total Renja -->
                            @if (Auth::user()->role == 'admin')
                            <div class="text-center">
                                {{-- <h5>Total Renja: <span class="badge bg-primary text-light">{{ $totalrenja }}</span></h5> --}}
                            </div>
                             <!-- Pie Chart -->
                             {{-- <div class="row justify-content-center mb-3">
                                <div class="col-md-8 col-lg-7">
                                    <div class="rounded-4">
                                        <div >
                                            <div class="text-center">
                                                <h5 class="fw-bold text-dark">Rencana Kerja</h5>
                                                <p class="text-muted small">Berdasarkan Unit Kerja</p>
                                            </div>
                                            <div class="d-flex justify-content-center align-items-center" style="height: 308px;">
                                                <canvas id="pieRenja" style="width: 60rem; height: 60rem;"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                            <!-- Pie Chart -->
                            @endif
                            <!-- Tabel Unit Kerja -->
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered text-center">
                                    <thead>
                                        <tr>
                                            <th>Nama Unit Kerja</th>
                                            <th>Jumlah Renja</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($unitKerjarenja as $unit)
                                            <tr>
                                                <td>{{ $unit->unit_nama }}</td>
                                                <td>{{ $unit->rencana_kerja_count }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-muted">Tidak ada data tersedia</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Peroide Monev --}}
                    @if (Auth::user()->role == 'admin')
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h4 class="mb-0">Periode Monev</h4>
                            <div class="card-header-action">
                                <a class="btn btn-primary" href="{{ route('periode-monitoring.index') }}"><i class="fa-solid fa-eye"></i> Lihat Detail </a>
                            </div>
                        </div>
                        {{-- <div class="mb-4">
                            <canvas id="monitoringChart" height="190.5"></canvas>
                        </div>--}}
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead class="text-center">
                                        <tr>
                                            <th>Periode</th>
                                            <th style="text-align: center;">Total Renja</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                        @forelse ($periodemonevrenja as $monitoring)
                                            <tr>
                                                <td>{{ $monitoring->pm_nama }}</td>
                                                <td style="text-align: center;">{{ $monitoring->rencana_kerjas_count }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">Tidak ada data tersedia</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                    {{-- Peroide Monev --}}
                    {{-- SURAT NOMOR --}}
                        @if (Auth::user()->role == 'unit kerja')
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h4 class="mb-0">Surat Nomor</h4>
                                <div class="card-header-action">
                                    <a class="btn btn-primary" href="{{ route('nomorsurat.index') }}"><i class="fa-solid fa-eye"></i> Lihat Detail </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive mt-2" style="max-height: 505px; overflow-y: auto;">
                                    <table class="table table-striped table-bordered">
                                        <!-- Sticky header hanya diterapkan pada tabel Surat -->
                                        <thead class="thead-dark text-center" style="position: sticky; top: 0; background-color: #fff; z-index: 2;">
                                            <tr>
                                                <th>Organisasi Jabatan</th>
                                                <th>Jumlah Surat</th>
                                                <th>Jumlah Revisi</th>
                                                <th>Jumlah Valid</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center">
                                            @foreach($suratSummary as $summary)
                                            <tr>
                                                <td>{{ $summary->organisasiJabatan->oj_nama }}</td>
                                                <td>{{ $summary->jumlah_surat }}</td>
                                                <td>{{ $summary->jumlah_revisi }}</td>
                                                <td>{{ $summary->jumlah_valid }}</td>
                                            </tr>
                                            @endforeach
                                            
                                            @if ($suratSummary->isEmpty())
                                            <tr>
                                                <td colspan="4" class="text-center">Tidak ada data</td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif
                    {{-- SURAT NOMOR --}}
                    {{-- REALISASI ADMIN DAN UNIT KERJA --}}
                        @if (Auth::user()->role == 'unit kerja' || Auth::user()->role == 'fakultas' || Auth::user()->role == 'prodi')                      
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h4 class="mb-0">Realisasi</h4>
                                <div class="card-header-action">
                                    <a class="btn btn-primary" href="{{ route('realisasirenja.index') }}"><i class="fa-solid fa-eye"></i> Lihat Detail </a>
                                </div>
                            </div>
                            <div class="card-body">
                            {{-- Chart section --}}
                                {{-- <div class="row">
                                    @foreach ($realisasi as $r)
                                        <div class="col-md-6">
                                            <div class="card shadow-sm">
                                                <div class="card-body">
                                                    <h6 class="card-title">{{ $r->unit_nama }}</h6>
                                                    <canvas id="chart-{{ $loop->index }}" height="120"></canvas>
                                                    <!-- Menampilkan jumlah nilai di bawah chart -->
                                                    <div id="value-{{ $loop->index }}" class="mt-3 text-center">
                                                        <span class="value-text" style="font-size: 16px; font-weight: bold;">
                                                           Renja: {{ $r->jumlah_renja }} | Realisasi: {{ $r->jumlah_realisasi }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div> --}}
                                {{-- Chart section --}}
                                {{-- Table moved above chart --}}
                                <div class="table-responsive mb-4">
                                    <table class="table table-striped table-bordered">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th class="text-center">Unit Kerja</th>
                                                <th class="text-center">Jumlah Renja</th>
                                                <th class="text-center">Jumlah Realisasi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($realisasi as $r)
                                            <tr>
                                                <td class="text-center">{{ $r->unit_nama }}</td>
                                                <td class="text-center">{{ $r->jumlah_renja }}</td>
                                                <td class="text-center">{{ $r->jumlah_realisasi }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">Tidak ada data tersedia</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                {{-- Table moved above chart --}}
                    
                            </div>                         
                        </div>
                        @endif  
                    </div>
                    
                    {{-- END REALISASI ADMIN DAN UNIT KERJA --}}


                    <div class="col-lg-12 col-md-12 col-12">

                        {{-- REALISASI UNIT KERJA--}}
                        {{-- @if (Auth::user()->role == 'unit kerja')
                        <div class="card shadow-sm">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">Realisasi</h4>
                                <a class="btn btn-primary" href="{{ route('realisasirenja.index') }}">
                                    <i class="fa-solid fa-eye"></i> Lihat Detail
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach ($realisasi as $r)
                                        <div class="col-md-6">
                                            <div class="card shadow-sm">
                                                <div class="card-body">
                                                    <h6 class="card-title">{{ $r->unit_nama }}</h6>
                                                    <canvas id="chart-{{ $loop->index }}" height="120"></canvas>
                                                    <!-- Menampilkan jumlah nilai di bawah chart -->
                                                    <div id="value-{{ $loop->index }}" class="mt-3 text-center">
                                                        <span class="value-text" style="font-size: 16px; font-weight: bold;">
                                                            Realisasi: {{ $r->jumlah_realisasi }} | Renja: {{ $r->jumlah_renja }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead class="thead-dark text-center">
                                            <tr>
                                                <th>Unit Kerja</th>
                                                <th>Jumlah Renja</th>
                                                <th>Jumlah Realisasi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($realisasi as $r)
                                                <tr>
                                                    <td class="text-center">{{ $r->unit_nama }}</td>
                                                    <td class="text-center">{{ $r->jumlah_renja }}</td>
                                                    <td class="text-center">{{ $r->jumlah_realisasi }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted">Tidak ada data tersedia</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif --}}

                    {{-- MONITORING --}}
                        @if (Auth::user()->role == 'admin' || Auth::user()->role == 'unit kerja' || Auth::user()->role == 'fakultas' || Auth::user()->role == 'prodi')
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h4 class="mb-0">Monitoring</h4>
                                <div class="card-header-action">
                                    <a class="btn btn-primary" href="{{ route('monitoring.index') }}"><i class="fa-solid fa-eye"></i> Lihat Detail </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead class="text-center">
                                            <tr>
                                                <th rowspan="2">Periode</th>
                                                <th rowspan="2">Total Renja</th>
                                                <th colspan="4" style="background-color: #EAEAEA;">Status</th>
                                            </tr>
                                            <tr>
                                                <th>Tercapai</th>
                                                <th>Belum Tercapai</th>
                                                <th>Tidak Terlaksana</th>
                                                <th>Perlu Tindak Lanjut</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center">
                                            @foreach($renjaPerPeriode as $periode)
                                            <tr>
                                                <td>{{ $periode->pm_nama }}</td>
                                                <td>{{ $periode->rencanaKerjas->count() }}</td>
                                                <td>{{ $periode->rencanaKerjas->sum('tercapai') }}</td>
                                                <td>{{ $periode->rencanaKerjas->sum('belum_tercapai') }}</td>
                                                <td>{{ $periode->rencanaKerjas->sum('tidak_terlaksana') }}</td>
                                                <td>{{ $periode->rencanaKerjas->sum('perlu_tindak_lanjut') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>                                    
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    {{-- END MONITORING --}}
            </div>
        </section>
    </div>
@endsection

@push('scripts')

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    

    {{-- RENJA PIECHART --}}
    <script>
        const pieRenja = new Chart(document.getElementById('pieRenja'), {
            type: 'pie',
            data: {
                labels: {!! json_encode($unitKerjarenja->pluck('unit_nama')) !!},
                datasets: [{
                    data: {!! json_encode($unitKerjarenja->pluck('rencana_kerja_count')) !!},
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
    {{-- RENJA PIECHART --}}

    {{-- IKT BARCHART --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById('iktColumnChart').getContext('2d');
    
            const data = {
                labels: @json($jumlahikt->pluck('nama_prodi')),
                datasets: [{
                    label: 'Jumlah IKT',
                    data: @json($jumlahikt->pluck('target_indikator_count')),
                    backgroundColor: 'rgba(255, 99, 132, 0.7)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            };
    
            const config = {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Jumlah IKT'
                            },
                            ticks: {
                                stepSize: 1
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Program Studi'
                            },
                            ticks: {
                                autoSkip: false,
                                maxRotation: 45,
                                minRotation: 0
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Distribusi Jumlah IKT per Program Studi'
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    }
                }
            };
    
            new Chart(ctx, config);
        });
    </script>
    {{-- IKT BARCHART --}}

    {{-- IKU BARCHART --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById('ikuColumnChart').getContext('2d');
    
            const data = {
                labels: @json($jumlahiku->pluck('nama_prodi')),
                datasets: [{
                    label: 'Jumlah IKU',
                    data: @json($jumlahiku->pluck('target_indikator_count')),
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            };
    
            const config = {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Jumlah IKU'
                            },
                            ticks: {
                                stepSize: 1
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Program Studi'
                            },
                            ticks: {
                                autoSkip: false,
                                maxRotation: 45,
                                minRotation: 0
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Distribusi Jumlah IKU per Program Studi'
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    }
                }
            };
    
            new Chart(ctx, config);
        });
    </script>
    
    {{-- IKU BARCHART --}}
    
    {{-- REALISASI DONUT CHART --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            @foreach ($realisasi as $r)
            const ctx{{ $loop->index }} = document.getElementById('chart-{{ $loop->index }}').getContext('2d');
            new Chart(ctx{{ $loop->index }}, {
                type: 'doughnut',
                data: {
                    labels: ['Renja', 'Realisasi'],  
                    datasets: [{
                        label: '{{ $r->unit_nama }}',
                        data: [{{ $r->jumlah_renja }}, {{ $r->jumlah_realisasi }}], 
                        backgroundColor: ['rgba(75, 192, 192, 0.6)', 'rgba(255, 99, 132, 0.6)'], 
                        borderColor: ['#4BC0C0', '#FF6384'], 
                        borderWidth: 2,
                        hoverBackgroundColor: ['rgba(75, 192, 192, 0.8)', 'rgba(255, 99, 132, 0.8)'],
                        hoverBorderColor: ['#4BC0C0', '#FF6384'],
                        hoverBorderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                generateLabels: function(chart) {
                                    const labels = chart.data.datasets.map((dataset, index) => {
                                        return {
                                            text: dataset.label + ' - Unit Kerja: {{ $r->unit_nama }}',
                                            fillStyle: index === 0 ? '#4BC0C0' : '#FF6384',  
                                            strokeStyle: index === 0 ? '#4BC0C0' : '#FF6384',
                                        };
                                    });
                                    return labels;
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Renja: ' + context.raw[0] + ', Realisasi: ' + context.raw[1]; // Urutan tooltip sesuai chart
                                }
                            },
                            backgroundColor: 'rgba(0, 0, 0, 0.7)',
                            titleColor: 'white',
                            bodyColor: 'white',
                            displayColors: false,
                        },
                        datalabels: {
                            color: 'black',
                            font: {
                                weight: 'bold',
                                size: 16
                            },
                            formatter: function(value, context) {
                                return value;
                            },
                            textShadow: '2px 2px 4px rgba(0, 0, 0, 0.6)',
                        }
                    },
                    cutoutPercentage: 50,
                    scales: {
                        r: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)',
                                lineWidth: 1
                            },
                            angleLines: {
                                display: true,
                                lineWidth: 2,
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeInOutQuad'
                    }
                }
            });
            @endforeach
        });
    </script>   
    {{-- REALISASI DONUT CHART --}}
    
    {{-- UNIT KERJA RENJA BAR CHART --}}   
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById('unitKerjaChart').getContext('2d');
            const data = {
                labels: @json($unitKerjarenja->pluck('unit_nama')),
                datasets: [{
                    label: 'Jumlah Renja',
                    data: @json($unitKerjarenja->pluck('rencana_kerja_count')),
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            };
            const config = {
                type: 'bar',
                data: data,
                options: {
                    indexAxis: 'y',
                    scales: {
                        x: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Jumlah Renja per Unit Kerja'
                        },
                        legend: {
                            display: false
                        }
                    }
                }
            };
            new Chart(ctx, config);
        });
    </script>
    {{-- UNIT KERJA RENJA BAR CHART --}}   

    {{-- IKT BAR CHART PRODI --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById('barIKT').getContext('2d');
    
            const data = {
                labels: @json($jumlahikt->pluck('nama_prodi')),
                datasets: [{
                    label: 'Jumlah IKT',
                    data: @json($jumlahikt->pluck('target_indikator_count')),
                    backgroundColor: 'rgba(153, 102, 255, 0.7)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            };
    
            const maxValue = Math.max(...data.datasets[0].data) + 1;
    
            const config = {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            suggestedMax: maxValue,
                            ticks: {
                                stepSize: 1
                            }
                        },
                        x: {
                            ticks: {
                                autoSkip: false,
                                maxRotation: 45,
                                minRotation: 0
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Jumlah IKT per Program Studi'
                        },
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    }
                }
            };
    
            new Chart(ctx, config);
        });
    </script>    
    {{-- IKT BAR CHART PRODI --}}

    {{-- IKU BAR CHART PRODI --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById('barIKU').getContext('2d');
    
            const data = {
                labels: @json($jumlahiku->pluck('nama_prodi')),
                datasets: [{
                    label: 'Jumlah IKU',
                    data: @json($jumlahiku->pluck('target_indikator_count')),
                    backgroundColor: 'rgba(255, 159, 64, 0.7)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1
                }]
            };
    
            const maxValue = Math.max(...data.datasets[0].data) + 1;
    
            const config = {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            suggestedMax: maxValue,
                            ticks: {
                                stepSize: 1
                            }
                        },
                        x: {
                            ticks: {
                                autoSkip: false,
                                maxRotation: 45,
                                minRotation: 0
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Jumlah IKU per Program Studi'
                        },
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    }
                }
            };
            
            new Chart(ctx, config);
        });
        </script>
    {{-- IKU BAR CHART PRODI --}}
    
    {{-- MONITORING Periode BAR CHART --}}    
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var labels = [];
            var totalRenjaData = [];
    
            @foreach ($periodemonevrenja as $monitoring)
                labels.push("{{ $monitoring->pm_nama }}");
                totalRenjaData.push({{ $monitoring->rencana_kerjas_count }});
            @endforeach
    
            var ctx = document.getElementById('monitoringChart').getContext('2d');
            var monitoringChart = new Chart(ctx, {
                type: 'bar',  
                data: {
                    labels: labels,  
                    datasets: [{
                        label: 'Total Renja',
                        data: totalRenjaData,  
                        backgroundColor: 'rgba(75, 192, 192, 0.6)', 
                        borderColor: 'rgba(75, 192, 192, 0.6)',
                        borderWidth: 2,
                        hoverBackgroundColor: 'rgba(75, 192, 192, 0.8)',
                        hoverBorderColor: 'rgba(75, 192, 192, 0.8)',
                        hoverBorderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,  
                            ticks: {
                                stepSize: 1,  
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top'  
                        },
                        title: {
                            display: true,  
                            text: 'Total Renja per Periode'  
                        }
                    }
                }
            });
        });
    </script>
    {{-- MONITORING Periode BAR CHART --}}    


    <!-- JS Libraies -->
    <script src="{{ asset('library/simpleweather/jquery.simpleWeather.min.js') }}"></script>
    <script src="{{ asset('library/chart.js/dist/Chart.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
    <script src="{{ asset('library/summernote/dist/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('library/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/index-0.js') }}"></script>
@endpush
