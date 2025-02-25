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
                <h1>Daftar Periode Monitoring</h1>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <form class="row g-2 align-items-center">
                        <div class="col-auto">
                            <input class="form-control" name="q" value="{{ $q }}" placeholder="Pencarian..." />
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-info"><i class="fa-solid fa-search"></i> Cari</button>
                        </div>
                        <div class="col-auto">
                            <a class="btn btn-primary" href="{{ route('periode-monitoring.create') }}"><i class="fa-solid fa-plus"></i> Tambah</a>
                        </div>
                    </form>
                </div>

                <div class="table-responsive text-center">
                    <table class="table table-hover table-bordered table-striped m-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tahun</th>
                                <th>Periode</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = $perides->firstItem(); @endphp
                            @foreach ($perides as $periode)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $periode->tahunKerja->th_tahun ?? '-' }}</td>
                                <td>
                                    @if($periode->periodeMonev && $periode->periodeMonev->isNotEmpty()) 
                                        @foreach ($periode->periodeMonev as $periodes)
                                            <span class="badge badge-info">{{ $periodes->pm_nama }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">Tidak ada periode</span>
                                    @endif
                                </td>                                   
                                <td>{{ \Carbon\Carbon::parse($periode->pmo_tanggal_mulai)->format('d-m-Y ( H:i )') }}</td>
                                <td>{{ \Carbon\Carbon::parse($periode->pmo_tanggal_selesai)->format('d-m-Y ( H:i )') }}</td>

                                <td>
                                    <a class="btn btn-warning" href="{{ route('periode-monitoring.edit', $periode->pmo_id) }}">
                                        <i class="fa-solid fa-pen-to-square"></i> Ubah
                                    </a>
                                    
                                    
                                    <form id="delete-form-{{ $periode->pmo_id }}" method="POST" class="d-inline" action="{{ route('periode-monitoring.destroy', $periode->pmo_id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger" onclick="confirmDelete(event, '{{ $periode->pmo_id }}')">
                                            <i class="fa-solid fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach

                            @if ($perides->isEmpty())
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @if ($perides->hasPages())
                    <div class="card-footer">
                        {{ $perides->links('pagination::bootstrap-5') }}
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
