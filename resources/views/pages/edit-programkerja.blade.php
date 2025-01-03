@extends('layouts.app')
@section('title', 'Edit Program Kerja')

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
                <h1>Edit Program Kerja</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item">Edit Program Kerja</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-lg-10 offset-lg-1">
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

                                <form action="{{ route('programkerja.update', $programkerja->rk_id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

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
                                                    <input type="text" name="rk_nama" class="form-control @error('rk_nama') is-invalid @enderror" value="{{ old('rk_nama', $programkerja->rk_nama) }}" required>
                                                    @error('rk_nama')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
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
                                                    <select class="form-control @error('unit_id') is-invalid @enderror" name="unit_id" required>
                                                        <option value="" disabled>Pilih Unit Kerja</option>
                                                        @foreach ($units as $unit)
                                                            @if ($unit->unit_kerja == 'y')
                                                                <option value="{{ $unit->unit_id }}" {{ old('unit_id', $programkerja->unit_id) == $unit->unit_id ? 'selected' : '' }}>{{ $unit->unit_nama }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                    @error('unit_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
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
                                                    <select class="form-control" name="th_id" required>
                                                        <option value="" disabled>Pilih Tahun</option>
                                                        @foreach ($tahuns as $tahun)
                                                            @if ($tahun->th_is_aktif == 'y')
                                                                <option value="{{ $tahun->th_id }}" {{ old('th_id', $programkerja->th_id) == $tahun->th_id ? 'selected' : '' }}>{{ $tahun->th_tahun }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Kolom Kanan -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="ti_id">Indikator Kinerja</label>
                                                <select name="ti_id[]" id="ti_id" class="form-control select2" multiple="multiple" required>
                                                    @foreach($targetindikators as $ti_id => $ik_nama)
                                                        <option value="{{ $ti_id }}" 
                                                            @if(in_array($ti_id, $selectedIndikators)) selected @endif>
                                                            {{ $ik_nama }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>                                        

                                            <div class="form-group">
                                                <label>Periode Monev</label>
                                                <div>
                                                    @foreach  ($periodes as $periode)
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="checkbox" name="pm_id[]" value="{{ $periode->pm_id }}" 
                                                            {{ in_array($periode->pm_id, $selectedPeriodes) ? 'checked' : '' }}>
                                                            <label class="form-check-label">
                                                                {{ $periode->pm_nama }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-footer text-right">
                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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
    <script src="{{ asset('library/select2/dist/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@endpush
