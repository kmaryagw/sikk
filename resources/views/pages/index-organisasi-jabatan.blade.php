@extends('layouts.app')
@section('title', 'Organisasi Jabatan')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Daftar Organisasi Jabatan</h1>
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
                            <a class="btn btn-primary" href="{{ route('organisasijabatan.create') }}"><i class="fa-solid fa-plus"></i> Tambah</a>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped m-0">
                        <thead class="text-center">
                            <tr>
                                <th>No</th>
                                <th>Jabatan</th>
                                <th>Kode</th>
                                <th>Mengeluarkan Nomor</th>
                                <th>Induk</th>
                                <th>Status Aktif</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach($organisasis as $organisasi)
                                {!! tampilkanJabatan($organisasi, $no++, 0) !!}
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    @php
    function tampilkanJabatan($organisasi, $no, $level) {
        $padding = $level * 20;
        $html = "<tr>
                    <td class='text-center'>{$no}</td>
                    <td style='padding-left: {$padding}px;'>".str_repeat(' ', $level)."{$organisasi->oj_nama}</td>
                    <td class='text-center'>{$organisasi->oj_kode}</td>
                    <td class='text-center'>";

        if (strtolower($organisasi->oj_mengeluarkan_nomor) === 'y') {
            $html .= "<span class='text-success'><i class='fa-solid fa-check-circle'></i> Ya</span>";
        } elseif (strtolower($organisasi->oj_mengeluarkan_nomor) === 'n') {
            $html .= "<span class='text-danger'><i class='fa-solid fa-times-circle'></i> Tidak</span>";
        } else {
            $html .= $organisasi->oj_mengeluarkan_nomor;
        }

        $html .= "</td>
                    <td class='text-center'>".($organisasi->parent ? $organisasi->parent->oj_nama : '-')."</td>
                    <td class='text-center'>";

        if (strtolower($organisasi->oj_status) === 'y') {
            $html .= "<span class='text-success'><i class='fa-solid fa-check-circle'></i> Ya</span>";
        } elseif (strtolower($organisasi->oj_status) === 'n') {
            $html .= "<span class='text-danger'><i class='fa-solid fa-times-circle'></i> Tidak</span>";
        } else {
            $html .= $organisasi->oj_status;
        }

        $html .= "</td>
                    <td class='text-center'>
                        <a class='btn btn-warning btn-sm mb-2 mt-2' href='".route('organisasijabatan.edit', $organisasi->oj_id)."'>
                            <i class='fa-solid fa-pen-to-square'></i> Ubah
                        </a>
                        <form id='delete-form-{$organisasi->oj_id}' method='POST' class='d-inline' action='".route('organisasijabatan.destroy', $organisasi->oj_id)."'>
                            ".csrf_field()."
                            ".method_field('DELETE')."
                            <button class='btn btn-danger btn-sm' onclick='confirmDelete(event, \"{$organisasi->oj_id}\")'>
                                <i class='fa-solid fa-trash'></i> Hapus
                            </button>
                        </form>
                    </td>
                </tr>";

        // Loop untuk sub-jabatan
        $childNo = 1;
        foreach ($organisasi->children as $child) {
            $html .= tampilkanJabatan($child, "{$no}.{$childNo}", $level + 1);
            $childNo++;
        }

        return $html;
    }
    @endphp
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