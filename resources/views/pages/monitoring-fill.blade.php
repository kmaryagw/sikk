@extends('layouts.app')
@section('title', 'Isi Monitoring')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Isi Monitoring</h1>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <form class="row g-2 align-items-center">
                        <div class="col-auto">
                            <select class="form-control" name="tahun" disabled>
                                <option selected>Tahun: {{ $monitoring->rencanaKerja->tahun->th_tahun }}</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <select class="form-control" name="periode" disabled>
                                <option selected>Periode: Q{{ ceil($monitoring->rencanaKerja->periode->month / 3) }}</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <label for="nama_rencana" class="form-label">Nama Rencana Kerja</label>
                            <input type="text" class="form-control" id="nama_rencana" name="nama_rencana" value="{{ $monitoring->rencanaKerja->rk_nama }}" disabled>
                        </div>
                    </form>
                </div>

                <div class="card-body">
                    <form action="{{ route('monitoring.update', $monitoring->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <!-- Tambahkan input field untuk data monitoring -->
                        <div class="form-group">
                            <label for="data">Data Monitoring</label>
                            <input type="text" class="form-control" id="data" name="data" placeholder="Isi data monitoring di sini" value="{{ old('data', $monitoring->data) }}">
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Simpan</button>
                    </form>
                </div>
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Tambahkan JavaScript khusus untuk konfirmasi hapus, jika diperlukan di halaman ini
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
