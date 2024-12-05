@extends('layouts.app')

@section('title', 'Tambah Program Kerja')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
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

                                    <form method="POST" action="{{ route('programkerja.store') }}">
                                        @csrf
                                        <div class="form-group">
                                            <label>Nama Program Kerja</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fa-solid fa-briefcase"></i>
                                                    </div>
                                                </div>
                                                <input class="form-control" type="text" name="rk_nama" value="{{ old('rk_nama') }}" required />
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Unit Kerja</label>
                                            <select class="form-control select2" name="unit_id" required>
                                                <option value="" disabled selected>Pilih Unit Kerja</option>
                                                @foreach ($units as $unit)
                                                    @if ($unit->unit_kerja == 'y')
                                                        <option value="{{ $unit->unit_id }}" {{ old('unit_id') == $unit->unit_id ? 'selected' : '' }}>
                                                            {{ $unit->unit_nama }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="th_id">Pilih Tahun</label>
                                            <select name="th_id" id="th_id" class="form-control" required>
                                                @foreach($tahunAktif as $tahun)
                                                    <option value="{{ $tahun->th_id }}">{{ $tahun->th_tahun }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Pilih Indikator Kinerja -->
                                        <div class="form-group">
                                            <label for="ik_id">Pilih Indikator Kinerja</label>
                                            <select id="ik_id" name="ik_id[]" class="form-control select2" multiple>
                                            
                                                @foreach($indikatorKinerja as $indikator)
                                                    <option value="{{ $indikator->ik_id }}">{{ $indikator->ik_nama }}</option>
                                                @endforeach
                                            </select>
                                         </div>

                                        <div class="form-group">
                                            <label>Pilih Periode Monev</label>
                                            <div class="form-check">
                                                @foreach ($periodes as $periode)
                                                    <input type="checkbox" class="form-check-input" name="pm_id[]" 
                                                        value="{{ $periode->pm_id }}" 
                                                        {{ in_array($periode->pm_id, old('pm_id', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label">{{ $periode->pm_nama }}</label><br>
                                                @endforeach
                                            </div>
                                        </div>
                                        
                                        

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                            <a href="{{ route('programkerja.index') }}" class="btn btn-danger">Kembali</a>
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
    <script src="{{ asset('library/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Pilih Opsi",
                allowClear: true
            });
            $('.select2-multiple').select2({
                placeholder: "Pilih Indikator Kinerja (IKU/IKT)",
                allowClear: true
            });
        });
    </script>
@endpush
