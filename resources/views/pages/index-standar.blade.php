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
                            <input class="form-control" name="q" id="searchInput" value="{{ $q }}" placeholder="Pencarian..." />
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-info"><i class="fa-solid fa-search"></i> Cari</button>
                        </div>
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

    <script>
        $(document).ready(function () {
            let delayTimer;

            $('#searchInput').on('input', function () {
                clearTimeout(delayTimer);
                let query = $(this).val();

                delayTimer = setTimeout(function () {
                    $.ajax({
                        url: "{{ route('standar.index') }}",
                        type: "GET",
                        data: {
                            q: query
                        },
                        success: function (response) {
                            $('#tableBody').html(response.html);
                            $('#paginationLinks').html(response.pagination);
                        },
                        error: function () {
                            alert('Gagal memuat data');
                        }
                    });
                }, 100); // delay ketik
            });

            // AJAX pagination
            $(document).on('click', '#paginationLinks a', function (e) {
                e.preventDefault();
                let url = $(this).attr('href');
                let page = url.split('page=')[1];
                let query = $('#searchInput').val();
                fetchData(page, query);
            });

            function fetchData(page, query) {
                $.ajax({
                    url: "{{ route('standar.index') }}" + '?page=' + page + '&q=' + encodeURIComponent(query),
                    success: function (response) {
                        $('#tableBody').html(response.html);
                        $('#paginationLinks').html(response.pagination);
                    },
                    error: function () {
                        alert('Gagal memuat data');
                    }
                });
            }
        });
    </script>

@endpush