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

                                    <div class="form-group d-flex align-items-center">
                                        <label for="th_id" class="mr-2" style="font-size: 1rem;">Tahun Aktif:</label>
                                        @foreach ($tahuns as $tahun)
                                            @if ($tahun->th_is_aktif === 'y')
                                                <span class="badge badge-primary p-3" style="font-size: 1rem;">
                                                    <i class="fa-solid fa-calendar-alt"></i> {{ $tahun->th_tahun }}
                                                </span>
                                                <input type="hidden" name="th_id" value="{{ $tahun->th_id }}">
                                            @endif
                                        @endforeach
                                    </div>
                                    <div class="row">
                                        <!-- Kolom Kiri -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Pilih Program Studi</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                    </div>
                                                    <select name="prodi_id[]" id="prodi_id" class="form-control select2" multiple="multiple" data-placeholder="Pilih Program Studi" required>
                                                        @foreach($programStudis as $prodi)
                                                            <option value="{{ $prodi->prodi_id }}" {{ collect(old('prodi_id'))->contains($prodi->prodi_id) ? 'selected' : '' }}>
                                                                {{ $prodi->nama_prodi }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

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

                                        <!-- Kolom Kanan -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Indikator Kinerja</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                    </div>
                                                    <select name="ti_id[]" id="ti_id" class="form-control select2" multiple="multiple" data-placeholder="Pilih Indikator Kinerja" required>
                                                        @foreach($targetindikators as $indikator)
                                                            <option value="{{ $indikator->ti_id }}" {{ collect(old('ti_id'))->contains($indikator->ti_id) ? 'selected' : '' }}>
                                                                {{ $indikator->ik_kode }} - {{ $indikator->ik_nama }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="unit_id">Unit Kerja</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fa-solid fa-sitemap"></i>
                                                        </div>
                                                    </div>
                                            
                                                    @if ($userRole === 'unit kerja' && $userUnit)
                                                        <!-- Jika user adalah unit, tampilkan readonly input -->
                                                        <input type="text" class="form-control" value="{{ $userUnit->unit_nama }}" readonly>
                                                        <input type="hidden" name="unit_id" value="{{ $userUnit->unit_id }}">
                                                    @else
                                                        <!-- Form biasa jika bukan prodi -->
                                                        <select class="form-control" name="unit_id" id="unit_id" required>
                                                            <option value="" disabled selected>Pilih Unit Kerja</option>
                                                            @foreach ($units as $unit)
                                                                @if ($unit->unit_kerja == 'y') <!-- Menampilkan hanya unit kerja yang aktif -->
                                                                    <option value="{{ $unit->unit_id }}" {{ old('unit_id') == $unit->unit_id ? 'selected' : '' }}>{{ $unit->unit_nama }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    @endif
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