@extends('layouts.app')
@section('title', 'Renstra')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush


@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Daftar Rencana Strategis</h1>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <form class="row row-cols-auto g-1">
                        <div class="col">
                            <input class="form-control" name="q" value="{{ $q }}" placeholder="Pencarian..." />
                        </div>
                        <div class="col">
                            <button class="btn btn-info"><i class="fa-solid fa-arrows-rotate"></i> Refresh</button>
                        </div>
                        <div class="col">
                            <a class="btn btn-primary" href="{{ route('rencana-strategis.create') }}"><i class="fa-solid fa-plus"></i> Tambah</a>
                        </div>
                    </form>
                </div>

                <div class="table-responsive text-center">
                    <table class="table table-hover table-bordered table-striped m-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Renstra</th>
                                <th>Pimpinan Renstra</th>
                                <th>Periode Awal Renstra</th>
                                <th>Periode Akhir Renstra</th>
                                <th>Renstra is Active</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = $renstras->firstItem(); @endphp
                            @foreach ($renstras as $renstra)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $renstra->ren_nama }}</td>
                                    <td>{{ $renstra->ren_pimpinan }}</td>
                                    <td>{{ $renstra->ren_periode_awal }}</td>
                                    <td>{{ $renstra->ren_periode_akhir }}</td>
                                    <td>{{ $renstra->ren_is_aktif }}</td>
                                    <td>
                                        <a class="btn btn-warning" href="#"><i class="fa-solid fa-pen-to-square"></i> Ubah </a>
                                        <form id="delete-form-{{ $no-1 }}" method="POST" class="d-inline" action="#">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger" onclick="confirmDelete(event, '{{ $no-1 }}' )"><i class="fa-solid fa-trash"></i> Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($renstras->hasPages())
                    <div class="card-footer">
                        {{ $renstras->links() }}
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
