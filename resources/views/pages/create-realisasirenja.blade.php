@extends('layouts.app')

@section('title', 'Tambah Realisasi Renja')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/bootstrap-daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
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

                                    <div class="form-group">
                                        <label for="rk_id">Rencana Kerja</label>
                                        <select class="form-control" name="rk_id" required>
                                            <option value="" disabled selected>Pilih Rencana Kerja</option>
                                            @foreach ($rencanakerjas as $rencana)
                                                <option value="{{ $rencana->rk_id }}" {{ old('rk_id') == $rencana->rk_id ? 'selected' : '' }}>
                                                    {{ $rencana->rk_nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    

                                    {{-- <div class="form-group">
                                        <label for="pm_id">Periode Monev</label>
                                        <select class="form-control" name="pm_id" required>
                                            <option value="" disabled selected>Pilih Periode Monev</option>
                                            @foreach ($periodes as $periode)
                                                <option value="{{ $periode->pm_id }}" {{ old('pm_id') == $periode->pm_id ? 'selected' : '' }}>
                                                    {{ $periode->pm_nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="rkr_url">URL Realisasi</label>
                                        <input class="form-control" type="url" name="rkr_url" value="{{ old('rkr_url') }}" required />
                                    </div>

                                    <div class="form-group">
                                        <label for="rkr_file">File Realisasi</label>
                                        <input class="form-control" type="file" name="rkr_file" required />
                                    </div>

                                    <div class="form-group">
                                        <label for="rkr_deskripsi">Deskripsi Realisasi</label>
                                        <textarea class="form-control" name="rkr_deskripsi" rows="3" required>{{ old('rkr_deskripsi') }}</textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="rkr_capaian">Capaian (angka)</label>
                                        <input class="form-control" type="number" name="rkr_capaian" value="{{ old('rkr_capaian') }}" required />
                                    </div> --}}

                                    <div class="form-group">
                                        <label for="rkr_tanggal">Tanggal Realisasi</label>
                                        <input class="form-control" type="date" name="rkr_tanggal" value="{{ old('rkr_tanggal') }}" required />
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                        <a href="{{ route('realisasirenja.index') }}" class="btn btn-danger">Kembali</a>
                                    </div>
                                </form>
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
@endpush