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
                                            <select id="std_id" name="std_id" class="form-control" required>
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
                                                <div class="input-group-text">
                                                    <i class="fas fa-building"></i>
                                                </div>
                                            </div>
                                            <select id="unit_id" name="unit_id[]" class="form-control select2" multiple required>
                                                <option value="">Pilih Unit Kerja</option>
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

                                    {{-- <div class="form-group">
                                        <label for="ik_baseline">Nilai Baseline</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-sort-amount-down"></i>
                                                </div>
                                            </div>

                                            @php
                                                $baselineValue = old('ik_baseline', $baseline_tahun_aktif);
                                                $ketercapaianType = strtolower($indikatorkinerja->ik_ketercapaian);
                                            @endphp

                                            @if($ketercapaianType === 'nilai')
                                                <input type="number" id="ik_baseline" name="ik_baseline"
                                                    class="form-control @error('ik_baseline') is-invalid @enderror"
                                                    value="{{ $baselineValue }}"
                                                    min="0" step="1"
                                                    placeholder="Masukkan nilai baseline untuk tahun {{ $activeTahun->th_tahun ?? 'aktif' }}" required>
                                            @elseif($ketercapaianType === 'persentase')
                                                <input type="number" id="ik_baseline" name="ik_baseline"
                                                    class="form-control @error('ik_baseline') is-invalid @enderror"
                                                    value="{{ $baselineValue }}"
                                                    min="0" max="100" step="1"
                                                    placeholder="Masukkan nilai baseline (0–100) untuk tahun {{ $activeTahun->th_tahun ?? 'aktif' }}" required>
                                            @elseif($ketercapaianType === 'ketersediaan')
                                                <select id="ik_baseline" name="ik_baseline"
                                                    class="form-control @error('ik_baseline') is-invalid @enderror" required>
                                                    <option value="ada" {{ $baselineValue === 'ada' ? 'selected' : '' }}>ada</option>
                                                    <option value="draft" {{ $baselineValue === 'draft' ? 'selected' : '' }}>draft</option>
                                                </select>
                                            @elseif($ketercapaianType === 'rasio')
                                                <input type="text" id="ik_baseline" name="ik_baseline"
                                                    class="form-control @error('ik_baseline') is-invalid @enderror"
                                                    value="{{ $baselineValue }}"
                                                    placeholder="contoh: 1:20" required>
                                            @endif

                                            @error('ik_baseline')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <small id="ti_baseline_hint" class="form-text text-muted">
                                            Isi sesuai dengan jenis ketercapaian.
                                        </small>
                                    </div> --}}

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
    {{-- <script>
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
    
        window.onload = updateBaselinePlaceholder;
    </script>    

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ketercapaianSelect = document.getElementById('ik_ketercapaian'); // Ambil elemen select ketercapaian
            const baselineContainer = document.querySelector('#ik_baseline').parentNode;
            let baselineInput = document.getElementById('ik_baseline');
            const oldValue = @json(old('ik_baseline', $baseline_tahun_aktif));

            function createSelectBaseline(selectedValue) {
                const select = document.createElement('select');
                select.name = 'ik_baseline';
                select.id = 'ik_baseline';
                select.className = baselineInput.className;
                ['ada', 'draft'].forEach(opt => {
                    const option = document.createElement('option');
                    option.value = opt;
                    option.textContent = opt;
                    if (opt === selectedValue) option.selected = true;
                    select.appendChild(option);
                });
                baselineContainer.replaceChild(select, baselineInput);
                baselineInput = select;
            }

            function createNumberBaseline(min, max = null, value = '') {
                const input = document.createElement('input');
                input.type = 'number';
                input.name = 'ik_baseline';
                input.id = 'ik_baseline';
                input.className = baselineInput.className;
                input.min = min;
                input.step = 1;
                if (max !== null) input.max = max;
                input.value = value;
                baselineContainer.replaceChild(input, baselineInput);
                baselineInput = input;
            }

            function createTextBaseline(placeholder = '', value = '') {
                const input = document.createElement('input');
                input.type = 'text';
                input.name = 'ik_baseline';
                input.id = 'ik_baseline';
                input.className = baselineInput.className;
                input.placeholder = placeholder;
                input.value = value;
                baselineContainer.replaceChild(input, baselineInput);
                baselineInput = input;
            }

            function updateBaselineField() {
                const type = ketercapaianSelect.value.toLowerCase();

                if (type === 'nilai') {
                    createNumberBaseline(0, null, oldValue);
                } else if (type === 'persentase') {
                    createNumberBaseline(0, 100, oldValue);
                } else if (type === 'ketersediaan') {
                    createSelectBaseline(oldValue);
                } else if (type === 'rasio') {
                    createTextBaseline('contoh: 1:20', oldValue);
                }
            }

            ketercapaianSelect.addEventListener('change', updateBaselineField);
            updateBaselineField(); // Jalankan saat halaman pertama kali dibuka
        });
    </script> --}}
@endpush
