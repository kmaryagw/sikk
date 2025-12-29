@extends('layouts.app')

@section('title', 'Dashboard')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Dashboard</h1>
            </div>
            <div class="row">
                {{-- CARD TAHUN PELAKSANAAN --}}
                <div class="col-lg-12 col-md-12 col-12">
                    <div class="card shadow-sm p-3 text-center text-danger">
                        @foreach ($tahuns as $tahun)
                            @if ($tahun->th_is_aktif === 'y')
                                <h5 class="mb-0"><i class="fa-solid fa-calendar-alt"></i> Tahun Pelaksanaan: <strong>{{ $tahun->th_tahun }}</strong></h5>
                            @endif
                        @endforeach
                    </div>
                </div>                               
                
                <div class="col-lg-12 col-md-12 col-12">
                    {{-- IKT PRODI FAKULTAS (Hanya Prodi & Fakultas) --}}
                    @if (Auth::user()->role == 'prodi' || Auth::user()->role == 'fakultas')
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h4 class="mb-0">Ringkasan IKU/IKT Prodi Saya</h4>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered text-center">
                                    <thead>
                                        <tr>
                                            <th>Nama Program Studi</th>
                                            <th>Jumlah IKT</th>
                                            <th>Tercapai</th>
                                            <th>Terlampaui</th>
                                            <th>Tidak Tercapai</th>
                                            <th>Tidak Terlaksana</th>
                                            <th>% Tuntas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($jumlahikt as $row)
                                            <tr>
                                                <td>{{ $row->nama_prodi }}</td>
                                                <td>{{ $row->jumlah }}</td>
                                                <td>{{ $row->tercapai }}</td>
                                                <td>{{ $row->terlampaui }}</td>
                                                <td>{{ $row->tidak_tercapai }}</td>
                                                <td>{{ $row->tidak_terlaksana }}</td>
                                                <td>{{ $row->persentase_tuntas }}%</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-muted">Tidak ada data tersedia</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                    {{-- END IKT PRODI FAKULTAS --}}

                    {{-- IKU/IKT UNIT KERJA SENDIRI --}}
                    @if (Auth::user()->role == 'unit kerja')
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h4 class="mb-0">IKU/IKT Unit Kerja Saya</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered text-center">
                                    <thead>
                                        <tr>
                                            <th>Nama Unit Kerja</th>
                                            <th>Jumlah IKU/IKT</th>
                                            <th>Tercapai</th>
                                            <th>Terlampaui</th>
                                            <th>Tidak Tercapai</th>
                                            <th>Tidak Terlaksana</th>
                                            <th>% Tuntas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($ikuiktPerUnitSendiri as $row)
                                            <tr>
                                                <td>{{ $row->unit_nama }}</td>
                                                <td>{{ $row->jumlah }}</td>
                                                <td>{{ $row->tercapai }}</td>
                                                <td>{{ $row->terlampaui }}</td>
                                                <td>{{ $row->tidak_tercapai }}</td>
                                                <td>{{ $row->tidak_terlaksana }}</td>
                                                <td>{{ $row->persentase_tuntas }}%</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-muted">Tidak ada data tersedia</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- IKU/IKT ADMIN (Ringkasan Seluruh Prodi) --}}
                   @if (Auth::user()->role == 'admin' || Auth::user()->role == 'prodi' || Auth::user()->role == 'fakultas'|| Auth::user()->role == 'unit kerja')
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h4 class="mb-0">Ringkasan IKU/IKT Seluruh Program Studi</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered text-center">
                                    <thead>
                                        <tr>
                                            <th>Nama Program Studi</th>
                                            <th>Jumlah IKU/IKT</th>
                                            <th>Tercapai</th>
                                            <th>Terlampaui</th>
                                            <th>Tidak Tercapai</th>
                                            <th>Tidak Terlaksana</th>
                                            <th>% Tuntas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($ikuiktPerProdiSemua as $row)
                                            <tr>
                                                <td>{{ $row->nama_prodi }}</td>
                                                <td>{{ $row->jumlah }}</td>
                                                <td>{{ $row->tercapai }}</td>
                                                <td>{{ $row->terlampaui }}</td>
                                                <td>{{ $row->tidak_tercapai }}</td>
                                                <td>{{ $row->tidak_terlaksana }}</td>
                                                <td>{{ $row->persentase_tuntas }}%</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-muted">Tidak ada data tersedia</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- RINGKASAN SELURUH UNIT KERJA & STATUS FINALISASI --}}
                    @if (Auth::user()->role == 'unit kerja' || Auth::user()->role == 'admin' || Auth::user()->role == 'prodi'|| Auth::user()->role == 'fakultas')
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h4 class="mb-0">Ringkasan IKU/IKT Seluruh Unit Kerja</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered text-center align-middle">
                                    <thead>
                                        <tr>
                                            <th>Nama Unit Kerja</th>
                                            <th>Jumlah IKU/IKT</th>
                                            <th>Tercapai</th>
                                            <th>Terlampaui</th>
                                            <th>Tidak Tercapai</th>
                                            <th>Tidak Terlaksana</th>
                                            <th>% Tuntas</th>
                                            <th>Status Finalisasi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($ikuiktPerUnitSemua as $row)
                                            <tr>
                                                <td>{{ $row->unit_nama }}</td>
                                                <td>{{ $row->jumlah }}</td>
                                                <td>{{ $row->tercapai }}</td>
                                                <td>{{ $row->terlampaui }}</td>
                                                <td>{{ $row->tidak_tercapai }}</td>
                                                <td>{{ $row->tidak_terlaksana }}</td>
                                                <td>{{ $row->persentase_tuntas }}%</td>
                                                
                                                <td>
                                                    @php
                                                        $mainBtnClass = 'btn-primary';
                                                        $mainBtnText = 'Lihat Status';
                                                        $mainIcon = 'fa-list-check';

                                                        if($row->status_global === 'semua') {
                                                            $mainBtnClass = 'btn-success'; 
                                                            $mainBtnText = 'Semua Sudah Final';
                                                            $mainIcon = 'fa-check-double';
                                                        } 
                                                        // elseif($row->status_global === 'sebagian') {
                                                        //     $mainBtnClass = 'btn-warning text-dark';
                                                        //     $mainBtnText = 'Proses Finalisasi';
                                                        //     $mainIcon = 'fa-spinner';
                                                        // }
                                                    @endphp

                                                    <div class="dropdown">
                                                        <button class="btn btn-sm dropdown-toggle {{ $mainBtnClass }}" 
                                                                type="button" 
                                                                data-toggle="dropdown"
                                                                aria-expanded="false">
                                                            <i class="fa-solid {{ $mainIcon }} me-1"></i> {{ $mainBtnText }}
                                                        </button>

                                                        <ul class="dropdown-menu shadow p-0" style="min-width: 320px; overflow: hidden;">
                                                            <li>
                                                                <div class="dropdown-header bg-light border-bottom fw-bold text-center">
                                                                    Status Validasi per Program Studi
                                                                </div>
                                                            </li>

                                                            @foreach($row->detail_finalisasi as $dtl)
                                                                @php
                                                                    $isFinal = $dtl['status'];
                                                                    $textClass = $isFinal ? 'text-success' : 'text-warning';
                                                                    $iconStatus = $isFinal ? 'fa-check-circle' : 'fa-clock';
                                                                    $bgColor = $isFinal ? 'background-color: #f0fff4;' : ''; 
                                                                @endphp

                                                                <li class="border-bottom px-3 py-2 d-flex justify-content-between align-items-center" style="{{ $bgColor }}">
                                                                    <span class="fw-semibold {{ $textClass }}" style="font-size: 0.85rem;">
                                                                        <i class="fa-solid {{ $iconStatus }} mr-1"></i> {{ $dtl['nama_prodi'] }}
                                                                    </span>
                                                                    
                                                                    <div>
                                                                        @if($isFinal)
                                                                            @if(Auth::user()->role == 'admin')
                                                                                <button class="btn btn-outline-danger btn-sm px-2 py-1 ml-2 batalSpesifikBtn" 
                                                                                        data-unit="{{ $row->unit_id }}" 
                                                                                        data-mti="{{ $dtl['mti_id'] }}"
                                                                                        data-prodi="{{ $dtl['nama_prodi'] }}"
                                                                                        title="Batalkan Validasi Prodi Ini"
                                                                                        style="font-size: 0.7rem;">
                                                                                    <i class="fa-solid fa-unlock"></i> Buka Kunci
                                                                                </button>
                                                                            @else
                                                                                <span class="badge badge-success" style="font-size: 0.7rem;">Final</span>
                                                                            @endif
                                                                        @else
                                                                            <span class="badge badge-warning text-dark" style="font-size: 0.7rem;">Belum</span>
                                                                        @endif
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="8" class="text-muted">Tidak ada data tersedia</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                    
                <div class="col-lg-12 col-md-12 col-12">
                    {{-- SURAT NOMOR (Hanya Unit Kerja) --}}
                    @if (Auth::user()->role == 'unit kerja' || Auth::user()->role == 'admin')
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
                                    <thead class="thead-dark text-center" ... >
                                        <tr>
                                            <th>{{ Auth::user()->role == 'admin' ? 'Nama Unit Kerja' : 'Tujuan Jabatan' }}</th>
                                            <th>Jumlah Surat</th>
                                            <th>Jumlah Revisi</th>
                                            <th>Jumlah Valid</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                        @foreach($suratSummary as $summary)
                                        <tr>
                                            {{-- Ubah Isi kolom pertama --}}
                                            <td>
                                                @if(Auth::user()->role == 'admin')
                                                    {{ optional($summary->unitKerja)->unit_nama ?? 'Unit Tidak Ditemukan' }}
                                                @else
                                                    {{ optional($summary->organisasiJabatan)->oj_nama ?? 'Jabatan Tidak Ditemukan' }}
                                                @endif
                                            </td>
                                            <td>{{ $summary->jumlah_surat }}</td>
                                            <td>{{ $summary->jumlah_revisi }}</td>
                                            <td>{{ $summary->jumlah_valid }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                    {{-- END SURAT NOMOR --}}
                </div>
                
                {{-- <div class="col-lg-12 col-md-12 col-12">
                    @if (Auth::user()->role == 'admin' || Auth::user()->role == 'unit kerja' || Auth::user()->role == 'fakultas' || Auth::user()->role == 'prodi')
                        <div class="card shadow-sm">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">Ringkasan Seluruh IKU/IKT</h4>
                                <a class="btn btn-primary btn-sm" href="{{ route('laporan-iku.index') }}">
                                    <i class="fa-solid fa-eye"></i> Lihat Detail
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered text-center">
                                        <thead>
                                            <tr>
                                                <th>Tahun</th>
                                                <th>Total Indikator</th>
                                                <th>Tercapai</th>
                                                <th>Terlampaui</th>
                                                <th>Tidak Tercapai</th>
                                                <th>Tidak Terlaksana</th>
                                                <th>% Tuntas</th>  
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($ringkasanIku as $data)
                                            <tr>
                                                <td>{{ $data->tahun }}</td>
                                                <td>{{ $data->total }}</td>
                                                <td>{{ $data->tercapai }}</td>
                                                <td>{{ $data->terlampaui }}</td>
                                                <td>{{ $data->tidak_tercapai }}</td>
                                                <td>{{ $data->tidak_terlaksana }}</td>
                                                <td>{{ $data->persentase_tuntas }}%</td> 
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-danger">Belum ada data IKU/IKT</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div> --}}
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    {{-- Script untuk Batal Finalisasi --}}
    <script>
        $(document).on('click', '.batalSpesifikBtn', function(e) {
            e.stopPropagation(); // Mencegah dropdown tertutup saat diklik
            e.preventDefault();

            let unit_id = $(this).data('unit');
            let mti_id = $(this).data('mti');
            let nama_prodi = $(this).data('prodi');

            Swal.fire({
                title: 'Buka Validasi?',
                text: `Anda akan membatalkan validasi Unit Kerja untuk Prodi: ${nama_prodi}.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Batalkan!',
                cancelButtonText: 'Kembali'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('monitoring.batal-final-spesifik') }}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            unit_id: unit_id,
                            mti_id: mti_id
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Berhasil!', 
                                    text: response.message, 
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => location.reload());
                            } else {
                                Swal.fire('Gagal!', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
                        }
                    });
                }
            });
        });
    </script>

    <!-- JS Libraries (General) -->
    <script src="{{ asset('library/simpleweather/jquery.simpleWeather.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
    <script src="{{ asset('library/summernote/dist/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('library/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/index-0.js') }}"></script>
@endpush