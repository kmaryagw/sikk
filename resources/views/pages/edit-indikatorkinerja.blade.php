@extends('layouts.app')

@section('title', 'edit-indikatorkinerjautama')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/bootstrap-daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">

    <style>
        /* Membuat Select2 menyesuaikan lebar di dalam input-group */
        .input-group > .select2-container--default {
            flex: 1 1 auto;
            width: 1% !important; /* Trik agar flexbox bekerja sempurna */
        }

        /* Menghilangkan radius kiri agar menyatu dengan ikon & atur border */
        .input-group > .select2-container--default .select2-selection--multiple {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            border: 1px solid #ced4da;
            min-height: 38px; /* Tinggi standar input Bootstrap */
        }

        /* Menyamakan efek fokus (biru) dengan Bootstrap */
        .input-group > .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        /* Memastikan ikon posisinya pas di tengah vertikal */
        .input-group-text {
            display: flex;
            align-items: center;
            height: 100%;
        }
    </style>
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Edit Indikator Kinerja Utama/Tambahan</h1>
            </div>

            <div class="section-body">
                <div class="card">
                    
                    <div class="card-body">
                        <!-- Menampilkan Error -->
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
                        <form method="POST" action="{{ route('indikatorkinerja.update', $indikatorkinerja->ik_id) }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <!-- Kolom Kiri -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ik_kode">Kode Indikator Kinerja Utama</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-code"></i>
                                                </div>
                                            </div>
                                            <input type="text" id="ik_kode" name="ik_kode" 
                                                class="form-control" 
                                                value="{{ old('ik_kode', $indikatorkinerja->ik_kode) }}" 
                                                placeholder="Masukkan kode indikator" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="ik_nama">Nama Indikator Kinerja Utama</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-user"></i>
                                                </div>
                                            </div>
                                            <input type="text" id="ik_nama" name="ik_nama" 
                                                class="form-control" 
                                                value="{{ old('ik_nama', $indikatorkinerja->ik_nama) }}" 
                                                placeholder="Masukkan nama indikator" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="std_id">Standar</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-thumbs-up"></i>
                                                </div>
                                            </div>
                                            <!-- Tambahkan class 'select2' di bawah ini -->
                                            <select id="std_id" name="std_id" class="form-control select2" required>
                                                <option value="" disabled selected>Pilih Standar</option>
                                                @foreach ($standar as $s)
                                                    <option value="{{ $s->std_id }}" {{ old('std_id', $indikatorkinerja->std_id ?? '') == $s->std_id ? 'selected' : '' }}>
                                                        {{ $s->std_nama }} - {{ $s->std_deskripsi }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="ren_is_aktif">Status Aktif</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-toggle-on"></i>
                                                </div>
                                            </div>
                                            <select class="form-control" name="ik_is_aktif">
                                                @foreach ($ik_is_aktifs as $ik_is_aktif)
                                                    <option value="{{ $ik_is_aktif }}" 
                                                        {{ old('ik_is_aktif', $indikatorkinerja->ik_is_aktif) == $ik_is_aktif ? 'selected' : '' }}>
                                                        {{ $ik_is_aktif == 'y' ? 'Ya' : 'Tidak' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                

                                <!-- Kolom Kanan -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="unit_id">Unit Kerja Penanggung Jawab</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-building"></i>
                                                </span>
                                            </div>
                                            
                                            <select id="unit_id" name="unit_id[]" class="form-control select2" multiple required>
                                                {{-- Hapus <option value=""> manual, gunakan JS placeholder --}}
                                                
                                                @foreach ($unitKerjas as $unit)
                                                    <option value="{{ $unit->unit_id }}" 
                                                        {{ in_array($unit->unit_id, old('unit_id', $indikatorkinerja->unitKerja->pluck('unit_id')->toArray() ?? [])) ? 'selected' : '' }}>
                                                        {{ $unit->unit_nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="ik_jenis">Jenis Indikator Kinerja</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-list-alt"></i>
                                                </div>
                                            </div>
                                            <select id="ik_jenis" name="ik_jenis" class="form-control" required>
                                                @foreach ($jeniss as $jenis)
                                                    <option value="{{ $jenis }}" {{ old('ik_jenis', $indikatorkinerja->ik_jenis) == $jenis ? 'selected' : '' }}>
                                                        {{ $jenis }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="ik_ketercapaian">Pengukur Ketercapaian</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-chart-line"></i>
                                                </div>
                                            </div>
                                            <select id="ik_ketercapaian" name="ik_ketercapaian" class="form-control" required onchange="updateBaselinePlaceholder()">
                                                @php
                                                    $selectedKetercapaian = strtolower(old('ik_ketercapaian', $indikatorkinerja->ik_ketercapaian ?? ''));
                                                @endphp
                                                <option value="nilai" {{ $selectedKetercapaian == 'nilai' ? 'selected' : '' }}>Nilai</option>
                                                <option value="persentase" {{ $selectedKetercapaian == 'persentase' ? 'selected' : '' }}>Persentase</option>
                                                <option value="ketersediaan" {{ $selectedKetercapaian == 'ketersediaan' ? 'selected' : '' }}>Ketersediaan</option>
                                                <option value="rasio" {{ $selectedKetercapaian == 'rasio' ? 'selected' : '' }}>Rasio</option>
                                            </select>
                                            
                                        </div> 
                                    </div>

                                </div>
                            </div>

                            <!-- Tombol Aksi -->
                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                                <a href="{{ route('indikatorkinerja.index') }}" class="btn btn-danger">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </form>
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

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/forms-advanced-forms.js') }}"></script>
   
    <script>
        $(document).ready(function() {
            $('#unit_id').select2({
                placeholder: "Pilih Unit Kerja",
                allowClear: true,
                width: 'resolve' 
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4', 
                placeholder: "Pilih Standar",
                allowClear: true,
                width: 'resolve' 
            });
        });
    </script>
@endpush
