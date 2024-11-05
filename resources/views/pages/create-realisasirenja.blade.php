@extends('layouts.app')

@section('title', 'Tambah Realisasi Renja')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/bootstrap-daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Form Tambah Realisasi Renja</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item">Form Tambah Realisasi Renja</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="col-6 col-lg-6">
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $err)
                                                    <li>{{ $err }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('realisasirenja.store') }}" enctype="multipart/form-data">
                                        @csrf

                                        <!-- Rencana Kerja (readonly) -->
                                        <div class="form-group">
                                            <label for="rk_nama">Rencana Kerja</label>
                                            <input type="text" class="form-control" id="rk_nama" name="rk_nama" value="{{ $rk_nama }}" readonly>
                                            <input type="hidden" name="rk_id" value="{{ $rencanaKerja->rk_id }}">
                                        </div>
                                        
                                        <!-- Deskripsi -->
                                        <div class="form-group">
                                            <label>Deskripsi</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fa-solid fa-clipboard-list"></i>
                                                    </div>
                                                </div>
                                                <textarea class="form-control" name="rkr_deskripsi" required>{{ old('rkr_deskripsi') }}</textarea>
                                            </div>
                                        </div>

                                        <!-- Capaian -->
                                        <div class="form-group">
                                            <label for="rkr_capaian">Capaian (angka)</label>
                                            <input class="form-control" type="number" name="rkr_capaian" value="{{ old('rkr_capaian') }}" required />
                                        </div>

                                        <!-- Tanggal Realisasi -->
                                        <div class="form-group">
                                            <label for="rkr_tanggal">Tanggal Realisasi</label>
                                            <input class="form-control" type="datetime-local" name="rkr_tanggal" value="{{ old('rkr_tanggal') }}" required />
                                        </div>

                                        <!-- URL Realisasi -->
                                        <div class="form-group">
                                            <label for="rkr_url">URL Realisasi</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fa-solid fa-link"></i>
                                                    </div>
                                                </div>
                                                <input class="form-control" type="url" name="rkr_url" value="{{ old('rkr_url') }}" />
                                            </div>
                                        </div>

                                        <!-- File Realisasi -->
                                        <div class="form-group">
                                            <label for="rkr_file">File Realisasi</label>
                                            <input class="form-control" type="file" name="rkr_file" />
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                                <a href="{{ route('realisasirenja.showRealisasi', $rencanaKerja->rk_id) }}" class="btn btn-danger">Kembali</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraries -->
    <script src="{{ asset('library/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') }}"></script>
    <script src="{{ asset('library/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>

    @include('sweetalert::alert')

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/forms-advanced-forms.js') }}"></script>
@endpush