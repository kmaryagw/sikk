@extends('layouts.app')

@section('title', 'Tambah Program Kerja')

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
                <h1>Form Program Kerja</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item">Form Program Kerja</div>
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

                                <form method="POST" action="{{ route('programkerja.store') }}">
                                    @csrf
                                    <div class="row">
                                        <!-- Kolom Kiri -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Nama Program Kerja</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fa-solid fa-briefcase"></i>
                                                        </div>
                                                    </div>
                                                    <input class="form-control" type="text" name="rk_nama" value="{{ old('rk_nama') }}" required/>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label>Unit Kerja</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fa-solid fa-sitemap"></i>
                                                        </div>
                                                    </div>
                                                    <select class="form-control" name="unit_id" id="unit_id" required>
                                                        <option value="" disabled selected>Pilih Unit Kerja</option>
                                                        @foreach ($units as $unit)
                                                            @if ($unit->unit_kerja == 'y') <!-- Menampilkan hanya unit kerja yang aktif -->
                                                                <option value="{{ $unit->unit_id }}" {{ old('unit_id') == $unit->unit_id ? 'selected' : '' }}>{{ $unit->unit_nama }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label>Tahun</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fa-solid fa-calendar-alt"></i>
                                                        </div>
                                                    </div>
                                                    <select class="form-control" name="th_id" id="th_id" required>
                                                        <option value="" disabled selected>Pilih Tahun</option>
                                                        @foreach ($tahuns as $tahun)
                                                            @if ($tahun->th_is_aktif == 'y')
                                                                <option value="{{ $tahun->th_id }}" {{ old('th_id') == $tahun->th_id ? 'selected' : '' }}>{{ $tahun->th_tahun }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Kolom Kanan -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Indikator Kinerja</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        
                                                    </div>
                                                    <select name="ti_id[]" id="ti_id" class="form-control select2" multiple="multiple" data-placeholder="Pilih Indikator Kinerja" required>
                                                        @foreach($targetindikators as $ti_id => $ik_nama)
                                                            <option value="{{ $ti_id }}" {{ collect(old('ti_id'))->contains($ti_id) ? 'selected' : '' }}>
                                                                {{ $ik_nama }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label>Pilih Periode Monev</label>
                                                <div>
                                                    @foreach ($periodes as $periode)
                                                        <div class="form-check form-check-inline">
                                                            <input type="checkbox" class="form-check-input" name="pm_id[]" value="{{ $periode->pm_id }}">
                                                            <label class="form-check-label">{{ $periode->pm_nama }}</label><br>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-footer text-right">
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                        <a href="{{ route('programkerja.index') }}" class="btn btn-danger">Kembali</a>
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