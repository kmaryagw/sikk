@extends('layouts.app')
@section('title', 'Data Monitoring')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ $title }}</h1>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <form class="row g-2 align-items-center" method="GET" action="{{ route('monitoring.index') }}">
                        <div class="col-auto">
                            <input class="form-control" name="q" value="{{ request('q') }}" placeholder="Pencarian..." />
                        </div>
                        <div class="col-auto">
                            <select class="form-control" name="th_id">
                                <option value="">Pilih Tahun</option>
                                @foreach ($tahuns as $tahun)
                                    <option value="{{ $tahun->th_id }}" {{ $tahunId == $tahun->th_id ? 'selected' : '' }}>
                                        {{ $tahun->th_tahun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <select class="form-control" name="pm_id">
                                <option value="">Pilih Periode</option>
                                @foreach ($periodes as $periode)
                                    <option value="{{ $periode->pm_id }}" {{ $periodeId == $periode->pm_id ? 'selected' : '' }}>
                                        {{ $periode->pm_nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-info"><i class="fa-solid fa-search"></i> Cari</button>
                        </div>
                        @if (Auth::user()->role == 'admin')
                        <div class="col-auto">
                            <a class="btn btn-primary" href="{{ route('monitoring.create') }}"><i class="fa-solid fa-plus"></i> Tambah</a>
                        </div>
                        @endif
                    </form>
                </div>

                <div class="table-responsive text-center">
                    <table class="table table-hover table-bordered table-striped m-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Rencana Kerja</th>
                                <th>Tahun</th>
                                <th>Periode</th>
                                <th>Capaian</th>
                                <th>Kondisi</th>
                                <th>Kendala</th>
                                <th>Tindak Lanjut</th>
                                <th>Tanggal Tindak Lanjut</th>
                                @if (Auth::user()->role == 'admin')
                                    <th>Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = $monitoring->firstItem(); @endphp
                            @foreach ($monitoring as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $item->rencanaKerja->rk_nama }}</td>
                                    <td>{{ $item->tahunKerja->th_tahun }}</td>
                                    <td>{{ $item->periodeMonev->pm_nama }}</td>
                                    <td>{{ $item->mtg_capaian }}</td>
                                    <td>{{ $item->mtg_kondisi }}</td>
                                    <td>{{ $item->mtg_kendala }}</td>
                                    <td>{{ $item->mtg_tindak_lanjut }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->mtg_tindak_lanjut_tanggal)->format('d-m-Y') }}</td>
                                    
                                    @if (Auth::user()->role == 'admin')
                                    <td>
                                        <a class="btn btn-warning" href="{{ route('monitoring.edit', $item->mtg_id) }}">
                                            <i class="fa-solid fa-pen-to-square"></i> Ubah 
                                        </a>
                                        <form id="delete-form-{{ $item->mtg_id }}" method="POST" class="d-inline" action="{{ route('monitoring.destroy', $item->mtg_id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger" onclick="confirmDelete(event, '{{ $item->mtg_id }}' )"><i class="fa-solid fa-trash"></i> Hapus</button>
                                        </form>
                                    </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($monitoring->hasPages())
                    <div class="card-footer">
                        {{ $monitoring->links('pagination::bootstrap-5') }}
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
        function confirmDelete(event, formid) {
            event.preventDefault();
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus data!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + formid).submit();
                }
            })
        }
    </script>
@endpush
