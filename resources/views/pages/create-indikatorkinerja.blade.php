@extends('layouts.app')

@section('title', 'Create Indikator Kinerja Utama')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/bootstrap-daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Form Indikator Kinerja Utama</h1>
            </div>

            <div class="section-body">
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

                        <form method="POST" action="{{ route('indikatorkinerja.store') }}">
                            @csrf
                            <div class="row">
                                <!-- Kolom Kiri -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ik_kode">Kode Indikator Kinerja</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-code"></i>
                                                </div>
                                            </div>
                                            <input type="text" id="ik_kode" name="ik_kode" 
                                                class="form-control @error('ik_kode') is-invalid @enderror" 
                                                value="{{ old('ik_kode') }}" placeholder="Masukkan kode indikator" required>
                                            @error('ik_kode')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="ik_nama">Nama Indikator Kinerja</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            </div>
                                            <input type="text" id="ik_nama" name="ik_nama" 
                                                class="form-control @error('ik_nama') is-invalid @enderror" 
                                                value="{{ old('ik_nama') }}" placeholder="Masukkan nama indikator" required>
                                            @error('ik_nama')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="std_id">Standar</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-check-circle"></i>
                                                </div>
                                            </div>
                                            <select id="std_id" name="std_id" 
                                                class="form-control @error('std_id') is-invalid @enderror" required>
                                                <option value="" disabled selected>Pilih Standar</option>
                                                @foreach ($standar as $s)
                                                    <option value="{{ $s->std_id }}" {{ old('std_id') == $s->std_id ? 'selected' : '' }}>
                                                        {{ $s->std_nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('std_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Status Aktif</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-toggle-on"></i>
                                                </div>
                                            </div>
                                            <select class="form-control" name="ik_is_aktif">
                                                @foreach ($ik_is_aktifs as $ik_is_aktif)
                                                    <option value="{{ $ik_is_aktif }}" {{ old('ik_is_aktif', 'y') == $ik_is_aktif ? 'selected' : '' }}>
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
                                        <label for="ik_jenis">Jenis Indikator Kinerja</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-bullseye"></i>
                                                </div>
                                            </div>
                                            <select id="ik_jenis" name="ik_jenis" 
                                                class="form-control" required>
                                                <option value="" disabled selected>Pilih Jenis</option>
                                                @foreach ($jeniss as $jenis)
                                                    <option value="{{ $jenis }}" {{ old('ik_jenis') == $jenis ? 'selected' : '' }}>
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
                                            <select id="ik_ketercapaian" name="ik_ketercapaian" 
                                                class="form-control" required onchange="updateBaselinePlaceholder()">
                                                <option value="" disabled selected>Pilih Ketercapaian</option>
                                                @foreach ($ketercapaians as $ketercapaian)
                                                    <option value="{{ $ketercapaian }}" {{ old('ik_ketercapaian') == $ketercapaian ? 'selected' : '' }}>
                                                        {{ $ketercapaian }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="ik_baseline">Nilai Baseline</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-sort-amount-down"></i>
                                                </div>
                                            </div>
                                            <input type="text" id="ik_baseline" name="ik_baseline" 
                                                class="form-control @error('ik_baseline') is-invalid @enderror" 
                                                value="{{ old('ik_baseline') }}" placeholder="Masukkan nilai baseline" required>
                                            @error('ik_baseline')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <small id="ti_baseline_hint" class="form-text text-muted">Isi sesuai dengan jenis ketercapaian.</small>
                                    </div>
                                    
                                </div>
                            </div>

                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                                <a href="{{ url('indikatorkinerja') }}" class="btn btn-danger">
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

    @include('sweetalert::alert')

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/forms-advanced-forms.js') }}"></script>
    <script>
        function updateBaselinePlaceholder() {
            const ketercapaian = document.getElementById('ik_ketercapaian').value;
            const baselineInput = document.getElementById('ik_baseline');
            const tibaselineHint = document.getElementById("ti_baseline_hint");

            if (ketercapaian === 'nilai') {
                baselineInput.placeholder = 'Menggunakan Ketercapaian Nilai';
                tibaselineHint.textContent = "Isi dengan angka (contoh: 1.1, 1.2)";
                baselineInput.type = 'number';
                baselineInput.min = 0;
                baselineInput.step = '0.1';
            } else if (ketercapaian === 'persentase') {
                baselineInput.placeholder = 'Menggunakan Ketercapaian Persentase';
                tibaselineHint.textContent = "Isi dengan angka 1-100%";
                baselineInput.type = 'number';
                baselineInput.min = 0;
                baselineInput.max = 100;
                baselineInput.step = '1';
            } else if (ketercapaian === 'ketersediaan') {
                baselineInput.placeholder = 'Menggunakan Ketercapaian Ketersediaan';
                tibaselineHint.textContent = 'Isi dengan "ada" atau "draft"';
                baselineInput.type = 'text';
                baselineInput.removeAttribute('min');
                baselineInput.removeAttribute('max');
                baselineInput.removeAttribute('step');
            } else if (ketercapaian === 'rasio') {
                baselineInput.placeholder = 'Menggunakan Ketercapaian Rasio';
                tibaselineHint.textContent = "Isi dengan rasio (contoh: 1:20, 1:25)";
                baselineInput.type = 'text';
                baselineInput.removeAttribute('min');
                baselineInput.removeAttribute('max');
                baselineInput.removeAttribute('step');
            }
        }
    </script>
@endpush