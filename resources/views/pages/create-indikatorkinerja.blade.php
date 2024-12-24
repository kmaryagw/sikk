@extends('layouts.app')

@section('title', 'create-indikatorkinerja')

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
                <h1>Form Indikator Kinerja Utama</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item">Form Indikator Kinerja Utama</div>
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

                                    <form method="POST" action="{{ route('indikatorkinerja.store') }}">
                                        @csrf

                                        <div class="form-group">
                                            <label>Kode Indikator Kinerja</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fa-solid fa-code"></i>
                                                    </div>
                                                </div>
                                                <input class="form-control @error('ik_kode') is-invalid @enderror" type="text" name="ik_kode" value="{{ old('ik_kode') }}"/>
                                                @error('ik_kode')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Nama Indikator Kinerja</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fa-solid fa-bullseye"></i>
                                                    </div>
                                                </div>
                                                <input class="form-control @error('ik_nama') is-invalid @enderror" type="text" name="ik_nama" value="{{ old('ik_nama') }}"/>
                                                @error('ik_nama')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="std_id">Standar</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fa-solid fa-thumbs-up"></i>
                                                    </div>
                                                </div>
                                                <select class="form-control @error('std_id') is-invalid @enderror" name="std_id" id="std_id" required>
                                                    <option value="" disabled selected>Pilih Standar</option>
                                                    @foreach ($standar as $s)
                                                        <option value="{{ $s->std_id }}" {{ old('std_id') == $s->std_id ? 'selected' : '' }}>{{ $s->std_nama }}</option>
                                                    @endforeach
                                                </select>
                                                @error('std_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="th_id">Tahun</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fa-solid fa-calendar-alt"></i>
                                                    </div>
                                                </div>
                                                <select class="form-control" name="th_id" id="th_id" required>
                                                    <option value="" disabled selected>Pilih Tahun</option>
                                                    @foreach ($tahunKerja as $tahun)
                                                        <option value="{{ $tahun->th_id }}">{{ $tahun->th_tahun }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Jenis Indikator Kinerja</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fa-solid fa-list-alt"></i>
                                                    </div>
                                                </div>
                                                <select class="form-control" name="ik_jenis" id="ik_jenis" required>
                                                    <option value="" disabled selected>Pilih Jenis</option>
                                                    @foreach ($jeniss as $jenis)
                                                        <option value="{{ $jenis }}" {{ old('ik_jenis') == $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Pengukur Ketercapaian</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fa-solid fa-chart-line"></i>
                                                    </div>
                                                </div>
                                                <select class="form-control" name="ik_ketercapaian" id="ik_ketercapaian" required>
                                                    <option value="" disabled selected>Pilih Ketercapaian</option>
                                                    @foreach ($ketercapaians as $ketercapaian)
                                                        <option value="{{ $ketercapaian }}" {{ old('ik_ketercapaian') == $ketercapaian ? 'selected' : '' }}>{{ $ketercapaian }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                            <a href="{{ url('indikatorkinerja') }}" class="btn btn-danger">Kembali</a>
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
    <script src="{{ asset('library/cleave.js/dist/cleave.min.js') }}"></script>
    <script src="{{ asset('library/cleave.js/dist/addons/cleave-phone.us.js') }}"></script>
    <script src="{{ asset('library/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('library/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') }}"></script>
    <script src="{{ asset('library/bootstrap-timepicker/js/bootstrap-timepicker.min.js') }}"></script>
    <script src="{{ asset('library/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
    <script src="{{ asset('library/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>

    @include('sweetalert::alert')

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/forms-advanced-forms.js') }}"></script>
@endpush
