@extends('layouts.app')
@section('title','SPMI')

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
                            <input class="form-control" name="q" id="searchInput" value="{{ $q }}" placeholder="Pencarian..." />
                        </div>
                        <div class="col-auto">
                            <select class="form-control" id="filterKategoriStandar" name="kategori">
                                <option value="">Semua Kategori Standar</option>
                                @foreach ($kategoriStandarList as $kategori)
                                    <option value="{{ $kategori }}">{{ $kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <select class="form-control" id="filterNamaStandar">
                                <option value="">Semua Nama Standar</option>
                                @foreach ($namaStandarList as $nama)
                                    <option value="{{ $nama }}" {{ request('nama') == $nama ? 'selected' : '' }}>{{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- <div class="col-auto">
                            <button class="btn btn-info"><i class="fa-solid fa-search"></i> Cari</button>
                        </div> --}}
                        <div class="col-auto">
                            <a class="btn btn-primary" href="{{ route('standar.create') }}"><i class="fa-solid fa-plus"></i> Tambah</a>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped m-0">
                        <thead>
                            <tr class="text-center">
                                <th style="width: 1%;">No</th>
                                <th style="width: 15%;">Kategori Standar</th>
                                <th style="width: 25%;">Nama Standar</th>
                                <th style="width: 50%;">Pernyataan Standar</th>
                                {{-- <th>URL</th> --}}
                                <th style="width: 5%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @include('pages.standar_table', ['standars' => $standars])
                        </tbody>
                    </table>
                </div>

                @if ($standars->hasPages())
                    <div class="card-footer" id="paginationLinks">
                        @include('pages.standar_pagination', ['standars' => $standars])
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- <script>
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
    </script> --}}

    <script>
        $(document).ready(function () {
            let delayTimer;

            // Trigger AJAX saat input berubah
            $('#searchInput, #filterNamaStandar, #filterKategoriStandar').on('input change', function () {
                clearTimeout(delayTimer);
                delayTimer = setTimeout(function () {
                    fetchData(1); // Reset ke halaman 1 saat filter berubah
                }, 100);
            });

            // Handle pagination klik
            $(document).on('click', '#paginationLinks a', function (e) {
                e.preventDefault();
                let url = $(this).attr('href');
                let page = new URLSearchParams(url.split('?')[1]).get('page') || 1;
                fetchData(page);
            });

            function fetchData(page = 1) {
                let query = $('#searchInput').val();
                let nama = $('#filterNamaStandar').val();
                let kategori = $('#filterKategoriStandar').val();

                $.ajax({
                    url: "{{ route('standar.index') }}",
                    type: "GET",
                    data: {
                        page: page,
                        q: query,
                        nama: nama,
                        kategori: kategori
                    },
                    success: function (response) {
                        $('#tableBody').html(response.html);
                        $('#paginationLinks').html(response.pagination);
                    },
                    error: function () {
                        alert('Gagal memuat data');
                    }
                });
            }

            $(document).on('click', '.btn-delete-custom', function (e) {
                e.preventDefault();
                
                let id = $(this).data('id');
                let nama = $(this).data('nama');
                let form = $('#delete-form-' + id);

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Standar \"" + nama + "\" akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33', 
                    cancelButtonColor: '#3085d6', 
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true 
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>


@endpush