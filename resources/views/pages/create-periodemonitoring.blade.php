@extends('layouts.app')

@section('title', 'create-periode-monitoring')

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
                <h1>Form Periode Monitoring</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item">Form Periode Monitoring</div>
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
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('periode-monitoring.store') }}">
                                        @csrf

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
                                                    @foreach ($tahuns as $tahun)
                                                        @if ($tahun->ren_is_aktif == 'y') <!-- Menampilkan hanya tahun yang aktif -->
                                                            <option value="{{ $tahun->th_id }}" {{ old('th_id') == $tahun->th_id ? 'selected' : '' }}>{{ $tahun->th_tahun }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="th_id">Periode Monev</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fa-solid fa-clock"></i>
                                                    </div>
                                                </div>
                                                <select class="form-control" name="pm_id" id="pm_id" required>
                                                    <option value="" disabled selected>Pilih Periode</option>
                                                    @foreach ($periodes as $periode)
                                                        <option value="{{ $periode->pm_id }}" {{ old('pm_id') == $periode->pm_id ? 'selected' : '' }}>
                                                            {{ $periode->pm_nama }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="pmo_tanggal_mulai">Tanggal Mulai</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fa-solid fa-calendar-day"></i>
                                                    </div>
                                                </div>
                                                <input type="datetime-local" name="pmo_tanggal_mulai" id="pmo_tanggal_mulai" class="form-control @error('pmo_tanggal_mulai') is-invalid @enderror" value="{{ old('pmo_tanggal_mulai') }}">
                                                @error('pmo_tanggal_mulai')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="pmo_tanggal_selesai">Tanggal Selesai</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fa-solid fa-calendar-check"></i>
                                                    </div>
                                                </div>
                                                <input type="datetime-local" name="pmo_tanggal_selesai" id="pmo_tanggal_selesai" class="form-control @error('pmo_tanggal_selesai') is-invalid @enderror" value="{{ old('pmo_tanggal_selesai') }}">
                                                @error('pmo_tanggal_selesai')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                            <a href="{{ url('periode-monitoring') }}" class="btn btn-danger">Kembali</a>
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