@extends('layouts.app')
@section('title','SPMI')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
@endpush

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Ubah Data Capaian Indikator Kinerja</h1>
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

                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h4 class="text-danger mb-3">Data Target Indikator</h4>
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
                                                    <label for="ik_ketercapaian">Jenis Ketercapaian</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fa-solid fa-tags"></i>
                                                            </span>
                                                        </div>
                                                        <!-- Mengambil data dari relasi indikatorKinerja -->
                                                        <input type="text" id="ik_ketercapaian" class="form-control text-capitalize" 
                                                            value="{{ $targetIndikator->indikatorKinerja->ik_ketercapaian ?? '-' }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- <div class="col-md-6">
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
                                            </div> --}}
                                        </div>
                                    </div>
                                </div>
                    
                                @php
                                    // Setup logic PHP di View untuk mempermudah kondisi
                                    $isAdmin = Auth::user()->role === 'admin';
                                    $readonlyMonitoring = $isAdmin; 
                                    $readonlyEvaluasi = $isAdmin && empty($monitoringikuDetail->mtid_capaian);

                                    $jenis_ik_db = strtolower($targetIndikator->indikatorKinerja->ik_ketercapaian ?? 'nilai');
                                    
                                    $is_ketersediaan = in_array($jenis_ik_db, ['ketersediaan', 'status', 'ada/tidak']);

                                    $capaian_terakhir = old('mtid_capaian', $monitoringikuDetail->mtid_capaian);
                                    
                                    $input_value = '';
                                    if (!$is_ketersediaan) {
                                        if (str_ends_with($capaian_terakhir, '%')) {
                                            $input_value = str_replace('%', '', $capaian_terakhir);
                                        } elseif (preg_match('/^\d+\s*:\s*\d+$/', $capaian_terakhir)) {
                                            $input_value = str_replace(' ', '', $capaian_terakhir);
                                        } else {
                                            $input_value = $capaian_terakhir; // Nilai murni
                                        }
                                    }
                                    
                                    if(old('capaian_value')) {
                                        $input_value = old('capaian_value');
                                    }
                                @endphp

                                <div class="card shadow-sm mb-3">
                                    <div class="card-body">
                                        <h4 class="text-danger mb-3">Data Capaian</h4>
                                        <div class="row">
                                            {{-- Baseline --}}
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

                                            {{-- Target --}}
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

                                            @if($is_ketersediaan)
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="mtid_capaian" class="font-weight-bold">Status Ketercapaian <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <select class="form-control select2" name="mtid_capaian" id="mtid_capaian" required @if($readonlyMonitoring) disabled @endif>
                                                                <option value="" disabled {{ empty($capaian_terakhir) ? 'selected' : '' }}>-- Pilih Status --</option>
                                                                <option value="ada" {{ $capaian_terakhir == 'ada' ? 'selected' : '' }}>Ada (Tercapai/Tersedia)</option>
                                                                <option value="draft" {{ $capaian_terakhir == 'draft' ? 'selected' : '' }}>Draft (Belum Lengkap)</option>
                                                            </select>
                                                        </div>
                                                        <input type="hidden" name="capaian_value" value="">
                                                    </div>
                                                </div>
                                            @else                                                
                                                <input type="hidden" name="mtid_capaian" value="{{ $jenis_ik_db }}">

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="capaian_value" class="font-weight-bold">
                                                            @if ($isAdmin)
                                                                Nilai Capaian 
                                                            @else
                                                                Masukkan Nilai Capaian 
                                                                @if($jenis_ik_db == 'persentase') (Tanpa %) 
                                                                @elseif($jenis_ik_db == 'rasio') (Format x:y) 
                                                                @endif
                                                            @endif
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text">
                                                                    <i class="fa-solid fa-chart-line fa-fw"></i>
                                                                </div>
                                                            </div>
                                                            <input type="text" class="form-control" name="capaian_value" id="capaian_value"
                                                                value="{{ $input_value }}" 
                                                                @if($readonlyMonitoring) readonly @endif
                                                                placeholder="{{ $jenis_ik_db == 'rasio' ? 'Contoh 1:20' : 'Masukkan Angka' }}"
                                                                required>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="mtid_url">URL Bukti Dukung <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fa-solid fa-link"></i>
                                                            </span>
                                                        </div>
                                                        <input class="form-control" type="url" name="mtid_url" 
                                                            value="{{ old('mtid_url', $monitoringikuDetail->mtid_url) }}" 
                                                            placeholder="https://..."
                                                            @if($readonlyMonitoring) readonly @endif>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="mtid_keterangan">Pelaksanaan</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fa-solid fa-clipboard-list"></i>
                                                            </span>
                                                        </div>
                                                        <textarea class="form-control" name="mtid_keterangan" rows="4" 
                                                            @if($readonlyMonitoring) readonly @endif>{{ old('mtid_keterangan', $monitoringikuDetail->mtid_keterangan) }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
        $(document).ready(function() {
            // 1. Inisialisasi Select2
            // Ini penting agar dropdown "Ada/Draft" tampil bagus
            if ($(".select2").length) {
                $(".select2").select2();
            }

            // 2. Format Otomatis (Opsional)
            // Karena library Cleave.js sudah diload, kita manfaatkan untuk format Rasio
            // Cek apakah input text capaian_value sedang dirender oleh PHP
            var inputCapaian = document.getElementById('capaian_value');
            var inputJenis = document.querySelector('input[name="mtid_capaian"]'); // Hidden/Readonly input

            if (inputCapaian && inputJenis) {
                var jenis = inputJenis.value.toLowerCase();

                // Jika jenisnya Rasio, format otomatis jadi "Angka : Angka"
                if (jenis === 'rasio') {
                    new Cleave(inputCapaian, {
                        delimiter: ' : ',
                        blocks: [5, 5], // Mengizinkan format seperti 12345 : 12345
                        numericOnly: true
                    });
                }
                
                // Jika jenisnya Persentase, pastikan hanya angka
                if (jenis === 'persentase') {
                    new Cleave(inputCapaian, {
                        numeral: true,
                        numeralDecimalScale: 2
                    });
                }
            }
        });
    </script>
@endpush
