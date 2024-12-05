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
                    <form class="row g-2 align-items-center" method="GET" action="{{ route('programkerja.index') }}">
                        <div class="col-auto">
                            <input class="form-control" name="q" value="{{ request('q') }}" placeholder="Pencarian..." />
                        </div>
                        @if (Auth::user()->role == 'admin' || Auth::user()->role == 'prodi')
                            <div class="col-auto">
                                <select class="form-control" name="unit_id">
                                    <option value="">Semua Unit Kerja</option>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->unit_id }}" {{ request('unit_id') == $unit->unit_id ? 'selected' : '' }}>
                                            {{ $unit->unit_nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-auto">
                                <select class="form-control" name="tahun">
                                    <option value="">Pilih Tahun</option>
                                    @foreach ($tahuns as $tahun)
                                        <option value="{{ $tahun->th_id }}" {{ request('tahun') == $tahun->th_id ? 'selected' : '' }}>
                                            {{ $tahun->th_tahun }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="col-auto">
                            <button class="btn btn-info"><i class="fa-solid fa-search"></i> Cari</button>
                        </div>
                        @if (Auth::user()->role == 'admin')
                            <div class="col-auto">
                                <a class="btn btn-primary" href="{{ route('programkerja.create') }}">
                                    <i class="fa-solid fa-plus"></i> Tambah
                                </a>
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
                                <th>Periode Monev</th>
                                <th>Indikator Kinerja</th>
                                @if (Auth::user()->role == 'admin')
                                    <th>Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = $programkerjas->firstItem(); @endphp
                            @foreach ($programkerjas as $programkerja)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $programkerja->rk_nama }}</td>
                                    <td>{{ $programkerja->unit_nama }}</td>
                                    <td>{{ $programkerja->tahun->th_tahun ?? 'N/A' }}</td>
                                    
                                    <td>
                                        @if ($programkerja->periodes->isNotEmpty())
                                            @foreach ($programkerja->periodes as $periode)
                                                <span class="badge badge-info">{{ $periode->pm_nama }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">Tidak ada periode</span>
                                        @endif
                                    </td>

                                    <td>{{ $programKerja->indikatorKinerja->ik_nama ?? 'Tidak ada data' }}</td>
                                    
                                    {{-- <td>
                                        @if ($programkerja->indikatorKinerja->isNotEmpty())
                                            @foreach ($programkerja->indikatorKinerja as $indikator)
                                                <span class="badge badge-primary">{{ $indikator->ik_nama }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">Tidak ada Indikator Kinerja</span>
                                        @endif
                                    </td> --}}

                                    @if (Auth::user()->role == 'admin')
                                        <td>
                                            <a class="btn btn-warning" href="{{ route('programkerja.edit', $programkerja->rk_id) }}">
                                                <i class="fa-solid fa-pen-to-square"></i> Ubah
                                            </a>
                                            <form id="delete-form-{{ $programkerja->rk_id }}" method="POST" class="d-inline" action="{{ route('programkerja.destroy', $programkerja->rk_id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger" onclick="confirmDelete(event, '{{ $programkerja->rk_id }}' )">
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
    <script src="{{ asset('library/sweetalert2/dist/sweetalert2.min.js') }}"></script>
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
