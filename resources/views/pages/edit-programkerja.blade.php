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
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
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
                                                    <select name="prodi_id[]" 
                                                            id="prodi_id" 
                                                            class="form-control select2" 
                                                            multiple="multiple" 
                                                            data-placeholder="Pilih Program Studi" required>
                                                        @foreach($programStudis as $prodi)
                                                            <option value="{{ $prodi->prodi_id }}" 
                                                                    {{ in_array($prodi->prodi_id, $selectedProgramStudis) ? 'selected' : '' }}>
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
                                                    <input type="text" name="rk_nama" 
                                                           class="form-control @error('rk_nama') is-invalid @enderror" 
                                                           value="{{ old('rk_nama', $programkerja->rk_nama) }}" required>
                                                    @error('rk_nama')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label>Periode Monev</label>
                                                <div>
                                                    @foreach($periodes as $periode)
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" 
                                                                   type="checkbox" 
                                                                   name="pm_id[]" 
                                                                   value="{{ $periode->pm_id }}" 
                                                                   {{ in_array($periode->pm_id, $selectedPeriodes) ? 'checked' : '' }}>
                                                            <label class="form-check-label">
                                                                {{ $periode->pm_nama }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    
                                        <!-- Kolom Kanan -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="ti_id">Indikator Kinerja</label>
                                                <div class="input-group">
                                                    <select name="ti_id[]" id="ti_id" class="form-control select2" multiple="multiple" required>
                                                        @foreach($targetindikators as $indikator)
                                                            <option value="{{ $indikator->ti_id }}" 
                                                                    {{ in_array($indikator->ti_id, $selectedIndikators) ? 'selected' : '' }}>
                                                                {{ $indikator->ik_kode }} - {{ $indikator->ik_nama }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="std_id">Standar</label>
                                                <select name="std_id" id="std_id" class="form-control select2" required>
                                                    <option value="">Pilih Standar</option>
                                                    @foreach($standars as $standar)
                                                        <option value="{{ $standar->std_id }}" 
                                                            {{ (old('std_id', $programkerja->std_id) == $standar->std_id) ? 'selected' : '' }}>
                                                            {{ $standar->std_nama }} - {{ $standar->std_deskripsi }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>                                            

                                            <div class="form-group">
                                                <label>Unit Kerja</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fa-solid fa-sitemap"></i>
                                                        </div>
                                                    </div>
                                                    @if ($userRole === 'unit kerja' && $userUnit)
                                                        <input type="text" class="form-control" value="{{ $userUnit->unit_nama }}" readonly>
                                                        <input type="hidden" name="unit_id" value="{{ $userUnit->unit_id }}">
                                                    @else
                                                        <select class="form-control @error('unit_id') is-invalid @enderror" name="unit_id" required>
                                                            <option value="" disabled>Pilih Unit Kerja</option>
                                                            @foreach ($units as $unit)
                                                                <option value="{{ $unit->unit_id }}" 
                                                                        {{ old('unit_id', $programkerja->unit_id) == $unit->unit_id ? 'selected' : '' }}>
                                                                    {{ $unit->unit_nama }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('unit_id')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group pt-4">
                                                <label for="anggaran">Anggaran (Rp)</label>
                                                <input type="number" name="anggaran" id="anggaran" class="form-control"
                                                       value="{{ old('anggaran', $programkerja->anggaran ?? '') }}" step="0.01" min="0">
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
