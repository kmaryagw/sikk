@extends('layouts.app')
@section('title', 'IKU/IKT')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Daftar Indikator Kinerja Utama/Tambahan</h1>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <form class="row g-2 align-items-center">
                        <div class="col-auto">
                            <select class="form-control" name="tahun">
                                @foreach ($tahun as $thn)
                                    <option value="{{ $thn->th_id }}" 
                                        {{ request('tahun') == $thn->th_id || (request('tahun') == null && $loop->first) ? 'selected' : '' }}>
                                        {{ $thn->th_tahun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>                        
                        <div class="col-auto">
                            <input class="form-control" name="q" value="{{ $q }}" placeholder="Pencarian..." />
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-info"><i class="fa-solid fa-search"></i> Cari</button>
                        </div>
                        @if (Auth::user()->role== 'admin')
                        <div class="col-auto">
                            <a class="btn btn-primary" href="{{ route('indikatorkinerja.create') }}"><i class="fa-solid fa-plus"></i> Tambah</a>
                        </div>
                        @endif
                    </form>
                </div>

                <div class="table-responsive text-center">
                    <table class="table table-hover table-bordered table-striped m-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode IKU/IKT</th>
                                <th>Nama IKU/IKT</th>
                                <th>Standar</th>
                                <th>Tahun</th>
                                <th>Jenis</th>
                                <th>Pengukur Ketercapaian</th>
                                @if (Auth::user()->role== 'admin')
                                <th>Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = $indikatorkinerjas->firstItem(); @endphp
                            @foreach ($indikatorkinerjas as $indikatorkinerja)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $indikatorkinerja->ik_kode }}</td>
                                    <td>{{ $indikatorkinerja->ik_nama }}</td>
                                    <td>{{ $indikatorkinerja->std_nama ?? '-' }}</td>
                                    <td>{{ $indikatorkinerja->th_tahun ?? '-' }}</td>
                                    <td>
                                        @if (strtolower($indikatorkinerja->ik_jenis) === 'iku')
                                            <span class="badge badge-success">IKU</span>
                                        @elseif (strtolower($indikatorkinerja->ik_jenis) === 'ikt')
                                            <span class="badge badge-primary">IKT</span>
                                        @else
                                            {{ $indikatorkinerja->ik_jenis }}
                                        @endif
                                    </td>                                    
                                    <td>{{ $indikatorkinerja->ik_ketercapaian }}</td>


                        
                         

                                    @if (Auth::user()->role== 'admin')
                                    <td>
                                        
                                            <a class="btn btn-warning" href="{{ route('indikatorkinerja.edit', $indikatorkinerja->ik_id) }}">
                                                <i class="fa-solid fa-pen-to-square"></i> Ubah
                                            </a>
                                            <form id="delete-form-{{ $indikatorkinerja->ik_id }}" method="POST" class="d-inline" action="{{ route('indikatorkinerja.destroy', $indikatorkinerja->ik_id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger" onclick="confirmDelete(event, '{{ $indikatorkinerja->ik_id }}' )">
                                                    <i class="fa-solid fa-trash"></i> Hapus
                                                </button>
                                            </form>
                                        
                                    </td>                                    
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($indikatorkinerjas->hasPages())
                    <div class="card-footer">
                        {{ $indikatorkinerjas->links('pagination::bootstrap-5') }}
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