@extends('layouts.app')
@section('title', 'Program Kerja')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Daftar Program Kerja</h1>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <form class="row g-2 align-items-center">
                        <div class="col-auto">
                            <input class="form-control" name="q" value="{{ $q }}" placeholder="Pencarian..." />
                        </div>
                        @if (Auth::user()->role == 'admin'|| Auth::user()->role == 'prodi')
                        <div class="col-auto">
                            <select class="form-control" name="unit_id">
                                <option value="">Semua Unit Kerja</option>
                                @foreach ($units as $unit)
                                     <!-- Menampilkan hanya unit kerja yang aktif -->
                                        <option value="{{ $unit->unit_id }}" {{ request('unit_id') == $unit->unit_id ? 'selected' : '' }}>{{ $unit->unit_nama }}</option>
                                    
                                @endforeach
                            </select>
                        </div>
                        

                        
                        <div class="col-auto">
                            <select class="form-control" name="tahun">
                                
                                @foreach ($tahuns as $tahun)
                                    @if ($tahun->ren_is_aktif == 'y') <!-- Menampilkan hanya tahun yang aktif -->
                                        <option value="{{ $tahun->th_id }}" {{ request('tahun') == $tahun->th_id ? 'selected' : '' }}>{{ $tahun->th_tahun }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="col-auto">
                            <button class="btn btn-info"><i class="fa-solid fa-search"></i> Cari</button>
                        </div>
                        @if (Auth::user()->role == 'admin')
                        <div class="col-auto">
                            <a class="btn btn-primary" href="{{ route('programkerja.create') }}"><i class="fa-solid fa-plus"></i> Tambah</a>
                        </div>
                        @endif
                    </form>
                </div>

                <div class="table-responsive text-center">
                    <table class="table table-hover table-bordered table-striped m-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Program Kerja</th>
                                <th>Unit Kerja</th>
                                <th>Tahun</th>
                                @if (Auth::user()->role == 'admin')
                                <th>Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = $programkerjas->firstItem(); @endphp
                            @foreach ($programkerjas as $programkerja)
                                {{-- @if ($programkerja->unit->unit_nama && $programkerja->tahun_kerja->ren_is_aktif == 'y') <!-- Filter program kerja berdasarkan unit dan tahun yang aktif --> --}}
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $programkerja->rk_nama }}</td>
                                        <td>{{ $programkerja->unit_nama }}</td>
                                        <td>{{ $programkerja->th_tahun }}</td>

                                        @if (Auth::user()->role== 'admin')
                                        <td>
                                            <a class="btn btn-warning" href="{{ route('programkerja.edit', $programkerja->rk_id) }}"><i class="fa-solid fa-pen-to-square"></i> Ubah </a>
                                            <form id="delete-form-{{ $programkerja->rk_id }}" method="POST" class="d-inline" action="{{ route('programkerja.destroy', $programkerja->rk_id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger" onclick="confirmDelete(event, '{{ $programkerja->rk_id }}' )"><i class="fa-solid fa-trash"></i> Hapus</button>
                                            </form>
                                        </td>
                                        @endif
                                    </tr>
                                {{-- @endif --}}
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($programkerjas->hasPages())
                    <div class="card-footer">
                        {{ $programkerjas->links('pagination::bootstrap-5') }}
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
