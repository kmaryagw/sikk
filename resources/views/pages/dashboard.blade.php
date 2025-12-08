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
                                                        } elseif($row->status_global === 'sebagian') {
                                                            $mainBtnClass = 'btn-warning text-dark';
                                                            $mainBtnText = 'Lihat Finalisasi';
                                                            $mainIcon = 'fa-spinner';
                                                        }
                                                    @endphp

                                                    <div class="dropdown">
                                                        {{-- Tombol Pemicu Dropdown --}}
                                                        <button class="btn btn-sm dropdown-toggle {{ $mainBtnClass }}" 
                                                                type="button" 
                                                                data-toggle="dropdown"
                                                                aria-expanded="false">
                                                            <i class="fa-solid {{ $mainIcon }} me-1"></i> {{ $mainBtnText }}
                                                        </button>

                                                        {{-- Daftar Prodi dalam Dropdown --}}
                                                        <ul class="dropdown-menu shadow p-0" style="min-width: 250px; overflow: hidden;">
                                                            <li>
                                                                <div class="dropdown-header bg-light border-bottom fw-bold text-center">
                                                                    Status per Program Studi
                                                                </div>
                                                            </li>

                                                            @foreach($row->detail_finalisasi as $dtl)
                                                                {{-- Tentukan Warna Item Berdasarkan Status --}}
                                                                @php
                                                                    $itemClass = $dtl['status'] 
                                                                        ? 'bg-success text-white'  // HIJAU jika Sudah Final
                                                                        : 'bg-warning text-dark';  // KUNING jika Belum Final
                                                                    
                                                                    $iconStatus = $dtl['status'] 
                                                                        ? 'fa-check-circle' 
                                                                        : 'fa-exclamation-circle';
                                                                @endphp

                                                                <li class="border-bottom">
                                                                    {{-- Item Dropdown --}}
                                                                    <span class="dropdown-item d-flex justify-content-between align-items-center py-2 {{ $itemClass }}" style="cursor: default;">
                                                                        <span class="fw-semibold" style="font-size: 0.9rem;">
                                                                            {{ $dtl['nama_prodi'] }}
                                                                        </span>
                                                                        
                                                                        <i class="fa-solid {{ $iconStatus }}"></i>
                                                                    </span>
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
        $(document).on('click', '.batalFinalBtn', function() {
            let unit_id = $(this).data('unit');

            Swal.fire({
                title: 'Batalkan Finalisasi?',
                text: "Apakah Anda yakin ingin membatalkan finalisasi unit ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, batalkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/monitoring/batal-final/' + unit_id,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            unit_id: unit_id
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Terjadi kesalahan: ' + xhr.responseText,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
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