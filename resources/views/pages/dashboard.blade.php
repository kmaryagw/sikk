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

                    {{-- IKU --}}
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h4 class="mb-0">Set IKU</h4>
                            <div class="card-header-action">
                                <a class="btn btn-primary" href="{{ route('targetcapaian.index') }}"><i class="fa-solid fa-eye"></i> Lihat Detail </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead class="text-center">
                                        <tr>
                                            <th>Nama Program Studi</th>
                                            <th style="text-align: center;">Jumlah IKU</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                        @forelse ($jumlahiku as $prodi)
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
                    
                    {{-- IKT --}}
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h4 class="mb-0">Set IKT</h4>
                            <div class="card-header-action">
                                <a class="btn btn-primary" href="{{ route('targetcapaian.index') }}"><i class="fa-solid fa-eye"></i> Lihat Detail </a>
                            </div>
                        </div>
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

                    {{-- MONITORING --}}
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h4 class="mb-0">Monitoring</h4>
                            <div class="card-header-action">
                                <a class="btn btn-primary" href="{{ route('programkerja.index') }}"><i class="fa-solid fa-eye"></i> Lihat Detail </a>
                            </div>
                        </div>
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
                </div>

                <div class="col-lg-6 col-md-6 col-12">
                    {{-- RENCANA KERJA --}}
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h4 class="mb-0">Rencana Kerja</h4>
                            <div class="card-header-action">
                                <a class="btn btn-primary" href="{{ route('programkerja.index') }}"><i class="fa-solid fa-eye"></i> Lihat Detail </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Total Renja -->
                            <div class="mb-4 text-center">
                                <h5>Total Renja: <span class="badge bg-success">{{ $totalrenja }}</span></h5>
                            </div>
                        
                            <!-- Tabel Unit Kerja -->
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead class="text-center">
                                        <tr>
                                            <th>Nama Unit Kerja</th>
                                            <th style="text-align: center;">Jumlah Renja</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                        @forelse ($unitKerjarenja as $unit)
                                            <tr>
                                                <td>{{ $unit->unit_nama }}</td>
                                                <td style="text-align: center;">{{ $unit->rencana_kerja_count }}</td>
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
                    
                    {{-- REALISASI --}}
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h4 class="mb-0">Realisasi</h4>
                            <div class="card-header-action">
                                <a class="btn btn-primary" href="{{ route('realisasirenja.index') }}"><i class="fa-solid fa-eye"></i> Lihat Detail </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Unit Kerja</th>
                                            <th style="text-align: center;">Jumlah Renja</th>
                                            <th style="text-align: center;">Jumlah Realisasi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($realisasi as $realisasi)
                                            <tr>
                                                <td>{{ $realisasi->unit_nama }}</td>
                                                <td style="text-align: center;">{{ $realisasi->jumlah_renja }}</td>
                                                <td style="text-align: center;">{{ $realisasi->jumlah_realisasi }}</td>
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
                </div>

                <div class="col-lg-12 col-md-12 col-12">
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
                                            <th colspan="4">Status</th>
                                        </tr>
                                        <tr>
                                            <th>Tercapai</th>
                                            <th>Belum Tercapai</th>
                                            <th>Tidak Terlaksana</th>
                                            <th>Perlu Tindak Lanjut</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <!-- Isi jumlah sesuai kategori status -->
                                            <td></td>   <!-- Tercapai -->
                                            <td></td>   <!-- Belum Tercapai -->
                                            <td></td>   <!-- Tidak Terlaksana -->
                                            <td></td>   <!-- Perlu Tindak Lanjut -->
                                        </tr>
                                    </tbody>                                    
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection

@push('scripts')
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
