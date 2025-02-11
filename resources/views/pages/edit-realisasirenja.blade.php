@extends('layouts.app')

@section('title', 'Edit Realisasi Renja')

@push('style')
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
                <h1>Form Edit Realisasi Renja</h1>
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

                                    <form method="POST" action="{{ route('realisasirenja.update', $realisasi->rkr_id) }}" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT') <!-- Menentukan metode PUT untuk update -->

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
                                                <textarea class="form-control" name="rkr_deskripsi" required>{{ old('rkr_deskripsi', $realisasi->rkr_deskripsi) }}</textarea>
                                            </div>
                                        </div>

                                        <!-- Capaian -->
                                        <div class="form-group">
                                            <label>Capaian</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fa-solid fa-percent"></i>
                                                    </div>
                                                </div>
                                                <input class="form-control" type="number" name="rkr_capaian" value="{{ old('rkr_capaian', $realisasi->rkr_capaian) }}" required />
                                            </div>
                                        </div>

                                        <!-- Tanggal Realisasi -->
                                        <div class="form-group">
                                            <label>Tanggal Realisasi</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fa-solid fa-calendar"></i>
                                                    </div>
                                                </div>
                                                <input class="form-control" type="datetime-local" name="rkr_tanggal" value="{{ old('rkr_tanggal', $realisasi->rkr_tanggal)  }}" required />
                                            </div>
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
                                                <input class="form-control" type="url" name="rkr_url" value="{{ old('rkr_url', $realisasi->rkr_url) }}" />
                                            </div>
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                            <a href="{{ route('realisasirenja.showRealisasi', $realisasi->rk_id) }}" class="btn btn-danger">Kembali</a>
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
    <script src="{{ asset('library/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') }}"></script>
    <script src="{{ asset('library/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>

    @include('sweetalert::alert')

    <script src="{{ asset('js/page/forms-advanced-forms.js') }}"></script>
@endpush