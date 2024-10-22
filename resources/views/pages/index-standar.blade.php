@extends('layouts.app')
@section('title', 'Standar')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Daftar Standar</h1>
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
                            <a class="btn btn-primary" href="{{ route('standar.create') }}"><i class="fa-solid fa-plus"></i> Tambah</a>
                        </div>
                    </form>
                </div>

                <div class="table-responsive text-center">
                    <table class="table table-hover table-bordered table-striped m-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Standar</th>
                                <th>Deskripsi</th>
                                <th>Dokumen</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = $standars->firstItem(); @endphp
                            @foreach ($standars as $standar)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $standar->std_nama }}</td>
                                    <td>{{ $standar->std_deskripsi }}</td>
                                    <td>
                                        @if($standar->standardokumen->isNotEmpty())
                                            @foreach($standar->standardokumen as $dokumen)
                                                <a class="btn btn-success" href="{{ asset('storage/' . $dokumen->stdd_file) }}" target="_blank">Lihat Dokumen</a><br>
                                            @endforeach
                                        @else
                                            Tidak Ada Dokumen
                                        @endif
                                    </td>                                                                      
                                    <td>
                                        <a class="btn btn-warning" href="{{ route('standar.edit', $standar->std_id) }}"><i class="fa-solid fa-pen-to-square"></i> Ubah </a>
                                        <form id="delete-form-{{ $standar->std_id }}" method="POST" class="d-inline" action="{{ route('standar.destroy', $standar->std_id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger" onclick="confirmDelete(event, '{{ $standar->std_id }}' )"><i class="fa-solid fa-trash"></i> Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($standars->hasPages())
                    <div class="card-footer">
                        {{ $standars->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection

@push('scripts')
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
            });
        }
    </script>
@endpush