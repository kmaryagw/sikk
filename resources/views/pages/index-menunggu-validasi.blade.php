@extends('layouts.app')
@section('title', 'Menunggu Validasi')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush


@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Daftar Surat yang Perlu Validasi</h1>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <form class="row g-2 align-items-center" method="GET" action="{{ route('menungguvalidasi.index') }}">
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
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = $ajukans->firstItem(); @endphp
                            @foreach($ajukans as $ajukan)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $ajukan->sn_nomor ?? 'Belum Valid '}}</td>
                                <td>{{ $ajukan->organisasiJabatan->oj_nama }} ({{ $ajukan->organisasiJabatan->parent->oj_nama ?? '-' }}, {{ $ajukan->organisasiJabatan->parent->parent->oj_nama ?? '-' }})</td>
                                <td>{{ $ajukan->lingkup->skl_nama }} ({{ $ajukan->lingkup->perihal->skp_nama ?? '' }}, {{ $ajukan->lingkup->perihal->fungsi->skf_nama ?? '' }})</td>
                                <td>{{ $ajukan->sn_tanggal }}</td>
                                <td>{{ $ajukan->sn_perihal }}</td>
                                <td>{{ $ajukan->unitKerja->unit_nama }}</td>
                                <td>{{ $ajukan->sn_keterangan }}</td>
                                <td>
                                    @if($ajukan->sn_status == 'draft')
                                        <span class="badge bg-info bg text-light"><i class="fa-solid fa-info-circle"></i> Draft</span>
                                    @elseif($ajukan->sn_status == 'ajukan')
                                        <span class="badge bg-warning text-light"><i class="fa-solid fa-clock"></i> Menunggu Validasi</span>
                                    @elseif($ajukan->sn_status == 'revisi')
                                        <span class="badge bg-danger text-light"><i class="fa-solid fa-clipboard-list"></i> Revisi</span>
                                    @else
                                        <span class="badge bg-success text-light"><i class="fa-solid fa-check-circle"></i> Valid</span>
                                    @endif
                                </td>
                                <td>
                                    @if($ajukan->sn_status == 'ajukan')
                                        <button class="btn btn-sm btn-success mt-2 mb-2" onclick="validasiSurat('{{ $ajukan->sn_id }}')"><i class="fa-solid fa-lock"></i> Valid</button>
                                        {{-- <button class="btn btn-sm btn-success mt-2 mb-2" onclick="revisiSurat('{{ $ajukan->sn_id }}')"><i class="fa-solid fa-clipboard-list"></i> Valid</button> --}}
                                        <button class="btn btn-sm btn-danger mt-2 mb-2" onclick="#"><i class="fa-solid fa-clipboard-list"></i> Revisi</button>
                                    @else
                                        <button class="btn btn-sm btn-secondary mt-2 mb-2" disabled><i class="fa-solid fa-check"></i> Sudah Valid</button>
                                    @endif
                                </td>
                                
                            </tr>
                            @endforeach

                            @if ($ajukans->isEmpty())
                                <tr>
                                    <td colspan="10" class="text-center">Tidak ada data</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @if ($ajukans->hasPages())
                    <div class="card-footer">
                        {{ $ajukans->links('pagination::bootstrap-5') }}
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

<script>
    function validasiSurat(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang divalidasi tidak dapat dirubah!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Validasi!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/menungguvalidasi/${id}/valid`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire(
                            'Berhasil!',
                            data.message,
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire(
                            'Gagal!',
                            'Terjadi kesalahan saat validasi.',
                            'error'
                        );
                    }
                }).catch(error => {
                    Swal.fire(
                        'Error!',
                        'Terjadi kesalahan dalam koneksi.',
                        'error'
                    );
                    console.error('Error:', error);
                });
            }
        });
    }
</script>

@endpush