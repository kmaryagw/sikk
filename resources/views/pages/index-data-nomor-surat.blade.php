@extends('layouts.app')
@section('title', 'Data Nomor Surat')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush


@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Daftar Surat</h1>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <form class="row g-2 align-items-center" method="GET" action="{{ route('datanomorsurat.index') }}">
                        <div class="col-auto">
                            <input class="form-control" name="q" value="{{ $q }}" placeholder="Pencarian..." />
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-info"><i class="fa-solid fa-search"></i> Cari</button>
                        </div>
                    </form>
                </div>

                <div class="table-responsive text-center">
                    <table class="table table-hover table-bordered table-striped m-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nomor Surat</th>
                                <th>Organisasi Jabatan</th>
                                <th>Lingkup</th>
                                <th>Tanggal</th>
                                <th>Perihal</th>
                                <th>Unit Kerja</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th>Revisi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = $dataSurats->firstItem(); @endphp
                            @foreach($dataSurats as $surat)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $surat->sn_nomor ?? 'Belum Valid '}}</td>
                                <td>{{ $surat->organisasiJabatan->oj_nama }} ({{ $surat->organisasiJabatan->parent->oj_nama ?? '-' }}, {{ $surat->organisasiJabatan->parent->parent->oj_nama ?? '-' }})</td>
                                <td>{{ $surat->lingkup->skl_nama }} ({{ $surat->lingkup->perihal->skp_nama ?? '' }}, {{ $surat->lingkup->perihal->fungsi->skf_nama ?? '' }})</td>
                                <td>{{ $surat->sn_tanggal }}</td>
                                <td>{{ $surat->sn_perihal }}</td>
                                <td>{{ $surat->unitKerja->unit_nama }}</td>
                                <td>{{ $surat->sn_keterangan }}</td>
                                <td>
                                    @if($surat->sn_status == 'draft')
                                        <span class="badge bg-info bg text-light"><i class="fa-solid fa-info-circle"></i> Draft</span>
                                    @elseif($surat->sn_status == 'ajukan')
                                        <span class="badge bg-warning text-light"><i class="fa-solid fa-clock"></i> Menunggu Validasi</span>
                                    @elseif($surat->sn_status == 'revisi')
                                        <span class="badge bg-danger text-light"><i class="fa-solid fa-clipboard-list"></i> Revisi</span>
                                    @else
                                        <span class="badge bg-success text-light"><i class="fa-solid fa-check-circle"></i> Valid</span>
                                    @endif
                                </td>
                                <td>{{ $surat->sn_revisi ?? '-' }}</td>
                                
                            </tr>
                            @endforeach

                            @if ($dataSurats->isEmpty())
                                <tr>
                                    <td colspan="10" class="text-center">Tidak ada data</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @if ($dataSurats->hasPages())
                    <div class="card-footer">
                        {{ $dataSurats->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraries -->
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