@extends('layouts.app')
@section('title', 'Edit Monitoring Indikator Kinerja')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
@endpush

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Edit Monitoring Indikator Kinerja</h1>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body" style="background-color: #f4f4f4;">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $err)
                                            <li>{{ $err }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                    
                            <!-- Form -->
                            <form action="{{ route('monitoringiku.update-detail', ['mti_id' => $monitoringiku->mti_id, 'ti_id' => $targetIndikator->ti_id]) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <!-- Card 1: Informasi Utama -->
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h4 class="text-danger mb-3">Data Target Capaian</h4>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="prodi">Program Studi</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fa-solid fa-building-columns"></i>
                                                            </span>
                                                        </div>
                                                        <input type="text" id="prodi" class="form-control" value="{{ $monitoringiku->prodi->nama_prodi }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="tahun">Tahun Kerja</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fa-solid fa-calendar"></i>
                                                            </span>
                                                        </div>
                                                        <input type="text" id="tahun" class="form-control" value="{{ $monitoringiku->tahunKerja->th_tahun }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="ik_nama">Indikator Kinerja</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fa-solid fa-bullseye"></i>
                                                            </span>
                                                        </div>
                                                        <input type="text" id="ik_nama" class="form-control" value="{{ $targetIndikator->indikatorKinerja->ik_kode }} - {{ $targetIndikator->indikatorKinerja->ik_nama }}" readonly>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Baseline Input -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="baseline">Baseline</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fa-solid fa-sort-amount-down"></i>
                                                            </span>
                                                        </div>
                                                        <input class="form-control" name="baseline" value="{{ old('baseline', $baseline ?? '') }}" readonly>
                                                    </div>
                                                </div>
                                            </div>                                        
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="ti_target">Target</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fa-solid fa-award"></i>
                                                            </span>
                                                        </div>
                                                        <input type="text" id="ti_target" class="form-control" value="{{ $targetIndikator->ti_target }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="ti_keterangan">Keterangan Indikator</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fa-solid fa-info-circle"></i>
                                                            </span>
                                                        </div>
                                                        <input class="form-control" name="ti_keterangan" value="{{ $targetIndikator->ti_keterangan }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                    
                                @php
                                    $isAdmin = Auth::user()->role == 'admin';
                                    $isEmptyMonitoring = empty($monitoringikuDetail->mtid_capaian);

                                    // Card 2: read-only jika admin, untuk unit kerja editable
                                    $readonlyMonitoring = $isAdmin ? true : false;

                                    // Card 3: hanya admin, read-only jika monitoring belum diisi
                                    $readonlyEvaluasi = $isEmptyMonitoring;
                                @endphp

                                <!-- Card 2: Data Monitoring Indikator Kinerja -->
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="text-danger mb-3">Data Monitoring Indikator Kinerja</h4>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="mtid_capaian">Capaian</label>
                                                    <select class="form-control" name="mtid_capaian" id="mtid_capaian" required @if($readonlyMonitoring) disabled @endif>
                                                        <option value="" disabled {{ empty(old('mtid_capaian', $monitoringikuDetail->mtid_capaian)) ? 'selected' : '' }}>Pilih Capaian</option>
                                                        <option value="ada" {{ old('mtid_capaian', $monitoringikuDetail->mtid_capaian) === 'ada' ? 'selected' : '' }}>Ada</option>
                                                        <option value="draft" {{ old('mtid_capaian', $monitoringikuDetail->mtid_capaian) === 'draft' ? 'selected' : '' }}>Draft</option>
                                                        <option value="persentase" {{ str_contains(old('mtid_capaian', $monitoringikuDetail->mtid_capaian), '%') ? 'selected' : '' }}>Persentase</option>
                                                        <option value="nilai" {{ is_numeric(old('mtid_capaian', $monitoringikuDetail->mtid_capaian)) ? 'selected' : '' }}>Nilai</option>
                                                        <option value="rasio" {{ preg_match('/^\d+:\d+$/', old('mtid_capaian', $monitoringikuDetail->mtid_capaian)) ? 'selected' : '' }}>Rasio</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6" id="capaian_value_group">
                                                <div class="form-group">
                                                    <label for="capaian_value">Masukkan Value</label>
                                                    <input type="text" class="form-control" name="capaian_value" id="capaian_value"
                                                        value="{{ old('capaian_value', $monitoringikuDetail->mtid_capaian) }}" @if($readonlyMonitoring) readonly @endif>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="mtid_url">URL</label>
                                                    <input class="form-control" type="url" name="mtid_url" value="{{ old('mtid_url', $monitoringikuDetail->mtid_url) }}" @if($readonlyMonitoring) readonly @endif />
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="mtid_keterangan">Keterangan Tambahan</label>
                                                    <textarea class="form-control" name="mtid_keterangan" rows="4" @if($readonlyMonitoring) readonly @endif>{{ old('mtid_keterangan', $monitoringikuDetail->mtid_keterangan) }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card 3: Data Evaluasi (hanya admin) -->
                                @if($isAdmin)
                                    <div class="card mt-3">
                                        <div class="card-body">
                                            <h4 class="text-danger mb-3">Data Evaluasi</h4>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="mtid_evaluasi">Evaluasi</label>
                                                        <textarea class="form-control" name="mtid_evaluasi" rows="4" @if($readonlyEvaluasi) readonly @endif>{{ old('mtid_evaluasi', $monitoringikuDetail->mtid_evaluasi) }}</textarea>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="mtid_tindaklanjut">Tindak Lanjut</label>
                                                        <textarea class="form-control" name="mtid_tindaklanjut" rows="4" @if($readonlyEvaluasi) readonly @endif>{{ old('mtid_tindaklanjut', $monitoringikuDetail->mtid_tindaklanjut) }}</textarea>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="mtid_peningkatan">Peningkatan</label>
                                                        <textarea class="form-control" name="mtid_peningkatan" rows="4" @if($readonlyEvaluasi) readonly @endif>{{ old('mtid_peningkatan', $monitoringikuDetail->mtid_peningkatan) }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="form-group text-right">
                                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Simpan</button>
                                    <a href="{{ route('monitoringiku.index-detail', $monitoringiku->mti_id) }}" class="btn btn-danger"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
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

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const mtidCapaianInput = document.getElementById("mtid_capaian");
            const capaianInputGroup = document.getElementById('capaian_value_group');
            const capaianInput = document.getElementById('capaian_value');
    
            function toggleInputVisibility() {
                const selectedValue = mtidCapaianInput.value;
    
                if (selectedValue === 'persentase' || selectedValue === 'nilai' || selectedValue === 'rasio') {
                    capaianInputGroup.style.display = 'block';
                } else {
                    capaianInputGroup.style.display = 'none';
                    capaianInput.value = ''; // reset value when not needed
                }
            }
    
            // Event listener
            mtidCapaianInput.addEventListener('change', toggleInputVisibility);
    
            // Inisialisasi saat halaman dimuat
            toggleInputVisibility();
        });
    </script>
@endpush
